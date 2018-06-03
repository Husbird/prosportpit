<?php
return
'
<br/>
<div class="alert alert-success" role="alert">
    <p><b>Поздравляем!</b></p>
    <p>Ваш новый пароль сгенерирован и отправлен на Вашу электронную почту ('.$_SESSION['userEmail'].')!</p>
    <p>Рекомендуем сохранить эти данные, а также в целях безопасности никому их не передавать!</p>
    <a href="/Login"><button type="button" class="btn btn-link">Перейти к авторизации</button></a> </p>
</div>
<br/>
'
?>
