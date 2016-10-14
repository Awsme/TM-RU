<?php
/**
 * Created by JetBrains PhpStorm.
 * User: terion
 * Date: 17.01.13
 * Time: 15:40
 * To change this template use File | Settings | File Templates.
 */
class Moto_PhotolandingOrder
{
    //protected $orderSum;
    private $orderId;
    protected $response;
    protected $orderDetails;
    protected $error = array('status' => false, 'details' => array());
    protected $paymentScript = 'test_link/';
    private $pref = 'rutm_v4';
    private $tab = 'photolanding_order';

    function create($orderDetails)
    {
        $this->response = new Moto_ApiResponse();

        try
        {
	        $this->orderDetails = new Moto_OrderDetails($orderDetails);

			if (!$this->isDomainAvailable($orderDetails['domain']))
			{
				return $this->getResponse();
			}
            $this->calculateSum($this->hasDiscount());
            $current_date = date("Y-m-d H:i:s");

            $insertOrderResult = Database::instance()
                            ->insert("{$this->tab}",
                                array(
                                    "order_status" => 'pending',
                                    "order_domain" => $this->orderDetails->domain,
                                    "order_hosting" => $this->orderDetails->hostingPackageId,
                                    "order_template" =>  $this->orderDetails->template,
                                    "client_name" => $this->orderDetails->name,
                                    "client_phone" => $this->orderDetails->phone,
                                    "client_mail" =>  $this->orderDetails->email,
                                    "order_date" => $current_date,
                                    "order_price" => $this->orderDetails->finalPrice
                                ))
            ;

            $this->orderId  = $insertOrderResult->insert_id();
            $_SESSION["Photolanding"]["id"] =  $this->orderId;
            $_SESSION["Photolanding"]["status"] =  'pending';
            $_SESSION["order_created"] = true;
            $response['response'] = "Ok";
            $response['formToPayment'] = '';
            $response['message'] = 'Order was created, redirecting to robokassa...';
            header('Content-Type: application/json');
            echo json_encode($response) . "\n";
            exit;
        }
        catch (Exception $e)
        {
            if ($e instanceof OrderException)
            {
                $this->response->setError($e->getOptions());
            }
            else
            {
                $err = array(
                    'type' => 'default',
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                );
                $this->response->setError($err);
            }

        }

        return $this->getResponse();
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

	protected function isDomainAvailable($domain)
	{
		$isAvailable = true;
		$regru = new Moto_RegruWrapper(
			Moto_PhotolandingConfig::$regru['user'],
			Moto_PhotolandingConfig::$regru['pwd']
		);

		$response = $regru->checkDomain($domain);
		if (isset($response['response']['answer']['domains'][0]['result']) && $response['response']['answer']['domains'][0]['result'] == 'error')
		{
			$this->response->setError(array(
				'type' => 'regru',
				'code' => $response['response']['answer']['domains'][0]['error_code'],
				'message' => $response['response']['answer']['domains'][0]['error_text']
			));
			$isAvailable = false;
		}

		return $isAvailable;
	}

    protected function getResponse()
    {
        if (!$this->response->hasErrors())
        {
            $res = $this->_getPaymentLink();

            if ($res['status'] == 'success')
            {
                $this->response->setResponse($res['response']);
            }
            else
            {
                $this->response->setError($res['error']);
            }

        }

        return $this->response->get();

    }

    protected function isLiked(Moto_VkApi $VK, $offset, $count)
    {
        $res = false;
        $response = $VK->api('likes.getList', array(
            'type' => 'sitepage',
            'owner_id' => Moto_PhotolandingConfig :: $vk['appId'],
            'page_url' => Moto_PhotolandingConfig :: getPath(),
            'offset' => $offset,
            'count' => $count
        ));

        if (($response instanceof Moto_ApiResponse) )
        {
            $res = $response->get();

            // if vk error is #6 "Too many requests per second" then perform another one request after some time.
            if ($response->hasErrors() &&
                $response->getErrorType() == 'vk' &&
                $response->getErrorCode() == 6)
            {
                sleep(1);
                $response = $this->isLiked($VK, $offset, $count);
                $res = $response->get();
            }

            if ($response->hasErrors())
            {
                throw new OrderException($response->getError());
            }
        }
        return $res;
    }

    protected function hasDiscount($VK = NULL, $offset = 0, $count = 100)
    {
        $isLiked = false;
	    //if (!$this->userData->vkuid || $this->orderDetails['discount'] == 'false')
	    if (!$this->orderDetails->userData->vkuid || !$this->orderDetails->discount)
	    {
		    return false;
	    }

        if ($VK instanceof Moto_VkApi)
        {
            $firstTime = false;
        }
        else
        {
            $VK = new Moto_VkApi(Moto_PhotolandingConfig :: $vk['appId'], Moto_PhotolandingConfig :: $vk['secretKey']);
            $firstTime = true;
        }

        $resp = $this->isLiked($VK, $offset, $count);

        if (is_array($resp) && $resp['status'] == 'success')
        {
            $uids = $resp['response']['response']['users'];
            $uidsCount = $resp['response']['response']['count'];

            $isLiked = in_array($this->orderDetails->userData->vkuid, $uids);

            if (!$isLiked && $uidsCount > $count + $offset)
            {
                $offset += $firstTime ? 100 : 1000;
                $isLiked = $this->hasDiscount($VK, $offset, 1000);
            }
        }

        return $isLiked;
    }

    protected function calculateSum($hasDiscount = false)
    {
        $prices = Moto_PhotolandingConfig :: $prices;
        $hostingPlan = $this->orderDetails->hostingPackageId;

	    if (isset($prices['hostingPlans']) && isset($prices['hostingPlans'][$hostingPlan]))
	    {
		    $planPrice = floatval($prices['hostingPlans'][$hostingPlan]['price']);
	    }
	    else
	    {
		    throw new Exception('Invalid hosting plan name');
	    }
        $sum = $prices['domainPrice'] + $prices['templatePrice'] + $planPrice;
        $this->orderDetails->fullPrice = $sum;
        $this->orderDetails->finalPrice = $sum;
        if ($hasDiscount)
        {
            $this->orderDetails->finalPrice *= (1 - $prices['discount']);
        }
    }


    // robokassa
    protected function _getPaymentLink()
    {
        // $api = new Moto_Payment();
        // $api->setOption('key', 'tmru-photolanding');
        // $api->setOption('pass', 'c4ca4238a0b923820dcc509a6f75849b');
        // $api->setOption('url', 'http://accounts.cms-guide.com.rogue.fmt/api.shop.photolanding.php');
        // $api->setOption('debug', false);

        // $data = array(
        //     'email' => $this->orderDetails->email,
        //     'name' => $this->orderDetails->name,
        //     'phone' => $this->orderDetails->phone,
        //     'domain' => $this->orderDetails->domain,
        //     'template' => $this->orderDetails->template,

        //     'product_id' => 2,

        //     'merchant_id' => 3, //PayPal: 1; WM : 3
        //     'locale' => 'ru',

        //     'remote_addr' => $_SERVER['REMOTE_ADDR'],
        //     'language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
        //     'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        //     'referer' => $_SERVER['HTTP_REFERER'],


        //     'hosting' => $this->orderDetails->hostingPackageId,
        //     'vk_id' => $this->orderDetails->userData->vkuid,
        //     'discount' => (int)$this->orderDetails->discount,
        //     'price' => $this->orderDetails->finalPrice, //*
        //     'real_price' => $this->orderDetails->fullPrice, //*
        //     'success' => 'http://www.templatemonster.com.fmt/ru/photolanding/success/',
        //     'fail' => 'http://www.templatemonster.com.fmt/ru/photolanding/fail/',

        // );
        // $res = $api->send('getLinkByOrder', $data);

        // return $res;
        /*
        echo "<hr>";
        echo htmlspecialchars(print_r($result, true));
        exit;
        */
    }

    /*
    protected function _getPaymentLink()
    {
        $case = rand(0, 1);
        //$case = 1;
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        array_pop($uri);
        $url = "http://".$_SERVER['SERVER_NAME'] . implode('/', $uri) . '/' . $this->paymentScript;

        if ($case)
        {

            if (function_exists('http_build_query'))
            {
                $paymentDetails = http_build_query(array(
                    'vkuid' => $this->userData->vkuid,
                    'amount' => $this->orderSum
                ));
            }
            else
            {
                $paymentDetails = implode('&', array('vkuid=' . $this->userData->vkuid, 'amount=' . $this->orderSum));
            }
	        $res = array('paymentLink' => $url . '?' . $paymentDetails);
        }
        else
        {
	        $res = array('paymentForm' => '<form id="testformtest" style="display: none;"
					  action="https://www.paypal.com/cgi-bin/webscr"
					  target="_blank"
					  method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="BY37QULRMS6ML">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>');

        }

        return $res;
    }
    */
}

class OrderException extends Exception
{

    private $_options;

    public function __construct($error = array())
    {
        // add Moto_Error class checking.

        parent::__construct('Photolanding. Order creating error', $error['code']);
        $this->_options = $error;
    }

    public function getOptions()
    {
        return $this->_options;
    }
}

