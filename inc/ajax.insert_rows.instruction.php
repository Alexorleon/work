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
		
			// TODO: нужна транзакция. очень много действий!
			$sql = <<<SQL
				INSERT INTO stat.ALLTRAINING_B_TN (ALLTRAININGID, DOLJNOSTID) VALUES('$instruction_id', '$dolj_id')
SQL;
			if($db->go_query($sql)){
				
				// получаем номер последнего ID после вставки. нужен для таблицы.
				$sql_max = <<<SQL
					SELECT MAX(ID) AS "max" FROM stat.ALLTRAINING_B_TN
SQL;
				$s_res = $db->go_result_once($sql_max);
				
				$max_res = $s_res['max'];
				
				// каждому сотруднику с этой должностью добавляем этот новый документ
				$sql_sotrud = <<<SQL
					SELECT SOTRUD_K FROM stat.SOTRUD WHERE DOLJ_K='$dolj_id'
SQL;
				$s_res_sotrud = $db->go_result($sql_sotrud);
				
				for($i_count = 0; $i_count < count($s_res_sotrud); $i_count++ ){
					
					$cur_id_sotrud = $s_res_sotrud[$i_count]['SOTRUD_K'];
					
					$sql = <<<SQL
						INSERT INTO stat.ALLTRAINING_B (ALLTRAININGID, SOTRUDID, STATUS) VALUES('$instruction_id', '$cur_id_sotrud', 1)
SQL;
					$db->go_query($sql);
				}
				
				$ans = $dolj_id."_".$instruction_id."_".$max_res;
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