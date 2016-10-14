<?php
$version = date("YmdHis");
$shareUrl = 'http://site33days.templatemonster.ru/';



$currentDay = date("d");
$currentMonth = date("m");
$currentYear = date("Y");

$show = false;
if($currentDay >= "12" &&  $currentMonth >= "09" && $currentYear >= "2016" ){
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
			<div class="section section-intro">
				<div class="container">
					<div class="row">
						<div class="col-1 left">
							<div class="section-intro_content">
								<img src="images/intro-section-logo.png" alt="">
								<div class="section-intro_content_info">
									<img src="images/intro-monster-logo.png" alt="">
									<img src="images/moto-intro-logo.png" alt="">
									<span>Наши официальные партнеры. 15 лет на рынке веб-дизайна</span>
									<div class="hgroup">
										<h2>149$ + 2 недели обучения с персональным тренером</h2>
										<h4>Ваш идеальный сайт-визитка и первые потенциальные клиенты</h4>
									</div>
									<a href="https://secure.templatemonster.com/ru/cart/?addOffer=534" class="btn btn_sm red" id="btn-buy-2">Купить</a>
									<a href="#" class="btn btn_sm dark btn-popup" data-popup="ask">задать вопрос</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>


			<div class="section section-content">
				<div class="container">
					<h2 class="section_title">Кому подойдет</h2>
					<div class="row equal-height">
						<div class="col-5">
							<figure class="figure center">
								<img src="images/propositions/light.png" alt="">
								<figcaption class="figure_title">
									У вас есть работающий
									бизнес или проект
								</figcaption>
							</figure>
						</div>
						<div class="col-5">
							<figure class="figure center">
								<img src="images/propositions/target.png" alt="">
								<figcaption class="figure_title">
									Знаете чего хотите и
									умеете этого достигать
								</figcaption>
							</figure>
						</div>
						<div class="col-5">
							<figure class="figure center">
								<img src="images/propositions/family.png" alt="">
								<figcaption class="figure_title">
									Нужно больше
									клиентов
								</figcaption>
							</figure>
						</div>
						<div class="col-5">
							<figure class="figure center">
								<img src="images/propositions/diagram.png" alt="">
								<figcaption class="figure_title">
									Держите руку на
									пульсе своего дела
								</figcaption>
							</figure>
						</div>
						<div class="col-5">
							<figure class="figure center">
								<img src="images/propositions/money.png" alt="">
								<figcaption class="figure_title">
									Стремитесь избегать
									необоснованных трат
								</figcaption>
							</figure>
						</div>
					</div>
				</div>
			</div>

			<div class="section section-content bg-grey center">
				<div class="container">
					<h2 class="section_title">Что мы предлагаем</h2>
					<div class="row">
						<div class="col-6">
							<figure class="figure left">
								<div class="figure_img-wrapper">
									<img src="images/numbers/01.png" alt="">
								</div>
								<figcaption class="figure_title figure_title__marked">
									Готовый сайт-визитка через 14 дней
								</figcaption>
							</figure>
						</div>
						<div class="col-6">
							<figure class="figure left">
								<div class="figure_img-wrapper">
									<img src="images/numbers/02.png" alt="">
								</div>
								<figcaption class="figure_title figure_title__marked">
									Никаких дополнительных оплат
								</figcaption>
							</figure>
						</div>
						<div class="col-6">
							<figure class="figure left">
								<div class="figure_img-wrapper">
									<img src="images/numbers/03.png" alt="">
								</div>
								<figcaption class="figure_title figure_title__marked">
									Консультант проведет вас через все этапы запуска сайта
								</figcaption>
							</figure>
						</div>
						<div class="col-6">
							<figure class="figure left">
								<div class="figure_img-wrapper">
									<img src="images/numbers/04.png" alt="">
								</div>
								<figcaption class="figure_title figure_title__marked">
									Вы лично сопровождаете и наполняете свой сайт
								</figcaption>
							</figure>
						</div>
						<div class="col-6">
							<figure class="figure left">
								<div class="figure_img-wrapper">
									<img src="images/numbers/05.png" alt="">
								</div>
								<figcaption class="figure_title figure_title__marked">
									Настройка рекламы в социальных сетях
								</figcaption>
							</figure>
						</div>
						<div class="col-6">
							<figure class="figure left">
								<div class="figure_img-wrapper">
									<img src="images/numbers/06.png" alt="">
								</div>
								<figcaption class="figure_title figure_title__marked">
									Первый поток потенциальных клиентов
									для вашего бизнеса
								</figcaption>
							</figure>
						</div>
					</div>
					<a href="https://secure.templatemonster.com/ru/cart/?addOffer=534" class="btn btn_sm red btn-section" id="btn-buy-3">Купить</a>
				</div>
			</div>

			<div class="section section-content center offset-bottom">
				<div class="container">
					<h2 class="section_title">Что от вас потребуется</h2>
					<div class="row">
						<div class="col-3">
							<figure class="figure left">
								<img src="images/tasks/pensil.png" alt="">
								<figcaption class="figure_title padd-off">
									<h4>Выполнять задания</h4>
									Для закрепления полученных знаний необходимо
									будет выполнить набор практических заданий.
								</figcaption>
							</figure>
						</div>
						<div class="col-3">
							<figure class="figure left">
								<img src="images/tasks/clock.png" alt="">
								<figcaption class="figure_title padd-off">
									<h4>Соблюдать дедлайны</h4>
									Все задания должны быть выполнены за строго
									отведенное время.
								</figcaption>
							</figure>
						</div>
						<div class="col-3">
							<figure class="figure left">
								<img src="images/tasks/bag.png" alt="">
								<figcaption class="figure_title padd-off">
									<h4>Материалы для сайта</h4>
									Мы подскажем, что вам надо для создания сайта
									(фото, тексты, логотип)
								</figcaption>
							</figure>
						</div>

					</div>
					<a href="https://secure.templatemonster.com/ru/cart/?addOffer=534" class="btn btn_sm red btn-section" id="btn-buy-4">Купить</a>
				</div>
			</div>


			<div class="section section-content center offset-all">
				<div class="container">
					<div class="row equal-height">
						<div class="col-2 bg-blue-black left">
							<div class="column-entry align-right">
								<h2>Что мы вам гарантируем</h2>
								<ol>
									<li>
										Крутой дизайн для вашего сайта
									</li>
									<li>
										Подробный пошаговый курс создания сайта “под ключ”
									</li>
									<li>
										Ответы на все вопросы
									</li>
									<li>
										Профессиональную помощь в настроке рекламной
										кампании для привлечения заинтересованных клиентов
									</li>
									<li>
										ВЕРНУТЬ ДЕНЬГИ
										<span>
											Если вы следовали всем инструкциям, но не справились.
										</span>
										<a href="rules.php">Правила возврата</a>
									</li>
								</ol>
							</div>
						</div>
						<div class="col-2 bg-blue left">
							<div class="column-entry align-left">
								<h2>ЧТО мы вам не гарантируем</h2>
								<ol>
									<li>
										Крутой интернет-магазин
										<span>Озон.ру мы с вами не построим ;)</span>
									</li>
									<li>
										Сделать все за вас
										<span>
											Каждый сам кузнец своего счастья, придется следовать инструкциям
											и выполнять указания вашего ментора!
										</span>
									</li>
								</ol>
							</div>
						</div>
					</div>
				</div>

				<a href="https://secure.templatemonster.com/ru/cart/?addOffer=534" class="btn btn_sm red btn-section-2" id="btn-buy-5">Купить</a>
			</div>

			<div class="popup-window" id="ask-popup">
				<div class="table" >
					<div class="table-cell">
						<div class="form-container">
							
							<form id="question-form">
								<h2 class="popup-title">задать вопрос</h2>
								<fieldset>
									<p>
										<input type="text" name="name" placeholder="Укажите свое имя">
									</p>
									<p>
										<input type="email" name="email" placeholder="Укажите свой e-mail:">
									</p>
									<textarea name="question" id="message" rows="30" placeholder="Задайте вопрос:"></textarea>
								</fieldset>
								<button type="submit" class="btn btn_popup question-small" id="send-question">задать вопрос</button>
							</form>
							<div id="success-answer" style="display: none;">
								<div class="question-title">Спасибо за вопрос!</div>
							</div>
							<i class="close"></i>
						</div>
					</div>
				</div>
			</div>

			<div class="popup-window" id="register-popup">
				<div class="table" >
					<div class="table-cell">
						<div class="form-container">
							<form id="subscribe-form">
								<h2 class="popup-title">Зарегистрироваться</h2>
								<fieldset>
									<input type="email" placeholder="Укажите свой e-mail:" name="email_subs" class="email-input email_subs">
								</fieldset>
								<button type="submit" class="btn btn_popup send-request">подать заявку</button>
							</form>
							<div id="email-exist" class="message" style="display: none;">
								<div class="question-title">Такой email уже существует!</div>
							</div>
							<div id="email-error" class="message" style="display: none;">
								<div class="question-title">Возникла ошибка при отправке заявки!</div>
							</div>
							
							<div id="error-answer" class="message" style="display: none;">
								<div class="question-title">Возникла ошибка при отправке ответа!</div>
							</div>
							<div id="response" class="message" style="display: none;">		
								<div class="question-title">
									"Вы подали заяву на участие в проекте "Свой сайт за 33 дня". Проверьте, пожалуйста , почту и подтвердите свое участие"
								</div>
							</div>
							<i class="close"></i>
						</div>
					</div>
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
								<li><a href="">Startup Hub</a></li>
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