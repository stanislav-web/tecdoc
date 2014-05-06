<?php
/**
* @date 20.12.2012
* @name Шаблон вывода листинга марок авто
* @author Stanislav WEB (stanislav@uplab.ru)
*/

if(!defined('TecDoc')) die('Access Denied!');

//Debug::deBug($this->marks);
?>

            <!-- Хлебные крошки -->
            <div class="bread">
                <?=$this->breadcrumbs;?>
            </div>
            <!-- Алфавитное меню навигации -->
            <div class="alphabet-frame">
                <ul class="alphabet">
                    <?foreach($this->marks as $alph => $res):?>
                    <li><a href="#"><?=$alph;?></a></li>        
                    <?endforeach;?>
                    <li><a href="#"><?=Language::getLanguage('ALL');?></a></li>          
                </ul>
            </div>
            <!-- Блок с разделами каталога -->
            <div class="border-con cols5 cols5ie">
                <ul>
                    <?foreach($this->marks as $key => $val):?>
                    <li id="<?=$key;?>">
                        <h2 class="letter"><?=$key;?> (<?=count($this->marks[$key]['id']);?>)</h2>
                        <ul class="tecdoc__marka">
                            <?for($i=0; $i<count($this->marks[$key]['id']); $i++):?>
                            <li>
                                <a class="m_select" href="<?=Router::URI('index.php?view=list_models&manufacturer_id='.$this->marks[$key]['name'][$i]);?>"><?=mb_convert_case($this->marks[$key]['name'][$i], MB_CASE_TITLE, 'UTF-8');?></a>
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
