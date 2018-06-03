<?php
class Video extends MsModel
{
    protected $translit; //(применяется для формирования GET запросов)
    protected $table_name = false; //имя таблицы
    protected $model_name; //имя модели
    protected $action = 'Index'; //действие по умолчанию
    protected $action_atribute = 'i'; //атрибут действия по умолчанию (применяется для формирования GET запросов)
    protected $page = false; //по умолчанию всегда первая страница
    protected $id = false; //по умолчанию
    protected $category_id = false; //id категории
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
        $this->category_id = $params['category_id'];//определяем id категории
        $this->system_massage = $params['system_massage_file']; //передаётся парсером затем извлекается их файла функцией getSysMassage
        
        //готовим данные модели к запрашиваему действию:
        switch($this->action){

            case 'View':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            $data = $this->dataForID();//получаем массив данных для сонтроллера (actionView)
            //var_dump($data);
            //Проверка доступа к закрытому видео
            if($data['access_level'] == 'closed'){ //если видео отмечено как скрытое - проверяем права
                if(!MSS::app()->accessCheck('Admin,Суперчеловек ;),SuperUser')){
                    header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
                    exit();
                };   
            }
            //в зависимости от прав включаем кнопки
            if(MSS::app()->accessCheck('Admin,Суперчеловек ;),Moderator')){
                $data['editBtn'] = true;
                $data['delBtn'] = true;
            }
            $data['pageTitle'] = $data["video_name"]; //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
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
            
            case 'Index':;
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            //если нажали по метке (поиск)
            if($_SESSION['searchWord']){
                 // ------------------------------------------------ поиск по ключевому слову -----------------------------------
                $MsSearchKWord = new MsSearchKWord($this->table_name,$this->model_name,$this->action);
                $idMassiv = $MsSearchKWord->searchEngine_mss($_SESSION['searchWord']);// получаем массив id соответствующих искомому слову
                $data = $MsSearchKWord->idToData_pageNav_mss($idMassiv,'search',$this->table_name,$this->action_atribute, $this->page,3, 'id', 'ASC');
            }else{
                $data = $this->AllVideoData('by category id');//получаем массив данных
            }
            //в зависимости от прав включаем кнопки
            //if((MSS::$user_role == 'Admin') OR (MSS::$user_role == 'Суперчеловек ;)')){
            if(MSS::app()->accessCheck('Admin,Суперчеловек ;),Moderator')){
                $data['addNewBtn'] = true;
            }
            $data['pageTitle'] = 'Видео альбом'; //определяем title для отображения в метатеге шаблона (в Index ставим вручную)
            $data['active'.$this->model_name] = 'active'; //для активации пункта меню навигации
            $this->actionData = (object)$data;
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink('Видео альбом',2);
            break;
            
            case 'Edit':
            //проверка прав:
            //для пользователей, имеющих права переданные в accessCheck - доступ будет открыт, для остальных закрыт!
            if(!MSS::app()->accessCheck('Admin,Суперчеловек ;),Moderator')){
                header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
                exit();
            }

            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            $data = $this->dataForID();//получаем основной массив данных
            //готовим ключевые слова
            $keyWords = $this->keepMarkerData(); //массив с ключевыми словами
            $data['allKeyWords'] = $keyWords; //добавляем полученный массив к основному массиву $data
            //данные о пользователе (доступны только ЕСЛИ пользователь авторизован)
            $data['edit_info'] = "id: ".MSS::$userData['id']." | Имя: ".MSS::$userData['name']." | IP:".MSS::$userData['ip']; //добавляем данные редактора
            $data['date_edit'] = time(); //добавляем дату редактирования
            //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            $data['pageTitle'] = 'Редактирование видео';
            $this->actionData = (object)$data;
            $this->actionData->massage = self::getSysMassage($this->system_massage); //добавляем в массив возможное системное сообщение
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['pageTitle'],3);
            break;
            
            case 'All':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            //если нажали по метке (поиск)
            if($_SESSION['searchWord']){
                 // ------------------------------------------------ поиск по ключевому слову -----------------------------------
                $MsSearchKWord = new MsSearchKWord($this->table_name,$this->model_name,$this->action);
                $idMassiv = $MsSearchKWord->searchEngine_mss($_SESSION['searchWord']);// получаем массив id соответствующих искомому слову
                $data = $MsSearchKWord->idToData_pageNav_mss($idMassiv,'search',$this->table_name,$this->action_atribute, $this->page,3, 'id', 'ASC');
            }else{
                $data = $this->AllVideoData();//получаем массив данных
            }
            //в зависимости от прав включаем кнопки
            if(MSS::app()->accessCheck('Admin,Суперчеловек ;)')){
                $data['addNewBtn'] = true;//включаем кнопку "добавить"
            }
            $data['pageTitle'] = 'Всё видео'; //определяем title для отображения в метатеге шаблона (в Index ставим вручную)
            $this->actionData = (object)$data;
            //инициализация ссылок в виджете хлебные крошки
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            $breadcumb->setLink($data['pageTitle'],2);
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
            //готовим данные имеющихся категорий
            $data['video_catData'] = $this->getTableInfo('video_cat'); //массив с категориями(обавляем полученный массив к основному массиву $data)
            $data['pageTitle'] = 'Добавление нового видео';
            $this->actionData = (object)$data;
            $this->actionData->massage = self::getSysMassage($this->system_massage); //добавляем в массив возможное системное сообщение
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['pageTitle'],3);
            break;
            
            case 'AddCategory':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            //$data = $this->dataForID();//получаем основной массив данных
            //данные о пользователе (доступны только ЕСЛИ пользователь авторизован)
            $data['admin_info'] = "id: ".MSS::$userData['id']." | Имя: ".MSS::$userData['name']." | IP:".MSS::$userData['ip']; //добавляем данные редактора
            $data['date_add'] = time(); //добавляем дату редактирования
            //готовим данные имеющихся авторов тезисов для выпадающего списка формы
            $data['video_catData'] = $this->getTableInfo('video_cat'); //массив с категориями статей (обавляем полученный массив к основному массиву $data)
            //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            $data['pageTitle'] = 'Добавляем новую категорию видео!';
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
    
    public function AllVideoData($options = false){
        $dbObj = new MsDBProcess;
        if(!$options){
            $mixedDataArray = $dbObj->AllVideoData($this->translit,$this->table_name,$this->action_atribute,$this->page,3,'id','DESC',false);    
        }elseif($options == 'by category id'){
            $mixedDataArray = $dbObj->AllVideoData($this->translit,$this->table_name,$this->action_atribute,$this->page,3,'id','DESC',
                                                                                                                            $this->category_id); 
        }
        //var_dump($mixedDataArray);
        return $mixedDataArray;
    }
}
?>