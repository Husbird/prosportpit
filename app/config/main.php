<?php
return (object)array(
   'app_name' => '"M & S::Frame"', // название нашего приложения
   'controller_path' => 'app/controllers/',  // путь к папке с контроллерами
   'views_path' => 'app/views/',    // путь к папке файлов отображения
   'error_404' => 'app/views/layouts/404.php',    // путь к файлу "Ошибка 404.php"
   'models_path' => 'app/models/',        // путь к папке с моделями
   'framework_base' => 'framework/',       // путь к папке фреймворка
   'framework_classes' => 'framework/classes/',       // путь к папке фреймворка
   'framework_widgets' => 'framework/components/widgets/',       // путь к папке c виджетами
   'framework_modules' => 'framework/components/modules/',       // путь к папке c виджетами
   'css_path' => '/assets/css/',    // путь к папке стилей
   'scripts_path' => '/assets/scripts/',     // путь к папке скриптов
   'bootstrap_path' => '/assets/bootstrap_336/',    // путь к папке фреймворка bootstrap
   
   // 'site_path' => 'http://prosportpit.com',    // абсолютный адрес сайта исп. для формирования обратных ссылок в письмах и т.д.
   'site_path' => 'http://prosportpit.loc',    // тестовый
   
   'debug' => true, //режим отладки true-включить; false-отключить
   
   'log_files_write' => true, //ведение журнала логов true - вкл, false - выкл
   'save_all' => true, //режим записи всех действий пользователей ДОДЕЛАТЬ
   
   //база данных на локальном ПК
   'db_host' => 'localhost',
   'db_user' => 'root',
   'db_pass' => 'root',
   'db_name' => 'prosportpit2',

   //арибуты действий (action):
   'action' => array(
       'i' => 'Index',
       'v' => 'View',
       'c' => 'Create',
       'u' => 'Update',
       'd' => 'Delete',
       'a' => 'Admin',
       'l' => 'Login',
       'r' => 'Registration',
       'e' => 'Edit',
       'add' => 'Add',
       'add_cat' => 'AddCategory',
       'all' => 'All', //вывод всего содержимого(например статей) без учёта категорий категории
   ),
); 
$GLOBALS['mss_monitor'] = array();
// возвращаем массив настроек в виде массива объектов
?>