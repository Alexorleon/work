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
			
		
		}else if($_SESSION['add_or_edit_test'] == 1){ // это редактирование
	
			$cur_test_id = $_POST['cur_test_id'];

			$sql = <<<SQL
				UPDATE stat.TESTPARAMETERS SET ACTIVE='0' WHERE TESTPARAMETERS.TESTNAMESID='$cur_test_id'
SQL;
			$db->go_query($sql);
			
			if(isset($_POST['arraytest_id'])){
				$arraytest_id = $_POST['arraytest_id'];
				foreach($arraytest_id as $key=>$value)
				{
					$sql = <<<SQL
					UPDATE stat.TESTPARAMETERS SET ACTIVE='1' WHERE TESTPARAMETERS.ID=$key
SQL;
					$db->go_query($sql);
				}
			}
			
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
	
			$_SESSION['add_or_edit_test'] = 1;
			
			// получаем значения для задания их по умолчанию
			$test_id = $_GET['test_id']; // id теста
			$test_title = $_GET['test_title']; // название
			$test_penalty = $_GET['test_penalty']; // штрафные баллы
			
			// получаем таблицу активности вопросов
			// TODO: модули должны располагаться в БД строго как - знания, умения, опыт, ПП
			$sql = <<<SQL
				SELECT TESTPARAMETERS.ID, TESTPARAMETERS.ACTIVE, TESTPARAMETERS.TYPEQUESTIONSID, TESTPARAMETERS.MODULEID, TYPEQUESTIONS.TITLE 
				FROM stat.TESTPARAMETERS, stat.TYPEQUESTIONS 
				WHERE TESTPARAMETERS.TESTNAMESID='$test_id' AND TESTPARAMETERS.TYPEQUESTIONSID=TYPEQUESTIONS.ID 
				ORDER BY TESTPARAMETERS.TYPEQUESTIONSID, TESTPARAMETERS.MODULEID
SQL;
			$active_modules_questions = $db->go_result($sql);

			$smarty->assign("cur_test_id", $test_id);
			$smarty->assign("cur_test_title", $test_title);
			$smarty->assign("cur_test_penalty", $test_penalty);
			
			$smarty->assign("active_modules_questions", $active_modules_questions);
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