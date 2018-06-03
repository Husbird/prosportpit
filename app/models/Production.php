<?php
class Production extends MsModel
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
    
    //public $actionIndexData = array();//массив данных для сонтроллера (actionIndex)
    public $actionData = false;//массив данных для сонтроллера (actionView)
    //public $actionViewData2 = 0;   
    
    function __construct($params = array()){
        $GLOBALS['mss_monitor'][] = '<hr>Модель '.ucfirst($this->table_name).' загружена успешно!!!<hr>';
        $this->translit = $params['translit'];//определяем 'translit' (применяется для формирования GET запросов)
        $this->model_name = $params['model']; //переданное имя модели
        $this->table_name = $params['table_name'];
        $this->table_name2 = 'article_cat'; //ставим вручную для каждого конкретного случая
        $this->action = $params['action'];//определяем action !!!
        $this->action_atribute = $params['action_atribute'];//определяем атрибут action. (применяется для формирования GET запросов)
        $this->page = $params['page'];//определяем номер страницы
        $this->id = $params['id'];//определяем id
        $this->category_id = $params['category_id'];//определяем id категории
        $this->category_key = $params['category_key'];//определяем id категории
        $this->sub_category_id = $params['sub_category_id'];//определяем подкатегорию sub_category_id
        $this->system_massage = $params['system_massage_file']; //передаётся парсером затем извлекается их файла функцией getSysMassage

        //готовим данные модели к запрашиваему действию:
        switch($this->action){

            case 'View':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            
            $dbProcess = new MsDBProcess;
            $data = $dbProcess->productSingleSelect($this->id);
            //var_dump($data);
            //$data = $this->dataForID();//получаем массив данных для сонтроллера (actionView)
            //в зависимости от прав включаем кнопки
            if(MSS::app()->accessCheck('Admin,Суперчеловек ;),Moderator')){
                $data['editBtn'] = true;
                $data['delBtn'] = true;
            }
            //SEO
            $MsStringProcess = new MsStringProcess;
            $data['title'] = strip_tags($data["prod_name"].' купить в Донецке, '.$data["category_name"]);
            $data['meta_description'] = strip_tags('Предлагаем Вашему вниманию '.$data["prod_name"].' - спортивное питание категории '.$data["category_name"].', известного производителя '.$data["brand_name"].'('.$data["country"].'): '.$MsStringProcess->cutText_mss($data["txt_full"],200).' Доставка по городам Донецк, Макеевка и другим городам Донецкой области!'); //описание страницы
            $data['meta_keywords'] = $data["prod_name"].','.$data["category_name"].','.$data["brand_name"].',купить,донецке,макеевке'; //ключевые слова
            
            $data['pageTitle'] = $data["prod_name"]; //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            $this->actionData = (object)$data;
            $this->actionData->massage = self::getSysMassage($this->system_massage); //добавляем в массив возможное системное сообщение
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data["prod_name"],4);
            //добавляем +1 к количеству просмотров
            //$dbProcess = new MsDBProcess;
            //$dbProcess->addViewToDB($this->id,$this->table_name);
            break;
            
            case 'Index':
            //die('ddd');
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';

            //массив данных с учётом категории
            $dbProcess = new MsDBProcess;
            $data = $dbProcess->productDataSelect($this->translit,$this->table_name, $this->action_atribute,$this->page, 9,'id', 'DESC',
                                                    $this->category_id, $this->category_key, $this->sub_category_id);
            //$data = $this->AllArticlesData('by category id');//получаем массив данных    
            //var_dump($data);
            //в зависимости от прав включаем кнопки
            if(MSS::app()->accessCheck('Admin,Суперчеловек ;),Moderator')){
                $data['addNewBtn'] = true;
            }
            //готовим заголовок второго уровня
            if($this->category_key == 'category'){ //если выбрали пункт категории
                $data['pageH2Tite'] = $data['data'][0]['category_name'];
                //SEO
                $MsStringProcess = new MsStringProcess;
                //var_dump($data);
                foreach($data['data'] as $key => $value){
                    $allProduction = $allProduction.strip_tags($value['prod_name']).' компании '.strip_tags($value['brand_name']).', ';
                    $data['meta_keywords'] = $data['meta_keywords'].','.strip_tags($value["prod_name"]);
                }
                $data['title'] = strip_tags($data['data'][0]['category_name'].' купить в Донецке');
                $data['meta_description'] = strip_tags('В категории '.$data['data'][0]['category_name'].', в продаже продукция премиум класса, такая как: '.$allProduction.', купить в Донецке, Макеевке');
                
                
            }elseif($this->category_key == 'brand'){//если выбрали пункт бренд
                $data['pageH2Tite'] = $data['data'][0]['brand_name'];
                //SEO
                $MsStringProcess = new MsStringProcess;
                foreach($data['data'] as $key => $value){
                    $allProduction = $allProduction.strip_tags($value['prod_name']).' категории '.strip_tags($value['category_name']).', ';
                    $data['meta_keywords'] = $data['meta_keywords'].','.strip_tags($value["prod_name"]);
                }
                $data['title'] = strip_tags($data['data'][0]['brand_name']).' купить в Донецке';
                $data['meta_description'] = strip_tags('Представляем спортивное питание компании '.$data['data'][0]['brand_name'].'('.$data['data'][0]['country'].'), а именно, продукцию премиум класса: '.$allProduction.', купить в Донецке, Макеевке');
            }
            
            if(($this->category_key == 'category') AND ($this->sub_category_id)){
                //SEO (выбрали категорию и в ней выбрали бренд)
                foreach($data['data'] as $key => $value){
                    $allProduction2 = $allProduction2.strip_tags($value['prod_name']).', ';
                    $data['meta_keywords'] = $data['meta_keywords'].','.strip_tags($value["prod_name"]);
                }
                $data['title'] = strip_tags($data['data'][0]['category_name'].' '.$data['data'][0]['brand_name'].' купить в Донецке');
                $data['meta_keywords'] = strip_tags($data['meta_keywords']).', купить, Донецке, Макеевке';
                $data['meta_description'] = strip_tags('Предлагаем '.$data['data'][0]['category_name'].' премиум класса, компании '.$data['data'][0]['brand_name'].', а именно: '.$allProduction2.' купить в Донецке, Макеевке');
                
                $data['pageH3Tite'] = $data['data'][0]['brand_name'];
            }elseif(($sub_category_id) AND ($this->category_key == 'brand')){
                
                $data['pageH3Tite'] = $data['data'][0]['category_name'];
            }
            
            $data['pageTitle'] = 'Спортивное питание '; //определяем title для отображения в метатеге шаблона (в Index ставим вручную)
            $this->actionData = (object)$data;
            //инициализация ссылок в виджете хлебные крошки
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            $breadcumb->setLink($data['pageH2Tite'],3);
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
            
            //готовим данные имеющихся категорий
            $data['product_category_all'] = $this->getTableInfo('prod_cat'); //массив с категориями товара (обавляем полученный массив к основному массиву $data)
            //готовим данные имеющихся производителей
            $data['product_brand_all'] = $this->getTableInfo('prod_brand'); //массив с производителями товара (обавляем полученный массив к основному массиву $data)
            
            $data['pageTitle'] = 'Редактирование данных продукта';
            $this->actionData = (object)$data;
            $this->actionData->massage = self::getSysMassage($this->system_massage); //добавляем в массив возможное системное сообщение
            //подключаем виджет хлебные крошки:
            $breadcumb = new Breadcrumb();
            //инициализация ссылок в виджете хлебные крошки
            $breadcumb->setLink($data['pageTitle'],3); //var_dump($this->data);
            break;
            
            case 'All':
            $GLOBALS['mss_monitor'][] =  '<br>работает case "'.$this->action.'" модели "'.ucfirst($this->model_name).'":<br>';
            //массив данных БЕЗ учёта категорий
            $dbProcess = new MsDBProcess;
            $data = $dbProcess->productDataSelect($this->translit,$this->table_name, $this->action_atribute,$this->page, 9,'id', 'DESC',
                                                    false, false, false);
            //в зависимости от прав включаем кнопки
            if(MSS::app()->accessCheck('Admin,Суперчеловек ;),Moderator')){
                $data['addNewBtn'] = true;//включаем кнопку "добавить"
            }
            //SEO
            $MsStringProcess = new MsStringProcess;
            foreach($data['data'] as $key => $value){
                $allProduction = $allProduction.$value['prod_name'].' компании '.$value['brand_name'].', ';
                $data['meta_keywords'] = $data['meta_keywords'].','.$value["prod_name"];
            }
            $data['title'] = strip_tags($data['data'][0]['category_name'].' купить в Донецке');
            $data['meta_description'] = strip_tags('Предлагаем спортивное питание премиум класса, а именно: '.$allProduction.', купить в Донецке, Макеевке');
            
            $data['pageTitle'] = 'Вся продукция'; //определяем title для отображения в метатеге шаблона (в Index ставим вручную)
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
            //готовим данные имеющихся категорий
            $data['product_category_all'] = $this->getTableInfo('prod_cat'); //массив с категориями товара (обавляем полученный массив к основному массиву $data)
            //готовим данные имеющихся производителей
            $data['product_brand_all'] = $this->getTableInfo('prod_brand'); //массив с производителями товара (обавляем полученный массив к основному массиву $data)
            //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            $data['pageTitle'] = 'Добавляем ноый продукт!';
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
            $data['article_catData'] = $this->getTableInfo('article_cat'); //массив с категориями статей (обавляем полученный массив к основному массиву $data)
            //определяем title для отображения в метатеге шаблона (подставляем в автомате из БД)
            $data['pageTitle'] = 'Добавляем новую категорию статей!';
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
    
    public function AllArticlesData($options = false){
        $dbObj = new MsDBProcess;
        if(!$options){
            $mixedDataArray = $dbObj->AllArticlesData($this->translit,$this->table_name,$this->action_atribute,$this->page,5,'id','DESC',false);    
        }elseif($options == 'by category id'){
            $mixedDataArray = $dbObj->AllArticlesData($this->translit,$this->table_name,$this->action_atribute,$this->page,5,'id','DESC',
                                                                                                                            $this->category_id); 
        }
        //var_dump($mixedDataArray);
        return $mixedDataArray;
    }
}
?>