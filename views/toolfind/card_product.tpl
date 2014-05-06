<?php
/**
* @date 10.01.2013
* @name Шаблон вывода карточки товара
* должен передаваться параметр $_GET['prod_id']
* @author Stanislav WEB (stanislav@uplab.ru)
*/

if(!defined('TecDoc')) die('Access Denied!');

//Debug::deBug($this->models);

?>

            <!-- Хлебные крошки -->
            <div class="bread">
                <?=$this->breadcrumbs;?>
            </div>
            <!-- Блок с товаром -->
            <h4><?=$this->title;?> (<?=$this->article;?>)</h4>
            <h5><?=Language::getLanguage('PRODUCT_MANUFACTURER');?>: <?=$this->brand;?></h5>
            <div class="recovery">
                <div class="left max-w-650">
                    <?if(empty($this->models['articleDocuments']->array)):?>
                    <a class="img-item-list__link" href="#"><img src="<?=$this->config['SITE_PATH'];?>/views/<?=$this->config['TEMPLATE'];?>/images/nopic.jpg" alt="Нет изображения"></a>
                    <?else:?>
                    <?foreach($this->models['articleDocuments']->array as $picval):?>
                    <!-- Изображение -->
                    <?if(TecDoc::isImage($this->config['MEDIA_SERVER'].'/'.$picval->docId) == true):?>
                    <a href="<?=$this->config['MEDIA_SERVER'];?>/<?=$picval->docId;?>/0.jpg" rel="single" class="img-item-list__link pirobox" title="<?=$this->title;?> (<?=$this->brand;?>)" alt="<?=$this->title;?> (<?=$this->brand;?>)"><img src="<?=$this->config['MEDIA_SERVER'];?>/<?=$picval->docId;?>/1" alt="<?=$this->title;?>"></a>
                    <?endif;?>
                    <?endforeach;?>
                    <?endif;?>
                </div>
                <div class="right">
                    <?if(!empty($this->models['articleAttributes']->array)):?>
                    <ul class="">
                        <!-- Характеристики (Аттрибуты);-->
                        <h6><?=Language::getLanguage('PRODUCT_FEATURES');?></h6>
                        <?foreach($this->models['articleAttributes']->array as $val):?>
                        <li><?=TecDoc::ucFirst($val->attrName, 'UTF-8');?>: <strong><?=$val->attrValue;?></strong></li>
                        <?endforeach;?>
                    </ul>
                    <br>
                    <?endif;?>
                    
                    <!-- PDF -->
                    <?foreach($this->models['articleDocuments']->array as $docval):?>
                        <?if(TecDoc::isImage($this->config['MEDIA_SERVER'].'/'.$docval->docId) == false):?>
                            <h6>
                                <?=$docval->docTypeName;?>
                            </h6>
                            <a target="_blank" class="dow-pdf" href="<?=$this->config['MEDIA_SERVER'];?>/<?=$docval->docId;?>/0"><img src="<?=$this->config['SITE_PATH'];?>/views/<?=$this->config['TEMPLATE'];?>/images/dow_pdf.png"><span class="univ-button">Скачать</span></a>
                        <?endif;?>
                    <?endforeach;?>
                    <!-- PDF -->                    
                </div>
            </div>
            <div class="recovery">
                <p>
                <form class="formBut" action="http://<?=$this->config['DOMAIN'];?><?=$this->config['SEARCH_STRING'];?>">
                    <input class="simple_link" type="hidden" name="q" value="<?=$this->title;?>">
                    <input class="multi_link" type="submit" value="<?=Language::getLanguage('SEARCH_BY_NAME');?>">
                </form>
                <form class="formBut" action="http://<?=$this->config['DOMAIN'];?><?=$this->config['SEARCH_STRING'];?>">
                    <input class="simple_link" type="hidden" name="article" value="<?=$this->article;?>">
                    <input class="multi_link" type="submit" value="<?=Language::getLanguage('SEARCH_BY_ARTICLE');?>">
                </form>                        
                </p>                
            </div>

<link href="<?=$this->config['SITE_PATH'];?>/views/<?=$this->config['TEMPLATE'];?>/css/pirobox.css" type="text/css" rel="stylesheet" />  
<script type="text/javascript" src="<?=$this->config['SITE_PATH'];?>/views/<?=$this->config['TEMPLATE'];?>/js/pirobox.js"></script>
<script type="text/javascript">
$(function() 
{
    /**
     * Инициализирую обработчик для изображений Pirobox
     */     
    $("a.pirobox").fancybox(); 
    
    var maxHeight = 0;
    $(".img-item-list__link").each(function()
    {
        if($(this).height() > maxHeight) 
        {
            maxHeight = $(this).height();
        }
    });
    $(".img-item-list__link").height(maxHeight);    
});  
</script>


