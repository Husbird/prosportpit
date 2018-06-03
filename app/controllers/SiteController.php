<?php
class SiteController extends MsController{
    
    public $model_name;//имя модели
    public $action;//вызываемое действие
    public $params = array();//принятый массив параметров
    public $pathToViewFile = 'app/views/site/index.php';//фаил отображения контента по умолчанию
    public $data = false;//данные полученные из модели для текушего действия (для использования из файла отображения)
    
    function __construct($params){
        $this->params = $params;//Внимание! Здесь пишем массив параметров в свойство. Это свойство будет часто использоваться!!!
        $this->model_name = $params['model'];
        $this->action = $params['action'];
        //формируем имя функции (action) и вызываем её передавая полученные параметры
        $actionName = 'action'.$this->action;
        $this->$actionName();//вызываем функцию соответствующего действия
        $GLOBALS['mss_monitor'][] = '<hr>'.$this->model_name.'Controller загружен успешно!!!<br>';
        //инициализируем контент в зависимости от текушей модели 
        }
    
    public function actionView(){
        //проверка на соответствие прав
        if(!MSS::app()->accessCheck('Admin,Суперчеловек ;),SuperUser,Moderator,User,Guest')){
            header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
            exit();
        };
        $GLOBALS['mss_monitor'][] = '<hr>Текущее действие: action'.$this->action.' контроллера: '.$this->model_name.'Controller<br>';
        $model = $this->loadModel();//подключаем модель
        //формируем объект с данными для использования в файле отображения
        $data = $model->actionData;//получаем все данные из соответствующей модели (в дан.случ. Site) предназначенные для текущего действия
        $data->parser = $this->params; //добавляем параметры переданные парсером
        $this->data = $data; //пишем в свойство для использования из файла отображения
        $this->render('view'); //подключаем фаил отображения и передаём ему массив данных
   	}
    
    public function actionIndex(){
        //проверка на соответствие прав
        if(!MSS::app()->accessCheck('Admin,Суперчеловек ;),SuperUser,Moderator,User,Guest')){
            header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
            exit();
        };
        $GLOBALS['mss_monitor'][] = '<hr>Текущее действие: action'.$this->action.' контроллера: '.$this->model_name.'Controller<br>';
        $model = $this->loadModel();//подключаем модель
        //формируем объект с данными для использования в файле отображения 
        $data = $model->actionData;//получаем все данные из соответствующей модели (в дан.случ. Site) предназначенные для текущего действия
        $data->parser = $this->params; //добавляем параметры переданные парсером
        $this->data = $data; //пишем в свойство для использования из файла отображения
        $this->render('index'); //подключаем фаил отображения и передаём ему массив данных
   	}
    //авторизация пользователей
    public function actionLogin(){
        //проверка на соответствие прав
        if(!MSS::app()->accessCheck('Guest')){
            header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
            exit();
        };
        $GLOBALS['mss_monitor'][] = '<hr>Текущее действие: action'.$this->action.' контроллера: '.$this->model_name.'Controller<br>';
        $model = $this->loadModel();//подключаем модель
        //$data = $model->actionViewData; //получаем данные модели соответствующие текущему действию actionView()
        $data = $model->actionData;//получаем все данные из соответствующей модели (в дан.случ. Site) предназначенные для текущего действия
        $this->data = $data; //пишем в свойство для использования из файла отображения
        //var_dump($this->data);
        $this->render('login'); //подключаем фаил отображения и передаём ему массив данных
   	}
    //регистрация пользователей
    public function actionRegistration(){
        //проверка на соответствие прав
        if(!MSS::app()->accessCheck('Guest')){
            header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
            exit();
        };
        $GLOBALS['mss_monitor'][] = '<hr>Текущее действие: action'.$this->action.' контроллера: '.$this->model_name.'Controller<br>';
        $model = $this->loadModel();//подключаем модель
        //$data = $model->actionViewData; //получаем данные модели соответствующие текущему действию actionView()
        $data = $model->actionData;//получаем все данные из соответствующей модели (в дан.случ. Site) предназначенные для текущего действия
        $this->data = $data; //пишем в свойство для использования из файла отображения
        //var_dump($this->data);
        $this->render('registration'); //подключаем фаил отображения и передаём ему массив данных
   	}
    //восстановление пароля
    public function actionPassRestore(){
        //проверка на соответствие прав
        if(!MSS::app()->accessCheck('Guest')){
            header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
            exit();
        };
        $GLOBALS['mss_monitor'][] = '<hr>Текущее действие: action'.$this->action.' контроллера: '.$this->model_name.'Controller<br>';
        $model = $this->loadModel();//подключаем модель
        //$data = $model->actionViewData; //получаем данные модели соответствующие текущему действию actionView()
        $data = $model->actionData;//получаем все данные из соответствующей модели (в дан.случ. Site) предназначенные для текущего действия
        $data->parser = $this->params; //добавляем параметры переданные парсером
        $this->data = $data; //пишем в свойство для использования из файла отображения
        //var_dump($this->data);
        $this->render('pass_restore'); //подключаем фаил отображения и передаём ему массив данных
   	}
    
    //настройки пользователя
    public function actionSettings(){
        //проверка на соответствие прав
        if(!MSS::app()->accessCheck('Admin,Суперчеловек ;),SuperUser,Moderator,User')){
            header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
            exit();
        };
        $GLOBALS['mss_monitor'][] = '<hr>Текущее действие: action'.$this->action.' контроллера: '.$this->model_name.'Controller<br>';
        $model = $this->loadModel();//подключаем модель
        //$data = $model->actionViewData; //получаем данные модели соответствующие текущему действию actionView()
        $data = $model->actionData;//получаем все данные из соответствующей модели (в дан.случ. Site) предназначенные для текущего действия
        $this->data = $data; //пишем в свойство для использования из файла отображения
        //var_dump($this->data);
        $this->render('settings'); //подключаем фаил отображения и передаём ему массив данных
   	}
    
    //страница обратной связи (контакты)
    public function actionСontacts(){
        //проверка на соответствие прав
        if(!MSS::app()->accessCheck('Admin,Суперчеловек ;),SuperUser,Moderator,User,Guest')){
            header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
            exit();
        };
        $GLOBALS['mss_monitor'][] = '<hr>Текущее действие: action'.$this->action.' контроллера: '.$this->model_name.'Controller<br>';
        $model = $this->loadModel();//подключаем модель
        //$data = $model->actionViewData; //получаем данные модели соответствующие текущему действию actionView()
        $data = $model->actionData;//получаем все данные из соответствующей модели (в дан.случ. Site) предназначенные для текущего действия
        $this->data = $data; //пишем в свойство для использования из файла отображения
        //var_dump($this->data);
        $this->render('contacts'); //подключаем фаил отображения и передаём ему массив данных
   	}
    
    public function actionAdmin(){
        //проверка на соответствие прав
        if(!MSS::app()->accessCheck('Admin,Суперчеловек ;),SuperUser,Moderator,User,Guest')){
            header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
            exit();
        };
        $GLOBALS['mss_monitor'][] = '<hr>Текущее действие: action'.$this->action.' контроллера: '.$this->model_name.'Controller<br>';
        $model = $this->loadModel();//подключаем модель
        //формируем объект с данными для использования в файле отображения
        $data = $model->actionData;//получаем все данные из соответствующей модели (в дан.случ. Site) предназначенные для текущего действия
        $data->parser = $this->params; //добавляем параметры переданные парсером
        $this->data = $data; //пишем в свойство для использования из файла отображения
        $this->render('view'); //подключаем фаил отображения и передаём ему массив данных
   	}
    
    public function loadModel(){
        //Пробуем подключить фаил соответствующего запросу контроллера
            try{
                if (!file_exists(MSS::app()->config->models_path.$this->model_name.".php")){
                    //если фаил модели отсутствует - формируем текст об ошибке
                    throw new Exception("<br><b>Error: Контроллер не смог открыть нужную модель во время выполнения 'action$this->action' !</b><br>
                                        Вероятно отсутствует фаил модели ($this->model_name.php) или ошибка в 'GET' запросе (<b>$this->model_name</b>)...
                                        ");
                }else{
                        ///создаём экземпляр класса необходимой модели (и проверяем)
                        if(is_object($model = new $this->model_name($this->params))){
                            //возвращаем экземпляр класса модели
                            return $model;
                        }else{
                            echo "<br><b>Error: Контроллер не смог открыть нужную модель во время выполнения 'loadModel()' !</b><br>";
                        }
                    }
                
            }catch (Exception $e){
                echo '<hr>Выброшено исключение: ',$e->getMessage(), "\n <hr>";
            }
    }
}
?>