<?php
//клиентское приложение
	$apikey = '';	
	include('telegramStorage.php'); //Подключаем библиотеку
	$telegram = new TelegramStorage($apikey);
	$localpath = "~/img.png";
	$answer = $telegram->getFile($localpath);
	if($answer) {
		echo $answer;
	} else {
		echo $telegram->saveFile($localpath);
	}
?>
