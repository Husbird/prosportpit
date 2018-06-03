<?php
/**
 * Админка!
 * Модель:
 * Текущие заказы пользователей
 */
class Orders extends MsModel
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
    protected $order_status = false; //статус заказа (реализованный, отменённый, текущий)
    
    //public $actionIndexData = array();//массив данных для сонтроллера (actionIndex)
    public $actionData = false;//массив данных для сонтроллера (actionView)
    //public $actionViewData2 = 0;   
    
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
        $this->order_status = $params['category_id']; //статус заказа (реализованный, отменённый, текущий)
        
        //готовим данные модели к запрашиваему действию:
        switch($this->action){

            case 'View':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            $data = $this->dataForID();//получаем массив данных для сонтроллера (actionView)
            if(MSS::app()->accessCheck('Admin,Суперчеловек ;)')){
                $data['editBtn'] = true;
                $data['delBtn'] = true;
            }
            $data['pageTitle'] = $data["photo_name"]; //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            $data['active'.$this->model_name] = 'active'; //для активации пункта меню навигации
            $this->actionData = (object)$data;
            $this->actionData->massage = self::getSysMassage($this->system_massage); //добавляем в массив возможное системное сообщение
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['pageTitle'],3);
            //добавляем +1 к количеству просмотров
            $dbProcess = new MsDBProcess;
            $dbProcess->addViewToDB($this->id,$this->table_name);
            break;
            
            case 'Index':
            //var_dump($this->order_status);die('dfsfsd');
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            //var_dump($params);die;
            $MsOrderProcess =  new MsOrderProcess;
            $data = $MsOrderProcess->SelectOrders($this->translit, $this->order_status, $this->page);
            //var_dump($data);
            //$data = $this->AllData();//получаем массив данных для сонтроллера (actionView)
            //в зависимости от прав включаем кнопки
            if(MSS::app()->accessCheck('Admin,Суперчеловек ;)')){
                $data['addNewBtn'] = true;
            }
            if($this->order_status == 'current'){
                $data['pageTitle'] = 'Текущие заказы'; //определяем title для отображения в метатеге шаблона (в Index ставим вручную)
            }elseif($this->order_status == 'sold'){
                $data['pageTitle'] = 'Реализованные заказы'; //определяем title для отображения в метатеге шаблона (в Index ставим вручную)
            }elseif($this->order_status == 'aborted'){
                $data['pageTitle'] = 'Отменённые заказы'; //определяем title для отображения в метатеге шаблона (в Index ставим вручную)
            }
            $data['active'.$this->model_name] = 'active'; //для активации пункта меню навигации
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink('управление',1);
            $breadcumb->setLink($data['pageTitle'],2);
            break;
            
            case 'Edit':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            $data = $this->dataForID();//получаем основной массив данных
            //готовим ключевые слова
            $keyWords = $this->keepMarkerData(); //массив с ключевыми словами
            $data['allKeyWords'] = $keyWords; //добавляем полученный массив к основному массиву $data
            //данные о пользователе (доступны только ЕСЛИ пользователь авторизован)
            $data['edit_info'] = "id: ".MSS::$userData['id']." | Имя: ".MSS::$userData['name']." | IP:".MSS::$userData['ip']; //добавляем данные редактора
            $data['date_edit'] = time(); //добавляем дату редактирования
            //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            $data['pageTitle'] = 'Редактирование фото';
            $this->actionData = (object)$data;
            $this->actionData->massage = self::getSysMassage($this->system_massage); //добавляем в массив возможное системное сообщение
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['pageTitle'],3);
            break;
            
            case 'Add':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            //$data = $this->dataForID();//получаем основной массив данных
            //готовим ключевые слова
            $keyWords = $this->keepMarkerData(); //массив с ключевыми словами
            $data['allKeyWords'] = $keyWords; //добавляем полученный массив к основному массиву $data
            //данные о пользователе (доступны только ЕСЛИ пользователь авторизован)
            $data['admin_info'] = "id: ".MSS::$userData['id']." | Имя: ".MSS::$userData['name']." | IP:".MSS::$userData['ip']; //добавляем данные редактора
            $data['date_add'] = time(); //добавляем дату редактирования
            //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            $data['pageTitle'] = 'Добавление нового фото';
            $this->actionData = (object)$data;
            $this->actionData->massage = self::getSysMassage($this->system_massage); //добавляем в массив возможное системное сообщение
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['pageTitle'],3);
            break;
            
            //если действие не определено
            default:
            die("<br>Модель: <b>$this->model_name</b>: Ошибка: Определить действие (action '$this->action') переданное в модель '$this->model_name' НЕ удалось!<br> Работа модели прервана.");
        }
    }
}
?>