<?php
/**
* @date 20.12.2012
* @name Шаблон вывода таблицы деталей на автомобили
* должен передаваться параметр $_GET['kit_id']
* @author Stanislav WEB (stanislav@uplab.ru)
*/

if(!defined('TecDoc')) die('Access Denied!');

//Debug::deBug($this->models);

?>

            <!-- Хлебные крошки -->
            <div class="bread">
                <?=$this->breadcrumbs;?>
            </div>
            <!-- Блок с таблицей товаров -->
            <h4><?=$this->title;?></h4>
            <table>
                <thead>
                    <tr>
                        <th><?=Language::getLanguage('PRODUCT_PICTURE');?></th>            
                        <th><?=Language::getLanguage('PRODUCT_ARTICLE');?></th>
                        <th><?=Language::getLanguage('PRODUCT_NAME');?></th>
                        <th><?=Language::getLanguage('PRODUCT_MANUFACTURER');?></th>
                        <th><?=Language::getLanguage('PRODUCT_TERMS');?></th>
                        <th><?=Language::getLanguage('PRODUCT_FEATURES');?></th>
                        <th><?=Language::getLanguage('PRODUCT_PRICE');?></th>            
                    </tr>
                </thead>
                <tbody>
                    <?for($i = 0; $i<count($this->models); $i++):?>
                    <tr>
                        <td class="pic">
                            <!-- Изображение -->
                            <?if(empty($this->models[$i][0]->articleDocuments->array[0]->docId)):?>
                            <img src="<?=$this->config['SITE_PATH'];?>/views/<?=$this->config['TEMPLATE'];?>/images/nopic.jpg" alt="Нет изображения">
                            <?else:?>
                            <?
                            // Тут проверяю, является ли файл изображением
                            ?>
                            <?if(TecDoc::isImage($this->config['MEDIA_SERVER'].'/'.$this->models[$i][0]->articleDocuments->array[0]->docId.'/1')):?>
                            <a href="<?=$this->config['MEDIA_SERVER'];?>/<?=$this->models[$i][0]->articleDocuments->array[0]->docId;?>/0" rel="single" class="pirobox" title="<?=$this->models[$i][1]->genericArticleName;?> (<?=$this->models[$i][1]->brandName;?>)" alt="<?=$this->models[$i][1]->genericArticleName;?> (<?=$this->models[$i][1]->brandName;?>)"><img src="<?=$this->config['MEDIA_SERVER'];?>/<?=$this->models[$i][0]->articleDocuments->array[0]->docId;?>/1" alt="<?=$this->models[$i][1]->genericArticleName;?>"></a>
                            <?else:?>
                            <a href="<?=$this->config['MEDIA_SERVER'];?>/<?=$this->models[$i][0]->articleDocuments->array[0]->docId;?>/0" title="<?=$this->models[$i][1]->genericArticleName;?> (<?=$this->models[$i][1]->brandName;?>)" alt="<?=$this->models[$i][1]->genericArticleName;?> (<?=$this->models[$i][1]->brandName;?>)"><img src="<?=$this->config['SITE_PATH'];?>/views/<?=$this->config['TEMPLATE'];?>/images/pdf.png" alt="<?=$this->models[$i][1]->genericArticleName;?>"></a>
                            <?endif;?>
                            <?endif;?>
                        </td>            
                        <td><a href="<?=Router::URI('index.php?view=list_engine_parts&manufacturer_id='.$this->url['manufacturer_id'].'&model_id='.$this->url['model_id'].'&parts_id='.$this->url['parts_id'].'&kit_id='.$this->url['kit_id'].'&prod_id='.$this->models[$i][1]->articleLinkId.'_'.$this->models[$i][1]->articleId);?>"><?=$this->models[$i][1]->articleNo;?></a></td>
                        <td><a href="<?=Router::URI('index.php?view=list_engine_parts&manufacturer_id='.$this->url['manufacturer_id'].'&model_id='.$this->url['model_id'].'&parts_id='.$this->url['parts_id'].'&kit_id='.$this->url['kit_id'].'&prod_id='.$this->models[$i][1]->articleLinkId.'_'.$this->models[$i][1]->articleId);?>"><?=$this->models[$i][1]->genericArticleName;?></a></td>
                        <td><?=$this->models[$i][1]->brandName;?></td>
                        <td>
                            <!-- Условия -->
                            <?foreach($this->models[$i][0]->articleAttributes->array as $val):?>
                            <? if($val->attrType == 'D'):?>
                            <?=$val->attrName ;?>: <?=substr($val->attrValue, 0, 4);?> 
                            <?endif;?>
                            <?endforeach;?>   
                        </td>
                        <td>
                            <!-- Характеристики (Аттрибуты);-->
                            <?foreach($this->models[$i][0]->articleAttributes->array as $val):?>
                            <? if($val->attrType != 'D'):?>
                            <?=TecDoc::ucFirst($val->attrName, 'UTF-8');?>: <?=$val->attrValue;?><br />
                            <?endif;?>
                            <?endforeach;?>                
                        </td> 
                        <td><a href="http://<?=$this->config['DOMAIN'];?><?=$this->config['SEARCH_STRING'];?>&q=<?=urlencode($this->models[$i][1]->genericArticleName);?>&article=<?=$this->models[$i][1]->articleNo;?>"><?=Language::getLanguage('TELL_A_PRICE');?></a></td>              
                    </tr>        
                    <?endfor;?>
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
<link href="<?=$this->config['SITE_PATH'];?>/views/<?=$this->config['TEMPLATE'];?>/css/pirobox.css" type="text/css" rel="stylesheet" />  
<script type="text/javascript" src="<?=$this->config['SITE_PATH'];?>/views/<?=$this->config['TEMPLATE'];?>/js/pirobox.js"></script>
<script type="text/javascript">
$(function() 
{
    /**
     * Инициализирую обработчик для изображений Pirobox
     */     
    $("a.pirobox").fancybox();   
    /**
     * Инициализирую обработчик для таблицы dataTables
     */    
    $('table').dataTable({
        "aaSorting": [[ 4, "desc" ]],//set the coloum and then the sorting order
        "sDom": 'Rlfrtip',
        "sDom": 'C<"clear">lfrtip'
    });
});  
</script>