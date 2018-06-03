<div class="clearfix"></div>
<div class="col-lg-2 col-md-2 col-sm-1 col-xs-1"><!-- (пустой блок левый)-->
<?php
 $ProductsBanner = new ProductsBanner();
 $ProductsBanner->bestInCategoryRun();
 ?>   
</div>

<div class="col-lg-8 col-md-8 col-sm-10 col-xs-10"><!-- контент (средний блок)-->
 
    <h1><?php echo $this->data->title ?></h1>
        <!--<p class="col-sm-4"><b>Содержимое View:</b></p>-->

            <?php
                echo $this->data->massage;
                
                echo nl2br($this->data->text);

            //Блок новостей сайта
            //echo "<h3>Новое на сайте:</h3>";
            //$x = new MsGetLastContent();
            //$x->printLastContent();
           // echo $this->print_content;                
                //подключаем вывод протокола работы приложения (если включен режим отладки)
                //include_once('framework/components/massages/sysLog.php');
            ?>
        
        <!-- Put this div tag to the place, where the Comments block will be -->
        <aside>
            <div id="vk_comments" style="margin: 0 auto; margin-top: 5%;"></div>
                <script type="text/javascript">
                VK.Widgets.Comments("vk_comments", {limit: 10, width: "665", attach: "*"});
                </script>
        </aside>

</div><!-- контент (средний блок) END -->

<div class="col-lg-2 col-md-2 col-sm-1 col-xs-1"><!-- (пустой блок правый)-->
</div>