<?php
$data = $this->data;
echo $data->massage; //для системных сообщений
//echo $this->data->text;
//$parser = $this->data->parser; //var_dump($parser);
//$tableName = $this->data->parser['table_name'];var_dump($tableName);
?>
    
<h1><?php echo $data->pageTitle; ?></h1>
        
<?php
$timeProcess = new MsTimeProcess;
$stringProcess = new MsStringProcess;
    //кнопка редактирования
if($this->data->editBtn){
    $editBtn = '<a href="/admin/'.$this->data->parser['model'].'/e/'.$this->data->id.'" title="Редактировать данные">
                <button type="button" class="btn btn-primary btn-sm">Редактировать</button></a>';
}
//кнопка удаления
if($this->data->delBtn){
    //пишем в массив пути файлов которые необходимо удалить (полные пути включая имя и расширение файла!!!)
    $file_path = array(
        "0" => "assets/media/images/{$this->data->parser['table_name']}/{$this->data->id}.jpg"
    ,);
    $delBtn = MSS::app()->delButtonRun($this->data->id, $this->data->parser['table_name'], $file_path, $dir_path);
}
?>
<div class="row">
    <?php
        //выводим видео
        new MsVLinkParser($data->file_adress, true);
    ?>
    
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <!-- name -->
        <p><big><big><?php echo nl2br($data->video_comment); //$data->video_comment;?></big></big></p>
    </div><!--.name -->
    
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"> <!-- name -->    
        <div class="text-muted">
            <p>Запись добавлена: <?php echo $timeProcess->dateFromTimestamp($data->date_add);?></p>
            <p>Добавил: <?php echo $stringProcess->cutAdminName_mss($data->admin_info);?></p>
            <p>Ключевые слова: <?php echo $data->video_keywords;?></p>
            <p>[Просмотров: <?php echo $data->views;?>]</p>
            <p>Последнее редактирование: <?php echo $timeProcess->dateFromTimestamp($data->date_edit);?></p>
            <p>Редактировал: <?php echo $stringProcess->cutAdminName_mss($data->edit_info);?></p>
        </div>
    </div>
    
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 text-right"> <!-- name -->    
        <p><?php echo $editBtn;?></p>
        <p><?php echo $delBtn;?></p>
        <p class="text-right"><a href="javascript:history.go(-1)" mce_href="javascript:history.go(-1)">
        <img src="/assets/media/images/main/back.png" height="130" width="130"/></a></p>
    </div>
</div>
<?php
//подключаем блок коммениарии:
$gBook = new MsGbook;
$gBook->openForm($this->data->parser['table_name'],$this->data->id);
?>