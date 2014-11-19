<?php

	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
	$db = new db;
	$db->GetConnect();
	$error_='';
	
	$type_question = filter_input(INPUT_POST, 'typequestion', FILTER_SANITIZE_NUMBER_INT);
	
	// TODO: магические числа
	// получаем необходимые данные по типу вопроса
	switch ($type_question) {
		case 8:
			
			break;
		case 9:
			
			break;
		case 10:
			
			break;
		case 21:
			
			break;
		case 22:
			
			break;
	}
	
	die($type_question);
?>