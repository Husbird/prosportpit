<?php
class MsModel {

    //public $actionIndexData = array();//массив данных для сонтроллера (actionIndex)
    public $actionData = false;//массив данных для сонтроллера (actionView)
    //public $actionViewData2 = 0;   
    
    function __construct(){
        $GLOBALS['mss_monitor'][] = '<hr>Подключаю класс MsModel!!!<hr>';
        //подключаем виджет хлебные крошки:
        //$breadcumb = new Breadcrumb();
    }
    
    //извлечение всех данных из указанной таблицы и генерирование пагинации
    public function AllData($DBNumber = false,$num = 5){
        $dbObj = new MsDBProcess($DBNumber);
        $mixedDataArray = $dbObj->MsAllSelect($this->translit,$this->table_name,$this->action_atribute,$this->page,$num,'id','DESC');
        //var_dump($mixedDataArray);
        return $mixedDataArray;
        //$data = $mixedDataArray['data'];
        //$pagesNav = $mixedDataArray['pagesNav']
        //var_dump($data['data']);
    }
    
    //извлечение всех данных определённой категории из указанной таблицы и генерирование пагинации
    /**
 * public function AllDataByCategory(){
 *         $dbObj = new MsDBProcess;
 *         $mixedDataArray = $dbObj->MsAllSelect($this->translit,$this->table_name,$this->action_atribute,$this->page,3,'id','DESC', 
 *                                                                                                 $this->category_id, $this->table_name2);
 *         //var_dump($mixedDataArray);
 *         return $mixedDataArray;
 *         //$data = $mixedDataArray['data'];
 *         //$pagesNav = $mixedDataArray['pagesNav']
 *         //var_dump($data['data']);
 *     }
 */
    
    //извлечение данных по принятому id из соответствующей таблицы
    public function dataForID(){
        $GLOBALS['mss_monitor'][] = 'dataForID(): Содаю объект $dbObj = new MsDBProcess ...<hr>';
        $dbObj = new MsDBProcess;
        $GLOBALS['mss_monitor'][] = 'dataForID(): Объект $dbObj создан !!!<hr>';
        $mixedDataArray = $dbObj->selectDataOnID($this->id,$this->table_name);
        return $mixedDataArray;
        //$data = $mixedDataArray['data'];
        //$pagesNav = $mixedDataArray['pagesNav']
        //var_dump($data['data']);
    }
    
    //выборка всех данных указанной таблицы
    public function getTableInfo($tableName = false, $whereString = false){
        $dbObj = new MsDBProcess;
        //массив с ключевыми словами (входящие параметры - обязательно массивы!)
        $mixedDataArray = $dbObj->getTableInfo($tableName,$whereString);
        //var_dump($mixedDataArray);
        return $mixedDataArray;
    }
    
    //выборка ключевых слов к данному разделу
    public function keepMarkerData(){
        //готовим ключевые слова
        $tableNames[] = $this->table_name;
        $fieldNames[] = $this->table_name.'_keywords';
        $dbObj = new MsDBProcess;
        //массив с ключевыми словами (входящие параметры - обязательно массивы!)
        $mixedDataArray = $dbObj->keepMarkerData($tableNames,$fieldNames);
        //var_dump($mixedDataArray);
        return $mixedDataArray;
    }
    
    //получаем системное сообщение из сообветсвующего фаила
    public function getSysMassage($massageName){
        //var_dump($massageName);
        if($massageName){
            $massageName = strtolower(trim($massageName));
            if (file_exists("framework/components/massages/_$massageName.php")){
                $_SESSION['mss_monitor'][] = "<b>getSysMassage():</b> подключаю фаил с системным сообщением:
                 модель <b>$this->model_name</b> метод <b>action$this->action</b> (имя файла: _$massageName.php)</span>";
                return include_once("framework/components/massages/_$massageName.php");
            }else{
                $_SESSION['mss_monitor'][] = "<span style='color:red'>Ошибка:</span> фаил с системным сообщением не найден:
                 модель <b>$this->model_name</b> метод <b>action$this->action</b> (имя файла: _$massageName.php)</span>";
                echo "<span style='color:red'>Ошибка:</span> подключения файла с системным сообщением см. журнал ошибок...";
            }   
        }
        
    }
}
?>