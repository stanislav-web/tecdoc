<?php

/**
 * @package TecDoc
 * @subpackage Independence platform
 * @version 1.1
 * @author Stanislav WEB | Lugansk <stanislav@uplab.ru>
 * @copyright Stanilav WEB
 * @license TecDoc Informations System GmbH
 * @date 20.12.2012
 * @filesource /CURRENT_DIR/index.php
 * @link https://github.com/Stanisov/tecdoc/blob/master/index.php Repository here
 * @todo точка входа в приложение
 */
if(!session_start()) session_start(); // стартую сессию по каталогу
setlocale(LC_CTYPE, "ru_RU.UTF-8"); // устанавливаю локаль UTF-8

/*
 * Устанавливаю header Bitrix со всеми его библиотеками
 */
if(file_exists($_SERVER["DOCUMENT_ROOT"].'/bitrix/header.php')) require_once($_SERVER[ "DOCUMENT_ROOT"].'/bitrix/header.php');
else die('Не найден файл '.$_SERVER["DOCUMENT_ROOT"].'/bitrix/header.php');

/**
 *  Подключаю файл конфигурации каталога
 */
if(file_exists('configuration.php')) require_once('configuration.php');
else die('Can\'t find configuration.php');

$exec = new Template($_CONFIG, $APPLICATION); // инициализирую объект каталога

/**
 * Выполняем приложение
 */
if($exec instanceof Template) $exec->view();

/*
 * Устанавливаю footer Bitrix
 */
if(file_exists($_SERVER[ "DOCUMENT_ROOT"]. "/bitrix/footer.php")) require_once($_SERVER[ "DOCUMENT_ROOT"]."/bitrix/footer.php");
else die('Can\'t find '.$_SERVER[ "DOCUMENT_ROOT"].'/bitrix/footer.php');
?>