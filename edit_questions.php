<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
	// проверка доступа к странице
	if( isset($_SESSION['admin_access']) && $_SESSION['admin_access'] === TRUE){
	}else{
		//если не авторизованы, то выкидываем на ивторизацию
		die('<script>document.location.href= "'.lhost.'/login"</script>');
	}
	
	$db = new db;
	$db->GetConnect();
	$error_='';
	$question_id = filter_input(INPUT_GET, 'question_id', FILTER_SANITIZE_NUMBER_INT); //ID вопроса
        
        $question_data = array();
        if ($question_id)
        {
            $sql = "SELECT
                    ALLQUESTIONS.TYPEQUESTIONSID AS TYPE, ALLQUESTIONS.MODULEID AS MID, ALLQUESTIONS.RISKLEVELID AS RISK, ALLQUESTIONS.TEXT AS TEXT,
                    ALLQUESTIONS.SIMPLEPHOTO AS PHOTO, ALLQUESTIONS.SIMPLEVIDEO AS VIDEO,
                    ALLQUESTIONS_B.TESTNAMESID AS TEST
                    FROM ALLQUESTIONS, ALLQUESTIONS_B
                    WHERE ALLQUESTIONS.ID=$question_id AND ALLQUESTIONS_B.ALLQUESTIONSID=ALLQUESTIONS.ID";
            $q_res = $db->go_result_once($sql);
            $sql = "SELECT ID, TEXT, PRICE FROM stat.ALLANSWERS WHERE ALLQUESTIONSID='$question_id'";
            $a_res = $db->go_result($sql);
            $question_data['id'] = $question_id;
            $question_data['type'] = $q_res['TYPE'];
            $question_data['module'] = $q_res['MID'];
            $question_data['risk'] = $q_res['RISK'];
            $question_data['test'] = $q_res['TEST'];
            $question_data['text'] = $q_res['TEXT'];
            $question_data['photo'] = $q_res['PHOTO'];
            $question_data['video'] = $q_res['VIDEO'];
            $question_data['answers'] = $a_res;   
        }
        else
        {
            $question_data['id'] = 0;
            $question_data['type'] = 0;
            $question_data['module'] = 0;
            $question_data['risk'] = 0;
            $question_data['test'] = 0;
            $question_data['text'] = '';
            $question_data['photo'] = '';
            $question_data['video'] = '';
            $question_data['answers'] = array();
            $question_data['answers'][0] = array();
            $question_data['answers'][0]['ID'] = '';
            $question_data['answers'][0]['TEXT'] = '';
            $question_data['answers'][0]['PRICE'] = '';
            $question_data['answers'][1] = array();
            $question_data['answers'][1]['ID'] = '';
            $question_data['answers'][1]['TEXT'] = '';
            $question_data['answers'][1]['PRICE'] = '';
            $question_data['answers'][2] = array();
            $question_data['answers'][2]['ID'] = '';
            $question_data['answers'][2]['TEXT'] = '';
            $question_data['answers'][2]['PRICE'] = '';
        }
        
        if (!empty($_POST))
        {
            $current_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $type_question = filter_input(INPUT_POST, 'type_question', FILTER_SANITIZE_NUMBER_INT);
            $module_question = filter_input(INPUT_POST,'module_question', FILTER_SANITIZE_NUMBER_INT);
            $risklevel_question = filter_input(INPUT_POST,'risklevel_question', FILTER_SANITIZE_NUMBER_INT);
            $testname_question = filter_input(INPUT_POST,'type_question', FILTER_SANITIZE_NUMBER_INT);
            
            $text_question = filter_input(INPUT_POST,'text_question', FILTER_SANITIZE_STRING);
            $text_question = iconv(mb_detect_encoding($text_question), "windows-1251", $text_question);
            
            $id_answer = filter_input(INPUT_POST, 'id_answer', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
            $text_answer = filter_input(INPUT_POST, 'text_answer', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
            $answer_price = filter_input(INPUT_POST, 'answer_price', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
            
            if ($current_id)
            {
                $sql_question = "UPDATE stat.ALLQUESTIONS SET
                        TEXT='$text_question',
                        TYPEQUESTIONSID='$type_question',
                        MODULEID='$module_question',
                        RISKLEVELID='$risklevel_question'
                        WHERE ID='$current_id'";
                $db->go_query($sql_question);
            }
            else
            {
                $sql_question = "INSERT INTO stat.ALLQUESTIONS
                                (TEXT, TYPEQUESTIONSID, MODULEID, RISKLEVELID)
                                VALUES ('$text_question', '$type_question', '$module_question', '$risklevel_question')
                                  returning ID into :mylastid";
                $stmt = OCIParse($c, $sql_question);
                oci_bind_by_name($stmt, "mylastid", $current_id, 32, SQLT_INT);
                OCIExecute($stmt);
            }
            
            for ($ans_iter = 0; $ans_iter<3; $ans_iter++)
            {
                $competencelevel_id = GetCompetenceLevelID($db, $answer_price);
                $text_answer[$ans_iter] = iconv(mb_detect_encoding($text_answer[$ans_iter]), "windows-1251", $text_answer[$ans_iter]);
                if ($id_answer[$ans_iter]!=0)
                {
                    $sql_answer = "UPDATE stat.ALLANSWERS SET
                        TEXT ='{$text_answer[$ans_iter]}',
                        ALLQUESTIONSID='$current_id',
                        COMPETENCELEVEL";
                }
            }
        }
	/*if (!empty($_POST)){
		$employeesur = filter_input(INPUT_POST, 'employeesur', FILTER_SANITIZE_SPECIAL_CHARS);
		$employeesur = iconv("utf-8", "windows-1251", $employeesur); // фамилия
                
        $employeename = filter_input(INPUT_POST, 'employeename', FILTER_SANITIZE_SPECIAL_CHARS);
		$employeename = iconv("utf-8", "windows-1251", $employeename); // имя
                
        $employeepat = filter_input(INPUT_POST, 'employeepat', FILTER_SANITIZE_SPECIAL_CHARS);
		$employeepat = iconv("utf-8", "windows-1251", $employeepat); // отчество
                
		$type_doljnost = filter_input(INPUT_POST, 'type_doljnost', FILTER_SANITIZE_SPECIAL_CHARS);//$_POST['type_doljnost']; // должность
		
        $employeetabel = filter_input(INPUT_POST, 'employeetabel', FILTER_SANITIZE_NUMBER_INT);//$_POST['employeetabel']; // табельный
		
		// определяем нужный запрос в зависимости от статуса. добавляем или редактируем
		if($_SESSION['add_or_edit_questions'] == 0){ // это добавление нового
		
			// проверяем табельный номер
			$sql = <<<SQL
			SELECT SOTRUD_K FROM stat.SOTRUD WHERE PREDPR_K='$predpr_k_glob' AND SOTRUD.TABEL_KADR='$employeetabel'
SQL;
			$check_employees_tabel = $db->go_result_once($sql);

			if(empty($check_employees_tabel)){ // если пусто, то такого табельного нет. добовляем.
				$error_='';//нулим ошибку,если повторно будет
				$smarty->assign("employeename", "");
				$sql = <<<SQL
				INSERT INTO stat.SOTRUD (SOTRUD_FAM, SOTRUD_IM, SOTRUD_OTCH, PREDPR_K, DOLJ_K, TABEL_KADR) 
				VALUES ('$employeesur', '$employeename', '$employeepat', '$predpr_k_glob', '$type_doljnost', '$employeetabel')
SQL;
				$db->go_query($sql);
			
			}else{ // иначе говорим что такой табельный уже есть
				
				//Во первых, нужно вывести ошибку, точнее текст ошибки
				$error_ = "Такой табельный уже есть!";
			}
		
		}else if($_SESSION['add_or_edit_questions'] == 1){ // это редактирование
	
			$employee_id_hidden = filter_input(INPUT_POST, 'employee_hidden_id', FILTER_SANITIZE_NUMBER_INT);//$_POST['employee_hidden_id'];
			//print_r($_POST);
			//die();
			
			// проверяем табельный номер. но если это тот же, то все норм.
			if($_SESSION['check_employee_tabel'] != $employeetabel){

				$sql = <<<SQL
				SELECT SOTRUD_K FROM stat.SOTRUD WHERE PREDPR_K='$predpr_k_glob' AND SOTRUD.TABEL_KADR='$employeetabel'
SQL;
				$check_employees_tabel = $db->go_result_once($sql);

				if(empty($check_employees_tabel)){ // если пусто, то такого табельного нет. добовляем.

					$sql = <<<SQL
					UPDATE stat.SOTRUD SET SOTRUD_FAM='$employeesur', SOTRUD_IM='$employeename', SOTRUD_OTCH='$employeepat', DOLJ_K='$type_doljnost', TABEL_KADR='$employeetabel' WHERE 
					SOTRUD.PREDPR_K='$predpr_k_glob' AND SOTRUD.SOTRUD_K='$employee_id_hidden'
SQL;
					$db->go_query($sql);
					
					// обновляем данные в полях
					$_GET['employee_cur'] = $employeesur; // фамилия
					$_GET['employee_name'] = $employeename; // имя
					$_GET['employee_pat'] = $employeepat; // отчество
					$_GET['employee_tabel'] = $employeetabel; // табельный
					$_GET['dolj'] = $type_doljnost; // ID должности
					
					// запоминаем новый табельный
					$_SESSION['check_employee_tabel'] = $employeetabel;
				}else{ // иначе говорим что такой табельный уже есть
					
					//Во первых, нужно вывести ошибку, точнее текст ошибки
					$error_ = "Такой табельный уже есть!";
				}
			}else{
			
				$sql = <<<SQL
					UPDATE stat.SOTRUD SET SOTRUD_FAM='$employeesur', SOTRUD_IM='$employeename', SOTRUD_OTCH='$employeepat', DOLJ_K='$type_doljnost', TABEL_KADR='$employeetabel' WHERE 
					SOTRUD.PREDPR_K='$predpr_k_glob' AND SOTRUD.SOTRUD_K='$employee_id_hidden'
SQL;
				$db->go_query($sql);
				
				// обновляем данные в полях
				$_GET['employee_cur'] = $employeesur; // фамилия
				$_GET['employee_name'] = $employeename; // имя
				$_GET['employee_pat'] = $employeepat; // отчество
				$_GET['employee_tabel'] = $employeetabel; // табельный
				$_GET['dolj'] = $type_doljnost; // ID должности
			}
		}else{
			
			die("У меня не прописано, что делать");
		}
	}*/
	$role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);
    
	$smarty->assign("role", $role);
	if(array_key_exists('posttype', $_GET)){
	
		$posttype = filter_input(INPUT_GET, 'posttype', FILTER_SANITIZE_NUMBER_INT);
		if($posttype == 0){ // это добавление нового
		
			$_SESSION['add_or_edit_questions'] = 0;
			
			// чистые значения
			$smarty->assign("text_question", '');
			
		}else if($posttype == 1){ // это редактирование
	
			$_SESSION['add_or_edit_questions'] = 1;
			
			// получаем значения для задания их по умолчанию
			//$employee_id = filter_input(INPUT_GET, 'employee_id', FILTER_SANITIZE_NUMBER_INT); //$_GET['employee_id']; // id сотрудника

			$smarty->assign("text_question", "-TEST-");
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
	// получаем список типов вопросов. в зависимости от выбора, свои настройки составления вопроса.
	$sql = "SELECT ID, TITLE FROM stat.TYPEQUESTIONS ORDER BY ID";
	$array_typequestions = $db->go_result($sql);
	
	// модуль и риск присутствуют во всех типах
	$sql = "SELECT ID, TITLE FROM stat.MODULE ORDER BY ID";
	$array_module = $db->go_result($sql);
	
	$sql ="SELECT ID, TITLE FROM stat.RISKLEVEL ORDER BY ID";
	$array_risklevel = $db->go_result($sql);
	
        $sql ="SELECT ID,TITLE FROM stat.TESTNAMES";
        $array_testnames = $db->go_result($sql);
        
	$smarty->assign("error_", $error_);
	
        $smarty->assign("question_data", $question_data);
	$smarty->assign("array_typequestions", $array_typequestions);
	$smarty->assign("array_module", $array_module);
	$smarty->assign("array_risklevel", $array_risklevel);
        $smarty->assign("array_testnames", $array_testnames);
	// TODO: через ИФ режактирование или создание новой
	$smarty->assign("title", "Редактирование вопросов");
	$smarty->display("edit_questions.tpl.html");

	// --- ФУНКЦИИ ---
function GetCompetenceLevelID($obj, $level_num)
{
    $sql_competencelevel = <<<SQL
                   SELECT ID, PENALTYPOINTS_MIN FROM stat.COMPETENCELEVEL ORDER BY PENALTYPOINTS_MAX
SQL;
    $competencelevel = $obj->go_result($sql_competencelevel);
    $result = '';
    foreach ($competencelevel as $cl)
    {
        if ($cl['PENALTYPOINTS_MIN']<=$level_num)
        {
           $result = $cl['ID']; 
        }
    }
    return  $result;
}
  ?>