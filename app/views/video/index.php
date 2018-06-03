<?php
/**
 * @author Biblos
 * @copyright 2014
 * index.php (Audio)
 */
 //var_dump($this->data);
 
//$pages = $this->data->pagesNav;//массив ссылок для постраничной навигации (для виджета Pagination)
$data = $this->data->data;//данные из БД
$parser = $this->data->parser;//поарметры из парсера

//var_dump($parser);

$modelName = strtolower($this->data->parser['model']); //для формирования ссылки на view

$timeProcess = new MsTimeProcess;
$stringProcess = new MsStringProcess;
$imgProcess = new MsIMGProcess;
$hfu = new MsHfu;
$comments = new MsGbook;//создаём объект
//подключаем поиск по ключевому слову
$MsSearchKWord = new MsSearchKWord($tableName = strtolower($this->data->parser['model']), strtolower($this->data->parser['model']), 
                                        $this->data->parser['action_atribute'] ,$this->data->parser['translit']);
 
 echo '<h1>'.$this->data->pageTitle.'</h1>';
 //кнопка "ДОБАВИТЬ"
if($this->data->addNewBtn){
print <<<HERE
   <p><a href="/admin/{$this->data->parser['model']}/add" class="a_decoration_off_ms">
   <button type="button" class="btn btn-primary btn-lg btn-block">Добавить новое видео!
   </button></a></p>
HERE;
}
//навигация страниц
Pagination::run($this->data->pagesNav);

    //var_dump($diaryPageData[$i]); 
    foreach ($data as $key => $value){
       // echo "<h1>$i)</h1>"; //var_dump($diaryPageData[$i]);
    $id = $value['id'];
    $video_name = $value['video_name'];
    $video_comment = $stringProcess->cutText_mss(nl2br($value['video_comment']), 580); //$video_comment = str_replace("\r\n", "<br>", $video_comment);
    $file_adress = $value['file_adress'];
    $file_adress = substr($file_adress, 17);
    
    $video_category_title = $value['title'];
    $video_category_description = $value['description'];

    $videoTranslit = $hfu->hfu_gen($video_name);//для ЧПУ
    
    $video_keywords = $value['video_keywords'];
    //инфо о добавлении
    $date_add = $value['date_add'];
    $date_add = $timeProcess->dateFromTimestamp($date_add);
    $admin_info = $value['admin_info'];
    $admin_info = $stringProcess->cutAdminName_mss($admin_info);
    //инфо о редактировании
    $date_edit = $value['date_edit'];
    $date_edit = $timeProcess->dateFromTimestamp($date_edit);
    $edit_info = $value['edit_info'];
    $edit_info = $stringProcess->cutAdminName_mss($edit_info);
    
    $views = intval($value['views']);

    //считаем кол-во комментариев у записи:
    $comments->selectComments($id, $parser['table_name']);//отбираем соответствующие комментарии
    $commentsCall = $comments->selectedCommentsCount;//получаем кол-во соответсвующих комментариев
    
    //готовим к выводу изображение
    $img_path = "assets/media/images/video/$id.jpg";//путь к изображению
    //проверяем наличие файла изображения
	if (!file_exists($img_path)) {
		$img_path = 'assets/media/images/main/god_of_bibleism.jpg';//указываем путь к "заглушке"
	}
    /**
 * $max_scale = 200;//максимальный размер наибольшей стороны изображения
 *     $ava_size_massiv = $imgProcess->img_out_size_mss($img_path, $max_scale); //ресайз изображения
 *     $h_view = $ava_size_massiv[0]; //полученная высота
 *     $w_view = $ava_size_massiv[1]; 
 */

print <<<HERE
<div class="row" id="ms_one_index_block">

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <!-- name -->
        <h3>$video_name <br><small> ("<span title='$video_category_description'>$video_category_title</span>")</small></h3>
    </div><!--.name -->
    
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"> <!-- name -->
        <p><a href="/$videoTranslit/$modelName/v/$id"><img src="/$img_path" class="img-responsive img-rounded"/></a></p>
        <div class="small text-muted">
            <p>Комментариев: $commentsCall</p>
            <p>[Просмотров:$views]</p>
        </div>
    </div><!--.name -->

    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12"> <!-- name -->
        <p><big><big>$video_comment</big></big></p>
        <p class="text-right"><a href="/$videoTranslit/$modelName/v/$id" class="btn btn-primary btn-large" ><big>Узнать больше!</big> :)</a></p>
    </div><!--.name -->
</div>
HERE;
    }
//навигация страниц
Pagination::run($this->data->pagesNav);
//выводим ключевые слова (метки):
$MsSearchKWord->keyWordsPrint();
 //кнопка "ДОБАВИТЬ"
if($this->data->addNewBtn){
print <<<HERE
   <p><a href="/admin/{$this->data->parser['model']}/add" class="a_decoration_off_ms">
   <button type="button" class="btn btn-primary btn-lg btn-block">Добавить новое видео!
   </button></a></p>
HERE;
}
?>