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
			
				$video_id = $_GET['video_id'];
				
				// получаем необходимое видео
				$sql = <<<SQL
				SELECT VIDEO FROM stat.INSTRUCTIONALVIDEO WHERE INSTRUCTIONALVIDEO.ID='$video_id'
SQL;
				$video_instr = $db->go_result_once($sql);
				
				$smarty->assign("video_instr", $video_instr);
				//print_r($video_instr['VIDEO']);
				
			}else{
				
				$sotrud_dolj = $_SESSION['sotrud_dolj'];
				
				// получаем видеоинструкции				
				$sql = <<<SQL
				SELECT ID, TITLE FROM stat.INSTRUCTIONALVIDEO WHERE INSTRUCTIONALVIDEO.ID IN
				(SELECT INSTRUCTIONALVIDEOID FROM stat.INSTVID_B_TN WHERE INSTVID_B_TN.TESTNAMESID IN 
				(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj'))
SQL;
				$array_instr = $db->go_result($sql);
				
				// TODO: сделать 2 запроса,с выборкой из _b. с Y и без.
				//$sotrud_id = $_SESSION['sotrud_id'];
				
				$smarty->assign("array_instr", $array_instr);
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