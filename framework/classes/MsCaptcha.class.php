<?php

/**
 * @author Biblos
 * @copyright 2014
#########################################
#	CAPTCHA						        #
#	фаил - класс:                       #
#    Completely Automated Public Turing # 
#    test to tell Computers and         # 
#    Humans Apart			            #
#	MsCaptcha.class.php  		    	#
#										#
#	DESIGNED BY M & S 					#
#	03.12.2016                          #
#######################################*/

class MsCaptcha 
{
	//настройки:

    //сообщение о бане по ip (вместо кнопки формы)
    public $banMassage = 'ВНИМАНИЕ: Ваш IP-адрес заблокирован, сообщите администрации сайта';
    //сообщение об отсутствии комментариев
    public $noCommentsMassage = '
            <div class="alert alert-success" role="alert">
                <p>Не верно введён код с картинки</p>
            </div>
        ';
    //режим отладки:
    public $debugMode = false; //true - включён; false - выключен
    

    public $mysqli; //метка соединения
    
    //для getSekretImg() свойства по умолчанию
    public $width = 200;//Ширина изображения
    public $height = 90;//Высота изображения
    public $font_size = 19;//Размер шрифта
    public $let_amount = 4;//Количество символов, которые нужно набрать
    public $fon_let_amount = 30;//Количество символов на фоне
    public $font = "assets/fonts/europe_normal.ttf";
    public $cod;//код с картинки
    

    function __construct(){
        $this->mysqli = MsDBConnect::getInstance()->getMysqli();
        //var_dump($this->link);
    }

    public function getSekretImg(){
        $width = $this->width;//Ширина изображения
        $height = $this->height;//Высота изображения
        $font_size = $this->font_size;//Размер шрифта
        $let_amount = $this->let_amount;//Количество символов, которые нужно набрать
        $fon_let_amount = $this->fon_let_amount;//Количество символов на фоне
        $font = $this->font;//Путь к шрифту (относительно корня сайта)

        $letters = array("2","b","e","4","s","h","7"); //набор символов
        $colors = array("90","110","130","150","170","190","210"); //цвета

        $src = imagecreatetruecolor($width,$height);    //создаем изображение       
        $fon = imagecolorallocate($src,255,255,255);    //создаем фон
        imagefill($src,0,0,$fon);                       //заливаем изображение фоном

        for($i=0;$i < $fon_let_amount;$i++) {         //добавляем на фон буковки
            $color = imagecolorallocatealpha($src,rand(0,255),rand(0,255),rand(0,255),100);//случайный цвет
            $letter = $letters[rand(0,sizeof($letters)-1)];//случайный символ                           
            $size = rand($font_size-2,$font_size+2);//случайный размер                                           
            imagettftext($src,$size,rand(0,45),
            rand($width*0.1,$width-$width*0.1),
            rand($height*0.2,$height),$color,$font,$letter);
        }
        //то же самое для основных букв
        for($i=0;$i < $let_amount;$i++)  {
            $color = imagecolorallocatealpha($src,$colors[rand(0,sizeof($colors)-1)],
            $colors[rand(0,sizeof($colors)-1)],
            $colors[rand(0,sizeof($colors)-1)],rand(20,40));
            $letter = $letters[rand(0,sizeof($letters)-1)];
            $size = rand($font_size*2-2,$font_size*2+2);
            $x = ($i+1)*($font_size + 15) + rand(1,5);//даем каждому символу случайное смещение ($font_size + 15) - расстояние между символами
            $y = (($height*2)/3) + rand(1,10);                           
            $cod[] = $letter;//запоминаем код
            imagettftext($src,$size,rand(0,15),$x,$y,$color,$font,$letter);
        }
    imagegif($src,'assets/temp/captcha/pic_'.$_COOKIE["PHPSESSID"].'.gif');//пишем картинку в папку
    $cod = implode("",$cod);  //переводим код в строку
    $this->cod = strtolower($cod);
    $_SESSION['captcha_secpic'] = $cod; //пишем в сессию код картинки
    //return $cod = implode("",$cod);  //переводим код в строку
    }
    
    //проверка правильности ввода кода с картинки
    public function captchaCodCheck($cod){
        /*$x = unlink('assets/temp/captcha/pic_'.$_COOKIE["PHPSESSID"].'.gif'); //удаляем уже не нужный картинку с кодом из временной папки
        if(!$x){
            new MsLogWrite('error',MSS::app()->config->save_all, MSS::app()->config->log_files_write,
                        'ошибка: картинку с кодом удалить не удалось',__METHOD__);
        }*/
        $cod = strtolower(trim(strip_tags($cod)));
        if($cod === $_SESSION['captcha_secpic']){
            unset($_SESSION['captcha_secpic']);
            return true;
        }else{
            unset($_SESSION['captcha_secpic']);
            return false;
        }
        
    }
}
?>