<?php
use testtask\Handler;
include('handler.php');
$handler = new Handler;
$edit = $handler->getNoteData();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестовое задание</title>
    <link rel="stylesheet" href="styles/style.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Записная книжка</h1>
        </div>
        <div class="content">
            <form action="/handler.php" method="POST" class="contact-form" id="contactForm">
            <? if(!isset($_SESSION['id']) && empty($_SESSION['id'])):?>
                <div class="input-block">
                    <div class="text-part">
                        <label for="fio">ФИО
                            <span class="required-input">*</span>
                        </label>
                    </div>
                    <input type="text" name="fio" class="input" id="fio" required>
                </div>
                <div class="input-block">
                    <div class="text-part">
                        <label for="company">Компания</label>
                    </div>  
                        <input type="text" name="company" class="input" id="company">
                </div>
                <div class="input-block">
                    <div class="text-part">
                        <label for="phone">Телефон
                            <span class="required-input">*</span>
                        </label>
                    </div>
                    
                    <input type="text" name="phone" title="Формат номера: +79161234567" class="input" id="phone" required>
                </div>
                <div class="input-block">
                    <div class="text-part">
                       <label for="email">Email
                            <span class="required-input">*</span>
                        </label>  
                    </div>
                    <input type="email" name="email" class="input" id="email" required>
                </div>
                <div class="input-block">
                    <label for="date">Дата рождения</label>
                    <input type="date" name="date" class="input" id="phone">
                </div>
                <div class="input-block">
                    <div class="text-part">
                        <label for="photo">Фото</label>
                    </div>
                    <input type="file" name="photo" class="input" id="photo" title="Выберите изображение" accept="image/jpeg,image/png,image/webp">
                </div>
                <? else:?>
                    <div class="input-block">
                    <div class="text-part">
                        <label for="fio">ФИО
                            <span class="required-input">*</span>
                        </label>
                    </div>
                    <? foreach ($edit as $val): ?>
                    <input type="text" name="fio" class="input" value="<?=$val['fio']?>" id="fio" required>
                </div>
                <div class="input-block">
                    <div class="text-part">
                        <label for="company">Компания</label>
                    </div>
                    <input type="text" name="company"  value="<? echo ($val['company'] !== "null" && $val['company'] !== "") ? $val['company'] : ""?>" class="input" id="company">
                </div>
                <div class="input-block">
                    <div class="text-part">
                        <label for="phone">Телефон
                            <span class="required-input">*</span>
                        </label>
                    </div>
                    <input type="text" name="phone" value="<?=$val['phone']?>" title="Формат номера: +79161234567" class="input" id="phone" required>
                </div>
                <div class="input-block">
                    <div class="text-part">
                       <label for="email">Email
                            <span class="required-input">*</span>
                        </label>  
                    </div>
                    <input type="email" name="email" value="<?=$val['email']?>" class="input" id="email" required>
                </div>
                <div class="input-block">
                    <label for="date">Дата рождения</label>
                    <input type="date" name="date" class="input" value="<? echo ($val['birth_date'] !== "null") ? $val['birth_date'] : ""?>" id="birth_date">
                </div>
                <div class="input-block">
                    <div class="text-part">
                        <label for="photo">Фото</label>
                    </div>
                    <input type="file" name="photo" class="input" id="photo"  title="Выберите изображение" accept="image/jpeg,image/png,image/webp">
                </div>
        
                <? endforeach;?>
                <? endif;?>
                <? if(isset($_SESSION['id']) && !empty($_SESSION['id'])):?>
                        <input type="hidden" name="actionFunction" value="editNote">
                    <? else:?>
                        <input type="hidden" name="actionFunction" value="checkNote">
                    <? endif;?>
                <div class="input-block">
                    <div class="required-tip">
                        <span class="required-input">*</span>
                        <p>&nbsp; - Обязательное поле</p>  
                    </div>
                    
                    <input type="submit" class="input" value="Отправить данные">
                </div>
            </form>
        </div>
        <div class='links'>
            <? if(empty($_SESSION['id']) && !isset($_SESSION['id']) ):?>
                <a class="link" href="/notes.php" class="notes-link">Перейти к записям</a>
            <? else:?>
                <a class="link" href="/note.php?id=<?=$_SESSION['id']?>" class="notes-link">Вернуться к записи № <?=$_SESSION['id']?></a>
            <? endif;?>
        </div>
    </div>

    <script src="/scripts/script.js"></script>
</body>

</html>