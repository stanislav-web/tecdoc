<?php
if(!defined('TecDoc')) die('Access Denied!');

/**
 * @package TecDoc
 * @version 1.1
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stansilav WEB
 * @license TecDoc Informations System GmbH
 * @testdox 20.12.2012
 * @filesource /CURRENT_DIR/system/libs/class.Debug.php
 * @link https://github.com/Stanisov/tecdoc/blob/master/system/libs/class.Debug.php Repository here
 * @todo Debugging Singleton PHP класс отладки
 */
class Debug {
    
	/**
	 * Переменная сохраняющая состояние этого класса
	 * @staticvar object $_instance
     * @access protected
	 */
    protected static $_instance;

    /**
     * Закрываем доступ к функции вне класса.
     * @return null
     */
    private function __construct(){
        /**
         * При этом в функцию можно вписать
         * свой код инициализации. Также можно
         * использовать деструктор класса.
         * Эти функции работают по прежднему,
         * только не доступны вне класса
         */
        
        return __METHOD_; // смотрим что вызывает
    }
 
    /**
     * Закрываем доступ к функции вне класса.
     * @return null
     */    
    private function __clone(){
    }

    /**
     * getInstance() Статический метод, которая возвращает
     * экземпляр класса или создает новый при
     * необходимости
     * @access static
     * @return object Debug
     */     
    public static function getInstance() 
    {
        // проверяем актуальность экземпляра
        if(null === self::$_instance) 
        {
            // создаем новый экземпляр
            self::$_instance = new self();
        }
        // возвращаем созданный или существующий экземпляр
        return self::$_instance;
    }    

    /**
     * deBug($var) Метод отладки передаваемого в нее объекта
     * @param mixed $var объект для просмотра
     * @access static
     * @return mixed объект для просмотра
     */    
    public static function deBug($var) 
    {
       if(is_object($var)) 
       {
           echo '<pre>';
           var_dump($var);
           echo '</pre>';
       }
       elseif(is_array($var))
       {
           echo '<pre>';
           print_r($var);
           echo '</pre>';          
       }
       else print($var);
    }  
    
    /**
     * getMessage($var) Метод форматирования строки с ошибкой
     * @param string $string объект для просмотра
     * @access static
     * @return $string форматированный вывод
     */    
    public static function getMessage($string) 
    {
       if(!empty($string) && is_string($string)) return '<div class="soapFault">'.$string.'</div>';
       else return false;
    }     
}
