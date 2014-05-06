<?php
if(!defined('TecDoc')) die('Access Denied!');

/**
 * @package TecDoc
 * @version 1.1
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stasnilav WEB
 * @license TecDoc Informations System GmbH
 * @testdox 20.12.2012
 * @filesource /CURRENT_DIR/system/libs/class.Language.php
 * @link https://github.com/Stanisov/tecdoc/blob/master/system/libs/class.Debug.php Repository here
 * @todo Language PHP class класс обработки языковых пакетов
 */
class Language {
	
   /**
     * Переменная сохраняющая массив с языковым набором
     * @staticvar array $LANG
     * @access static
     */    
    static $LANG = array();

    /**
     * Конструктор инициализации $LANG, как массива с настройками
     * @param array $_CONFIG массив с настройками приложения
     * @return void array Language
     */    
    function __construct($_CONFIG = '') 
    {
        self::$LANG = $_CONFIG['LANG'];
    }
    
    /**
     * getLanguage($val) Метод для перевода передаваемой строки на указанный в настройках язык
     * @param string $val строка для перевода по языковому файлу
     * @access static
     * @return string строка с переводом
     */    
    public static function getLanguage($val)
    {
        if(isset(self::$LANG[$val]) && !empty(self::$LANG[$val])) return self::$LANG[$val];
        else return $val;
    } 
}