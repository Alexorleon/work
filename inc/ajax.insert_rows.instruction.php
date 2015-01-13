<?php
	/*if ((!isset($_SESSION['sotrud_id'])) or (empty($_SESSION['sotrud_id'])))
	{
		die('not login');	
	}else{*/
		require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 		
		$db = new db;
		$db->GetConnect();
		$error_='';
		
	if (!empty($_POST)){
	
		//print_r($_POST);
		$dolj_id = filter_input(INPUT_POST, 'dolj_id', FILTER_SANITIZE_NUMBER_INT);//$_POST['dolj_id'];
		$instruction_id = filter_input(INPUT_POST, 'instruction_id', FILTER_SANITIZE_NUMBER_INT); //$_POST['instruction_id'];
		
		// проверяем на совпадение
		$sql = <<<SQL
			SELECT ID FROM stat.ALLTRAINING_B_TN WHERE ALLTRAININGID='$instruction_id' AND DOLJNOSTID='$dolj_id'
SQL;
		$s_res = $db->go_result_once($sql);
		
		if(empty($s_res)){
		
			$sql = <<<SQL
				INSERT INTO stat.ALLTRAINING_B_TN (ALLTRAININGID, DOLJNOSTID) VALUES('$instruction_id', '$dolj_id')
SQL;
			if($db->go_query($sql)){
				
				// получаем номер последнего ID после вставки. нужен для таблицы.
				$sql = <<<SQL
					SELECT Max(ID) AS "max" FROM stat.ALLTRAINING_B_TN
SQL;
				$s_res = $db->go_result_once($sql);
				
				$ans = $dolj_id."_".$instruction_id."_".$s_res['max'];
				die($ans);
			}else{
				$ans = "0";
				die($ans);
			}
			//$ans = ($db->go_query($sql))?"0":$dolj_id."_".$test_id;
		}else{
			$ans = "not";
			die($ans);
		}
	}
//}
?>