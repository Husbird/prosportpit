<div class="row"><!-- content row-->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--content div-->
        <div class="ms_registration_form_div"><!--ms_registration_form_div-->
            <h1><?php echo $this->data->title ?></h1>
            
            <br/>
                <div class="alert alert-success" role="alert">
                    <?php echo $this->data->text?>
                </div>
                
                <?php echo $this->data->system_massage?>
            <br/>
            
            <form method="POST" action="/" enctype="multipart/form-data" role="form">
            
                <div class="form-group <?php echo $_SESSION['registration_form_data']['reg_form_status_name'] ?>">
                    <label for="name">Имя</label>
                    <input name="name" required  class="form-control" id="name" 
                    value="<?php echo $_SESSION['registration_form_data']['name'] ?>" placeholder="Введите имя">
                </div>
                
                <div class="form-group <?php echo $_SESSION['registration_form_data']['reg_form_status_patronymic'] ?>">
                    <label for="patronymic">Отчество</label>
                    <input name="patronymic" required  class="form-control" id="patronymic"
                    value="<?php echo $_SESSION['registration_form_data']['patronymic'] ?>" placeholder="Введите отчество">
                </div>
                
                <div class="form-group <?php echo $_SESSION['registration_form_data']['reg_form_status_lastname'] ?>">
                    <label for="lastname">Фамилия</label>
                    <input name="lastname" required  class="form-control" id="lastname"
                    value="<?php echo $_SESSION['registration_form_data']['lastname'] ?>"placeholder="Введите фамилию">
                </div>
                
                <div class="form-group">
                    <label for="image">Ava:</label>
                    <input type="file" name="image" class="btn btn-link"/>
                </div>
            
                <div class="form-group <?php echo $_SESSION['registration_form_data']['reg_form_status_email'] ?>">
                    <label for="email" class="control-label" >Email</label>
                    <input name="email" required type="email" class="form-control" id="email" 
                    value="<?php echo $_SESSION['registration_form_data']['email'] ?>" placeholder="Введите email">
                    <p class="help-block">введите Ваш действующий e-mail</p>
                </div>
                
                <div class="form-group <?php echo $_SESSION['registration_form_data']['reg_form_status_pass'] ?>">
                    <label for="pass">Пароль</label>
                    <input name="pass" required type="password" class="form-control" id="pass" placeholder="Пароль">
                </div>
               <!-- <div class="radio">
                    <label>
                        <input type="radio" name="optionsRadios" id="radio1" value="2" checked>
                        Чужой компьютер (автовыход через 2 часа)
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="optionsRadios" id="radio2" value="72">
                        Запомнить меня на 72 часа (3 дня)
                    </label>
                </div> -->
                <button name="registration" type="submit" class="btn btn-success">Готово</button>
                <a href="/Login"><button type="button" class="btn btn-link">Вернуться к авторизации</button></a>
            </form>
            <?php unset($_SESSION['registration_form_data']);?>
        </div><!--.ms_registration_form_div-->
    </div><!--.content div-->
</div><!-- .content row-->