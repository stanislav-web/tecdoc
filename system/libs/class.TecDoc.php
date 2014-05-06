<?php
if(!defined('TecDoc')) die('Access Denied!');

/**
 * @package TecDoc
 * @version 1.1
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stasnilav WEB
 * @license TecDoc Informations System GmbH
 * @testdox 20.12.2012
 * @filesource /CURRENT_DIR/system/libs/class.TecDoc.php
 * @uses Является наследником класса Language()
 * @link https://github.com/Stanisov/tecdoc/blob/master/system/libs/class.TecDoc.php Repository here
 * @todo TecDoc PHP class. Главный класс - ядро для вызова приложения
 */
class TecDoc extends Language {
    
    /**
     * Переменная сохраняющая идентификатор клиента SOAP
     * @access private
     * @var object $_SOAP
     */ 
    private $_SOAP;
    
    /**
     * Переменная сохраняющая главный объект приложений на 1-С Битрикс
     * @access public
     * @var object $APPLICATION
     */ 
    public $APPLICATION;

    /**
     * Переменная сохраняющая массив с настройками приложения
     * @access protected
     * @var array $_CONFIG
     */
    protected $_CONFIG;
    
    /**
     * Переменная для подхвата объекта отладки
     * @access protected
     * @var object $_DEBUG
     */    
    protected $_DEBUG;
    
    /**
     * Массив для хранения списков производителей
     * @access protected
     * @var array $_MANUFACTURERS
     */    
    protected $_MANUFACTURERS = array(); 
    
    /**
     * Массив для хранения списков моделей
     * @access protected
     * @var array $_MODELS
     */    
    protected $_MODELS = array();   
    
    /**
     * Массив для хранения списков модификаций
     * @access protected
     * @var array $_MODIFY
     */    
    protected $_MODIFY = array(); 
    
    /**
     * Массив для хранения списков модификаций (extended)
     * @access protected
     * @var array $_MODIFYDETAIL
     */    
    protected $_MODIFYDETAIL = array();      
    
    /**
     * Массив для хранения списка запчастей для древовидного меню
     * @access protected
     * @var array $_PARTSENGINE
     */    
    protected $_PARTSENGINE = array();  

    /**
     * Массив для хранения списка позиций детали выбранных из древовидного списка
     * @access protected
     * @var array $_PARTSENGINE
     */    
    protected $_POSITIONSTABLE = array();      
    
    /**
     * Массив для хранения Детальной информации товара (для вывода на список)
     * @access protected
     * @var array $_POSITIONSTABLEDETAIL
     */    
    protected $_POSITIONSTABLEDETAIL = array();  

    /**
     * Массив для хранения Детальной информации товара (для вывода на карточки)
     * @access protected
     * @var array $_FLYCARD
     */    
    protected $_FLYCARD = array();  
    
    /**
     * Переменные для хранения Id выбранных категорий
     * @access protected
     * @var int $_manuId, $modelId
     */    
    protected $_manuId, $modelId;    
    
    /**
     * Массив для фильтрации $_GET параметров
     * @access public
     * @var array $GET_data
     */
    public $GET_data = array();
    
    /**
     * Массив для фильтрации $_POST параметров
     * @access public
     * @deprecated since version 1.0 Не использую... Так чисто для примера
     * @var array $POST_data
     */    
    public $POST_data = array();
    
    /**
     * Массив для фильтрации $_COOKIE параметров
     * @access public
     * @deprecated since version 1.0 Не использую... Так чисто для примера
     * @var array $COOK_data
     */     
    public $COOK_data = array();
     
    /**
     * Массив для хранения переменных , передаваемых в шаблон
     * @access private
     * @var array $_var
     */     
    private $_var = array(); 
    
    /**
     * Строка для строения цепочки хлебных крошек
     * @access protected
     * @var string $_html
     */     
    protected $_html = '';     
    
    /**
     * Конструктор инициализации соединения с сервером SOAP
     * @param array $_CONFIG массив с настройками приложения
     * @param object $APPLICATION главный объект 1-С Битрикс
     * @return object TecDoc
     */       
    function __construct($_CONFIG, $APPLICATION) 
    {
        if($_CONFIG) $this->_CONFIG = $_CONFIG;
        if($APPLICATION) $this->APPLICATION = $APPLICATION; 
        parent::__construct($this->_CONFIG); // вызываем родительский конструктор
        try 
        {
            $this->_SOAP = new SoapClient($this->_CONFIG['SERVER'], array('trace' => true));
        }
        catch(SoapFault $e)
        {
            /**
             * Ошибка соединения, сбрасываю..
             */
            $this->APPLICATION->SetTitle(Language::getLanguage('CONNECTION_FAIL'));
            Debug::getMessage($e->faultcode.': '.$e->faultstring); echo $exception->faultcode; // вывожу исключение
            unset($this->_SOAP);
        }
        
        if($this->_CONFIG['ENABLE_CACHE'])
        {
            /**
            * Включаю кэширование SOAP запросов
            */
            ini_set("soap.wsdl_cache_enabled", "On"); 
            ini_set('soap.wsdl_cache_dir', $this->_CONFIG['CACHE_PATH']);
            ini_set('soap.wsdl_cache_ttl', '86400'); // на сутки            
        }    
        
        /*
         * Фильтрую входящие данные
         */
        $this->GET_data = $this->__filter_vars($_GET);
        $this->POST_data = $this->__filter_vars($_POST);       
        $this->COOK_data = $this->__filter_vars($_COOKIE); 
        //DEBUG::deBug($_SESSION);          
    }
    
    /**
     * __data_clean($input) - Метод очистки суперглобальных массивов
     * @access protected
     * @param string $input строка , которую необходимо обработать
     * @return string обраьботанная строка
     */      
    protected function __data_clean($input)
    {
        return strip_tags(htmlspecialchars(trim($input)));
    }   
    
    /**
     * __filter_vars($input) - Метод фильтрации соперглобальных массивов GPC
     * @access private
     * @param string $input строка, которую необходимо обработать
     * @return string
     */      
    private function __filter_vars($input)
    {
        foreach($input as $k => &$v)
        {
            if(is_array($v)) $v = $this->__filter_vars($v);
            else $v = self::__data_clean($v);
            unset($v);
        }
        return $input;
    }    
    
    /**
     * _setobj($name, $value) - Метод установки переменных в шаблон
     * @param string $name имя в шаблоне
     * @param mixed $value значение переменной
     * @access protected
     * @return mixed 
     */
    protected function _setobj($name, $value)
    {
        return $this->_var[$name] = $value;
    }
  
    /**
     * __get($name) - Магический метод для сокращения цепочки свойств (переменных)
     * @param string $name имя для массива
     * @access private
     * @return mixed object 
     */    
    private function __get($name)
    {
        if(isset($this->_var[$name])) return $this->_var[$name];
        return '';
    }     
    
    /**
     *  _getManufacturers() - Метод выгрузки марок автомобилей с их ID
     * @access protected
     * @return array марки всех автомобилей
     */ 
    protected function _getManufacturers()
    {
        try 
        {
            $result = $this->_SOAP->getVehicleManufacturers3(array(
                'carType' => 1, // Тип авто: 1 - пассажирские               
                'provider' => $this->_CONFIG['PROVIDER_ID'], // Provider ID
                'lang' => $this->_CONFIG['PROVIDER_LANG'],
                'country' => $this->_CONFIG['PROVIDER_LANG'],
                'countriesCarSelection' => $this->_CONFIG['PROVIDER_LANG'],
                'countryGroupFlag' => false,
                'evalFavor' => false,
            ));
            $result = $result->data->array;
            
            /*
             * Делаю алфавитный массив
             */
            
            $section = array();
            for($i = 0; $i<count($result); $i++)
            {
                $section['manufacturers'][$result[$i]->manuName[0]]['id'][] = $result[$i]->manuId; // ID марки 
                $section['manufacturers'][$result[$i]->manuName[0]]['name'][] = $result[$i]->manuName; // Название марки 
            }
            //$this->__saveXML($this->SESSION_data[$this->_CONFIG['TEMPLATE']]['manuId'], 'marks', 'utf-8', 'marks.xml'); // save в XML

            /**
             * Присваиваю весь результат в сессию 
             */
            $this->_MANUFACTURERS = $_SESSION[$this->_CONFIG['TEMPLATE']]['manufacturers'] = $section['manufacturers'];
            //DEBUG::deBug($this->_MANUFACTURERS);
            return $this->_MANUFACTURERS;  // возвращаю массив со списком производителей                
        }
        catch(SoapFault $e) 
        {
            echo $e->getMessage();
        }
    }
   
    /**
     * _getModels($carId) - Метод выгрузки моделей автомобилей по ID
     * всегда вызывается после отработки метода _getAutoMarks()
     * @param int $carId идентификатор марки автомобиля
     * @access protected
     * @return array модели по марке
     */ 
    protected function _getModels($carId)
    {
        try 
        {
            $result = $this->_SOAP->getVehicleModels3(array(
                'carType' => 1, // Тип авто: 1 - пассажирские            
                'provider' => $this->_CONFIG['PROVIDER_ID'], // Provider ID
                'lang' => $this->_CONFIG['PROVIDER_LANG'],
                'country' => $this->_CONFIG['PROVIDER_LANG'],
                'manuId' => $carId, // ID производителя
                'countriesCarSelection' => $this->_CONFIG['PROVIDER_LANG'],
                'countryGroupFlag' => false,
                'evalFavor' => false,
            ));
            $result = $result->data->array;          
            //DEBUG::deBug($result);      

            /*
             * Делаю алфавитный массив
             */
            
            $section = array();
            for($i = 0; $i<count($result); $i++)
            {
                if(preg_match('/^(\d)/i', $result[$i]->modelname))
                {
                    $section['models']['0-9']['id'][] = $result[$i]->modelId; // ID модели           
                    $section['models']['0-9']['name'][] = $result[$i]->modelname; // Название модели
                    $section['models']['0-9']['yearstart'][] =  substr($result[$i]->yearOfConstrFrom, 0, 4); // Год выпуска
                    $section['models']['0-9']['yearend'][] = substr($result[$i]->yearOfConstrTo, 0, 4); // Год окончания                    
                }
                else
                {
                    $section['models'][$result[$i]->modelname[0]]['id'][] = $result[$i]->modelId; // ID модели 
                    $section['models'][$result[$i]->modelname[0]]['name'][] = $result[$i]->modelname; // Название модели
                    $section['models'][$result[$i]->modelname[0]]['yearstart'][] =  substr($result[$i]->yearOfConstrFrom, 0, 4); // Год выпуска          
                    $section['models'][$result[$i]->modelname[0]]['yearsend'][] = substr($result[$i]->yearOfConstrTo, 0, 4); // Год окончания                       
                }
            }
            
            /**
             * Присваиваю весь результат в сессию 
             */
            $this->_MODELS = $_SESSION[$this->_CONFIG['TEMPLATE']]['models'] = $section['models'];
            //DEBUG::deBug($this->_MODELS);
            return $this->_MODELS;  // возвращаю массив со списком моделей                     
        }
        catch(SoapFault $e) 
        {
            echo $e->getMessage();
        }
    }    
   
    /**
     * _getModifications($manuId, $modId) - Метод выгрузки детальной информации о выбранной моделе
     * всегда вызывается после отработки метода __getAutoModels($carId)
     * @param int $manuId идентификатор производителя авто
     * @param int $modId идентификатор модели автомобиля
     * @access protected
     * @return array детальное представления списка выбранной моделе
     */ 
    protected function _getModifications($manuId, $modId)
    {
        try 
        {
             $result = $this->_SOAP-> getVehicleIdsByCarTypeManuIdModelIdCriteria3(array(
                'provider' => $this->_CONFIG['PROVIDER_ID'], // Provider ID
                'carType' => 1, // Тип авто: 1 - пассажирские
                'countryGroupFlag' => false,  // группировать по странами
                'countriesCarSelection' => $this->_CONFIG['PROVIDER_LANG'], // выбор языка отображения
                'lang' => $this->_CONFIG['PROVIDER_LANG'], // выбор языка отображения
                'manuId' => $manuId, // ID производителя
                'modId' => $modId,  // ID модели
            ));
            unset($_SESSION[$this->_CONFIG['TEMPLATE']]['modify']); // уничтожаю перед перезаписью            
            for($i =0; $i<sizeof($result->data->array); $i++):
                
                /**
                 * Перезаписываю массив для выдачи в шаблон
                 */
                 $_SESSION[$this->_CONFIG['TEMPLATE']]['modify'][] = $this->_getModificationsDetail($result->data->array[$i]->carId);
            endfor;
            
            $this->_MODIFY = $_SESSION[$this->_CONFIG['TEMPLATE']]['modify'];
            //DEBUG::deBug($this->_MODIFY);           

            ///////////////////////////
            return $this->_MODIFY;  // возвращаю массив со списком производителей                
        }
        catch(SoapFault $e) 
        {
            echo $e->getMessage();
        }
    }    

    /**
     * _getModificationsDetail($carIds) - Метод выгрузки детальной информации о выбранной моделе (extended version)
     * всегда вызывается после отработки метода _getModificationsDetail($carIds)
     * @param array $carIds массив с ID's модификаций авто
     * @access private
     * @return array детальное представления списка с параметрами модели
     */ 
    private function _getModificationsDetail($carIds)
    {
        try 
        {
             $result = $this->_SOAP->getVehicleByIds2StringList(array(
                'provider' => $this->_CONFIG['PROVIDER_ID'], // Provider ID
                'axles' => true,
                'cabs' => true,
                'countryGroupFlag' => false,  // группировать по странами                 
                'motorCodes' => true,                 
                'secondaryTypes' => true,
                'vehicleDetails2' => true, 
                'vehicleTerms' => true,
                'wheelbases' => true, 
                'carIds' => array(
                    'array' => new SoapVar(array("{$carIds}"), SOAP_ENC_ARRAY), // IDs моделей авто 
                    'empty' => false,
                ),                             
                'lang' => $this->_CONFIG['PROVIDER_LANG'], // выбор языка отображения                 
                'country' => $this->_CONFIG['PROVIDER_LANG'], // выбор страны
                'countryUserSetting' => $this->_CONFIG['PROVIDER_LANG'], // настройки языка юзера
                'countriesCarSelection' => $this->_CONFIG['PROVIDER_LANG'], // выбор языка отображения                 
                 ));
             
            /**
             * Присваиваю весь результат в сессию 
             */
            //DEBUG::deBug($result); 
            $this->_MODIFYDETAIL = $_SESSION[$this->_CONFIG['TEMPLATE']]['modifydetail'] = $result->data->array[0];
            //DEBUG::deBug($this->_MODIFYDETAIL);
            return $this->_MODIFYDETAIL;  // возвращаю массив со списком производителей                
        }
        catch(SoapFault $e) 
        {
            echo $e->getMessage();
        }
    }    
    
    /**
     * _getManufacturerID($name) - Метод получения id производителя авто по названию
     * сначала преобразуем в верхний регистр, потом осуществляем поиск
     * @param string $string строка (название производителя), по которой необходимо вычислить id
     * @access protected
     * @return int ID производителя
     */    
    protected function _getManufacturerID($name)
    {
        if(!empty($name))
        {
            $name = trim(mb_strtoupper(urldecode($name), 'utf-8')); // преобразуем в верхний регистр
            $key = array_keys($_SESSION[$this->_CONFIG['TEMPLATE']]['manufacturers'][$name[0]]['name'], $name);
            
            /**
             * Осуществляю поиск ID в многомерном массиве по первой букве Производителя
             */
            $id = $_SESSION[$this->_CONFIG['TEMPLATE']]['manufacturers'][$name[0]]['id'][$key[0]];

            //exit('name: '.$name.' / id: '.$id);
            //$id = $this->__loadXML($name, 'xml/marks.xml');
            return $id;
        }
        else return false;
    }
    
    /**
     * _getModelID($name) - Метод получения id модели авто по названию
     * сначала преобразуем в верхний регистр, потом осуществляем поиск
     * @param string $string строка (название марки), которую необходимо обработать
     * @access protected
     * @return int ID модели
     */    
    protected function _getModelID($name)
    {

        if(!empty($name))
        {
            $name = trim(mb_strtoupper(urldecode($name), 'utf-8')); // преобразуем из URL строки
            if(is_numeric($name[0])) $alph = '0-9'; // проверяю , является ли первая буква названия цифрой
            else $alph = $name[0];
            for($i=0; $i<count($_SESSION[$this->_CONFIG['TEMPLATE']]['models'][$alph]['name']); $i++) 
            {
                $_SESSION[$this->_CONFIG['TEMPLATE']]['models'][$alph]['name'][$i] = mb_strtoupper($_SESSION[$this->_CONFIG['TEMPLATE']]['models'][$alph]['name'][$i]);
            }
            $key = array_keys($_SESSION[$this->_CONFIG['TEMPLATE']]['models'][$alph]['name'], $name);
            /**
             * Осуществляю поиск ID в многомерном массиве по первой букве Производителя
             */
            $id = $_SESSION[$this->_CONFIG['TEMPLATE']]['models'][$alph]['id'][$key[0]];
            return $id;
        }
        else return false;
    }    
    
    /**
     * __getEngineName() - Метод получения названия комплектующего по id
     * @access protected
     * @return string название комплектующего
     */     
    protected function __getEngineName()
    {
        $arr = $this->_getEnginePartsTree($this->GET_data['parts_id']); // берем в массив чтобы прочитать  его и найти заголовок 
        foreach($arr as $value)
        {
            if((int)$value->assemblyGroupNodeId == (int)$this->GET_data['kit_id']) 
            {
                return $value->assemblyGroupName;
            }
       }         
    }
 
    /**
     * _getProductName() - Метод получения названия товара по его ID
     * @access protected
     * @return array (артикул, заголовок, брэнд)
     */     
    protected function _getProductName()
    {
        $arr = $this->_getListPostitionTable($this->GET_data['kit_id'], $this->GET_data['parts_id']); // берем в массив чтобы прочитать  его и найти заголовок 
        //Debug::deBug($arr);
        $articleNo = explode("_", $this->GET_data['prod_id']); // разбиваю URL
        $titleArr = array();
        foreach($arr as $value)
        {
            if((int)$value[1]->articleId == (int)$articleNo[1]) 
            {
                $titleArr['title'] = $value[1]->genericArticleName;
                $titleArr['brand'] = $value[1]->brandName;
                $titleArr['article'] = $value[1]->articleNo;                  
            }
       }
       return $titleArr;
    }    
    
    /**
     * _getEnginePartsTree($targetId, $parentId = 0) - Метод выгрузки запчастей в древовидное меню
     * @access protected
     * @param int $targetId ID по которому определяются запчасти
     * @param int $parentId ID по которому можно уточнить детали на выбранную модель
     * @return array массив с группами комплектующих для древовидного меню
     */ 
    protected function _getEnginePartsTree($targetId, $parentId = 0)
    {
        try 
        {
            $result = $this->_SOAP->getLinkedChildNodesAllLinkingTarget(array(
                'childNodes' => true, // Тип авто: 1 - пассажирские  
                'country' => $this->_CONFIG['PROVIDER_LANG'],    
                'lang' => $this->_CONFIG['PROVIDER_LANG'],                
                'provider' => $this->_CONFIG['PROVIDER_ID'], // Provider ID
                'linkingTargetType' => 'C',
                // Тип запчастей
                //  C: Vehicle ID
                //  M: Motor ID
                //  A: Axle ID
                //  K: Body Type ID
                //  U: null
                'linkingTargetId' => (int)$targetId,
                'parentNodeId' => $parentId,
            ));
            $this->_PARTSENGINE = $_SESSION[$this->_CONFIG['TEMPLATE']]['parsengine'] = $result->data->array;
            //Debug::deBug($this->_PARTSENGINE);
            return $this->_PARTSENGINE;    
        }
        catch(SoapFault $e) 
        {
            echo $e->getMessage();
        }
    }    
    
    /**
     * _getListPostitionTable($assemblyGroupNodeId, $carId) - Метод вывода позиций запчастей по выбранной группе из древовидного меню
     * @access protected
     * @param int $assemblyGroupNodeId ID Группы запчастей из древовидного списка
     * @param int $carId ID Автомобиля (модификации)
     * @return array массив с запчастями (страница сразу после древовидного меню)
     */ 
    protected function _getListPostitionTable($assemblyGroupNodeId, $carId)
    {
        try 
        {
            $result = $this->_SOAP->getArticleIds3StringList(array(
                'provider' => $this->_CONFIG['PROVIDER_ID'], // Provider ID 
                'country' => $this->_CONFIG['PROVIDER_LANG'],    
                'lang' => $this->_CONFIG['PROVIDER_LANG'],                    
                'assemblyGroupNodeId' => $assemblyGroupNodeId, // ID Группы запчастей из древовидного списка
                'linkingTargetId' => $carId, // ID Автомобиля (модификации)
                'linkingTargetType' => 'C', // Тип авто: Vehicle
                'sort' => 2, // genericArticleName
            ));            
            $resArray = array();
            $ARRAY[] = array();
            for($i = 0; $i<count($result->data->array); $i++)
            {                
                $article = $this->_getDocument($result->data->array[$i]->articleId, $result->data->array[$i]->articleLinkId); // вытаскиваю фото и характеристики товара
                
                /*  $article = array();
                    $article[] = new SoapVar(array(
                    'articleId' =>$result->data->array[$i]->articleId,
                    'articleLinkId' => $result->data->array[$i]->articleLinkId,
                    ), SOAP_ENC_OBJECT);   
                 */   
                
                $resArray[$i] = $this->_getListPostitionTableDetail($article);
                $ARRAY[$i][] = $resArray[$i];
                $ARRAY[$i][] = $result->data->array[$i];               
            }
            //Debug::deBug($ARRAY);
            $this->_POSITIONSTABLE = $_SESSION[$this->_CONFIG['TEMPLATE']]['position'] = $ARRAY;
            return $this->_POSITIONSTABLE;    
        }
        catch(SoapFault $e) 
        {
            echo $e->getMessage();
        }
    } 

    /**
     * _getListPostitionTableDetail($articles) - Метод дополняющий _getListPostitionTable() для выгрузки доп. св-в в таблицу(extended version)
     * @param SOAP object $article ID's пара array($articleId => $articleLinkId)
     * @access protected
     * @return array детальное представление товара
     */ 
    protected function _getListPostitionTableDetail($articles)
    {   
        try 
        {
             $result = $this->_SOAP->getAssignedArticlesByIds2(array(
                'provider' => $this->_CONFIG['PROVIDER_ID'], // Provider ID                 
                'country' => $this->_CONFIG['PROVIDER_LANG'], // выбор страны                 
                'lang' => $this->_CONFIG['PROVIDER_LANG'], // выбор языка отображения
                'articleIdPairs'=> array(
                    'array' => new SoapVar($articles, XSD_ANYTYPE),
                    'empty' => false),
                'linkingTargetType' => 'C', // тип Авто
                'manuId' => $this->_getManufacturerID($this->GET_data['manufacturer_id']),
                'modId' => $this->_getModelID($this->GET_data['model_id']),
                'linkingTargetId' => $this->GET_data['parts_id'],
                'attributs' => true, // загружать аттрибуты?                           
                'documents' => true, // загружать документацию
                'documentsData' =>  true, // загружать данные                
                'eanNumbers' => false, // EAN номер
                'immediateAttributs' => true, // Мелиа атрибуты (фото итп)
                'immediateInfo' => true, // медиа информация
                'info' => true, // прочая информация                            
                'mainArticles' => false, // загружать информацию о производителях
                'normalAustauschPrice' => false, // показывать цену?
                'oeNumbers' => false, // OE номера
                'replacedByNumbers' => false,
                'replacedNumbers' => false, 
                'usageNumbers' => false,
                'prices' => false, // Загрузить каталог
                 ));
             
            /**
             * Присваиваю весь результат в сессию 
             */
            //DEBUG::deBug($result);
            $this->_POSITIONSTABLEDETAIL = $_SESSION[$this->_CONFIG['TEMPLATE']]['positiondetail'] = $result->data->array[0];
            //DEBUG::deBug($this->_POSITIONSTABLEDETAIL);
            return $this->_POSITIONSTABLEDETAIL;  // возвращаю массив со списком производителей                
        }
        catch(SoapFault $e) 
        {
            echo $e->getMessage();
        }
    } 

    /**
     * _getFlyCard($articles) - Метод вывода карточки товара
     * @param SOAP object $article ID's пара array($articleId => $articleLinkId)
     * @access protected
     * @return array детальное представление товара
     */ 
    protected function _getFlyCard($articles)
    {   
        //Debug::deBug($articles);
        //Debug::deBug($this->GET_data);
        
        try 
        {
             $result = $this->_SOAP->getAssignedArticlesByIds2(array(
                'provider' => $this->_CONFIG['PROVIDER_ID'], // Provider ID                 
                'country' => $this->_CONFIG['PROVIDER_LANG'], // выбор страны                 
                'lang' => $this->_CONFIG['PROVIDER_LANG'], // выбор языка отображения
                'articleIdPairs'=> array(
                    'array' => new SoapVar($articles, XSD_ANYTYPE),
                    'empty' => false),
                'linkingTargetType' => 'C', // тип Авто
                'manuId' => $this->_getManufacturerID($this->GET_data['manufacturer_id']),
                'modId' => $this->_getModelID($this->GET_data['model_id']),
                'linkingTargetId' => $this->GET_data['parts_id'],
                'attributs' => true, // загружать аттрибуты?                           
                'documents' => true, // загружать документацию
                'documentsData' =>  true, // загружать данные                
                'eanNumbers' => true, // EAN номер
                'immediateAttributs' => true, // Мелиа атрибуты (фото итп)
                'immediateInfo' => true, // медиа информация
                'info' => true, // прочая информация                            
                'mainArticles' => true, // загружать информацию о производителях
                'normalAustauschPrice' => false, // показывать цену?
                'oeNumbers' => true, // OE номера
                'replacedByNumbers' => true,
                'replacedNumbers' => true, 
                'usageNumbers' => true,
                'prices' => true, // Загрузить каталог
                 ));
            
            /**
             * Присваиваю весь результат в сессию 
             */
            //DEBUG::deBug($result);
            $this->_FLYCARD = $_SESSION[$this->_CONFIG['TEMPLATE']]['flycard'] = $result->data->array[0];
            //DEBUG::deBug($this->_FLYCARD);
            return $this->_FLYCARD;  // возвращаю массив со списком производителей                
        }
        catch(SoapFault $e) 
        {
            echo $e->getMessage();
        }
    }    
    
    /**
     * _getDocument($articleId, $articleLinkId) - Метод создания массива с типом ArticleIdPairSeq
     * Там видно, что у этого элемента должен быть тип ArticleIdPairSeq. 
     * Ищем его и видим, что это последовательность двух элементов - array и empty 
     * (фактически то же, что и для carIds в методе getVehicleByIds2StringList). 
     * Смотрим дальше: array должен реализовывать структуру ArrayOf_tns5_ArticleIdPair,
     * которая является последовательностью элементов типа ArticleIdPair.
     * Ну а уже сам этот элемент - должен содержать два поля: articleId и articleLinkId.
     * @param $articleId ID артикула
     * @param $articleLinkId ID ссылки на артикул
     * @access protected
     * @return SOAP array $article ID's пара array($articleId => $articleLinkId)
     */     
    protected function _getDocument($articleId, $articleLinkId)
    {
        $article = array();
        $article[] = new SoapVar(array(
            'articleId' => $articleId,
            'articleLinkId' => $articleLinkId,
        ), SOAP_ENC_OBJECT); 
        return $article;
    }
    
    /**
     * _saveXML($array, $type, $charset, $filename) - Метод сохранения значений ID - Марка авто в XML (для ЧПУ).
     * Этот метод может быть пригоден для различных ситуаций
     * @deprecated since version 1.0 Отказался от XML
     * @param array $array массив (пара: ключ=>значение)
     * @param string $type тип списка XML (обычное название , типо о чем список)
     * @param string $charset кодировка файла
     * @param string $filename относительный путь к файлу, например file.xml
     * @access private
     * @return file XML
     */   
    private function __saveXML($array, $type, $charset, $filename)
    {
        $dom = new DomDocument('1.0', $charset); // указываем кодировку и версию xml файла
        $root = $dom->appendChild($dom->createElement($type)); //добавление корня
        
        /**
         * Парсинг массива в XML
         */
        
        foreach($array as $mkey => $mvalue)
        {
            $node = $root->appendChild($dom->createElement('node')); // создаем элемент node
            $node->setAttribute('id',intval($mkey));
            $node->appendChild($dom->createTextNode(htmlspecialchars(trim($mvalue))));  // сохраняем значение n
        }        
        $dom->formatOutput = true; // установка атрибута formatOutput      
        $save = $dom->saveXML(); //сохранение XML
        $dom->save('xml/'.$filename); // сохранение дерева        
    }
   
    /**
     * _loadXML($name, $filename) - Метод выборки значения node из XML файла по имени
     * Этот метод может быть пригоден для различных ситуаций
     * @deprecated since version 1.0 Отказался от XML
     * @param string $name значение которое ищем в XML файле
     * @param string $filename XML файл (file.xml) в котором ищем
     * @access private
     * @return int $id идентификатор значения
     */   
    private function __loadXML($name, $filename)
    {
        if($name && $filename) // осуществляю XML разбор
        {
            $xml = simplexml_load_file($filename);
            $result = $xml->xpath('//node[.="'.$name.'"]');  // ищем id аттрибута по значению        
            //DEBUG::deBug($result);
            if($result[0]['id']) return $result[0]['id'];
            else return false;              
        }
        else return false;
    }
    
     /**
     * __setBreadcrumbs($currpage) Метод строения цепочки навигации хлебные крошки
     * @param string $currpage значение текущей страницы
     * @access protected
     * @return string $this->_html HTML код навигатора
     */   
    protected function __setBreadcrumbs($currpage)
    {
        $this->_html .= '<a href="/">'.Language::getLanguage('MAIN').'</a>';
        if($currpage != $this->_CONFIG['SITE_PATH'].'/')
        {
            /**
             * Строим цепочку
             */
            $this->_html .= ' » <a href="'.$this->_CONFIG['SITE_PATH'].'">'.Language::getLanguage('TITLE').'</a>';
            if(isset($this->GET_data['manufacturer_id']) && !empty($this->GET_data['manufacturer_id']))
            {
                if(isset($this->GET_data['model_id']) && !empty($this->GET_data['model_id']))
                {                    
                    if(isset($this->GET_data['parts_id']) && !empty($this->GET_data['parts_id']))
                    {
                        if(isset($this->GET_data['kit_id']) && !empty($this->GET_data['kit_id']))
                        {
                            if(isset($this->GET_data['prod_id']) && !empty($this->GET_data['prod_id']))
                            {
                                /**
                                * Товар (финальная остановка)
                                */
                               $_getProductName = $this->_getProductName(); // достаю заголовок
                                $this->_html .= ' » <a href="'.$this->_CONFIG['SITE_PATH'].'/'.$this->GET_data['manufacturer_id'].'/">'.$this->GET_data['manufacturer_id'].'</a>';
                                $this->_html .= ' » <a href="'.$this->_CONFIG['SITE_PATH'].'/'.$this->GET_data['manufacturer_id'].'/'.$this->GET_data['model_id'].'/">'.$this->GET_data['model_id'].'</a>';                         
                                $this->_html .= ' » 
<a href="'.$this->_CONFIG['SITE_PATH'].'/'.$this->GET_data['manufacturer_id'].'/'.$this->GET_data['model_id'].'/'.$this->GET_data['parts_id'].'/">'.Language::getLanguage('PARTS_FOR_MODEL').' '.$this->GET_data['manufacturer_id'].' '.$this->GET_data['model_id'].'</a>';
                                $this->_html .= ' » <a href="'.$this->_CONFIG['SITE_PATH'].'/'.$this->GET_data['manufacturer_id'].'/'.$this->GET_data['model_id'].'/'.$this->GET_data['parts_id'].'/'.$this->GET_data['kit_id'].'/">'.$this->__getEngineName().'</a>';
                                $this->_html .= ' » <span class="current">'.$_getProductName['title'].'</span>';                               
                            }
                            else
                            {
                                /**
                                * Запчасти к комплектам
                                */
                                $this->_html .= ' » <a href="'.$this->_CONFIG['SITE_PATH'].'/'.$this->GET_data['manufacturer_id'].'/">'.$this->GET_data['manufacturer_id'].'</a>';
                                $this->_html .= ' » <a href="'.$this->_CONFIG['SITE_PATH'].'/'.$this->GET_data['manufacturer_id'].'/'.$this->GET_data['model_id'].'/">'.$this->GET_data['model_id'].'</a>';                         
                                $this->_html .= ' » 
<a href="'.$this->_CONFIG['SITE_PATH'].'/'.$this->GET_data['manufacturer_id'].'/'.$this->GET_data['model_id'].'/'.$this->GET_data['parts_id'].'/">'.Language::getLanguage('PARTS_FOR_MODEL').' '.$this->GET_data['manufacturer_id'].' '.$this->GET_data['model_id'].'</a>';

                                $this->_html .= ' » <span class="current">'.$this->__getEngineName($this->GET_data['parts_id']).'</span>';                                
                            }
                        }                        
                        else
                        {
                            /**
                            * Комплектующие
                            */         
                            $this->_html .= ' » <a href="'.$this->_CONFIG['SITE_PATH'].'/'.$this->GET_data['manufacturer_id'].'/">'.$this->GET_data['manufacturer_id'].'</a>';
                            $this->_html .= ' » <a href="'.$this->_CONFIG['SITE_PATH'].'/'.$this->GET_data['manufacturer_id'].'/'.$this->GET_data['model_id'].'/">'.$this->GET_data['model_id'].'</a>';                         
                            $this->_html .= ' » <span class="current">'.Language::getLanguage('PARTS_FOR_MODEL').' '.$this->GET_data['manufacturer_id'].' '.$this->GET_data['model_id'].'</span>';                         
                        }
                    }
                    else
                    {
                        /**
                        * Модели
                        */
                        $this->_html .= ' » <a href="'.$this->_CONFIG['SITE_PATH'].'/'.$this->GET_data['manufacturer_id'].'/">'.$this->GET_data['manufacturer_id'].'</a>';
                        $this->_html .= ' » <span class="current">'.$this->GET_data['model_id'].'</span>';                 
                    } 
                }
                else $this->_html .= ' » <span class="current">'.$this->GET_data['manufacturer_id'].'</span>'; // Текущая - Каталог моделей
            }
        }
        else 
        {
            /**
             * Текущая - Каталог автомобилей
             */
             $this->_html .= ' » <span class="current">'.Language::getLanguage('TITLE').'</span>';           
        }
        return $this->_html;
    }

    /**
     * ucFirst($str, $encoding='UTF-8') - преобразует первый символ в верхний регистр
     * @access static
     * @param string $str - строка
     * @param string $encoding - кодировка, по-умолчанию UTF-8
     * @return string
     */
    static function ucFirst($str, $encoding='UTF-8')
    {
        $str = mb_ereg_replace('^[\ ]+', '', $str);
        $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
               mb_substr($str, 1, mb_strlen($str), $encoding);
        return $str;
    }
    
    /**
     * __getVersion() Версия каталога
     * @access protected
     * @deprecated since version 1.0 Не ставлю, так как не нужно
     * @return object SOAP
     */    
    protected function __getVersion()
    {
        try 
        {
             $result = $this->_SOAP->getPegasusVersionInfo(array(
                'provider' => $this->_CONFIG['PROVIDER_ID'], // Provider ID                 
                )
            );
            //DEBUG::deBug($result);
            return $result;  // возвращаю массив со списком производителей                
        }
        catch(SoapFault $e) 
        {
            echo $e->getMessage();
        }
    }

    /**
     * isImage($url) - Метод проверки, является ли файл $url изображением
     * @access static 
     * @param string $url URL файла
     * @return boolean (true) image else (flase)
     */   
    static function isImage($url)
    {
        $is = @getimagesize($url);
        if(!$is) print false;
        elseif(!in_array($is[2], array(1,2,3))) return false;
        else return true;
    }

    /**
     * searchValue($text, $var) - Метод поиска значения в многомерном масссиве
     * Используется рекурсия.
     * @access static
     * @deprecated since version 1.0 Не используется, тут есть другой метод
     * @param string $text текст для поиска
     * @param array $var
     * @return boolean (Yes/No)
     */    
    static function searchValue($text, $var)
    {
        $var = (array)$var;
        foreach($var as $val)
        {
            if (is_array($val) && searchValue($text, $val)) return true;
            elseif ($val==$text) return true;
        }
        return false;
    }      
    
    /**
     * __setRedirect($url) - Метод перенаправления на указанный URL
     * @param string $url URL страницы
     * @access protected
     * @return redirect перенаправление на страницу
     */   
    protected function __setRedirect($url)
    {
      header('location: '.$url);
      exit();
    }    
    
    /**
     * Деструктор приложения
     * Уничтожаю главный объект приложения
     * @access public
     * @return void boolean
     */    
    function __destruct() 
    {
        unset($this->_SOAP);
        return false;
    }
}