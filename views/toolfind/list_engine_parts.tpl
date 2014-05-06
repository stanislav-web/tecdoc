<?php
/**
* @date 08.12.2012
* @name Шаблон вывода списка комплектующих по модификации двигателя
* должен передаваться параметр $_GET['parts_id']
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
            <!-- Многоуровневый список меню -->
            <div id="tree">
                <ul>
                    <?foreach($this->models as $parentVal):?>        
                    <li>
                        <?if ($parentVal->hasChilds == 1 && $parentVal->parentNodeId == ''): // если есть потомки и это главный элемент ?>
                        <span class="pm"></span> <a href="<?=Router::URI('index.php?view=list_engine_parts&manufacturer_id='.$this->url['manufacturer_id'].'&model_id='.$this->url['model_id'].'&parts_id='.$this->url['parts_id'].'&type_id='.$parentVal->assemblyGroupNodeId);?>"><?=mb_convert_case($parentVal->assemblyGroupName, MB_CASE_TITLE, 'UTF-8');?></a>
                        <ul>
                            <?foreach($this->models as $childVal):?>   
                            <?if($childVal->parentNodeId == $parentVal->assemblyGroupNodeId): // если это потомок ?>
                            <li>
                                <?if ($childVal->hasChilds == 1):?>
                                <span class="pm"></span>
                                <?else:?>
                                <span class="last">-</span> 
                                <?endif;?> <a href="<?=Router::URI('index.php?view=list_engine_parts&manufacturer_id='.$this->url['manufacturer_id'].'&model_id='.$this->url['model_id'].'&parts_id='.$this->url['parts_id'].'&type_id='.$childVal->assemblyGroupNodeId);?>"><?=mb_convert_case($childVal->assemblyGroupName, MB_CASE_TITLE, 'UTF-8');?></a>
                                <?if($childVal->hasChilds == 1): // Ого !!! Есть еще наследники 8D ?>
                                <ul>
                                    <?foreach($this->models as $postChildVal):?>
                                    <? if($postChildVal->parentNodeId == $childVal->assemblyGroupNodeId):?>
                                    <li>
                                        <?if ($postChildVal->hasChilds == 1):?>
                                        <span class="pm"></span>
                                        <?else:?>
                                        <span class="last">-</span> 
                                        <?endif;?>
                                        <a href="<?=Router::URI('index.php?view=list_engine_parts&manufacturer_id='.$this->url['manufacturer_id'].'&model_id='.$this->url['model_id'].'&parts_id='.$this->url['parts_id'].'&type_id='.$postChildVal->assemblyGroupNodeId);?>"><?=mb_convert_case($postChildVal->assemblyGroupName, MB_CASE_TITLE, 'UTF-8');?></a>                                            <?if($postChildVal->hasChilds == 1): // Ого !!! Тут уже правнуки! 8D ?>                 
                                        <ul>
                                            <?foreach($this->models as $postEndChildVal):?>
                                            <? if($postEndChildVal->parentNodeId == $postChildVal->assemblyGroupNodeId):?>
                                            <li>
                                                <span class="last">-</span> <a href="<?=Router::URI('index.php?view=list_engine_parts&manufacturer_id='.$this->url['manufacturer_id'].'&model_id='.$this->url['model_id'].'&parts_id='.$this->url['parts_id'].'&type_id='.$postEndChildVal->assemblyGroupNodeId);?>"><?=mb_convert_case($postEndChildVal->assemblyGroupName, MB_CASE_TITLE, 'UTF-8');?></a>
                                            </li>
                                            <?endif;?>
                                            <?endforeach;?>
                                        </ul>
                                        <?endif;?>                           
                                    </li>
                                    <?endif;?>
                                    <?endforeach;?>
                                </ul>
                                <?endif;?>
                            </li>
                            <?endif;?>                
                            <?endforeach;?>
                        </ul>               
                        <?endif;?>
                    </li>
                    <?endforeach;?>
                </ul>
            </div>
            <div class="tecdocinfo">
                <!--<?$this->version;?>-->
            </div>
