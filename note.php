<?php 
use testtask\Handler;

include('handler.php');
$handler = new Handler;
$noteData = $handler->showNote();
session_start();
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
    <div class="container">
        <a class="link" href="/notes.php">Вернуться ко всем записям</a>
        <div class="header">
            <h1>Запись № <?=$_GET['id']?></h1>
        </div>
    
        <div class="content">
        <? foreach ($noteData as $note): ?>
                    <div class="note-block">
                        <p><b>ФИО:</b> <?=$note['fio']?></p>
                        <? echo ($note['company'] !== 'null' && trim($note['company'] !== "")) ? '<p><b>Компания:</b> '.$note['company'].'</p>' : '' ?> 
                        <p><b>Телефон:</b> <?=$note['phone']?></p>
                        <p><b>Email:</b> <?=$note['email']?></p>
                        <? echo ($note['birth_date'] !== 'null') ? '<p><b>Дата рождения:</b> '.$note['birth_date'].'</p>' : '' ?>              
                        <p><b>Дата создания: </b> <?=$note['created_at']?></p>
                        <? echo ($note['photo'] !== 'null') ? '<img class="photo" src="content/images/'.$note['photo'].'"' : '' ?>  
                        <div class='links'>
                            <a class="link" href="/">Редактировать запись</a>
                            <a class="link" href="/notes.php?id=<?=$note['id']?>&email=<?=$note['email']?>">Удалить запись</a>
                            <? $_SESSION['id'] = $note['id'];?>
                            <? $_SESSION['photo'] = $note['photo'];?>
                        </div>
                    </div>
            <? endforeach; ?>
        </div>
</body>
</html>
