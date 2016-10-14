<?php

class Moto_Wpproxy
{
	protected 	$_cacheDir;
	protected 	$_params = array(
					'currentSection' => 'faq',
					'proxyTime' => 7200,
					'apiurl' => 'http://faq.templatemonster.ru/',
					'devVersion' => false,
					'cache' => true,
					'errormessage' => 'Извините, проблема на сервере',
					'logFile' => './faqerrorlog.txt',
					'cacheDir' => 'cacheWP',
					'apikey' => 'moto'
				);

	public function __construct($params)
	{
		foreach ($params as $k => $param)
		{
			$this->_params[$k] = $param;
		}
		$this->_cacheDir = CURRENT_THEME_DIR . 'cache/' . $this->_params['cacheDir'] . '/';
	}
	
	public function getPage($uri, $data = array())
	{
		if ($this->_params['cache'])
		{
			$content = $this->_getPageWithCache($uri, $data);
		}
		else
		{
			$response = $this->_getContent($this->_params['apiurl'] . $uri, $data);
			$content = $response['content'];
			if ($response['code'] != '200')
			{
				header("Location: /ru/404.html");
				return;
			}
		}
		return $content;
	}
	
	protected function _getPageWithCache($uri, $data)
	{
		if (!is_dir(CURRENT_THEME_DIR . 'cache'))
			mkdir(CURRENT_THEME_DIR . 'cache', 0755, true);
		if (!is_dir(CURRENT_THEME_DIR . 'cache/' . $this->_params['cacheDir']))
			mkdir(CURRENT_THEME_DIR . 'cache/' . $this->_params['cacheDir'], 0755, true);
		$filepath = $this->_cacheDir . md5($uri);

		if (!file_exists($filepath) || ($this->_getLastModTime($filepath) + $this->_params['proxyTime'] < time()) )  //if file was updated more then $this->_params['proxyTime'] seconds ago or not exists
		{
			try
			{
				$response = $this->_getContent($this->_params['apiurl'] . $uri, $data);

				if($response['code'] == 200)
				{
					$content = $response['content'];
					file_put_contents($filepath, $content);
				} 
				else if ($response['code'] == '301' || $response['code'] == '404' )
				{
					header("Location: /ru/404.html");
					return;
				}
				else
				{
					throw new Exception("request: '".$this->_params['apiurl'] . $uri."'\nserver response code is ".$response['code']);
				}
			}
			catch (Exception $e) 
			{
				if(file_exists($filepath))
				{
					$content = file_get_contents($filepath);
				}
				else
				{
					$content = $this->_params['errormessage'];
				}
				file_put_contents($this->_params['logFile'], date("Y-m-d H:i:s") . ' ' . $e->getMessage() . "\n\n", FILE_APPEND);
			}
		}
		else
		{
			$content = file_get_contents($filepath);
		}
		return $content;
	}
	
	public function clearCacheFile($uri)
	{
		$fname = md5($uri);
		if (!file_exists($fname))
		{
			throw new Exception('cache file with uri '.$uri.' is not exist');
		}
		unlink ($this->_cacheDir . $fname);
	}
	
	public function clearAllCache()
	{
		$files = scandir($this->_cacheDir);
		foreach ($files as $file) 
		{
			if($file != '.' && $file != '..')
				unlink ($this->_cacheDir . $file);
		}
	}
	
	protected function _getLastModTime($path)
	{
		$s = stat($path);
		return $s['mtime'];
	}
	
	protected function _getContent($url, $data = array())
	{
		if(isset($this->_params['debug']) && $this->_params['debug'])
			file_put_contents('./log.txt', date("Y-m-d H:i:s") . ' ' . $url . "\n\n", FILE_APPEND);
		if (!function_exists('curl_init'))
			throw new Exception('cURL is not installed!');
		$ch = curl_init();
		if (!empty($this->_params['apikey']))
		{
			$url .= (strpos($url, '?') ? '&' : '?');
			$url .= 'apikey=' . $this->_params['apikey'];
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		if (count($data) > 0)
		{
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		 
		$content = trim(curl_exec($ch));
		$code = curl_getinfo ($ch, CURLINFO_HTTP_CODE);
		return array('code' => $code, 'content' => $content);
	}
	
	function getHeaderFromContent($content, $skip)
	{
		$headers = array();
		if (preg_match_all('/<meta name=[\"\']([^\"\']+)[\"\'] content=[\"\']([^\"\']+)[\"\']/ius', $content, $match))
		{
			for($i = 0, $icount = count($match[1]); $i < $icount; $i++)
			{
				if (trim($match[1][$i]) != '' && trim($match[2][$i]) != '' && !in_array(trim($match[1][$i]), $skip) )
				{
					$headers[ trim($match[1][$i]) ] = trim($match[2][$i]);
				}
			}
		}
		return $headers;
	}
}

