<?php

use notebook\Handler;

include('handler.php');
$handler = new Handler;
$notes = $handler->showNotes();
session_start();    
unset($_SESSION['id']);
?> 
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <div>
        <div class="header flex-block">
            <h1>Записи</h1>
            <a class="link to-main" href="/">Создать новую запись</a>
        </div>
        <div class="content">
    
        <? if(empty($notes)): ?>
           <p class="empty-notes">Записей нет</p>
        <? else: ?>
            <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">ФИО</th>
                <th scope="col">Компания</th>
                <th scope="col">Телефон</th>
                <th scope="col">Email</th>
                <th scope="col">Дата рождения</th>
                <th scope="col">Фото</th>
                <th scope="col">Дата создания</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <? foreach ($notes as $note): ?>
                    <tr>
                        <td data-label='#'><?=$note['id']?></td>
                        <td data-label='ФИО'><?=$note['fio']?></td>
                        <td data-label='Компания'><?=$note['company']?></td>
                        <td data-label='Телефон'><?=$note['phone']?></td>
                        <td data-label='Email'><?=$note['email']?></td>
                        <td data-label='Дата рождения'><?=$note['birth_date']?></td>
                        <td data-label='Фото'><?=$note['photo']?></td>                    
                        <td data-lable='Дата создания'><?=$note['created_at']?></td>
                        <td><a class='note-link' href="/note.php?id=<?=$note['id']?>">Перейти к записи</a></td>
                    </tr>

            <? endforeach; ?>
            <? endif;?>
            </tbody>
        </table>
        
        </div>
    </div>
</body>
</html>
