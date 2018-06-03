<?php
class Site extends MsModel
{
    protected $translit; //(применяется для формирования GET запросов)
    protected $table_name = false; //имя таблицы
    protected $model_name; //имя модели
    protected $action = 'Index'; //действие по умолчанию
    protected $action_atribute = 'i'; //атрибут действия по умолчанию (применяется для формирования GET запросов)
    protected $page = false; //по умолчанию всегда первая страница
    protected $id = false; //по умолчанию
    protected $system_massage = false; //полученное системное сообщение
    protected $paginationNum = 3; //по умолчанию выводим 3 строки таблицы на странице
    protected $params = false;
    
    public $actionData = false;//массив данных для сонтроллера (actionView)
    
    function __construct($params = array()){
        $GLOBALS['mss_monitor'][] = '<hr>Модель '.ucfirst($this->table_name).' загружена успешно!!!<hr>';
        $this->translit = $params['translit'];//определяем 'translit' (применяется для формирования GET запросов)
        $this->model_name = $params['model']; //переданное имя модели
        $this->table_name = $params['table_name'];
        $this->action = $params['action'];//определяем action !!!
        $this->action_atribute = $params['action_atribute'];//определяем атрибут action. (применяется для формирования GET запросов)
        $this->page = $params['page'];//определяем номер страницы
        $this->id = $params['id'];//определяем id
        $this->system_massage = $params['system_massage_file']; //передаётся парсером затем извлекается их файла функцией getSysMassage
        
        //готовим данные модели к запрашиваему действию:
        switch($this->action){

            case 'View':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            $data = $this->dataForID();//получаем массив данных для сонтроллера (actionView)
            $data['pageTitle'] = $data['title']; //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            $data['activeHome'] = 'active'; //для активации пункта меню навигации
            $data['meta_keywords'] = 'спортивного,питания,интернет,магазин,prosportpit'; //ключевые слова
            //$data['meta_description'] = ;
            $this->actionData = (object)$data;
            $this->actionData->massage = self::getSysMassage($this->system_massage); //добавляем в массив возможное системное сообщение
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['title'],2);
            break;
            
            case 'Index':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            //var_dump($params);die;
            $data = $this->AllData();//получаем массив данных для сонтроллера (actionView)
            $data['pageTitle'] = $data['title']; //определяем title для отображения в метатеге шаблона (в Index ставим вручную)
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['title'],2);
            break;
            
            case 'Login':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            $data = $this->dataForID();//получаем массив данных для kонтроллера
            //если возникла ошибка - передаём её из сессии в системное сообщение (как элемент массива общих данными)
            if($_SESSION['authErr']){
                $data['system_massage'] = $_SESSION['authErr']; //добавляем к массиву данных элемент с системным сообщением
                unset($_SESSION['authErr']);
            }
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['title'],3);
            break;
            
            case 'Registration':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            //var_dump($params);die;
            $data = $this->dataForID();//получаем массив данных для kонтроллера
            if($_SESSION['registration_error']){
                $data['system_massage'] = $_SESSION['registration_error']; //добавляем к массиву данных элемент с системным сообщением
                unset($_SESSION['registration_error']);
            }
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['title'],2);
            break;
            
            case 'PassRestore':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            $data = $this->dataForID();//получаем массив данных для kонтроллера
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['title'],2);
            break;
            
            case 'Settings':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            //var_dump(MSS::$userData['id']);die;
            $data = $this->dataForID();//получаем массив данных для kонтроллера
            $data['pageTitle'] = 'Настройка профиля';
            $data['active'.$this->model_name] = 'active'; //для активации пункта меню навигации
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки pageTitle
            $breadcumb->setLink("{$data['pageTitle']}",2);
            break;
            
            case 'Сontacts':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            //var_dump(MSS::$userData['id']);die;
            $data = $this->dataForID();//получаем массив данных для kонтроллера
            $data['pageTitle'] = 'Напишите нам';
            $data['activeContacts'] = 'active'; //для активации пункта меню навигации
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки pageTitle
            $breadcumb->setLink("{$data['pageTitle']}",2);
            break;
            
            //если действие не определено
            default:
            die("<br>Модель: <b>$this->model_name</b>: Ошибка: Определить действие (action '$this->action') переданное в модель '$this->model_name' НЕ удалось!<br> Работа модели прервана.");
        }
    }
}
?>