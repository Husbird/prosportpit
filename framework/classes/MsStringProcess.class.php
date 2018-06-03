<?php
//набор функций обработки строк
class MsStringProcess
{
    public $mysqli;
 /**
 *    function __construct(){
 *         
 *     }
 */
    //вырезаем имя админа из строки типа: id: 72 | Имя: Сергей | IP:127.0.0.1
    //ВНИМАНИЕ: работает только если кодировка файла (содержащего данную функцию) UTF-8
    public function cutAdminName_mss($string){
        //var_dump($string);
        $string = strstr($string, 'Имя'); //получаем: Имя: Сергей | IP:127.0.0.1
        //var_dump($string);
        $string = strstr($string, '| IP', true); //получаем: Имя: Сергей
        $string = substr($string, 7, 15);//получаем: Сергей    (+9 символов пустых после имени )
        
        return trim($string); //убираем лишние пробелы
    }
    
    public function cutText_mss($string, $number){
        $cutText = substr($string, 0, $number)." ...";
    return nl2br($cutText); //nl2br - сохраняем переносы строк
    }
    
    //вывод меток (ключевых слов) принимает массив
    public function echoKeyWords($keyWords) {
	if(is_array($keyWords)){
        echo "<div style='max-width:90%; margin: 0 auto; margin:2%;'>";
        foreach ($keyWords as $key => $value){
            $value = trim(mb_strtoupper($value, 'UTF-8'));
                $backgroundColor = 'background-color:#000';
            echo "<div style='padding:3px; $backgroundColor; display:inline-block; margin:3px;'>
                <span style='color:#fff;
                 text-decoration: none;'>$value</span></div>";
        }
            echo "</div>";
        }else{
            echo 'ключевых слов\меток не найдено...';
        }
    }
    
    //обработка строковых данных получяемых из форм ввода $mysqli  link
    public function checkStr($string){
        $this->mysqli = MsDBConnect::getInstance()->getMysqli();
        $str = mysqli_real_escape_string($this->mysqli,strip_tags(trim($string)));
        $_SESSION['mss_monitor'][] = '<b>'.__METHOD__.'</b>: проверяю строку - '.$str;
        return ($str);
    }
    
}
?>