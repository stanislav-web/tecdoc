<?php
if(!defined('TecDoc')) die('Access Denied!');

/**
 * @package TecDoc
 * @version 1.1
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanislav WEB
 * @license TecDoc Informations System GmbH
 * @testdox 08.12.2012
 * @filesource /CURRENT_DIR/system/libs/class.Router.php
 * @link https://github.com/Stanisov/tecdoc/blob/master/system/libs/class.Router.php Repository here
 * @todo Router PHP class маршрутизатор для обработки обычных URL => ЧПУ
 */
class Router {

    /**
     * Конструктор инициализации роутера.
     * Делаю пустой, так как объект будет глобальный, как и Language(), Debug()
     * @return null
     */    
    function __construct() { }    
    function __clone() { }   
    function __wakeup() {}
    
    /*
     * _cleanURL($string) Метод очистки передаваемого URL
     * @access private
     * @param string $string строка URL
     * @return string URL
     */ 
    private function _cleanURL($string)
    {
        $string =  stripslashes(trim($string));
        return $string;
    }
    
    /**
     * URI($string) Метод преобразования строки URL в короткий адрес
     * @access static
     * @param string $val строка для перевода по языковому файлу
     * @return string Форматированный короткий URL
     */    
    public static function URI($string)
    {
        if(isset($string) && !empty($string))
        {
            $string = parse_url($string, PHP_URL_QUERY);
            $parse_url = explode('&', $string);          
            unset($parse_url[0]); // удаляю первый элемент так как он содержит view параметр шаблона
            //Debug::deBug($parse_url);  
            $url = null;
            
            if(sizeof($parse_url) > 1) // если больше одного параметра
            {
                foreach($parse_url as $key => $value)
                {
                    $string = urlencode(mb_convert_case($value, MB_CASE_TITLE, 'UTF-8'));
                    $url .= preg_replace("/^(.+?)\%3D/i", '', $string).'/'; // удаляю названия параметров, оставляя только их значения
                }
                preg_match("#^\/([^\/]+)\/#is", $_SERVER['REQUEST_URI'], $maindir); // подставляю директорию с каталогом
                //Debug::deBug($out);
                $url = $maindir[0].$url;
            }
            else 
            {
                if(!empty($parse_url[1])) // преобразовываем стоку
                {
                    $string = urlencode(mb_convert_case($parse_url[1], MB_CASE_TITLE, 'UTF-8'));
                    $url .= preg_replace("/^(.+?)\%3D/i", '', $string).'/'; // удаляю названия параметров, оставляя только их значения         
                    return $url; // возвращали то что получили                    
                }
                return $string; // возвращали то что получили
            }
            return $url;
        }
        else return '#'; // глушитель ??? не уверен на счет него :)))
    } 
}