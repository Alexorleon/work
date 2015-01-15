<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

// проверка доступа к странице
if( !isset($_SESSION['admin_access']) || $_SESSION['admin_access'] !== TRUE)
{
    die('<script>document.location.href= "'.lhost.'/login"</script>');//если не авторизованы, то выкидываем на авторизацию
}
else
{
    $db = new db;
    $db->GetConnect();
    $error_='';
    $role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);
    $instructions_id = filter_input(INPUT_GET, 'instructions_id', FILTER_SANITIZE_NUMBER_INT); //ID инструкции
    $dir_regulations = $_SERVER['DOCUMENT_ROOT']."/storage/regulations/";
    $dir_compmodel = $_SERVER['DOCUMENT_ROOT']."/storage/compmodel/";
    $dir_video_briefings = $_SERVER['DOCUMENT_ROOT']."/storage/video_briefings/";

    $smarty->assign("role", $role);

    if (!empty($_POST))
    {
        $type_instruction = filter_input(INPUT_POST, 'type_instruction', FILTER_SANITIZE_NUMBER_INT); //Тип инструкции
        $text_instruction = filter_input(INPUT_POST,'text_instruction', FILTER_SANITIZE_STRING); //Текст инструкции
        $instruction_name = filter_input(INPUT_POST,'instruction_hidden_name', FILTER_SANITIZE_STRING); //Текст инструкции
        $download_file = filter_input(INPUT_POST,'download_file', FILTER_SANITIZE_STRING); //Имя файла
        $temp_arrayAllDolj = filter_input(INPUT_POST,'arrayAllDolj', FILTER_SANITIZE_STRING); //массив должностей и их статусов
		//$exp = substr(strrchr($download_file, '.'), 0);

		$arrayAllDolj = explode(",", $temp_arrayAllDolj);
		
		// смотрим куда положить файл
		$dir_complex = "";
		$exp = "";
		switch ($type_instruction) {
			case 1:
				$dir_complex = $_SERVER['DOCUMENT_ROOT']."/storage/regulations/";
				$exp = ".pdf";
				break;
			case 2:
				$dir_complex = $_SERVER['DOCUMENT_ROOT']."/storage/video_briefings/";
				$exp = ".mp4";
				break;
			case 3:
				$dir_complex = $_SERVER['DOCUMENT_ROOT']."/storage/compmodel/";
				$exp = ".mp4";
				break;
		}
		
		$filename = md5(microtime() . rand(0, 9999));
		$filename = $filename.$exp;
		
		if($_SESSION['add_or_edit_instructions'] == 0){ // это добавление нового
		
			// TODO: нужна транзакция
			$sql = <<<SQL
				INSERT INTO stat.ALLTRAINING (TITLE, NAME, ALLTRAININGTYPEID) 
				VALUES ('$text_instruction', '$filename', '$type_instruction')
SQL;
				$db->go_query($sql);
		
			// берем только что вставленный ID
			$sql = <<<SQL
			SELECT MAX(ID) AS "max" FROM stat.ALLTRAINING
SQL;
			$res_max_alltraining = $db->go_result_once($sql)['max'];
		
			// заполняем соотношения
			for($count_alldolj = 0; $count_alldolj < count($arrayAllDolj); $count_alldolj=($count_alldolj + 2)){
			
				$temp_idDolj = $arrayAllDolj[$count_alldolj]; // запоминаем должность

				// проверяем, есть ли уже такое соотношение
				$sql = <<<SQL
					SELECT ID FROM stat.ALLTRAINING_B_TN WHERE ALLTRAINING_B_TN.ALLTRAININGID='$res_max_alltraining' 
					AND ALLTRAINING_B_TN.DOLJNOSTID='$temp_idDolj'
SQL;
				//print_r($sql);
				//die();
				$res_ratio = $db->go_result_once($sql);
				
				$count_step = $count_alldolj + 1; // указываем на рядом стоящий статус к должности
				
				// теперь сверяем, нужно добавить или удалить соотношение
				if( !empty($res_ratio)){
				
					$result_ratio = $res_ratio['ID'];
					
					if($arrayAllDolj[$count_step] == '1'){
					
						// ничего не делаем, соотношение остается
					}else{
					
						// удаляем соотношение
						$sql = <<<SQL
							DELETE FROM stat.ALLTRAINING_B_TN WHERE ALLTRAINING_B_TN.ID='$result_ratio'
SQL;
						$db->go_query($sql);
					}
				}else{
				
					if($arrayAllDolj[$count_step] == '0'){
					
						// ничего не делаем, соотношение не нужно
					}else{
					
						// добавляем соотношение
						$sql = <<<SQL
							INSERT INTO stat.ALLTRAINING_B_TN (ALLTRAININGID, DOLJNOSTID) 
							VALUES('$res_max_alltraining', '$temp_idDolj')
SQL;
						if($db->go_query($sql)){
					
							// каждому сотруднику с этой должностью добавляем этот новый документ
							$sql_sotrud = <<<SQL
								SELECT SOTRUD_K FROM stat.SOTRUD WHERE DOLJ_K='$temp_idDolj'
SQL;
							$s_res_sotrud = $db->go_result($sql_sotrud);
							
							for($i_count = 0; $i_count < count($s_res_sotrud); $i_count++ ){
								
								$cur_id_sotrud = $s_res_sotrud[$i_count]['SOTRUD_K'];
								
								$sql = <<<SQL
									INSERT INTO stat.ALLTRAINING_B (ALLTRAININGID, SOTRUDID, STATUS) 
									VALUES('$res_max_alltraining', '$cur_id_sotrud', 1)
SQL;
								$db->go_query($sql);
							}
						}else{
							// ошибка вставки
						}
					}
				}
			}
		
			// загружам файл
			if (isset($_FILES['download_file']['tmp_name'])&&(isset($_FILES['download_file']['name']))) //Сохраняем пролог вопроса (если таковой есть)
			{
				if (move_uploaded_file($_FILES['download_file']['tmp_name'], "".$dir_complex.$_FILES['download_file']['name']))
				{
					chmod($dir_complex.$_FILES['download_file']['name'], 0644);
					//$ext = pathinfo($_FILES['download_file']['name'], PATHINFO_EXTENSION);

					rename($dir_complex.$_FILES['download_file']['name'], "".$dir_complex.$filename);
				}                
			}
				
		}else if($_SESSION['add_or_edit_instructions'] == 1){ // это редактирование
	
			$curent_id = filter_input(INPUT_POST, 'instruction_hidden_id', FILTER_SANITIZE_NUMBER_INT); //ID инструкции

			//TODO: нужна транзакция
			
			$sql = <<<SQL
				UPDATE stat.ALLTRAINING SET TITLE='$text_instruction', ALLTRAININGTYPEID='$type_instruction' 
				WHERE ALLTRAINING.ID='$curent_id'
SQL;
			$db->go_query($sql);
			
			// заполняем соотношения
			for($count_alldolj = 0; $count_alldolj < count($arrayAllDolj); $count_alldolj=($count_alldolj + 2)){
			
				$temp_idDolj = $arrayAllDolj[$count_alldolj]; // запоминаем должность

				// проверяем, есть ли уже такое соотношение
				$sql = <<<SQL
					SELECT ID FROM stat.ALLTRAINING_B_TN WHERE ALLTRAINING_B_TN.ALLTRAININGID='$curent_id' 
					AND ALLTRAINING_B_TN.DOLJNOSTID='$temp_idDolj'
SQL;
				//print_r($sql);
				//die();
				$res_ratio = $db->go_result_once($sql);
				
				$count_step = $count_alldolj + 1; // указываем на рядом стоящий статус к должности
				
				// теперь сверяем, нужно добавить или удалить соотношение
				if( !empty($res_ratio)){
				
					$result_ratio = $res_ratio['ID'];
					
					if($arrayAllDolj[$count_step] == '1'){
					
						// ничего не делаем, соотношение остается
					}else{
					
						// удаляем соотношение
						$sql = <<<SQL
							DELETE FROM stat.ALLTRAINING_B_TN WHERE ALLTRAINING_B_TN.ID='$result_ratio'
SQL;
						$db->go_query($sql);
						
						// получаем всех сотрудников с текущей должностью
						$sql_sotrud = <<<SQL
							SELECT SOTRUD_K FROM stat.SOTRUD WHERE DOLJ_K='$temp_idDolj'
SQL;
						$s_res_sotrud = $db->go_result($sql_sotrud);

						// и удаляем соотношение у сотрудников
						for($i_count = 0; $i_count < count($s_res_sotrud); $i_count++ ){
					
							$cur_id_sotrud = $s_res_sotrud[$i_count]['SOTRUD_K'];
							
							$sql = <<<SQL
								DELETE FROM stat.ALLTRAINING_B WHERE ALLTRAINING_B.SOTRUDID='$cur_id_sotrud' AND ALLTRAINING_B.ALLTRAININGID='$curent_id'
SQL;
							$db->go_query($sql);
						}
					}
				}else{
				
					if($arrayAllDolj[$count_step] == '0'){
					
						// ничего не делаем, соотношение не нужно
					}else{
					
						// добавляем соотношение
						$sql = <<<SQL
							INSERT INTO stat.ALLTRAINING_B_TN (ALLTRAININGID, DOLJNOSTID) 
							VALUES('$curent_id', '$temp_idDolj')
SQL;
						if($db->go_query($sql)){
					
							// каждому сотруднику с этой должностью добавляем этот новый документ
							$sql_sotrud = <<<SQL
								SELECT SOTRUD_K FROM stat.SOTRUD WHERE DOLJ_K='$temp_idDolj'
SQL;
							$s_res_sotrud = $db->go_result($sql_sotrud);
							
							for($i_count = 0; $i_count < count($s_res_sotrud); $i_count++ ){
								
								$cur_id_sotrud = $s_res_sotrud[$i_count]['SOTRUD_K'];
								
								$sql = <<<SQL
									INSERT INTO stat.ALLTRAINING_B (ALLTRAININGID, SOTRUDID, STATUS) 
									VALUES('$curent_id', '$cur_id_sotrud', 1)
SQL;
								$db->go_query($sql);
							}
						}else{
							// ошибка вставки
						}
					}
				}
			}
			
			// обновляем данные в полях
			$_GET['instructiontitle_cur'] = $text_instruction; // название инструкции
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
    if(array_key_exists('posttype', $_GET))
    {
        $posttype = filter_input(INPUT_GET, 'posttype', FILTER_SANITIZE_NUMBER_INT);
		if($posttype == 0){ // это добавление нового
			
			$_SESSION['add_or_edit_instructions'] = 0;
			
			// список должностей
			$sql = "SELECT KOD, TEXT FROM stat.DOLJNOST WHERE DOLJNOST.PREDPR_K='$predpr_k_glob'";
			$array_doljnost = $db->go_result($sql);
			
			// чистые значения
			$smarty->assign("cur_instruction_id", 0);
			$smarty->assign("cur_instruction_type", 1);
			$smarty->assign("cur_instructiontitle", "");
			$smarty->assign("instructionname_cur", "");
			$smarty->assign("array_doljnost", $array_doljnost);
			$smarty->assign("array_dolj_ratio", "");
		}else if($posttype == 1){ // это редактирование
		
			$_SESSION['add_or_edit_instructions'] = 1;
			
			// получаем значения для задания их по умолчанию
			//?$instruction_id = filter_input(INPUT_GET, 'instruction_id', FILTER_SANITIZE_NUMBER_INT);
			$instructiontype_cur = filter_input(INPUT_GET, 'instructiontype_cur', FILTER_SANITIZE_NUMBER_INT);
			$instructiontitle_cur = filter_input(INPUT_GET, 'instructiontitle_cur', FILTER_SANITIZE_STRING);
			$instructionname_cur = filter_input(INPUT_GET, 'instructionname_cur', FILTER_SANITIZE_STRING);

			// TODO: может возможно объеденить эти два запроса с условием
			// список должностей
			$sql = "SELECT KOD, TEXT FROM stat.DOLJNOST WHERE DOLJNOST.PREDPR_K='$predpr_k_glob'";
			$array_doljnost = $db->go_result($sql);
			
			// получаем список должностей из соотношения
			$sql_ratio = <<<SQL
				SELECT KOD FROM stat.DOLJNOST WHERE DOLJNOST.KOD 
				IN (SELECT DOLJNOSTID FROM stat.ALLTRAINING_B_TN WHERE ALLTRAININGID='$instructions_id')
SQL;
			$array_dolj_ratio = $db->go_result($sql_ratio);
			
			$smarty->assign("cur_instruction_id", $instructions_id);
			$smarty->assign("cur_instruction_type", $instructiontype_cur);
			$smarty->assign("cur_instructiontitle", $instructiontitle_cur);
			$smarty->assign("instructionname_cur", $instructionname_cur);
			$smarty->assign("array_doljnost", $array_doljnost);
			$smarty->assign("array_dolj_ratio", $array_dolj_ratio);
		}else{
		
			die("У меня не прописано, что делать");
		}
    }

    // получаем список типов инструкций.
    $sql = "SELECT ID, TITLE FROM stat.ALLTRAININGTYPE ORDER BY ID";
    $array_alltrainingtype = $db->go_result($sql);
	
    $smarty->assign("error_", $error_);
	$smarty->assign("array_typeinstructions", $array_alltrainingtype);
	$smarty->assign("add_or_edit_instructions", $_SESSION['add_or_edit_instructions']);

    if ($_SESSION['add_or_edit_instructions'] == 1)
    {
        $smarty->assign("title", "Редактирование инструкции");
    }
    else
    {
        $smarty->assign("title", "Добавление инструкции");
    }
	
    $smarty->display("edit_instructions.tpl.html");
}

// --- ФУНКЦИИ ---

?>