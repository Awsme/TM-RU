<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'makewebsite');

/** Имя пользователя MySQL */
define('DB_USER', 'makewebsite');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'ChauGoo6');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '8#}0mxrM>|KczH$s{5M=1;3%VE_aHrbbXrgj5>v{g%O4ADASIf[`Klx%I%I^y[F5');
define('SECURE_AUTH_KEY',  '0EF#D-as^!|6x|C;YHb>4qn(bDW#h#Se},.tqvf&)O D[C-(&=](U3n =<z+<`]`');
define('LOGGED_IN_KEY',    'X8lSUHp#!/]mvxH%OlTWQo JN~|+z5n#TV{s3Dx[@5q9dY*)u$B<U,d-QD+M6fKD');
define('NONCE_KEY',        '7/Ju@2+WA]+B0X6 Uw(ln%hCXc6!YO[3>3IV^!^9`)s8j{9C|eAUiCDmx4ByPPAr');
define('AUTH_SALT',        '3Pg6t*h8_S/QIzx{R+$QE(d,wGp34H,{,b>.`&LMnZx2QgFF?2-Tu;1<5BH}7{j3');
define('SECURE_AUTH_SALT', '-8um]r*P^_r><Z#|L~,d:I!>zE/zmd(%EMQ#!K,bYBYV}+#Ocq|j`f2OU85-=(w|');
define('LOGGED_IN_SALT',   'LT.AZ=JOb0Jfi.Zl<blA?XhyW|G~WxN|QQX4j117:lL(U{}bzj&]>vO5i mC6*J?');
define('NONCE_SALT',       '8Q+D0@)+5]:<iX=7@b{h}0fC7VZ{p%r8A:2 Ah1OhO5lm|y&0QF&e5}OH?T6XNfY');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 * 
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
