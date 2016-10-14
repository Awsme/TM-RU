<?php
$version = date("YmdHis");
$shareUrl = 'http://site33days.templatemonster.ru/';



$currentDay = date("d");
$currentMonth = date("m");
$currentYear = date("Y");

$show = false;
if($currentDay >= "05" &&  $currentMonth >= "09" && $currentYear >= "2016" ){
	$show = false;
} else {
	$show = true;
}

/* add on closed */
$show = true;

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Свой сайт за 14 дней</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<link rel="icon" href="http://static.templatemonster.com/img/favicon.ico?772a997" type="image/x-icon">
	<link rel="shortcut icon" href="http://static.templatemonster.com/img/favicon.ico?772a997" type="image/x-icon">
	
	<!-- <link rel="stylesheet" href="css/reset.css?v<?php echo $version; ?>" type="text/css" media="all"> -->
	<link rel="stylesheet" href="css/style.css?v<?php echo $version; ?>" type="text/css" media="all">
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
	


	<script type="text/javascript" src="js/jquery-1.8.1.min.js?v<?php echo $version; ?>"></script>
	<script type="text/javascript" src="js/jquery.qtip.js?v<?php echo $version; ?>"></script>
	<script type="text/javascript" src="js/jquery.placeholder.js?v<?php echo $version; ?>"></script>
	<script type="text/javascript" src="js/jquery.validate.js?v<?php echo $version; ?>"></script>
	<script type="text/javascript" src="js/jquery.form.js?v<?php echo $version; ?>"></script>
	<script type="text/javascript" src="js/jquery.fancybox.js?v<?php echo $version; ?>"></script>	
	<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?48"></script>
	<script type="text/javascript" src="js/jquery.knob.js?v<?php echo $version; ?>"></script>
	<script type="text/javascript" src="js/countdown.js?v<?php echo $version; ?>"></script>
	<script type="text/javascript" src="js/script.js?v<?php echo $version; ?>"></script>
</head>

<body class="closed"> <!-- add class closed on closed-->
	<script>
		VK.init({apiId: 5272511, onlyWidgets: true});
	</script>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-T65DLF"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push(
{'gtm.start': new Date().getTime(),event:'gtm.js'}
);var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-T65DLF');</script>
<!-- End Google Tag Manager -->

	<?php if (isset($_GET['code']) && isset($_GET['id'])) { ?>
		<script>
			$('document').ready(function () {
				var urlAjax = "http://" + window.location.host + window.location.pathname + "ajax.php";
				var data = {
					code: '<?php echo $_GET['code']; ?>',
					id: '<?php echo $_GET['id']; ?>',
				};

				$.ajax({
                    type: "POST",
                    url: urlAjax,
                    data: data,
                    success: function (data) {
                    },
                    error: function () {}
                });
				window.location.href = "https://docs.google.com/forms/d/17ShyerEo_RSW9wMShf9L65_wa9fmB46AHhv8uVrm4ls/viewform?edit_requested=true";

				// $.fancybox("#marafon-questions");
				// dataLayer.push({ 'event':'studio61', 'eventCategory':'reg-marathon-61', 'eventAction':'success' });
			});
		</script>
	<?php } else {?>
		<script>
			$('document').ready(function () {
				//$.fancybox("#closed");
			});
		</script>
	<?php } ?>

	<div class="wrapper">
		<!-- HEADER -->
		<header class="header">
			<div class="header_top">
				<div class="container">
					<div class="row md">
						<div class="col-3 left">
							<div id="countdown" class="clearfix" ms-user-select="none">
								<input class="knob" id="days" data-readonly=true data-min="0" data-max="99"  data-width="72" data-height="72" data-thickness="0.1" data-fgcolor="#ffffff" data-bgColor="#41acca">
								<input class="knob" id="hours" data-readonly=true data-min="0" data-max="24"  data-width="72" data-height="72" data-thickness="0.1" data-fgcolor="#ffffff" data-bgColor="#41acca">
								<input class="knob" id="mins" data-readonly=true data-min="0" data-max="60"  data-width="72" data-height="72" data-thickness="0.1" data-fgcolor="#ffffff" data-bgColor="#41acca">
								<input class="knob" id="secs" data-readonly=true data-min="0" data-max="60"  data-width="72" data-height="72" data-thickness="0.1" data-fgcolor="#ffffff" data-bgColor="#41acca">
								<span class="text_1">дней</span>
								<span class="text_2">часов</span>
								<span class="text_3">минут</span>
								<span class="text_4">секунд</span>
							</div>
						</div>
						<div class="col-3 center">
							<div class="header_top_title">
								12:00 / 12 сентября
								<span>старт первого марафона</span>
							</div>
						</div>
						<div class="col-3 right">
							<a href="https://secure.templatemonster.com/ru/cart/?addOffer=534" class="btn btn_lg dark" id="btn-buy-1">Купить</a>
						</div>
					</div>
				</div>
			</div>
			<div class="header_bottom">
				<div class="container">
					<div class="row md">
						<div class="col-2 left">
							<a href="http://www.templatemonster.com/" class="logo">
								<img src="images/header-logo.png" alt="">
							</a>
						</div>
						<div class="col-2 right">
							<nav class="nav">
								<ul>
									<li><a href="http://www.templatemonster.com/ru/sign-up/">Партнерская программа</a></li>
									<li><a href="http://www.templatemonsterblog.ru/">блог</a></li>
									<li><a href="http://sertificat.templatemonster.ru/">Центр сертификации</a></li>
								</ul>
							</nav>
							<div class="mobile-menu">
								<a class="responsive-nav-button" href="#"></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>

		<main class="content">
			<div class="section section-content bg-grey section-rules">
				<div class="container">
					<h2 class="section_title">Правила возврата денег</h2>
					<p class="rules">
						В том случае, если вы придерживались всех правил марафона, выполняли задания и не получили в итоге готового сайта, мы обязуемся вернуть деньги.<br>

Под термином <strong>«готовый сайт»</strong> следует понимать — ресурс с собственным доменом и хостингом, доступный в Интернете, содержащий минимум 3 страницы.<br>

Под определением <strong>«придерживались всех правил марафона и выполняли задания»</strong> следует понимать:
					</p>
					<ul class="rules-list">
						<li>
							редактировали страницы сайта, задавали вопросы, вносили изменения, согласно рекомендациям тренера;
						</li>
						<li>
						добавляли готовые материалы в админку сайта;
						</li>
						<li>
						выбрали доменное имя и прислали его нам;
							
						</li>
						<li>
							зарегистрировали аккаунты в рекомендованных социальных сетях;
						</li>
						<li>
							зарегистрировались в системе Google Analytics и настроили его связь с сайтом.
						</li>
					</ul>
				</div>
			</div>
		</main>

		<footer class="footer">
			<div class="footer_top">
				<div class="container">
					<div class="row">
						<div class="col-4">
							<h5 class="footer-title">Продукты</h5>
							<ul>
								<li><a href="http://www.templatemonster.com/ru/wordpress-themes-type/">Wordpress шаблоны</a></li>
								<li><a href="http://www.templatemonster.com/ru/website-templates-type/">HTML шаблоны</a></li>
								<li><a href="http://www.templatemonster.com/ru/opencart-templates-type/">OpenCart шаблоны</a></li>
								<li><a href="http://www.templatemonster.com/ru/joomla-templates-type/">Joomla шаблоны</a></li>
								<li><a href="http://www.templatemonster.com/ru/prestashop-themes-type/">PrestaShop шаблоны</a></li>
								<li><a href="http://www.templatemonster.com/ru/woocommerce-themes-type/">WooCommerce шаблоны</a></li>
								<li><a href="http://www.templatemonster.com/ru/moto-cms-3-templates-type/">Конструктор сайтов</a></li>
								<li><a href="http://www.templatemonster.com/ru/magento-themes-type/">Magento шаблоны</a></li>
								<li><a href="http://www.templatemonster.com/ru/drupal-templates-type/">Drupal шаблоны</a></li>
								<li><a href="http://www.templatemonster.com/ru/shopify-themes-type/">Shopify шаблоны</a></li>
							</ul>
						</div>
						<div class="col-4">
							<h5 class="footer-title">Категории</h5>
							<ul>
								<li><a href="http://www.templatemonster.com/ru/category/business/">Сайты-визитки</a></li>
								<li><a href="http://www.templatemonster.com/ru/category/fashion-beauty/">Мода и красота</a></li>
								<li><a href="http://www.templatemonster.com/ru/category/family/">Семья</a></li>
								<li><a href="http://www.templatemonster.com/ru/category/design-photography/">Дизайн и фото</a></li>
								<li><a href="http://www.templatemonster.com/ru/category/real-estate/">Недвижимость</a></li>
								<li><a href="http://www.templatemonster.com/ru/category/cars-motorcycles/">Авто и мото</a></li>
								<li><a href="http://www.templatemonster.com/ru/category/medical/">Медицина</a></li>
								<li><a href="http://www.templatemonster.com/ru/category/sports-outdoors-travel/">Путешествия и спорт</a></li>
								<li><a href="http://www.templatemonster.com/ru/category/food-restaurant/">Еда и рестораны</a></li>
								<li><a href="http://www.templatemonster.com/ru/category/electronics/">Электроника</a></li>
							</ul>
						</div>
						<div class="col-4">
							<h5 class="footer-title">Компания</h5>
							<ul>
								<li><a href="http://www.templatemonster.com/ru/about.html">О компании</a></li>
								<li><a href="http://www.templatemonster.com/testimonials.php">Отзывы</a></li>
								<li><a href="http://templatemonsterblog.ru/">Блог</a></li>
								<li><a href="http://www.templatemonster.com/ru/sign-up/">Партнерская программа</a></li>
								<li><a href="http://www.templatemonster.com/ru/service-center/">Сервис Центр</a></li>
								<li><a href="http://www.templatemonster.com/ru/contact_us/">Связаться с нами</a></li>
								<li><a href="http://www.templatemonster.com/our-team/">Команда</a></li>
								<li><a href="http://www.templatemonster.com/ru/press-release/">Пресса</a></li>
							</ul>
						</div>
						<div class="col-4">
							<h5 class="footer-title">Поддержка</h5>
							<ul>
								<li><a href="http://www.templatemonster.com/help/ru/">Справочный центр</a></li>
								<li><a href="http://www.templatemonster.com/ru/report-spam.html">Пожаловаться на спам</a></li>
							</ul>

						</div>
						<div class="social">
							<h5 class="footer-title">Расскажите о нас</h5>
							<ul class="social_icons">
								<li class="vk">
									<a href="https://vk.com/templatemonster"><i class="fa fa-vk" aria-hidden="true"></i></a>
								</li>
								<li class="facebook">
									<a href="https://www.facebook.com/TemplateMonster-Russia-917610591620942"><i class="fa fa-facebook" aria-hidden="true"></i></a>
								</li>
								<li class="twitter"><a href="https://twitter.com/temmonru"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="footer_bottom">
				<div class="container">
					<div class="row">
						<div class="col-2">
							<p class="copyright">Copyright©
								<span id="copyright-year">2003-2016</span>
								<a href="http://www.templatemonster.com">Templatemonster.com</a>
								Все права защищены.
								<a href="http://makewebsite.templatemonster.ru/rules">Правила курса</a>
							</p>
						</div>
					</div>
				</div>
			</div>
		</footer>
	</div>
</body>
</html>