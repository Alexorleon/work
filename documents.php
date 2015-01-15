<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if(array_key_exists('type_doc', $_GET)){
		$type_doc = filter_input(INPUT_GET, 'type_doc', FILTER_SANITIZE_NUMBER_INT); // назначим переменную сразу
		
		$temp_sotrud_id = $_SESSION['sotrud_id'];
		
		if($type_doc == 1){ // нормативные документы
                        
            $norm_doc_id = filter_input(INPUT_GET, 'norm_doc_id', FILTER_SANITIZE_NUMBER_INT);//$_GET['norm_doc_id'];
			if ($norm_doc_id){
			
				//$norm_doc_id = $_GET['norm_doc_id'];
				
				// получаем необходимый документ
				$sql = <<<SQL
				SELECT NAME FROM stat.ALLTRAINING WHERE ALLTRAINING.ID='$norm_doc_id'
SQL;
				$doc_instr = $db->go_result_once($sql);
				
				// запоминаем, что документ открывался
				$sql = <<<SQL
				UPDATE stat.ALLTRAINING_B SET STATUS='0' WHERE 
				ALLTRAINING_B.SOTRUDID='$temp_sotrud_id' AND ALLTRAINING_B.ALLTRAININGID='$norm_doc_id'
SQL;
				$db->go_query($sql);
				
				$smarty->assign("doc_instr", $doc_instr);
				//print_r($doc_instr['NAME']);
				
			}else{
				
				$sotrud_dolj = $_SESSION['sotrud_dolj'];
				
				// получаем нормативные документы
				// статус документа и в сортированном порядке
				/*$sql = <<<SQL
				SELECT ALLTRAINING.ID, ALLTRAINING.TITLE, ALLTRAINING_B.STATUS FROM stat.ALLTRAINING, stat.ALLTRAINING_B WHERE ALLTRAINING.ID IN
				(SELECT ALLTRAININGID FROM stat.ALLTRAINING_B_TN WHERE ALLTRAINING_B_TN.DOLJNOSTID='$sotrud_dolj' AND ALLTRAINING_B.ALLTRAININGID=ALLTRAINING_B_TN.ALLTRAININGID) 
				AND ALLTRAINING.ALLTRAININGTYPEID=1 ORDER BY STATUS DESC
SQL;
				$array_instr = $db->go_result($sql);*/
				
				$sql = <<<SQL
				SELECT ALLTRAINING.ID, ALLTRAINING.TITLE, ALLTRAINING_B.STATUS FROM stat.ALLTRAINING, stat.ALLTRAINING_B WHERE ALLTRAINING.ID IN
				(SELECT ALLTRAININGID FROM stat.ALLTRAINING_B_TN WHERE ALLTRAINING_B_TN.DOLJNOSTID='$sotrud_dolj' AND ALLTRAINING_B.SOTRUDID='$temp_sotrud_id') 
				AND ALLTRAINING.ALLTRAININGTYPEID=1 ORDER BY STATUS DESC
SQL;
				$array_instr = $db->go_result($sql);
				
				$smarty->assign("array_instr", $array_instr);
			}
		}elseif ($type_doc == 2){ // видеоинструктажи
            
			$video_id = filter_input(INPUT_GET, 'video_id', FILTER_SANITIZE_NUMBER_INT);
                        
			if ($video_id){
			
				//$video_id = $_GET['video_id'];
				
				// получаем необходимое видео
				$sql = <<<SQL
				SELECT NAME FROM stat.ALLTRAINING WHERE ALLTRAINING.ID='$video_id'
SQL;
				$video_instr = $db->go_result_once($sql);
				
				// запоминаем, что документ открывался
				$sql = <<<SQL
				UPDATE stat.ALLTRAINING_B SET STATUS='0' WHERE 
				ALLTRAINING_B.SOTRUDID='$temp_sotrud_id' AND ALLTRAINING_B.ALLTRAININGID='$video_id'
SQL;
				$db->go_query($sql);
				
				//print_r(iconv ('utf-8', 'windows-1251', $video_instr['NAME']));
				//die();
				$smarty->assign("video_instr", $video_instr['NAME']);//iconv ('windows-1251', 'utf-8', $video_instr['NAME']));
				
			}else{
				
				$sotrud_dolj = $_SESSION['sotrud_dolj'];
				
				// получаем видео				
				$sql = <<<SQL
				SELECT ALLTRAINING.ID, ALLTRAINING.TITLE, ALLTRAINING_B.STATUS FROM stat.ALLTRAINING, stat.ALLTRAINING_B WHERE ALLTRAINING.ID IN
				(SELECT ALLTRAININGID FROM stat.ALLTRAINING_B_TN WHERE ALLTRAINING_B_TN.DOLJNOSTID='$sotrud_dolj' AND ALLTRAINING_B.SOTRUDID='$temp_sotrud_id') 
				AND ALLTRAINING.ALLTRAININGTYPEID=2 ORDER BY STATUS DESC
SQL;
				$array_instr = $db->go_result($sql);
				
				$smarty->assign("array_instr", $array_instr);
			}
		}elseif ($type_doc == 3){ // компьютерные модели
			$comp_model_id = filter_input(INPUT_GET, 'comp_model_id', FILTER_SANITIZE_NUMBER_INT);
			if ($comp_model_id){
			
				//$comp_model_id = $_GET['comp_model_id'];
				
				// получаем необходимое видео
				$sql = <<<SQL
				SELECT NAME FROM stat.ALLTRAINING WHERE ALLTRAINING.ID='$comp_model_id'
SQL;
				$video_instr = $db->go_result_once($sql);
				
				// запоминаем, что документ открывался
				$sql = <<<SQL
				UPDATE stat.ALLTRAINING_B SET STATUS='0' WHERE 
				ALLTRAINING_B.SOTRUDID='$temp_sotrud_id' AND ALLTRAINING_B.ALLTRAININGID='$comp_model_id'
SQL;
				$db->go_query($sql);
				
				$smarty->assign("video_instr", $video_instr);
			}else{
				
				$sotrud_dolj = $_SESSION['sotrud_dolj'];
				
				// получаем компьютерные модели				
				$sql = <<<SQL
				SELECT ALLTRAINING.ID, ALLTRAINING.TITLE, ALLTRAINING_B.STATUS FROM stat.ALLTRAINING, stat.ALLTRAINING_B WHERE ALLTRAINING.ID IN
				(SELECT ALLTRAININGID FROM stat.ALLTRAINING_B_TN WHERE ALLTRAINING_B_TN.DOLJNOSTID='$sotrud_dolj' AND ALLTRAINING_B.SOTRUDID='$temp_sotrud_id') 
				AND ALLTRAINING.ALLTRAININGTYPEID=3 ORDER BY STATUS DESC
SQL;
				$array_instr = $db->go_result($sql);
				
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