<!-- <div class="col-lg-2 col-md-2 hidden-sm hidden-xs">
</div> --> <!-- (пустой блок левый)-->

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!-- контент (средний блок)-->

<?php
//var_dump($_SESSION['id_ms_product']);
$data = $this->data;
//echo $data->massage; //для системных сообщений
//echo $this->data->text;
//$parser = $this->data->parser; //var_dump($parser);
//$tableName = $this->data->parser['table_name'];var_dump($tableName);
//Выводим корзину покупок:
$MsBasket = new MsBasket;
$MsBasket->echoBasket();
//выводим сообщение о результате обработки заказа
$MsOrderProcess = new MsOrderProcess;
$MsOrderProcess->orderConfirmMassage();//вывод сообщения пользователю о результате обработки его заказа
?>
    
<h1><?php //echo $data->pageTitle; ?></h1>
     
<?php
$timeProcess = new MsTimeProcess;
$stringProcess = new MsStringProcess;
$imgProcess = new MsIMGProcess;

//$date_add = $timeProcess->dateFromTimestamp($data->date_add);

$img_path = "assets/media/images/production/$data->id.jpg";//путь к изображению
//проверяем наличие файла изображения
if (!file_exists($img_path)) {
	$img_path = 'assets/media/images/main/god_of_bibleism.jpg';//указываем путь к "заглушке"
}
//$ava_size_massiv = $imgProcess->img_out_size_mss($img_path, 300);
//var_dump($ava_size_massiv);die;
//$ava_size_massiv = img_out_size_mss($img_path, $max_scale); //ресайз изображения
//$h_view = $ava_size_massiv[0]; //полученная высота
//$w_view = $ava_size_massiv[1]; //полученная длинна

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
<script type="text/javascript">
jQuery( document ).ready(function() {
    $("#btn").click(
		function(){
			sendAjaxForm('order_quick_block', 'ajax_quick_order_confirm', '/action_ajax_form.php');
			return false; 
		}
	);
});
</script>


<script type="text/javascript">
//активируем маску для поля ввода номера телефона
jQuery(function($){
   $("#phone_for_quick_order").mask("+38 (999) 999-99-99");
});
</script>

    <div class="row">
        <section>
            <header>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="text-align: center; margin-bottom: 5%;"> <!-- name -->
                    <p>
                        <figure>
                            <img src="<?php echo "/assets/media/images/prod_brand/$data->brand_id.jpg";?>" class="img-responsive center-block"/>
                            <figcaption>
                                <span style="display:none;">
                                    Известный бренд <b><?php echo $data->brand_name; ?></b> представляет Вам спортивное питание
                                    производства в <?php echo $data->country; ?>
                                </span>
                            </figcaption>
                        </figure>
                    </p>
                    <h2><?php echo $data->brand_name; ?> <small>(<?php echo $data->country; ?>) представляет:</small></h2>  
                </div><!--.name -->
            
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="text-align: center;"> <!-- name -->          
                    <p>
                        <img src="<?php echo "/".$img_path;?>" class="img-responsive center-block" style="max-height: 300px;"/>
                        <figcaption>
                            <span style="display:none;">
                                Предлагаем спортивную добавку <b><?php echo $data->prod_name; ?></b> производства <?php echo $data->brand_name; ?>
                                 <?php echo $data->country; ?>
                            </span>
                        </figcaption>
                    </p>
                    <p><a href="/addToBasket/<?php echo $data->id; ?>" rel="nofollow"><button class="btn btn-primary btn-bg">Добавить в корзину</button></a></p>
                    <p><a href="#" rel="nofollow" onclick="diplay_hide('#order_quick_block');return false;"><button class="btn btn-success btn-sm">Заказать одной кнопкой</button></a></p>
                    <div id="order_quick_block" style="display: none; text-align: left;">
                        <!-- форма заказа одной кнопкой -->
                        <form method="POST" id="ajax_quick_order_confirm" action="/" role="form">
                            <div class="form-group">
                                <label for="client_name">Ваше имя:</label>
                                <input type="text" name="client_name" value="" required
                                     class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="telephone_num">Ваш номер телефона:</label>
                                <input type="text" name="telephone_num" value="" required
                                    placeholder="" id="phone_for_quick_order" class="form-control" />
                            </div>
                            <input name="product_id" type="hidden" value="<?php echo $data->id; ?>" />
                            <input type="submit" id="btn" class="btn btn-success btn-sm" value="Готово" /> 
                            <a href="#" rel="nofollow" onclick="diplay_hide('#order_quick_block');return false;">Свернуть</a>
                        </form>
                        
                    </div> <!-- order_quick_block END -->
                    
                </div><!--.name -->
                
                <div class="col-lg-4 col-md-4 col-sm-8 col-xs-8" style="text-align: left;"> <!-- name -->
                    <div class="panel panel-default">
                      <div class="panel-body">
                        <h1><?php echo $data->prod_name; ?></h1>
                        <div id="vk_like"></div><!-- vk_like -->
                            <script type="text/javascript">
                            window.onload = function () {
                             VK.init({apiId: 5680768, onlyWidgets: true});
                             VK.Widgets.Like('vk_like', {width: 500, pageTitle: '<?php echo $data->prod_name; ?>', pageDescription: '<?php echo $data->category_name; ?>', type: 'button', pageImage: 'http://psp2.prosportpit.com/assets/media/images/production/<?php echo $data->id;?>.jpg'}, <?php echo $data->id; ?>);
                            }
                        </script><!-- vk_like END-->
                      </div>
                      <div class="panel-footer">
                        <p>Категория: <?php echo $data->category_name; ?></p>
                        <p>Упаковка: <?php echo $data->v; ?></p>
                        <p><span class="ms_old_price"><?php echo $data->old_price; ?></span></p>
                        <p>Цена: <span class="ms_price"><?php echo $data->price; ?> p.</span></p>
                        <p><?php echo $data->sklad; ?></p>
                      </div>
                    </div>
                </div><!--.name -->
            </header>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top: 5%;"> <!-- name -->
                <article>
                    <p style="font-size: 17px;"><b><?php echo $data->prod_name; ?></b></p>
                    <p class="text-justify"><big><?php echo nl2br($data->txt_full);?></big></p>
                </article>
            </div><!--.name -->
            
            <div class="clearfix"></div>
        
          
        
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right" style="background-color:"> <!-- buttons -->
                <p><?php echo $editBtn;?></p>
                <p><?php echo $delBtn;?></p>
                <p><a href="javascript:history.go(-1)" mce_href="javascript:history.go(-1)">
                <img src="/assets/media/images/main/back.png" height="130" width="130"/></a></p>
            </div><!--.buttons -->
        </section>
    </div>   


<?php
//подключаем блок коммениарии:
//$gBook = new MsGbook;
//$gBook->openForm($this->data->parser['table_name'],$this->data->id);
?>

<!-- Put this div tag to the place, where the Comments block will be -->
<aside>
    <div id="vk_comments" style="margin: 0 auto; margin-top: 5%;"></div>
    <script type="text/javascript">
    VK.Widgets.Comments("vk_comments", {limit: 10, width: "665", attach: "*"});
    </script>
</aside>


</div><!-- контент (средний блок) END -->

<div class="col-lg-1 col-md-1 hidden-sm hidden-xs"><!-- (пустой блок правый)-->
</div>