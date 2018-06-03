<?php
class MsParserGet{
    
    public $incoming_request;
    public $params = array();//изначально полученные параметры запроса
    //результат работы парсера
    public $translit;//транслит
    public $controller_name;//имя контроллера
    public $action;//действие
    public $action_atribute; //атрибут действия (необходим для формирования GET запросов, например для формирования ссылок навигации страниц)
    public $model_name;//имя модели
    public $table_name;//имя таблицы БД
    
    public $result = array();//общий результат работы парсера
    
    function __construct($incoming_request){
//        var_dump($incoming_request); die("---");
        $GLOBALS['mss_monitor'][] = 'Запускаю парсер запроса...<br>';
        $resultDataArray = array();//создаём массив для данных

        //Если запрос пуст - Вводим параметры "ПО УМОЛЧАНИЮ"
        if($incoming_request == 'empty'){
            $GLOBALS['mss_monitor'][] = 'MsParserGet: получен пустой запрос, загружаю параметры "по умолчанию"...<br>';
            $resultDataArray = array(
                'incoming_request' => 'empty',//содержимое запроса
                'translit' => 'Home',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id по умолчанию
                'page' => false,//номер страницы по умочанию
            );
            $this->result = $resultDataArray;//присваиваем свойству resul результат работы парсера (массив данных)
            return 'массив данных для пустого запроса присвоен в свойство $result (MsParserGet->result)'; 
        }
        
        //ОБРАБОТКА ЗАПРОСА
        $GLOBALS['mss_monitor'][] = 'MsParserGet: обработка GET запроса ('.$incoming_request.')...<br>';
        $this->incoming_request = $incoming_request; //входящий запрос ОБРАБОТАТЬ ФИЛЬТРОМ!!!
        $params = explode('/',$this->incoming_request); //получаем массив параметров из входящего запроса
        $this->params = $params;//Внимание! Здесь пишем массив параметров в свойство. Это свойство будет часто использоваться!!! ОТФИЛЬТРОВАТЬ!!!
/**
 *         ВНИМАНИЕ - правило: не в зависимости от кол-ва параметров - параметры со следующими ключами:
 *         [0]-транслитерация (например заголовок страницы), как правило нигде не используется в коде (присутствует в запросе исключительно для ЧПУ)
 *         [1]-всегда ИМЯ МОДЕЛИ(и часть имени контроллера типа ИмяController)
 *         [2]-всегда ДЕЙСТВИЕ(action)
 *         [3]-в зависимости от действия(action): может быть номером страницы (если Index) или id строки таблицы БД (если View)
 *          *если необходимо получать больше\меньше параметров - необходимо дописать дополнительный "case" в конструкцию "switch"
 *           при этом настоятельно рекомедуется придерживаться данного правила!!! Moskaleny...
 */
        $count = count($this->params);
        $GLOBALS['mss_monitor'][] = 'MsParserGet: получено '.$count.' параметра...<br>';
        //var_dump($count); die;
        //var_dump($GLOBALS['mss_monitor']);
        switch($count){          
 /**
 * если принят 1 параметра
 */
            case 1:
             //var_dump($params[0]);die;
             //если отправлено письмо подтверждения регистрации выводим исп контроллер Site, action View (главную страницу) для вывода системного сообщения 
             if($params[0] == 'checkYourMail'){
             $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Home',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id по умолчанию
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
             );
             
             //если ошибка регистрации (не совпал хеш)
             }elseif($params[0] == 'regError'){
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Home',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id по умолчанию
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
             );
             
             //если регистрация и автоматическая авторизация прошли успешно:
             }elseif($params[0] == 'congratulations'){
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Home',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id по умолчанию
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
             );
             
             //если нажали кнопку Вход
             }elseif($params[0] == 'Login'){
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Login',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'Login', //действие по умолчанию
                'action_atribute' => 'l', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 6, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                //'system_massage' => $params[0],//получаем название вызываемого системного сообщения
             );
             
             //если нажали регистрация
             }elseif($params[0] == 'Registration'){
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Registration',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'Registration', //действие по умолчанию
                'action_atribute' => 'r', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 7, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                //'system_massage' => $params[0],//получаем название вызываемого системного сообщения Welcome
             );
             
             //если произвели вход
             }elseif($params[0] == 'Welcome'){
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Welcome',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
             );
             
             //если недостаточно прав
             }elseif($params[0] == 'AccessDenied'){
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'AccessDenied',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения AccessDenied
             );
             
             //нажали "Забыли пароль"
             }elseif($params[0] == 'PassRestore'){
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'PassRestore',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'PassRestore', //действие по умолчанию
                'action_atribute' => false, //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 8, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
                'email_not_find' => $_SESSION['email_not_find'],//добавляем в массив возможную ошибку дублирования email
             );
                unset($_SESSION['email_not_find']);
             
             //если пароль успешно сменён
             }elseif($params[0] == 'PassRestored'){
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'PassRestored',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
                'userMailToMsg' => $_SESSION['userEmail'],//
             );
             unset($_SESSION['userEmail']); //чистим сессию с имейлом пользователя  PassRestoreError
             
             //если пароль восстановить/сменить не удалось
             }elseif($params[0] == 'PassRestoreError'){
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'PassRestoreError',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'View', //действие по умолчанию
                'action_atribute' => 'v', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'site', //имя таблицы данных по умолчанию
                'id' => 1, //id на строку страницы входа
                'page' => false,//номер страницы по умочанию
                'system_massage_file' => $params[0],//получаем название вызываемого системного сообщения
             );
             
             //если нажали регистрация
             }elseif($params[0] == 'Settings'){
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'Settings',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'Settings', //действие по умолчанию
                'action_atribute' => 'set', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'user', //имя таблицы данных по умолчанию
                'id' => MSS::$userData['id'], //
                'page' => false,//номер страницы по умочанию
                //'system_massage' => $params[0],//получаем название вызываемого системного сообщения Welcome
             );
             
             //если нажали контакты
             }elseif($params[0] == 'contacts'){
                 //die("-----");
                $resultDataArray = array(
                'incoming_request' => $this->incoming_request,//содержимое запроса
                'translit' => 'contacts',//транслит
                'controller' => 'SiteController',//контроллер по умолчанию
                'action' => 'Сontacts', //действие по умолчанию
                'action_atribute' => '', //атрибут действия по умолчанию
                'model' => 'Site', //имя модели по умолчанию 
                'table_name' => 'user', //имя таблицы данных по умолчанию
                'id' => MSS::$userData['id'], //
                'page' => false,//номер страницы по умочанию
                //'system_massage' => $params[0],//получаем название вызываемого системного сообщения Welcome
             );
             
             //если нажали кнопку выход restore
             }elseif($params[0] == 'Exit'){
                new MsLogout;
                exit();
             //если такой запрос не предусмотрен - отправляем на стр 404   
             }else{
                include_once(MSS::app()->config->error_404); //подключаем файл отображения ошибки 404
                //exit('Неизвестный запрос');
             }
            break;
 /**
 * если принято 2 параметра
 */
            case 2:
             //если перешли по ссылке из письма регистрации...
             if($params[0] == 'activate'){
                //var_dump($params[1]);
                $hash = $params[1];
                //die('dfsdfsf');
                $userReg = new MsRegistr($hash);
                //$checkHash = $userReg->checkHash($hash);  
             }elseif($params[0] == 'СhangePass'){
                $getHash = $params[1]; //полученный из письма хеш
                $cookieHash = $_COOKIE['changePassHash']; //хеш сохранённый перед отправкой письма
                $cookieId = $_COOKIE['changePassId']; //id пользователя у которого меняем пароль
                $changePass = new MsPassRestore;
                $changePass->chengePass($getHash,$cookieHash,$cookieId);
                
             }elseif($params[0] == 'setSearchWord'){
                //die("BIBLEEEE");
                //var_dump($params[1]); dropSearchWord
                $searchWordEncoded = $params[1];
                //die('dfsdfsf');
                $MsSearchKWord = new MsSearchKWord();
                $MsSearchKWord->setSearchWord($searchWordEncoded);//декодируем и пишем в сессию слово
                header('location:'.$_SESSION['searchGetBackURI'].'');
                exit();
                
             }elseif($params[0] == 'dropSearchWord'){
                //die("BIBLEEEE");
                //var_dump($params[1]); dropSearchWord
                //$searchWordEncoded = $params[1];
                //die('dfsdfsf'); /poznavatelnoe/video/i/1     /poznavatelnoe/video/i/1
                $MsSearchKWord = new MsSearchKWord();
                $MsSearchKWord->dropSearchWord();//чистим сессию
                //var_dump($_SESSION['searchGetBackURI']);die;
                header('location:'.$_SESSION['searchGetBackURI'].'');
                exit();
                
             }elseif($params[0] == 'addToBasket'){
                //var_dump($params[1]); die;
                $MsBasket = new MsBasket;
                $MsBasket->addToBasket($params[1]);
                //var_dump($x);
                //die();
                header('location:'.$MsBasket->url_back.'');
                exit();

             }elseif($params[0] == 'dellFromBasket'){
                $MsBasket = new MsBasket;
                $MsBasket->dellFromBasket($params[1]);
                header('location:'.$MsBasket->url_back.'');
                exit();
             }
            break;
 /**
 * если принято 3 параметра
 */
            case 3:
            $this->translit = $params[0];//транслит
            if(!$this->translit){
                //echo "<br><b>Error: Парсер не смог определить транслит для чпу!</b><br>";
            }
            
            $this->controller_name = ucfirst(mb_strtolower($params[1])).'Controller';//устанавливаем имя контроллера
            if(!$this->controller_name){
                //echo "<br><b>Error: Парсер не смог определить имя контроллера!</b><br>";
            }
            
            $this->action = MSS::app()->config->action[$params[2]];//определяем action !!! (настраивается в файле конфигурации app/config/main.php)
            if(!$this->action){
                //echo "<br><b>Error: Парсер не смог определить действие!</b><br>";
            }
            
            $this->action_atribute = $params[2];//определяем атрибут действия action (i,v,c,d...)
            if(!$this->action){
                //echo "<br><b>Error: Парсер не смог определить атрибут действия!</b><br>";
            }
            
            $this->model_name = ucfirst(mb_strtolower($params[1]));//устанавливаем имя модели
            if(!$this->model_name){
                //echo "<br><b>Error: Парсер не смог определить имя модели!</b><br>";
            }
            
            $this->table_name = mb_strtolower($params[1]);//устанавливаем имя таблицы БД (не всегда совпадает например: при добавлении категории..)
            if(!$this->table_name){
                //echo "<br><b>Error: Парсер не смог определить имя таблицы!</b><br>";
            }
            
            //добавить
            if($this->action == 'Add'){
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'id' => false,//не определяется для данного действия
                    'page' => false,//не определяется для данного действия
                );  
            
            }elseif($this->action == 'AddCategory'){ //добавление категории (полученный параметр действия сравнивается с соотв. атрибутом из файла конфигурации)
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'id' => false,//не определяется для данного действия
                    'page' => false,//не определяется для данного действия
                );  
            
            }else{
                $GLOBALS['mss_monitor'][] = "<p style='color:red;'><b>MsParserGet:</b> Ошибка: передано 3 параметра, 
                сценарий для запрашиваемого действия <b>$this->action</b> отсутствует...</p> ";
            }
            break;
 /**
 * если принято 4 параметра
 */
            case 4:
            
            //редирект 301: образец редиректа 301:    header( 'Location: http://www.example.com/', true, 301 );
            if($params[3] == 'b'){
                header('location:/sport/production/all/1', true, 301); //если отказано в доступе - отправляем на страницу с сообщением
                exit();
            }
            //старый урл пример: /Combat/46/1/category
            if( $params[3] =='category'){
                header('location:/'.strtolower($params[0]).'/production/v/'.$params[1].'', true, 301); // на новый: пример:  hypercuts/production/v/4
                exit();
            }
            
            //старый урл пример: /Angel-Dust-V2/348/1/brand
            if( $params[3] =='brand'){
                header('location:/'.strtolower($params[0]).'/production/v/'.$params[1].'', true, 301); // на новый: пример:  hypercuts/production/v/4
                exit();
            }
            
            // старый урл пример: Vitaminy/4/1/c
            if($params[3] == 'c'){
                header('location:/'.strtolower($params[0]).'/production/i/1/'.$params[1].'/category', true, 301); //на новый: пример: /vitaminy/production/i/1/4/category
                exit();
            }
            
            $this->translit = $params[0];//транслит
            //if(!$this->translit){echo "<br><b>Error: Парсер не смог определить транслит для чпу!</b><br>";}
            
            $this->controller_name = ucfirst(mb_strtolower($params[1])).'Controller';//устанавливаем имя контроллера
            //if(!$this->controller_name){echo "<br><b>Error: Парсер не смог определить имя контроллера!</b><br>";}
            
            $this->action = MSS::app()->config->action[$params[2]];//определяем action !!! (настраивается в файле конфигурации app/config/main.php)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить действие!</b><br>";}
            
            $this->action_atribute = $params[2];//определяем атрибут действия action (i,v,c,d...)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить атрибут действия!</b><br>";}
            
            $this->model_name = ucfirst(mb_strtolower($params[1]));//устанавливаем имя модели
            //if(!$this->model_name){echo "<br><b>Error: Парсер не смог определить имя модели!</b><br>";}
            
            $this->table_name = mb_strtolower($params[1]);//устанавливаем имя таблицы БД
            //if(!$this->table_name){echo "<br><b>Error: Парсер не смог определить имя таблицы!</b><br>";}

            //если действие Index то параметр с ключом [3] означает номер страницы
            if($this->action == 'Index'){
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'page' => $params[3],
                    'id' => false, //не нужен для данного действия
                );
            }elseif($this->action == 'View'){ //если действие View то параметр с ключом [3] означает id строки таблицы БД
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'id' => $params[3],
                    'page' => false,//не определяется для данного действия
                );
            }elseif($this->action == 'Edit'){
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'id' => $params[3],
                    'page' => false,//не определяется для данного действия
                );
                
                }elseif($this->action == 'All'){
                    $resultDataArray = array(
                        'incoming_request' => $this->incoming_request,
                        'translit' => $this->translit,
                        'controller' => $this->controller_name,
                        'action' => $this->action,
                        'action_atribute' => $this->action_atribute,
                        'model' => $this->model_name,
                        'table_name' => $this->table_name,
                        'page' => $params[3],
                        'id' => false, //не нужен для данного действия
                    );
             
                }else{
                    $GLOBALS['mss_monitor'][] = "<p style='color:red;'><b>MsParserGet:</b> Ошибка: передано 4 параметра, 
                    сценарий для запрашиваемого действия <b>$this->action</b> отсутствует...</p> ";
                }
            break;   
 /**
 * если принято 5 параметра
 */
            case 5:
            //var_dump($params);die;
             $this->translit = $params[0];//транслит
            //if(!$this->translit){echo "<br><b>Error: Парсер не смог определить транслит для чпу!</b><br>";}
            
            $this->controller_name = ucfirst(mb_strtolower($params[1])).'Controller';//устанавливаем имя контроллера
            //if(!$this->controller_name){echo "<br><b>Error: Парсер не смог определить имя контроллера!</b><br>";}
            
            $this->action = MSS::app()->config->action[$params[2]];//определяем action !!! (настраивается в файле конфигурации app/config/main.php)
            //var_dump($this->action);die;
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить действие!</b><br>";}
            
            $this->action_atribute = $params[2];//определяем атрибут действия action (i,v,c,d...)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить атрибут действия!</b><br>";}
            
            $this->model_name = ucfirst(mb_strtolower($params[1]));//устанавливаем имя модели
            //if(!$this->model_name){echo "<br><b>Error: Парсер не смог определить имя модели!</b><br>";}
            
            $this->table_name = mb_strtolower($params[1]);//устанавливаем имя таблицы БД
            //if(!$this->table_name){echo "<br><b>Error: Парсер не смог определить имя таблицы!</b><br>";}
            
            //если действие Index то параметр с ключом [3] означает номер страницы
            if($this->action == 'Index'){
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'page' => $params[3],
                    'category_id' => $params[4], //id категории (например статьи)
                );
                break;
                
            }/**
 * elseif($this->action == 'Admin'){
 *                 $resultDataArray = array(
 *                     'incoming_request' => $this->incoming_request,
 *                     'translit' => $this->translit,
 *                     'controller' => $this->controller_name,
 *                     'action' => $this->action,
 *                     'action_atribute' => $this->action_atribute,
 *                     'model' => $this->model_name,
 *                     'table_name' => $this->table_name,
 *                     'page' => $params[3],
 *                     'object' => $params[4], //например orders (заказы)
 *                 );
 *                 break;
 *             }
 */
            
 /**
 * если принято 6 параметра
 */
            case 6:
             $this->translit = $params[0];//транслит
            //if(!$this->translit){echo "<br><b>Error: Парсер не смог определить транслит для чпу!</b><br>";}
            
            $this->controller_name = ucfirst(mb_strtolower($params[1])).'Controller';//устанавливаем имя контроллера
            //if(!$this->controller_name){echo "<br><b>Error: Парсер не смог определить имя контроллера!</b><br>";}
            
            $this->action = MSS::app()->config->action[$params[2]];//определяем action !!! (настраивается в файле конфигурации app/config/main.php)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить действие!</b><br>";}
            
            $this->action_atribute = $params[2];//определяем атрибут действия action (i,v,c,d...)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить атрибут действия!</b><br>";}
            
            $this->model_name = ucfirst(mb_strtolower($params[1]));//устанавливаем имя модели
            //if(!$this->model_name){echo "<br><b>Error: Парсер не смог определить имя модели!</b><br>";}
            
            $this->table_name = mb_strtolower($params[1]);//устанавливаем имя таблицы БД
            //if(!$this->table_name){echo "<br><b>Error: Парсер не смог определить имя таблицы!</b><br>";}
            
            //если действие Index то параметр с ключом [3] означает номер страницы
            if($this->action == 'Index'){
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'page' => $params[3],
                    'category_id' => $params[4], //id категории (например статьи)
                    'category_key' => $params[5], //всё что угодно, например ключ категории (например: category или brands) с помощью этого в модели можно настроить товары какой именно суперкатегории выводить'
                );
            }
            
            break;
            
            case 7:
             $this->translit = $params[0];//транслит
            //if(!$this->translit){echo "<br><b>Error: Парсер не смог определить транслит для чпу!</b><br>";}
            
            $this->controller_name = ucfirst(mb_strtolower($params[1])).'Controller';//устанавливаем имя контроллера
            //if(!$this->controller_name){echo "<br><b>Error: Парсер не смог определить имя контроллера!</b><br>";}
            
            $this->action = MSS::app()->config->action[$params[2]];//определяем action !!! (настраивается в файле конфигурации app/config/main.php)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить действие!</b><br>";}
            
            $this->action_atribute = $params[2];//определяем атрибут действия action (i,v,c,d...)
            //if(!$this->action){echo "<br><b>Error: Парсер не смог определить атрибут действия!</b><br>";}
            
            $this->model_name = ucfirst(mb_strtolower($params[1]));//устанавливаем имя модели
            //if(!$this->model_name){echo "<br><b>Error: Парсер не смог определить имя модели!</b><br>";}
            
            $this->table_name = mb_strtolower($params[1]);//устанавливаем имя таблицы БД
            //if(!$this->table_name){echo "<br><b>Error: Парсер не смог определить имя таблицы!</b><br>";}
            
            //если действие Index то параметр с ключом [3] означает номер страницы
            if($this->action == 'Index'){
                $resultDataArray = array(
                    'incoming_request' => $this->incoming_request,
                    'translit' => $this->translit,
                    'controller' => $this->controller_name,
                    'action' => $this->action,
                    'action_atribute' => $this->action_atribute,
                    'model' => $this->model_name,
                    'table_name' => $this->table_name,
                    'page' => $params[3],
                    'category_id' => $params[4], //id категории (например статьи)
                    'category_key' => $params[5], //всё что угодно, например ключ категории (например: category или brands) с помощью этого в модели можно настроить товары какой именно суперкатегории выводить'
                    'sub_category_id' => $params[6],
                );
            }
            
            break;
            
            //если неудовлетворительное кол-во параметров - выводим сообщение об ошибке
            default:
            $_SESSION['mss_monitor'][] = '<p style="color:red; font-weight:bold;">ошибка MsGetParser:</p>  
            Неверное количество параметров в запросе или неизвестное действие!<br>
             Дальнейшая работа парсера невозможна. (Страница 404!!!)';
            include_once(MSS::app()->config->error_404);
            
            //die("<br><b>Error: MsGetParser: Неверное количество параметров в запросе или неизвестное действие!</b><br>
            // Дальнейшая работа парсера невозможна. (Страница 404!!!)");
        } //switch END
        
            //после проверки в switch получаем массив данных и пишем его
            // в свойство resul
            $count = count($resultDataArray);
            if($count > 0){
                $this->result = $resultDataArray;//присваиваем свойству resul результат работы парсера (массив данных) 
                //var_dump($this->result);
                $GLOBALS['mss_monitor'][] = 'MsParserGet: получено '.$count.' параметров для action'.$this->action.'<br>';
            }else{
                $_SESSION['mss_monitor'][] = '<br><b>Error: Парсер: ключевые параметры для исполнения действия:
                 '.$this->action.' НЕ получены ! Работа сайта остановлена...</b><br>';
                include_once(MSS::app()->config->error_404);
            } 
        
        //$action = MSS::app()->config->action[$params[3]];
        //var_dump($action);
    } 
}
?>