<?php
//error_reporting(E_ALL);// Выводить все PHP ошибки
header("Content-Type: text/html; charset=utf-8");//устанавливаем кодировку
require_once("framework/classes/MSS.class.php");
//MSS::app();
function __autoload($class) // пишем функцию автозагрузки классов
{
    if (file_exists(MSS::app()->config->framework_classes.$class.".class.php"))
        require_once(MSS::app()->config->framework_classes.$class.".class.php");//файлы классы фреймворка
        
    if (file_exists(MSS::app()->config->controller_path.$class.".php"))
        require_once(MSS::app()->config->controller_path.$class.".php");//файлы сонтроллеров приложения

    if (file_exists(MSS::app()->config->models_path.$class.".php"))     
        require_once(MSS::app()->config->models_path.$class.".php");//файлы моделей приложения
        
    if (file_exists(MSS::app()->config->framework_widgets.$class.".php"))     
        require_once(MSS::app()->config->framework_widgets.$class.".php");//файлы виджетов
        //var_dump(MSS::app()->config->framework_classes.$class.".class.php"); //framework/classes/MsController.class.php framework_widgets
}
//$bible = new MsVLinkParser();
//var_dump($_GET); die("---");
new MsController;//включаем Front Controller!
?>