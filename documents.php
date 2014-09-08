<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if(isset($_GET['type_doc'])){
		$type_doc = (int)$_GET['type_doc']; // назначим переменную сразу
		
		if($_GET['type_doc'] == 1){ // нормативные документы
		
			if (isset($_GET['norm_doc_id']) && !empty($_GET['norm_doc_id'])){
				print_r("Вывести документы - ".$_GET['norm_doc_id']);
			}else{
				print_r("<a href='/show_regulations_1'>Нормативный1</a><br>");
				print_r("<a href='/show_regulations_2'>Нормативный2</a><br>");
				print_r("<a href='/show_regulations_3'>Нормативный3</a><br>");
				print_r("<a href='/show_regulations_4'>Нормативный4</a><br>");
			}
		}elseif ($_GET['type_doc'] == 2){ // видеоинструктажи
		
			if (isset($_GET['video_id']) && !empty($_GET['video_id'])){
				print_r("Вывести видео - ".$_GET['video_id']);
			}else{
				print_r("<a href='/show_video_1'>видео1</a><br>");
				print_r("<a href='/show_video_2'>видео2</a><br>");
				print_r("<a href='/show_video_3'>видео3</a><br>");
				print_r("<a href='/show_video_4'>видео4</a><br>");
			}
		}elseif ($_GET['type_doc'] == 3){ // компьютерные модели
			
			if (isset($_GET['comp_model_id']) && !empty($_GET['comp_model_id'])){
				print_r("Вывести модель - ".$_GET['comp_model_id']);
			}else{
				print_r("<a href='/show_compmodel_1'>модель1</a><br>");
				print_r("<a href='/show_compmodel_2'>модель2</a><br>");
				print_r("<a href='/show_compmodel_3'>модель3</a><br>");
				print_r("<a href='/show_compmodel_4'>модель4</a><br>");
			}
		}else{
		}
	}else{
	}
	
	$smarty->assign("error_", $error_);
	$smarty->assign("type_doc", $type_doc);

	// TODO: заголовок тоже через иф
	$smarty->assign("title", "TODO:");
	$smarty->display("documents.tpl.html");
?>