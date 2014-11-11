<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if(array_key_exists('type_doc', $_GET)){
		$type_doc = filter_input(INPUT_GET, 'type_doc', FILTER_SANITIZE_NUMBER_INT); // назначим переменную сразу
		
		if($type_doc == 1){ // нормативные документы
                        
                        $norm_doc_id = filter_input(INPUT_GET, 'norm_doc_id', FILTER_SANITIZE_NUMBER_INT);//$_GET['norm_doc_id'];
			if ($norm_doc_id){
			
				//$norm_doc_id = $_GET['norm_doc_id'];
				
				// получаем необходимый документ
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
		}elseif ($type_doc == 2){ // видеоинструктажи
                        $video_id = filter_input(INPUT_GET, 'video_id', FILTER_SANITIZE_NUMBER_INT);
                        
			if ($video_id){
			
				//$video_id = $_GET['video_id'];
				
				// получаем необходимое видео
				$sql = <<<SQL
				SELECT NAME FROM stat.ALLTRAINING WHERE ALLTRAINING.ID='$video_id'
SQL;
				$video_instr = $db->go_result_once($sql);
				
				//print_r(iconv ('utf-8', 'windows-1251', $video_instr['NAME']));
				//die();
				$smarty->assign("video_instr", iconv ('windows-1251', 'utf-8', $video_instr['NAME']));
				
			}else{
				
				$sotrud_dolj = $_SESSION['sotrud_dolj'];
				
				// получаем видео				
				$sql = <<<SQL
				SELECT ID, TITLE FROM stat.ALLTRAINING WHERE ALLTRAINING.ID IN
				(SELECT ALLTRAININGID FROM stat.ALLTRAINING_B_TN WHERE ALLTRAINING_B_TN.TESTNAMESID IN 
				(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj')) 
				AND ALLTRAINING.ALLTRAININGTYPEID=2
SQL;
				$array_instr = $db->go_result($sql);
				// TODO: сделать 2 запроса,с выборкой из _b. с Y и без.
				// !!!!! заменить Y на 0 и 1, где 1 это прочитано. Тогда можно будет просто сортировать при выборке и массив 
				// заполнится в порядке прочтения. С другой стороны, это медленне чем 2 запроса.
				
				//$sotrud_id = $_SESSION['sotrud_id'];
				
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