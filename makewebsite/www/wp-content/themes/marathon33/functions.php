<?php
/**
 * Get list posts
 * @return array
 */
function getListPosts(){
	$args = array(
		'orderby'  => 'id',
		'order' => 'ASC',
		'post_type' => 'post',
		'posts_per_page' => -1,
		'post_status' => 'publish',
	);
	$postsArray = get_posts($args);
	return $postsArray;
}

/**
 * Get number lesson
 * @param int $number
 * @return array
 */
function getNumber($number){
	return $number < 10 ? "0" . $number : $number;
}

/**
 * Get title page
 * @return string
 */
function get_tittle_page(){	
	$search_parameter = get_query_var('s');
	if($search_parameter == ""){
		$title_raw = is_home() ? get_bloginfo('description') : get_the_title();
		$title = "<title>".$title_raw."</title>";
	}else{
		$title = "<title>". pll__("title_search_result") . ": " . $search_parameter . "</title>";
	}
	return $title;	
}

/**
 * Declension months
 * @param string $month
 * @return string
 */ 
function declensionMonth($month){
	$translate = "";
	switch ($month) {
		case '01':
			$translate = "января";
			break;
		case '02':
			$translate = "февраля";
			break;
		case '03':
			$translate = "марта";
			break;
		case '04':
			$translate = "апреля";
			break;
		case '05':
			$translate = "мая";
			break;
		case '06':
			$translate = "июня";
			break;
		case '07':
			$translate = "июля";
			break;
		case '08':
			$translate = "августа";
			break;
		case '09':
			$translate = "сентября";
			break;
		case '10':
			$translate = "октября";
			break;
		case '11':
			$translate = "ноября";
			break;
		case '12':
			$translate = "декабря";
			break;
	}
	return mb_strtoupper($translate, "UTF-8");
}

/**
 * Get left time
 * @param int $time
 * @return array
 */
function leftTime($time){
	$time = $time + 86400;
	$hours = floor($time/3600);
	$minutes = floor(($time/3600 - $hours)*60);
	if($hours < 5 && $hours >0){
		$text_hour = "час";
	}else{
		$text_hour = "часов";
	}
 	return array(
 		'hour' => $hours,
 		'minut' => $minutes,
 		"text_hour" => $text_hour,
 		"text_minut" => "минут"
 	);
}

/**
 * Get information about lesson
 * @param array $params
 * @return array
 */ 
function getInformationLesson($params){
	$result = array();
	$dateNow = time();

	if(!isset($params["wpcf-date-start-lesson"][0])){
		$result["state_description"] = "feature";
		$result["class_wrapper"] = "locked";
	}else{
		$dateStop = time();
		$dateStart = $params["wpcf-date-start-lesson"][0];
		if(isset($params["wpcf-date-end-lesson"][0])){
			$dateStop = $params["wpcf-date-end-lesson"][0];
		}

		if($dateNow >= $dateStart && $dateNow <= $dateStop){	
			$result["state_description"] = "now";
			$diffTime = $dateStop - $dateNow;
			$left = leftTime($diffTime);
			$result["time_completion"] = "<i style='font-style: normal;'>" . $left["hour"] . "</i> " . $left["text_hour"] . " <b style='font-weight: 400; color: #616161'>" . $left["minut"] . "</b> " . $left["text_minut"];
			$result["time_completion_hour"] = $left["hour"];
			$result["time_completion_minut"] = $left["minut"];
			$result["time_dif"] = $diffTime;
		}else if($dateNow > $dateStop){
			$result["state_description"] = "past";
		}else{
			$result["state_description"] = "feature";	
			$dateAvailable = date("d", $dateStart) . " " . declensionMonth(date("m", $dateStart));
			$result["date_available"] = $dateAvailable;
		}

		$result["class_wrapper"] = "";
		if($result["state_description"] == "feature"){
			$result["class_wrapper"] = "locked";
		}
	}
	return $result;
}

/**
 * Get title post (trim protected)
 * @param string $title
 * @return string 
 */
function the_title_trim($title){
    $title = str_replace("Защищено:", "", $title);
    return $title;
}
add_filter('the_title', 'the_title_trim');


/**
 * Protected post paswword
 * @param object $posr
 */
function wpse_post_password_required($post = null) {
    $post = get_post($post);

    if (empty( $post->post_password)){
    	return false;
    }            

    if (!isset($_COOKIE['wp-postpass_' . COOKIEHASH])){
    	return true;
    }    

    require_once ABSPATH . WPINC . '/class-phpass.php';
    $hasher = new PasswordHash( 8, true );

    $hash = wp_unslash( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ]);
    if (0 !== strpos( $hash, '$P$B')){
        return true;
    }

    if( $hasher->CheckPassword( $post->post_password, $hash )){    	
        return false;
    }

    $custom = get_post_meta( $post->ID);
    if(!isset($custom["wpcf-wpse_extra_passwords"][0])){
        return true;
    }

    $extra_passwords = $custom["wpcf-wpse_extra_passwords"][0];
    $extra = explode(',', $extra_passwords );      
    foreach( (array) $extra as $password ){
        $password = trim( $password);
        if(!empty($password) && $hasher->CheckPassword($password, $hash)){        	
            return false;           
        }
    }   
    return true;
}

/**
 * Genereted custom form
 * @return string
 */
function custom_password_form() {
    $form = <<<FORM

	




    <form  class="post-password-form access k-545454" action="/wp-login.php?action=postpass" method="post">
		<div class="lesson_task_subtitle">
			Введите ключ для доступа к тексту урока
		</div>
		<fieldset>
			<div class="field-regular">
				<input type="text" placeholder="Ваш ключ: *" name="post_password" required/>
			</div>
			<span class="hint">
				Код доступа вы получите по email после предыдущего урока
			</span>
		</fieldset>
		<button type="submit" class="btn btn_sm red">войти</button>
	</form>
FORM;
	return $form;
}
add_filter('the_password_form', 'custom_password_form' );

/**
 * Get content post protected post
 * @param string $output
 * @return string
 */
add_filter('the_password_form', function($output){
    if(!is_single() || !in_the_loop() ||did_action('the_password_form')){
        return $output;
    }

    $post = get_post();
    if(wpse_post_password_required($post)){
        return $output;
    }

    $password = $post->post_password;
    $post->post_password = '';
    $content = get_the_content();
    $post->post_password = $password;
    return $content;
});