<?php
if(!defined('TecDoc')) die('Access Denied!');

/**
 * @package TecDoc
 * @version 1.1
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanislav WEB
 * @license TecDoc Informations System GmbH
 * @testdox 12.01.2013
 * @filesource /CURRENT_DIR/system/libs/class.Cache.php
 * @link https://github.com/Stanisov/tecdoc/blob/master/system/libs/class.Cache.php Repository here
 * @todo Простенький класс кэширования страниц по запросу, по истечении времени очистка
 */
class Cache
{
    /**
     * Директория для кэшированных файлов
     * @access private
     * @static const __CACHE_DIR__
     */    
    const __CACHE_DIR__ = 'cache/';
    
    /**
     * Расширения кэшируемых файлов
     * @access private
     * @static const __CACHE_EXT__
     */      
    const __CACHE_EXT__ = '.che';
    
    /**
     * Срок кэширования файлов в сек
     * @access private
     * @static const __CACHE_TIME__
     */      
    const __CACHE_TIME__ = 86800;    

    /**
     * Переменная сохраняющая массив с настройками приложения
     * @access protected
     * @var array $_CONFIG
     */
    static $_CONFIG;
    
    /**
     * Переменная сохраняющая состояние текущей страницы в ФС
     * присвоение желательно в конструкторе
     * @access public
     */
    public $CACHE_FILE = '';
    
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
    }
 
    /**
     * Закрываем доступ к функции вне класса.
     * @return null
     */    
    private function __clone(){
    }

    /**
     * Статический метод, которая возвращает
     * экземпляр класса или создает новый при
     * необходимости
     * @access static
     * @return object Cache
     */     
    public static function getInstance($CONFIG) 
    {
        self::$_CONFIG = $CONFIG;
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
     * __isExists() Метод определения наличия директории для кэширования и ее чтения
     * @access public
     * @return boolean (true/false)
     */     
    public function __isExists() 
    {
        if(!file_exists(getcwd().DS.self::__CACHE_DIR__) && is_dir(getcwd().DS.self::__CACHE_DIR__))
        {
            throw new Exception(Language::getLanguage('DIRECTORY_NOT_FOUND').getcwd().DS.self::__CACHE_DIR__); // выбрасываю исключение 
        }
        else
        {
            /**
             * Проверяю права на запись
             */
            try 
            {
                $this->__isWritable(getcwd().DS.self::__CACHE_DIR__);
                /**
                 * Ловлю страницу такой, какой она должна быть в ФС
                 */
                $this->_CACHE_FILE = trim($this->__translitDir(urldecode(getcwd().DS.self::__CACHE_DIR__.$this->__getCurrentCachePage()), 1));
            }
            catch(Exception $e)
            {
                $e->getMessage();
            }
        }
    } 
    
    /**
     * __isWritable($filename) Метод проверяет объект на запись
     * @access private
     * @param string $filename оносительный путь объекту
     * return boolean (true|false)
     */        
    private function __isWritable($filename)
    {
        if(!is_writable($filename)) throw new Exception(Language::getLanguage('OBJECT_ISNT_WITABLE').$filename);
        else return true;
    }

    /**
     * __getCurrentCachePage() Метод определия текущей страницы
     * также производится обработка страницы для файловой системы
     * @access private
     * return string $page текущая отформатированная страница
     */    
    public function __getCurrentCachePage()
    {
        $page = str_replace(DS,'_', str_replace($this->_CONFIG['SITE_PATH'], '', $_SERVER['REQUEST_URI'])).self::__CACHE_EXT__;
        return $page; 
    }

    /**
     * isCached() Метод проверяет страницу в кэше
     * @access public
     * @param string $filename оносительный путь объекту
     * return boolean (true|false)
     */        
    public function isCached()
    {
        if($this->__isWritable(getcwd().DS.self::__CACHE_DIR__))
        {
            /**
             * Ищем , есть ли уже закэшированный файл в системе?
             */
            if($this->__searchPage($this->_CACHE_FILE) == true) 
            {
                /**
                 * Нашли! Теперь проверяем его
                 * есть ли сходства с текущей страницей,
                 * определяем когда удалять
                 */
                if(time() > intval(filemtime($this->_CACHE_FILE)+self::__CACHE_TIME__))
                {
                    /**
                    * Если вышло время хранения страницы,
                    * можно ее удалить
                    */
                    unlink($this->_CACHE_FILE);
                    return false;
                }
                else return true;  
            }
            else return false;
        }
        else 
        {
            throw new Exception(Language::getLanguage('CANT_READ_DIRECTORY'));
        }
    } 
    
    /**
     * __searchPage($needle) Метод поиска страницы в директории
     * @access public
     * @param file $needle объект поиска
     * return boolean (true/false)
     */
    private function __searchPage($needle)
    {     
        $needle = substr($needle, strrpos($needle, '/')+1); // выбераю имя файла из строки
        $filearr = array();
        foreach(glob(self::__CACHE_DIR__."*".self::__CACHE_EXT__) as $filename)
        {
            $filearr[] = str_replace(self::__CACHE_DIR__, "", $filename);
        }
        //Debug::deBug($filearr);
        if(in_array($needle, $filearr))
        {
            if(filesize(self::__CACHE_DIR__.$needle) > 0) return true;
            else 
            {
                unlink($this->_CACHE_FILE);
                throw new Exception(Language::getLanguage('EMPTY_FILE').$this->_CACHE_FILE);
	
            }
        }
	else return false;
    }
    
    /**
     * __translitDir($string, $mode) Метод транслитерации русскоязычных названий в директориях
     * @access private
     * @param string $string строка для транслита
     * @param boolean $mode режим 1 с русского на английский, 2 с английского на русский
     * return string результат перевода
     */     
    private function __translitDir($string, $mode)
    {
        $russimvol = array("а","А","б","Б","в","В","г","Г","д","Д","е","Е","ё","Ё","ж","Ж","з","З",
                   "и","И","й","Й","к","К","л","Л","м","М","н","Н","о","О","п","П","р","Р",
                   "с","С","т","Т","у","У","ф","Ф","х","Х","ц","Ц","ч","Ч","ш","Ш","щ","Щ",
                   "ы","Ы","э","Э","ю","Ю","я","Я","a","A","b","B","c","C","d","D","e","E",
                   "f","F","g","G","h","H","i","I","j","J","k","K","l","L","m","M","n","N",
                   "o","O","p","P","q","Q","r","R","s","S","t","T","u","U","v","V","w","W",
                   "x","X","y","Y","z","Z","1","2","3","4","5","6","7","8","9","0","-"," ", "+","(",")"); //Массив с русcкими и английскими символами
        $anglsimvol = array("a","a","b","b","v","v","g","g","d","d","e","e","yo","yo","zh","zh",
                    "z","z","i","i","j","j","k","k","l","l","m","m","n","n","o","o","p","p",
                    "r","r","s","s","t","t","u","u","ph","ph","h","h","c","c","ch","ch",
                    "sh","sh","shh","shh","y","y","e","e","yu","yu","ya","ya","a","a",
                    "b","b","c","c","d","d","e","e","f","f","g","g","h","h","i","i",
                    "j","j","k","k","l","l","m","m","n","n","o","o","p","p","q","q",
                    "r","r","s","s","t","t","u","u","v","v","w","w","x","x","y","y",
                    "z","z","1","2","3","4","5","6","7","8","9","0","-","-", "", "", ""); //массив с аналогом русских и английских символов
        if($mode == 1) $result = str_replace($russimvol, $anglsimvol , strtolower($string));
        else $result = str_replace($anglsimvol, $russimvol, strtolower($string));
        return $result; //выводим результат
    }   
    
    /**
     * setCachePage($include) Метод записует страницу в кэш
     * @access public
     * @param file $include URL шаблона для кэшрования
     * return mixed boolean (true|false)
     */
    public function setCachePage($include)
    {       
        $h = fopen($this->_CACHE_FILE,"w");
        if(!fwrite($h, "$include")) throw new Exception(Language::getLanguage('CANT_WRITE_THE_FILE').$this->_CACHE_FILE);
        fclose($h);
        ob_end_flush(); // Сохранил, теперь отправляю в браузер
    }

    /**
     * getCachePage() Метод выдачи страницы из кэша
     * @access public
     * @param string $filename оносительный путь объекту
     * return mixed boolean (true|false)
     */
    public function getCachePage()
    {
        include_once $this->_CACHE_FILE;
    }    
}
?>