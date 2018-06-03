<!--<div class="col-lg-1 col-md-1 hidden-sm hidden-xs"> пустой блок левый (правый - в файлах отображени¤)
</div> -->

<div class="col-lg-3 col-md-3 hidden-sm hidden-xs"> <!-- левый блок-->
    <aside>
        <!--меню с категори¤ми товаров-->
        <div class="col-lg-12 col-md-12 hidden-sm hidden-xs">
            <?php
            $menu = new NavigationWG;
            $menu->runVertMenuOne($this->data->parser['category_id'],$this->data->parser['category_key']);
            ?>
        
        <!--меню с категори¤ми товаров END -->
        
            <!--слайдер - карусель -->
            <div class="container-fluid">
                <div class="row" style="background-color:">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div id="myCarousel" class="carousel slide">
                            <!-- Carousel items -->
                            <?php
                                    $header_slide_1 = 'open_slide_1.jpg';
                                    $header_slide_2 = 'open_slide_2.jpg';
                                    $header_slide_3 = 'open_slide_3.jpg';
                                    $header_slide_4 = 'open_slide_4.jpg';
                                    $header_slide_5 = 'open_slide_5.jpg';
                                    $header_slide_6 = 'open_slide_6.jpg';
                            ?>
                            <div class="carousel-inner">
                                <div class="active item"><img src="/assets/images/header/<?php echo $header_slide_1?>" class="img-responsive" style="min-width: 100%; "/></div>
                                <div class="item"><img src="/assets/images/header/<?php echo $header_slide_2?>" class="img-responsive" style="min-width: 100%; "/></div>
                                <div class="item"><img src="/assets/images/header/<?php echo $header_slide_3?>" class="img-responsive" style="min-width: 100%; "/></div>
                                <div class="item"><img src="/assets/images/header/<?php echo $header_slide_4?>" class="img-responsive" style="min-width: 100%; "/></div>
                                <div class="item"><img src="/assets/images/header/<?php echo $header_slide_5?>" class="img-responsive" style="min-width: 100%; "/></div>
                                <div class="item"><img src="/assets/images/header/<?php echo $header_slide_6?>" class="img-responsive" style="min-width: 100%; "/></div>
                            </div>
                            <!-- Carousel nav -->
                            <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
                            <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
                        </div>
                        <script>
                        $('.carousel').carousel()
                        </script>
                    </div>
                    </div>
                </div>

            </div><!--слайдер - карусель END-->
            
<?php
 //$ProductsBanner = new ProductsBanner();
 //$ProductsBanner->bestInCategoryRun();
 ?>   
            
    </aside>
</div><!-- левый блок END-->