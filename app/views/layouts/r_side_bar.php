
<div class="col-lg-3 col-md-3 hidden-sm hidden-xs"> <!-- левый блок-->
    <aside>
        <!--меню с категори¤ми товаров-->
        <div class="col-lg-12 col-md-12 hidden-sm hidden-xs">
            
            <?php
            $menu = new NavigationWG;
            $menu->runGorMenuOneVertAnalog($this->data->parser['category_id'],$this->data->parser['sub_category_id'], $this->data->parser['category_key']);
            ?>
        
        </div><!--слайдер - карусель END-->
    </aside>
</div><!-- левый блок END-->

<!--<div class="col-lg-1 col-md-1 hidden-sm hidden-xs"> пустой блок правый (левый - в файлах отображения ИЛИ в l_side_bar.php)
</div> -->