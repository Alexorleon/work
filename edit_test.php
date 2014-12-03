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
	
	if(isset($_GET['exit'])){
		if($_GET['exit'] == 'exit'){
		
			$_SESSION['add_or_edit_test'] = 1;
		}
	}

	if (!empty($_POST)){
		
		// определяем нужный запрос в зависимости от статуса. добавляем или редактируем
		if($_SESSION['add_or_edit_test'] == 0){ // это добавление нового
		
			$testname = filter_input(INPUT_POST, 'testname', FILTER_SANITIZE_SPECIAL_CHARS); //$_POST['testname'];
			$testpenalty = filter_input(INPUT_POST, 'testpenalty', FILTER_SANITIZE_NUMBER_INT); //$_POST['testpenalty'];
			
			// TODO: по хорошему тут обязательно нужна транзакция
			
			$sql = <<<SQL
			INSERT INTO stat.TESTNAMES (TITLE, PENALTYPOINTS, ACTIVE) VALUES ('$testname', '$testpenalty', 'Y')
SQL;
			$db->go_query($sql);
			
			// получаем номер последнего ID после вставки.
			$sql = <<<SQL
				SELECT Max(ID) AS "max" FROM stat.TESTNAMES
SQL;
			$s_res = $db->go_result_once($sql);
			
			$last_testid = (int)$s_res['max'];
			
			// получаем все модули
			$sql = <<<SQL
				SELECT ID FROM stat.MODULE
SQL;
			$all_modules = $db->go_result($sql);
			
			// получаем все типы вопросов
			$sql = <<<SQL
				SELECT ID FROM stat.TYPEQUESTIONS
SQL;
			$all_types_ques = $db->go_result($sql);
			
			// записываем параметры по умолчанию
			for($mod = 0; $mod < count($all_modules); $mod++){
			
				$int_all_mod = (int)$all_modules[$mod]['ID'];
				
				for($typ = 0; $typ < count($all_types_ques); $typ++){
			
					// COEFFICIENT по умолчанию равен 50. Это 50%. Т.е. распределять равномерно.
					$int_all_typ = (int)$all_types_ques[$typ]['ID'];
					$sql = <<<SQL
					INSERT INTO stat.TESTPARAMETERS (ACTIVE, COEFFICIENT, TESTNAMESID, TYPEQUESTIONSID, MODULEID) 
					VALUES ('0', 50, '$last_testid', '$int_all_typ', '$int_all_mod')
SQL;
					$db->go_query($sql);
				}
			}
			
			// TODO: после успешной записи запомнить ID. и активировать кнопку редактирования последней этой записи.
			
		}elseif($_SESSION['add_or_edit_test'] == 1){ // это редактирование

			if(isset($_POST['status_edit_test'])){
				
				$status_edit_test = filter_input(INPUT_POST, 'status_edit_test', FILTER_SANITIZE_SPECIAL_CHARS); // $_POST['status_edit_test'];

				if($status_edit_test == "save"){
				
					// TODO: начать транзакцию
					
					$cur_test_id = filter_input(INPUT_POST, 'cur_test_id', FILTER_SANITIZE_NUMBER_INT); // $_POST['cur_test_id'];
					
					$sql = <<<SQL
					UPDATE stat.TESTPARAMETERS SET ACTIVE='0' WHERE TESTPARAMETERS.TESTNAMESID='$cur_test_id'
SQL;
					$db->go_query($sql);
					
					if(isset($_POST['arraytest_id'])){

						$arraytest_id = filter_input(INPUT_POST, 'arraytest_id', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY); // $_POST['arraytest_id'];

						foreach($arraytest_id as $key=>$value)
						{
							$sql = <<<SQL
							UPDATE stat.TESTPARAMETERS SET ACTIVE='1' WHERE TESTPARAMETERS.ID=$key
SQL;
							$db->go_query($sql);
						}
					}
				}elseif($status_edit_test == "add_question"){

					$_SESSION['add_or_edit_test'] = 2;
					
				}elseif($status_edit_test == "exit"){
			
					$_SESSION['add_or_edit_test'] = 1;
		print_r("dfgdfgdfdfgdf");
				}else{
				}
			}
		}elseif($_SESSION['add_or_edit_test'] == 2){ // это добавление вопроса

		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
	if(isset($_GET['testtype'])){
		if($_GET['testtype'] == 0){ // это добавление нового
		
			$_SESSION['add_or_edit_test'] = 0;
			
			// чистые значения
			$smarty->assign("cur_test_id", '');
			$smarty->assign("cur_test_title", '');
			$smarty->assign("cur_test_penalty", '');
		}else if($_GET['testtype'] == 1){ // это редактирование
	
			// получаем значения для задания их по умолчанию
			$test_id = $_GET['test_id']; // id теста
			$test_title = $_GET['test_title']; // название
			$test_penalty = $_GET['test_penalty']; // штрафные баллы
			
			if($_SESSION['add_or_edit_test'] == 2){

				// получаем уже добавленные вопросы к тесту и исключаем их из всех вопросов
				$sql = <<<SQL
					SELECT ALLQUESTIONS.ID, ALLQUESTIONS.TEXT, MODULE.TITLE AS T_MODULE, RISKLEVEL.TITLE AS T_RISK, TYPEQUESTIONS.TITLE AS T_TYPE FROM stat.ALLQUESTIONS, stat.MODULE, stat.RISKLEVEL, stat.TYPEQUESTIONS 
					WHERE ALLQUESTIONS.MODULEID=MODULE.ID AND ALLQUESTIONS.RISKLEVELID=RISKLEVEL.ID AND ALLQUESTIONS.TYPEQUESTIONSID=TYPEQUESTIONS.ID AND 
					ALLQUESTIONS.ID NOT IN (SELECT ALLQUESTIONS.ID FROM stat.ALLQUESTIONS_B, stat.ALLQUESTIONS WHERE ALLQUESTIONS_B.ALLQUESTIONSID = ALLQUESTIONS.ID AND ALLQUESTIONS.ID IN (SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID='$test_id'))
				
SQL;
				$all_questions = $db->go_result($sql);
			
				$smarty->assign("all_questions", $all_questions);
			
			}else{
				$_SESSION['add_or_edit_test'] = 1;
			}
			
			if(array_key_exists('disabled_questionid', $_GET))
			{
				$disabled_questionid = filter_input(INPUT_GET, 'disabled_questionid', FILTER_SANITIZE_NUMBER_INT);
				if($disabled_questionid != '')
				{
					// удаляем вопрос из теста
					$sql = <<<SQL
						DELETE FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.ID='$disabled_questionid'
SQL;
					$db->go_query($sql);
					//unset($_GET['disabled_questionid']);
				}
			}
			
			// получаем таблицу активности вопросов
			// TODO: модули должны располагаться в БД строго как - знания, умения, опыт, ПП
			$sql = <<<SQL
				SELECT TESTPARAMETERS.ID, TESTPARAMETERS.ACTIVE, TESTPARAMETERS.TYPEQUESTIONSID, TESTPARAMETERS.MODULEID, TYPEQUESTIONS.TITLE 
				FROM stat.TESTPARAMETERS, stat.TYPEQUESTIONS 
				WHERE TESTPARAMETERS.TESTNAMESID='$test_id' AND TESTPARAMETERS.TYPEQUESTIONSID=TYPEQUESTIONS.ID 
				ORDER BY TESTPARAMETERS.TYPEQUESTIONSID, TESTPARAMETERS.MODULEID
SQL;
			$active_modules_questions = $db->go_result($sql);
			
			// получаем список вопросов к этому тесту
			$sql = <<<SQL
				SELECT ALLQUESTIONS_B.ID, ALLQUESTIONS.TEXT, MODULE.TITLE AS T_MODULE, RISKLEVEL.TITLE AS T_RISK, TYPEQUESTIONS.TITLE AS T_TYPE FROM stat.ALLQUESTIONS_B, stat.ALLQUESTIONS, stat.MODULE, stat.RISKLEVEL, stat.TYPEQUESTIONS 
						WHERE ALLQUESTIONS_B.TESTNAMESID='$test_id' AND ALLQUESTIONS_B.ALLQUESTIONSID = ALLQUESTIONS.ID AND ALLQUESTIONS.MODULEID=MODULE.ID AND ALLQUESTIONS.RISKLEVELID=RISKLEVEL.ID AND ALLQUESTIONS.TYPEQUESTIONSID=TYPEQUESTIONS.ID AND 
            ALLQUESTIONS.ID IN (SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID='$test_id')
SQL;
			$questions_this_test = $db->go_result($sql);

			$smarty->assign("cur_test_id", $test_id);
			$smarty->assign("cur_test_title", $test_title);
			$smarty->assign("cur_test_penalty", $test_penalty);
			
			$smarty->assign("active_modules_questions", $active_modules_questions);
			$smarty->assign("questions_this_test", $questions_this_test);
			
		}elseif($_GET['testtype'] == 2){ // добавление вопросов

			$_SESSION['add_or_edit_test'] = 2;
			
			
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
	$smarty->assign("error_", $error_);

	// редактирование или создание
	if($_SESSION['add_or_edit_test'] == 0){
	
		$smarty->assign("title", "Создание теста");
	}else{
	
		$smarty->assign("title", "Редактирование теста");
	}
	
	$smarty->assign("add_or_edit_test", $_SESSION['add_or_edit_test']);
	$smarty->display("edit_test.tpl.html");

	// --- ФУНКЦИИ ---

  ?>