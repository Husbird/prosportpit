<?php
//Front Contriller
class MsController
{
     public $request_method;//метод запроса
     public $session_status = null;//статус сессий (bool)
     public $incoming_request = false;//входящий GET запрос
     public $view_file_path = false; //полный путь к файлу представления
     public $layout = 'app/views/layouts/main'; //путь к шаблону
     public $layout_left_sidebar = 'app/views/layouts/l_side_bar.php'; //путь к шаблону левого сайдбара
     public $layout_right_sidebar = 'app/views/layouts/r_side_bar.php'; //путь к шаблону правого сайдбара
     public $content = '<center><p><h3 style="color:red">Контент отсутствует!!!</h3>
                                    вероятно, что фаил отображения контента ещё не создан...
                                </p>
                        </center>';
     public $leftSideBarContent = '<center><p><h3 style="color:red">Контент отсутствует!!!</h3>
                                    вероятно, что фаил отображения контента левой боковой панели ещё не создан...<br>
                                    или добавьте в метод сонтроллера render второй параметр true... 
                                </p>
                        </center>';
     
     function __construct(){
        //session_start();
        $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: успешно подключен!<br>'; //var_dump($controllerName); //persony/author/1/i
        $this->session_status = $this->is_session_started(); //проверяем включены ли сессии если нет то включаем
        //var_dump($this->session_status);die;
        $this->request_method = $_SERVER['REQUEST_METHOD'];//определяем метод запроса

        //определяем права.....
        $role = new MsCheckRole($_COOKIE);
        
        //если права определены и пользователь НЕ гость - инициализация данных пользователя
        if($_SESSION['auth'] == true){
            $userInit = new MsUserInit($_COOKIE['id'], 'user');
            MSS::app()->setUserData($userInit->userData);//присваиваем пользователю права! (в объект приложения)
            //добавляем + 1 посещение и обновляем дату последнего посещения:
            $MsDBProcess = new MsDBProcess;
            $MsDBProcess->addActivityToDB();
            
            MSS::app()->getUserData(); //пишем данные в общий журнал
        }else{
            $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: инициализация пользователя - отменена! (пользователь - Guest)<br>';
        }
        
        //если метод запроса POST:
        if($this->request_method == 'POST'){
            new MsParserPost($_POST);
            exit('Парсер POST отработал безрезультатно!'); //ЗАПИСАТЬ В ЛОГ ОШИБОК!!!
            
        //если метод запроса GET: 
        } elseif ($this->request_method == 'GET') {
            //var_dump($_GET['route']); die("---");
            $this->incoming_request = $_GET['route'];//получаем строку запроса
            //var_dump($this->incoming_request);die;
            if(!$this->incoming_request){
                $this->incoming_request = 'empty';
            }
            if($this->incoming_request){
                $msParserGet = new MsParserGet($this->incoming_request);//передаём содержимое запроса в парсер
                
                $params = $msParserGet->result;//получаем параметры из парсера
                //Пробуем подключить фаил соответствующего запросу контроллера
                try{
                    if (!file_exists(MSS::app()->config->controller_path.$params['controller'].".php")){
                        //если фаил контроллера отсутствует - формируем текст об ошибке
                        throw new Exception("<p><b>Error: ".__METHOD__." не смог открыть соответствующий запросу контроллер! ({$params['controller']})</b></p>
                                            <p>Отсутствует фаил контроллера (".$params['controller'].".php) или ошибка в 'GET' запросе: '$params[incoming_request]'...</p>
                                            <p></p>
                                            ");
                    }else{
            /**                     Передаём управление и необходимые параметры соответствующему контроллеру,
            *                      создаём (и проверяем) класс нужного контроллера,передаём массив параметров полученных из парсера GET запроса*/
                            if(is_object(new $params['controller']($params))){ //попытка создать экземпляр класса соответствующего контроллера
                                //echo 'Объект создан';
                            }else{
                                echo "<br><b>Error: ".__METHOD__." не смог передать упраление соответствующему запросу контроллеру!</b><br>";
                            }
                        }
                }catch (Exception $e){
                    //echo '<hr>Выброшено исключение: ',$e->getMessage(), "\n <hr>"; // реализовать сообщение в лог
                }
                //var_dump($this->open_controller);
            }else{
                //echo '<hr>Ошибка инициализации контроллера! Передан параметр:'.var_dump($this->incoming_request).'<hr>'; // реализовать сообщение в лог
            }
         }
         //чистим сессию (mss_monitor)
         unset($_SESSION['mss_monitor']); //die('fdfdddddd');
         //var_dump($_SESSION['mss_monitor']);
     }
      //проверка перед выводом отображения, возвращает полный путь к фаилу отображения
      public function getContent($view, $leftSideBar = false, $rightSideBar = false){
            $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: начинаю работу ...<br>';
            //var_dump(MSS::app()->config->views_path.strtolower($this->params['model']).'/'.$view.".php");
            //проверяем существует ли файл с контентом
            $contentFilePath = MSS::app()->config->views_path.strtolower($this->params['model']).'/'.$view.".php";
            // var_dump($contentFilePath);
             //include('/app/views/layouts/site/view.php');
            if (!file_exists($contentFilePath)){
                //var_dump($fileFullPath);
                $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b> отсутствует файл отображения!<br>';
                //если фаил отображения отсутствует - возвращаем путь к стандартному шаблону
                return $this->content;
                //$this->view_file_path = $this->layout.'.php'; //путь к шаблону
                //var_dump($this->view_file_path);
            }else{
                    //пишем в свойство $this->content всё содержимое html файла контента
                    ob_start();
                    include_once($contentFilePath);//полный путь к файлу контента
                    $html = ob_get_clean();
                    $this->content = $html;//помещаем содержимое html файла отображения в свойство в виде объекта, для дальнейшего ввода в шаблоне 
                    $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: файл ('.$contentFilePath.') отображения передан в свойство как объект!<br>';
            }
            //если указали включить левую панель
            //var_dump($leftSideBar);die;
            if($leftSideBar){
                //проверяем наличие файла отображения левой панели
                if (!file_exists($this->layout_left_sidebar)){
                    //var_dump($fileFullPath);
                    $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b> отсутствует файл отображения левой панели!<br>';
                    //если фаил отображения отсутствует - возвращаем путь к стандартному шаблону 
                    return $this->leftSideBarContent;//возвращаем сообщение об отсутствии файла отображения
                    //$this->view_file_path = $this->layout.'.php'; //путь к шаблону
                    //var_dump($this->view_file_path);
                }else{
                        //пишем в свойство $this->content всё содержимое html файла отображения левого сайдбара
                        ob_start();
                        include_once($this->layout_left_sidebar);//полный путь к файлу контента
                        $html = ob_get_clean();
                        $this->leftSideBarContent = $html;//помещаем содержимое html файла отображения в свойство в виде объекта, для дальнейшего ввода в шаблоне 
                        $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: файл ('.$contentFilePath.') отображения левой панели передан в свойство как объект!<br>';
                }
                
            }else{
                $this->leftSideBarContent = '';//возвращаем пустое значение контента левого блока (отменяем его отображение)
            }
            
            if($rightSideBar){
                //проверяем наличие файла отображения левой панели
                if (!file_exists($this->layout_right_sidebar)){
                    //var_dump($fileFullPath);
                    $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b> отсутствует файл отображения правой панели!<br>';
                    //если фаил отображения отсутствует - возвращаем путь к стандартному шаблону 
                    return $this->rightSideBarContent; //возвращаем сообщение об отсутствии файла отображения
                    //$this->view_file_path = $this->layout.'.php'; //путь к шаблону
                    //var_dump($this->view_file_path);
                }else{
                        //пишем в свойство $this->content всё содержимое html файла отображения левого сайдбара
                        ob_start();
                        include_once($this->layout_right_sidebar);//полный путь к файлу контента
                        $html = ob_get_clean();
                        $this->rightSideBarContent = $html;//помещаем содержимое html файла отображения в свойство в виде объекта, для дальнейшего ввода в шаблоне 
                        $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: файл ('.$contentFilePath.') отображения правой панели передан в свойство как объект!<br>';
                }
                
            }else{
                $this->rightSideBarContent = '';//возвращаем пустое значение контента левого блока (отменяем его отображение)
            }
    }
    //если $leftSideBar = true - подкючится левая боковая панель
    public function render($view,$leftSideBar = false,$rightSideBar = false){
        $this->getContent($view,$leftSideBar,$rightSideBar); //инициализируем путь к файлу представления в метод view_file_path
        $GLOBALS['mss_monitor'][] = 'работает '.__METHOD__.' ... подключаю шаблон: '.$this->layout.'.php';
            //var_dump( MSS::app()->config->css_path);die;
            //var_dump($this->layout);die;
            require_once($this->layout.'.php'); // ТУТ происходит Вывод основного шаблона html кода (боковые панели вызываются уже в нём)
            //require_once($this->layout_left_sidebar.'.php');
		
	}
    
    //проверка включены ли сессии
    public function is_session_started(){
        if (is_array($_SESSION)){
             $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: Режим работы с сессиями включён!<br>'; 
        }else{
            $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: Режим работы с сессиями НЕ включён!<br>';
            $GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: запускаю session_start() ...<br>'; 
            session_start();
        }
        //var_dump($_SESSION); die;//session_status();
    }
}
//echo 'ПРИВЕТ!!!';
//$GLOBALS['mss_monitor'][] = '<b>'.__METHOD__.'</b>: Чищу сессию (ms_monitor)<br>';
?>