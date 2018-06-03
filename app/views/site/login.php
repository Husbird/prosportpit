<div class="row"><!-- content row-->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--content div-->
        <div class="ms_login_form_div"><!--ms_login_form_div-->
            <h1><?php echo $this->data->title ?></h1>
                
            <br/>
                <div class="alert alert-success" role="alert">
                    <p>Добро пожаловать на страницу авторизации! :)</p>
                    <p>Если вы уже регистрировались, для входа на сайт введите свой e-mail и пароль, указанные при регистрации и нажмите "Вход".</p>
                </div>
            <br/>
            <?php echo $this->data->text?>
            
            
            
            
            <?php echo $this->data->system_massage?>
            <form method="POST" action="/" role="form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input name="email" required type="email" class="form-control" id="email" placeholder="Введите email">
                    <!-- <p class="help-block">если вы уже зарегистрированы - введите e-mail, который указывали при регистрации</p> -->
                </div>
                
                <div class="form-group">
                    <label for="pass">Пароль</label>
                    <input name="pass" required type="password" class="form-control" id="pass" placeholder="Пароль">
            
                </div>
                
                <div class="radio">
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
                </div>
                
                
                <!-- <div class="checkbox">
                    <label><input type="checkbox" name="checkbox2" value="1"> Чужой компьютер (автовыход через 2 часа)</label>
                    <label><input type="checkbox" name="checkbox72" value="1"> Запомнить меня на 72 часа (3 дня)</label>
                </div> -->
                
                
                
                
                <button name="log_in" type="submit" class="btn btn-success">Вход</button>
                <a href="/PassRestore"><button type="button" class="btn btn-link">Забыли пароль?</button></a>
                <a href="/Registration"><button type="button" class="btn btn-primary pull-right">Регистрация</button></a>
            </form>
        </div><!--.ms_login_form_div-->
    </div><!--.content div-->
</div><!-- .content row-->