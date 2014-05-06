<?php
/**
* @date 20.12.2012
* @name Шаблон вывода листинга моделей авто
* должен передаваться параметр $_GET['manufacturer_id']
* @author Stanislav WEB (stanislav@uplab.ru)
*/

if(!defined('TecDoc')) die('Access Denied!');

//Debug::deBug($this->models);
?>

            <!-- Хлебные крошки -->
            <div class="bread">
                <?=$this->breadcrumbs;?>
            </div>
            <!-- Алфавитное меню навигации -->
            <div class="alphabet-frame">
                <ul class="alphabet">
                    <?foreach($this->models as $alph => $res):?>
                    <li><a href="#"><?=$alph;?></a></li>
                    <?endforeach;?>
                    <li><a href="#"><?=Language::getLanguage('ALL');?></a></li>          
                </ul>
            </div>
            <!-- Блок с разделами каталога -->
            <div class="border-con cols5 cols5ie">
                <ul>
                    <?foreach($this->models as $key => $val):?>
                    <li id="<?=$key;?>">
                        <h2 class="letter"><?=$key;?> (<?=count($this->models[$key]['id']);?>)</h2>
                        <ul class="tecdoc__marka">
                            <?for($i=0; $i<count($this->models[$key]['id']); $i++):?>
                            <li>
                                <a class="t" title="<?=Language::getLanguage('YEARS');?> <?=$this->models[$key]['yearstart'][$i];?>&nbsp;<?=$this->models[$key]['yearsend'][$i];?>" class="m_select" href="<?=Router::URI('index.php?view=list_models_engine&manufacturer_id='.$this->url['manufacturer_id'].'&model_id='.$this->models[$key]['name'][$i]);?>"><?=mb_convert_case($this->models[$key]['name'][$i], MB_CASE_TITLE, 'UTF-8');?></a>
                            </li>
                            <?endfor;?> 
                        </ul>
                    </li>
                    <?endforeach;?>            
                </ul>
            </div>
            <div class="tecdocinfo">
                <!--<?$this->version;?>-->
            </div>