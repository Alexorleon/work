<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if(isset($_GET['type_doc'])){
		$type_doc = (int)$_GET['type_doc']; // назначим переменную сразу
		
		if($_GET['type_doc'] == 1){ // нормативные документы
		
			if (isset($_GET['norm_doc_id']) && !empty($_GET['norm_doc_id'])){
			
				$norm_doc_id = $_GET['norm_doc_id'];
				
				// получаем необходимое видео
				$sql = <<<SQL
				SELECT NAME FROM stat.ALLTRAINING WHERE ALLTRAINING.ID='$norm_doc_id'
SQL;
				$doc_instr = $db->go_result_once($sql);
				
				$smarty->assign("doc_instr", $doc_instr);
				//print_r($doc_instr['NAME']);
				
			}else{
				
				$sotrud_dolj = $_SESSION['sotrud_dolj'];
				
				// получаем нормативные документы				
				$sql = <<<SQL
				SELECT ID, TITLE FROM stat.ALLTRAINING WHERE ALLTRAINING.ID IN
				(SELECT ALLTRAININGID FROM stat.ALLTRAINING_B_TN WHERE ALLTRAINING_B_TN.TESTNAMESID IN 
				(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj')) 
				AND ALLTRAINING.ALLTRAININGTYPEID=1
SQL;
				$array_instr = $db->go_result($sql);
				
				// TODO: сделать 2 запроса,с выборкой из _b. с Y и без.
				// !!!!! заменить Y на 0 и 1, где 1 это прочитано. Теперь можно просто сортировать при выборке и массив 
				// заполнится в порядке прочтения. С другой стороны, это медленне чем 2 запроса.
				
				//$sotrud_id = $_SESSION['sotrud_id'];
				
				$smarty->assign("array_instr", $array_instr);
			}
		}elseif ($_GET['type_doc'] == 2){ // видеоинструктажи
		
			if (isset($_GET['video_id']) && !empty($_GET['video_id'])){
			
				$video_id = $_GET['video_id'];
				
				// получаем необходимое видео
				$sql = <<<SQL
				SELECT NAME FROM stat.ALLTRAINING WHERE ALLTRAINING.ID='$video_id'
SQL;
				$video_instr = $db->go_result_once($sql);
				
				$smarty->assign("video_instr", $video_instr);
				//print_r($video_instr['NAME']);
				//die();
				
			}else{
				
				$sotrud_dolj = $_SESSION['sotrud_dolj'];
				
				// получаем нормативные документы				
				$sql = <<<SQL
				SELECT ID, TITLE FROM stat.ALLTRAINING WHERE ALLTRAINING.ID IN
				(SELECT ALLTRAININGID FROM stat.ALLTRAINING_B_TN WHERE ALLTRAINING_B_TN.TESTNAMESID IN 
				(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj')) 
				AND ALLTRAINING.ALLTRAININGTYPEID=2
SQL;
				$array_instr = $db->go_result($sql);
				
				// TODO: сделать 2 запроса,с выборкой из _b. с Y и без.
				// !!!!! заменить Y на 0 и 1, где 1 это прочитано. Теперь можно просто сортировать при выборке и массив 
				// заполнится в порядке прочтения. С другой стороны, это медленне чем 2 запроса.
				
				//$sotrud_id = $_SESSION['sotrud_id'];
				
				$smarty->assign("array_instr", $array_instr);
			}
		}elseif ($_GET['type_doc'] == 3){ // компьютерные модели
			
			if (isset($_GET['comp_model_id']) && !empty($_GET['comp_model_id'])){
			
				$comp_model_id = $_GET['comp_model_id'];
				
				// получаем необходимое видео
				$sql = <<<SQL
				SELECT NAME FROM stat.ALLTRAINING WHERE ALLTRAINING.ID='$comp_model_id'
SQL;
				$video_instr = $db->go_result_once($sql);
				
				$smarty->assign("video_instr", $video_instr);
			}else{
				
				$sotrud_dolj = $_SESSION['sotrud_dolj'];
				
				// получаем компьютерные модели				
				$sql = <<<SQL
				SELECT ID, TITLE FROM stat.ALLTRAINING WHERE ALLTRAINING.ID IN
				(SELECT ALLTRAININGID FROM stat.ALLTRAINING_B_TN WHERE ALLTRAINING_B_TN.TESTNAMESID IN 
				(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj')) 
				AND ALLTRAINING.ALLTRAININGTYPEID=3
SQL;
				$array_instr = $db->go_result($sql);
				
				// TODO: -//-
				
				$smarty->assign("array_instr", $array_instr);
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