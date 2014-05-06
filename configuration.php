<?php

/**
 * @package TecDoc
 * @subpackage Independence platform
 * @version 1.1
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license TecDoc Informations System GmbH
 * @date 20.12.2012
 * @filesource /CURRENT_DIR/configuration.php
 * @link https://github.com/Stanisov/tecdoc/blob/master/configuration.php Repository here
 * @todo конфигурация приложение
 */
define('TecDoc', true); // ставлю защиту от прямого доступа
define('DS', DIRECTORY_SEPARATOR); // разделитель директорий по умолчанию
/*
 * Системные настройки
 */
$_CONFIG['TITLE'] = 'Каталог Автозапчастей'; // название по умолчанию
$_CONFIG['SYSTEM_LANG'] = 'ru'; // язык по умолчанию
$_CONFIG['SYSTEM_ENCODING'] = 'UTF-8'; // кодировка по умолчанию
$_CONFIG['ENABLE_CACHE'] = true; // Включить Кэш SOAP запросов и страниц?

/*
 * Устанавливаю пути
 */

$_CONFIG['DOMAIN'] = $_SERVER['HTTP_HOST']; // имя домена
$_CONFIG['SITE_PATH'] = DS.'autocatalog'; // путь относительно сайта
$_CONFIG['CORE_PATH'] = $_SERVER['DOCUMENT_ROOT'].$_CONFIG['SITE_PATH']; // путь к компоненту
$_CONFIG['TEMPLATE'] = 'toolfind'; // текущий шаблон
$_CONFIG['LIBS_PATH'] = $_CONFIG['CORE_PATH'].DS.'system'.DS.'libs'; // путь к библиотекам
$_CONFIG['LANG_PATH'] = $_CONFIG['CORE_PATH'].DS.'system'.DS.'languages'.DS.$_CONFIG['SYSTEM_LANG'].'.php'; // языки
$_CONFIG['TEMPLATE_PATH'] = $_CONFIG['CORE_PATH'].DS.'views'.DS.$_CONFIG['TEMPLATE']; // путь к шаблону
$_CONFIG['JQUERY_PATH'] = 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.2'; // JQuery библиотека
$_CONFIG['CSS_PATH'] = $_CONFIG['SITE_PATH'].DS.'views'.DS.$_CONFIG['TEMPLATE'].DS.'css'; // CSS
$_CONFIG['JS_PATH'] = $_CONFIG['SITE_PATH'].DS.'views'.DS.$_CONFIG['TEMPLATE'].DS.'js'; // JavaScript Init
$_CONFIG['CACHE_PATH'] = DS.'cachewsdl'; // директория для кэшируемых запросов SOAP
$_CONFIG['SEARCH_STRING'] = DS.'toolfindsearch'.DS.'index.php?type=prices'; // строка к выдаче результатов поиска по сайту

/**
 * Настройки подключения к SOAP Серверу
 */
$_CONFIG['SERVER'] = 'http://webservicepilot.tecdoc.net/pegasus-2-0/wsdl/TecdocToCat'; // сервер SOAP для подключения
$_CONFIG['MEDIA_SERVER'] = 'http://webservicepilot.tecdoc.net/pegasus-2-0/documents/20048'; // сервер SOAP для
$_CONFIG['PROVIDER_ID'] = 283; // ID провайдера от tecdoc.de
$_CONFIG['PROVIDER_LANG'] = 'ru'; // Язык соединения
////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Подключаю языковые настройки
 */
if(file_exists($_CONFIG['LANG_PATH'])) require_once($_CONFIG['LANG_PATH']);
else die('Не найден файл '.$_CONFIG['LANG_PATH']);

/**
 * Массив, хранящий имя класса и путь до файла
 */

$__autoload = array(
    'Debug' => $_CONFIG['LIBS_PATH'].DS.'class.Debug.php',
    'Router' => $_CONFIG['LIBS_PATH'].DS.'class.Router.php',
    'Language' => $_CONFIG['LIBS_PATH'].DS.'class.Language.php',
    'TecDoc' => $_CONFIG['LIBS_PATH'].DS.'class.TecDoc.php',
    'Cache' => $_CONFIG['LIBS_PATH'].DS.'class.Cache.php',    
    'Template' => $_CONFIG['LIBS_PATH'].DS.'class.Template.php',
);

/**
 * 
 * @package TecDoc
 * __autoload($class) - Метод перенаправления на указанный URL
 * @param string $class название класса
 * @access public
 * @return class подключенный класс
 */  
function __autoload($class)
{
    global $__autoload;
    if(isset($__autoload[$class])) 
    {
        if(file_exists($__autoload[$class])) require_once($__autoload[$class]);
        else die('Could not find the class '.$__autoload[$class]);        
    } 
}

/**
 * Красиво выгружаю классы :D
 */
__autoload('Debug');
__autoload('Router');
__autoload('Language');
__autoload('Cache');
__autoload('TecDoc');
__autoload('Template');