<?php
//набор функций обработки изображений
class MsIMGProcess
{
 /**
 *    function __construct(){
 *         
 *     }
 */
    //подготовка изображения к выводу на экран по установленному значению максимально-выводимой стороны, с учётом пропорций
    //реального размера изображения
    public function img_out_size_mss($img_path, $max_scale) {
    
    // $img_path - путь к изображению $img_path = 'view/i/prod_img_mss/'.$id.'.jpg';
    //$max_scale - максимально допустимый размер бОльшей стороны изображения
    
    	list($w, $h) = getimagesize($img_path); // получаем размеры изображения
    
    	//если высота больше ширины
    	if ($h > $w) {
    		$one_procent = $h/100; //вычисляем 1% из бОльшей стороны (h)
    		$procent = $w/$one_procent; //вычисляем сколько процентов составляет меньшая сторона (w) от большей (h)
    		
    		//вычисляем размеры картинки для вывода на экран
    		$h_view = $max_scale;//т.к. высота - бОльшая величина - присваиваем ей максимально допустимый размер заданый в $max_scale
    		$w_view = $max_scale/100 * $procent;//т.к h = $max_scale, находим 1% от $max_scale и умножаем на составляющую ширины (w) $procent 
    											// и получаем новый ($w_view) размер ширины с учётом нового размера высоты $h_view
    		$w_view = round($w_view,0);//округляем результат
    	}
    	//если высота меньше ширины
    	if ($w > $h) {
    		$one_procent = $w/100; //вычисляем 1% из бОльшей стороны (h)
    		$procent = $h/$one_procent; //вычисляем сколько процентов составляет меньшая сторона (w) от большей (h)
    		
    		//вычисляем размеры картинки для вывода на экран
    		$w_view = $max_scale;//т.к. ширина - бОльшая величина - присваиваем ей максимально допустимый размер заданый в $max_scale
    		$h_view = $max_scale/100 * $procent;//находим высоту учитывая её процентное отношение к ширине и максимально допустимый размер заданый в $max_scale
    		$h_view = round($h_view,0);//округляем результат
    	}
    	if ($h == $w) {
    		$h_view = $max_scale;
    		$w_view = $max_scale;
    	}
    	
    	//добавляем полученные данные в массив $img_out_size_mss_rezult
    	$img_out_size_mss_rezult[0] = $h_view;//конечная высота
    	$img_out_size_mss_rezult[1] = $w_view;//конечная ширина
    	$img_out_size_mss_rezult[2] = $h;//изначальная высота h
    	$img_out_size_mss_rezult[3] = $w;//изначальная ширина w
    	$img_out_size_mss_rezult[4] = $procent;//сколько процентов составляет меньшая сторона от большей в % (из изначальных параметром h и w)
    	
    	return $img_out_size_mss_rezult;//возвращаем массив
    }

    //функция ресайза (с сохранением пропорций) и записи изображения полученного из формы
    //$max_scale - максимальный допустимый размер наибольшей стороны в пикселах
    //$maxSizeMB - максимальный размер изображения в мегабайтах 
    //$save_path - полный путь для сохранения (вместе с сохраняемым названием файла пример: $save_path = 'view/i/albums/_'.$id_last.'/_ava.jpg';) 
    public function cut_and_save_img_mss($max_scale,$maxSizeMB,$save_path) {
        if($_FILES["image"]["size"] > 1024*$maxSizeMB*1024) {
    		 echo ("Размер файла превышает $maxSizeMB5 мегабайт");
    		 exit;
        }
    	   // Проверяем загружен ли файл
        if(is_uploaded_file($_FILES["image"]["tmp_name"])) {
            // Если файл загружен успешно...
            
            //подставляем максимальный размер бОльшей стороны используемый для ресайза $max_real_scale
            $max_real_scale = $max_scale;
            
            $input_img_file = $_FILES["image"]["tmp_name"];
            list($w, $h) = getimagesize($input_img_file); // получаем размеры изображения
            //var_dump($w);var_dump($h);die;
            
            //если ширина больше высоты - задаём ширину = $max_real_scale а высота расчитывается авто с учётом пропорций
            if ($w > $h) {
            	//$save_path = 'view/i/albums/_'.$id_last.'/_ava.jpg';//определяем путь для сохранения
            	$this->resize($input_img_file,$save_path,$max_real_scale,0); // задаём ширину 600 (сохраняя пропорции) и сохраняем в $save_path
            }
            
            //если высота больше ширины - задаём высоту = $max_real_scale а ширина расчитывается авто с учётом пропорций
            if ($h > $w) {
            	//$save_path = 'view/i/albums/_'.$id_last.'/_ava.jpg';//определяем путь для сохранения
                //var_dump($save_path);die;
            	$this->resize($input_img_file,$save_path,0,$max_real_scale); // задаём высоту 600 (сохраняя пропорции) и сохраняем в $save_path
            }
            
             //если стороны равны - задаём высоту = $max_real_scale а ширина расчитывается авто с учётом пропорций
            if ($w == $h) {
            	//$save_path = 'view/i/albums/_'.$id_last.'/_ava.jpg';//определяем путь для сохранения
            	$this->resize($input_img_file,$save_path,$max_real_scale,$max_real_scale); // задаём ширину 600 (сохраняя пропорции) и сохраняем в $save_path
            }
        }//else{
            //die('фаил не загружен!');
        //}
    }
    
    //Функция масштабирования
    public function resize($file_input, $file_output, $w_o, $h_o, $percent = false) {
    	list($w_i, $h_i, $type) = getimagesize($file_input);
    	if (!$w_i || !$h_i) {
    		echo 'Невозможно получить длину и ширину изображения';
    		return;
            }
            $types = array('','gif','jpeg','png');
            $ext = $types[$type];
            if ($ext) {
        	        $func = 'imagecreatefrom'.$ext;
        	        $img = $func($file_input);
            } else {
        	        echo 'Некорректный формат файла';
    		return;
            }
    	if ($percent) {
    		$w_o *= $w_i / 100;
    		$h_o *= $h_i / 100;
    	}
    	if (!$h_o) $h_o = $w_o/($w_i/$h_i);
    	if (!$w_o) $w_o = $h_o/($h_i/$w_i);
    
    	$img_o = imagecreatetruecolor($w_o, $h_o);
    	imagecopyresampled($img_o, $img, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);
    	if ($type == 2) {
    		return imagejpeg($img_o,$file_output,100);
    	} else {
    		$func = 'image'.$ext;
    		return $func($img_o,$file_output);
    	}
    }
}
?>