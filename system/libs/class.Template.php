<?php
if(!defined('TecDoc')) die('Access Denied!');

/**
 * @package TecDoc
 * @version 1.1
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stansilav WEB
 * @license TecDoc Informations System GmbH
 * @testdox 20.12.2012
 * @filesource /CURRENT_DIR/system/libs/class.Template.php
 * @link https://github.com/Stanisov/tecdoc/blob/master/system/libs/class.Template.php Repository here
 * @todo Template PHP class. Класс шаблонизатор
 * используется для вклюечения шаблонов в страницу
 */
class Template extends TecDoc {
    
    /**
     * Выводимый по умолчанию шаблон
     * @access private
     * @var string $_TEMPLATE
     */
    private $_TEMPLATE = 'list_manufacturers';
    
    /**
     * Заголовок по умолчанию
     * @access private
     * @var string $_TITLE
     */
    private $_TITLE;

    /**
     * Переменная для хранения состояния Singleton Cache object
     * @access private
     * @var object $_CACHE
     */
    private $_CACHE;
    
    /**
     * Конструктор инициализации входящих параметров
     * @param array $_CONFIG массив с настройками приложения
     * @param object $APPLICATION главный объект 1-С Битрикс
     * @return object Template
     */     
    function __construct($_CONFIG, $APPLICATION)
    {      
        if($_CONFIG) $this->_CONFIG = $_CONFIG;
        if($APPLICATION) $this->APPLICATION = $APPLICATION;
        parent::__construct($this->_CONFIG, $this->APPLICATION); // вызываем родительский конструктор 
        if($this->_CONFIG['ENABLE_CACHE'])
        {       
            $this->_CACHE = Cache::getInstance($this->_CONFIG); // загружаю объект класса Cache
            try 
            {
                $this->_CACHE->__isExists(); // даю стартовую проверку готовности директорий
            }
            catch(Exception $e)
            {
                $e->getMessage();
            }
        }
    }
    
    /**
     * Метод __includeTemplate($name)включения шаблона в страницу
     * @param string $name имя шаблона без .php
     * @access private
     * @return file 
     */
    private function __includeTemplate($name)
    {
        if(file_exists($this->_CONFIG['TEMPLATE_PATH'].'/'.$name.'.tpl'))
        {
            include $this->_CONFIG['TEMPLATE_PATH'].'/'.$name.'.tpl';
            return true;
        }
        else die('Шаблон '.$this->_CONFIG['TEMPLATE_PATH'].'/'.$name.'.tpl - не найден');
    }  
    
    /**
     * __templateBuffer() - Метод буферризации шаблона,
     * пригодиться для его кэширования
     * @access private
     * @return content $content 
     */    
    private function __templateBuffer()
    {
        try 
        {
            ob_start();
            $this->__includeTemplate($this->_TEMPLATE); // подключаем шаблон
            $data = ob_get_contents();
            return $data;   
        }
        catch (Exception $e)
        {
            throw new Exception(Language::getLanguage('CANT_CALC_THE_SIZE'));
        }        
    }
    
    /**
     * getContentSize($content) Метод определяет размер шаблона
     * @access public
     * @deprecated since version 1.0 Уже не использую
     * @param $content include страница
     * return int filesize
     */     
    public function getContentSize($content)
    {
       try 
        {
            ob_start();
            $this->__includeTemplate($this->_TEMPLATE); // подключаем шаблон      
            $page_source = ob_get_contents();
            $counter = strlen($page_source);
            return $counter; // вуаля
            ob_end_clean();            
        }
        catch (Exception $e)
        {
            throw new Exception(Language::getLanguage('CANT_CALC_THE_SIZE'));
        }        
    }    
    
    /**
     * view() - Метод рендеринга приложения,
     * включает в шаблон объекты и отображает их
     * @access public final
     * @return application 
     */
    final public function view()
    {   
       /**
        * Импортирую параметры в шаблон
        */

       $breadcrumbs = $this->__setBreadcrumbs($this->APPLICATION->GetCurPage());

       /**
        * Устанавливаю CSS стили для каталога
        */
       
       $this->APPLICATION->SetAdditionalCSS($this->_CONFIG['CSS_PATH'].'/autocat.css');
       
       /**
        * Устанавливаю JavaScript
        */
       
       $this->APPLICATION->AddHeadScript($this->_CONFIG['JQUERY_PATH'].'/jquery.min.js');  // подключаю jQuery Google
       $this->APPLICATION->AddHeadScript($this->_CONFIG['JS_PATH'].'/autocat.js'); // главный скрипт Init  
       
       /**
        * Устанавливаю переменные в шаблон
        */

       $this->_setobj('breadcrumbs', $breadcrumbs);  // загружаем крошки       
       $this->_setobj('config', $this->_CONFIG);  // загружаем конфигурации
       //$this->_setobj('version', $this->__getVersion()); // информация о сервисе
       if(empty($this->GET_data)) // если у нас index (главная)
       { 
           $this->APPLICATION->SetTitle(Language::getLanguage('TITLE')); // заголовок по умолчанию          
           $marks = $this->_getManufacturers();
           //DEBUG::deBug($marks);
           $this->_setobj('marks', $marks);  // загружаем марки из Сессии
           if($this->_CONFIG['ENABLE_CACHE'])
           {  
                if(!$this->_CACHE->isCached())
                {
                    /**
                     * Если страница не закэширована
                     * Если страницы разные, то будем их заменять
                     */
                     $this->_CACHE->setCachePage($this->__templateBuffer()); // устанавливаем ее в кэш                  
                }
                else
                {
                    /**
                     * Достаем из кэша 
                     */
                    $this->_CACHE->getCachePage(); 
                }
            }
            else $this->__includeTemplate($this->_TEMPLATE); // подключаем шаблон
       } 
       else
       {
           /**
            * Если передаются $_GET параметры,
            * то каталог подгружает следующие шаблоны
            */
           //DEBUG::deBug($this->GET_data);
           $this->_TEMPLATE = $this->GET_data['view']; // присваиваю шаблону значение из GET
           if(!empty($this->GET_data['manufacturer_id']))
           {
               /**
                * Определяю по каким параметрам загружать страницу
                */
               $this->_TITLE = ucwords($this->GET_data['manufacturer_id']); // получаю TITLE                       
               $this->_manuId = $this->_getManufacturerID($this->GET_data['manufacturer_id']); // загружаю модели
               $cars = (array)$this->_getModels($this->_manuId); // ID производителя
               try 
               {
                   if(!isset($this->GET_data['model_id'])) 
                   {
                        /**
                        * 2ая страница (Загрузка моделей)
                        */

                        if(!$this->_manuId)
                        {
                            $this->_TITLE = ucwords(Language::getLanguage('CATEGORY_NOT_FOUND')); // заголовок
                            $this->APPLICATION->SetTitle($this->_TITLE); // устанавливаю заголовок
                            throw new Exception(Language::getLanguage('CATEGORY_NOT_FOUND')); // выбрасываю исключение 
                        }                       
                   }
                   else
                   {
                       if(isset($this->GET_data['parts_id'])) 
                       {
                           if(isset($this->GET_data['kit_id']))
                           {                              
                               if(isset($this->GET_data['prod_id']))
                               {
                                    /**
                                    * 6ая страница (Карточка товара)
                                    */
                                    $article = explode("_", $this->GET_data['prod_id']); // разбиваю URL
                                    //Debug::deBug($this->_getDocument($article[1],$article[0]));
                                    $cars = (array)$this->_getFlyCard($this->_getDocument((int)$article[1], (int)$article[0]));// загружаю карточку товар
                                    //Debug::deBug($cars);
                                    $_getProductName = $this->_getProductName();
                                    //Debug::deBug($_getProductName);
                                    $this->_setobj('brand', $_getProductName['brand']); // устанавливаю значение для брэнда
                                    $this->_setobj('article', $_getProductName['article']); // устанавливаю значение для артикула                                    
                                    $this->_TITLE = ucwords($_getProductName['title']); // получаю TITLE
                            
                                    if(!is_array($cars) || empty($cars))
                                    {
                                        $this->_TITLE = ucwords(Language::getLanguage('PRODUCT_NOT_FOUND')); // заголовок
                                        $this->APPLICATION->SetTitle($this->_TITLE); // устанавливаю заголовок
                                        throw new Exception(Language::getLanguage('PRODUCT_NOT_FOUND')); // выбрасываю исключение 
                                    } 
                               }
                               else
                               {
                                    /**
                                    * 5ая страница (Выгрузка таблицы запчастей , после древовидного меню)
                                    */ 
                                    $cars = $this->_getListPostitionTable($this->GET_data['kit_id'], $this->GET_data['parts_id']); // загружаю список запчастей
                                    $this->_TITLE = ucwords($this->__getEngineName()); // получаю TITLE
                                    //Debug::deBug($cars);
                            
                                    if(!is_array($cars) || empty($cars))
                                    {
                                        $this->_TITLE = ucwords(Language::getLanguage('KIT_NOT_FOUND')); // заголовок
                                        $this->APPLICATION->SetTitle($this->_TITLE); // устанавливаю заголовок
                                        throw new Exception(Language::getLanguage('KIT_NOT_FOUND')); // выбрасываю исключение 
                                    }                                    
                               }
                           }
                           else
                           {
                                /**
                                * 4ая страница (Выгрузка дерева списка комплектующих)
                                */
                                $cars = $this->_getEnginePartsTree($this->GET_data['parts_id']); // загружаю список комплектующих
                                $this->_TITLE = Language::getLanguage('PARTS_FOR_MODEL');
                                $this->_TITLE .= ucwords($this->GET_data['manufacturer_id'].' '.$this->GET_data['model_id']); // получаю TITLE
                                //Debug::deBug($cars);
                            
                                if(!is_array($cars) || empty($cars))
                                {
                                    $this->_TITLE = ucwords(Language::getLanguage('PARTS_NOT_FOUND')); // заголовок
                                    $this->APPLICATION->SetTitle($this->_TITLE); // устанавливаю заголовок
                                    throw new Exception(Language::getLanguage('PARTS_NOT_FOUND')); // выбрасываю исключение 
                                }                              
                           }
                       }
                       else
                       {
                            /**
                            * 3ая страница (Загрузка модификаций моделей)
                            */
                            $this->_modelId = $this->_getModelID($this->GET_data['model_id']); // загружаю модификации
                            $this->_TITLE = ucwords($this->GET_data['model_id']); // получаю TITLE
                            $cars = (array)$this->_getModifications($this->_manuId, $this->_modelId); // получаю модификации
                            if(!$this->_modelId)
                            {
                                $this->_TITLE = ucwords(Language::getLanguage('MODEL_NOT_FOUND')); // заголовок
                                $this->APPLICATION->SetTitle($this->_TITLE); // устанавливаю заголовок 
                                throw new Exception(Language::getLanguage('MODEL_NOT_FOUND')); // выбрасываю исключение
                                
                            }
                       }
                   }
                   $this->APPLICATION->SetTitle($this->_TITLE); // устанавливаю заголовок 
                   $this->_setobj('title', $this->_TITLE); // устанавливаю заголовокв шаблон
                   $this->_setobj('url', $this->GET_data);  // передаю параметры для URL
                   $this->_setobj('models', $cars);  // загружаем данные SOAP в шаблон
                   if($this->_CONFIG['ENABLE_CACHE'])
                   {  
                        if($this->_CONFIG['ENABLE_CACHE'])
                        {  
                            if(!$this->_CACHE->isCached())
                            {
                                /**
                                * Если страница не закэширована
                                * Если страницы разные, то будем их заменять
                                */
                                $this->_CACHE->setCachePage($this->__templateBuffer()); // устанавливаем ее в кэш                  
                            }
                            else
                            {
                                /**
                                * Достаем из кэша 
                                */
                                $this->_CACHE->getCachePage(); 
                            }
                        }
                        else $this->__includeTemplate($this->_TEMPLATE); // подключаем шаблон
                    }
                    else $this->__includeTemplate($this->_TEMPLATE); // подключаем шаблон
               }
               catch (Exception $e)
               {
                   /**
                    * Выбрасываю скромное исключение
                    */
                   echo $e->getMessage();
               }               
           }
           else $this->__setRedirect ('/'); // выкидываем
       }
    }  

    /**
     * Деструктор приложения
     * @access public
     * @return boolean
     */    
    function __destruct() 
    {
        return true;
    }
}
