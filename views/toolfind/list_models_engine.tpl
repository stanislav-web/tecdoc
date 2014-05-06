<?php
/**
* @date 20.12.2012
* @name Шаблон вывода листинга модификаций выбранной модели
* должен передаваться параметр $_GET['model_id']
* @author Stanislav WEB (stanislav@uplab.ru)
*/

if(!defined('TecDoc')) die('Access Denied!');

//Debug::deBug($this->models);

?>

            <!-- Хлебные крошки -->
            <div class="bread">
                <?=$this->breadcrumbs;?>
            </div>
            <h4><?=$this->title;?></h4>
            <!-- Блок с разделами каталога -->
            <table>
                <thead>
                    <tr>
                        <th><?=Language::getLanguage('MODIFY');?></th>
                        <th><?=Language::getLanguage('ENGINE_TYPE');?></th>
                        <th><?=Language::getLanguage('ENGINE_MODEL');?></th>
                        <th><?=Language::getLanguage('ENGINE_VOLUME');?></th>
                        <th><?=Language::getLanguage('ENGINE_POWER');?></th>
                        <th><?=Language::getLanguage('DRIVE');?></th>
                        <th><?=Language::getLanguage('BODY_TYPE');?></th>
                        <th><?=Language::getLanguage('FUEL_TYPE');?></th>
                        <th><?=Language::getLanguage('DATE_RELEASES');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?foreach($this->models as $key => $value):?>
                    <?if(!empty($value->carId)):?>
                    <tr>
                        <td><a href="<?=Router::URI('index.php?view=list_engine_parts&manufacturer_id='.$this->url['manufacturer_id'].'&model_id='.$this->url['model_id'].'&parts_id='.$value->carId);?>"><?=$value->vehicleTerms->carType;?></td>
                        <td><?=$value->vehicleDetails2->motorType;?></td>
                        <td><?=$value->motorCodes->array[0]->motorCode;?></td>
                        <td>
                            <? $motor = explode(" ", (string)$value->vehicleTerms->carType);?>
                            <?=$motor[0];?>
                        </td>
                        <td><?=$value->vehicleDetails2->powerHP;?></td>
                        <td><?=$value->vehicleDetails2->impulsionType;?></td>
                        <td><?=$value->vehicleDetails2->constructionType;?></td>
                        <td><?=$value->vehicleDetails2->fuelType;?></td>
                        <td>
                            <?=substr($value->vehicleDetails2->yearOfConstructionFrom, 0, 4);?> 
                            -
                            <?if(!empty($value->vehicleDetails2->yearOfConstructionTo)):?>
                            <?=substr($value->vehicleDetails2->yearOfConstructionTo, 0, 4);?>
                            <?else:?>
                            <?=Language::getLanguage('REAL_TIME');?>
                            <?endif;?>
                        </td>   
                    </tr>
                    <?endif;?>
                    <?endforeach;?>
                </tbody>
            </table>
            <div class="tecdocinfo">
                <!--<?$this->version;?>-->
            </div>

<!-- Стилизация таблицы комплектующих -->
<link href="<?=$this->config['SITE_PATH'];?>/views/<?=$this->config['TEMPLATE'];?>/css/jquery.dataTables.css" type="text/css" rel="stylesheet" /> 
<link href="<?=$this->config['SITE_PATH'];?>/views/<?=$this->config['TEMPLATE'];?>/css/jquery.dataTables_themeroller.css" type="text/css" rel="stylesheet" />   
<link href="<?=$this->config['SITE_PATH'];?>/views/<?=$this->config['TEMPLATE'];?>/css/demo_table_jui.css" type="text/css" rel="stylesheet" />  
<script type="text/javascript" src="<?=$this->config['SITE_PATH'];?>/views/<?=$this->config['TEMPLATE'];?>/js/jquery.dataTables.js"></script>
<script type="text/javascript">
    /**
     * Инициализирую обработчик для таблицы
     */
    $(function() {
    $('table').dataTable({
    "aaSorting": [[ 4, "desc" ]], //set the coloum and then the sorting order
    "sDom": 'Rlfrtip',
    "sDom": 'C<"clear">lfrtip'
});
});  
</script>

