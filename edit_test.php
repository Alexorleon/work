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
		
	if ($_POST){
		
		// определяем нужный запрос в зависимости от статуса. добавляем или редактируем
		if($_SESSION['add_or_edit_test'] == 0){ // это добавление нового
		
			$testname = $_POST['testname'];
			$testpenalty = $_POST['testpenalty'];
			
			$sql = <<<SQL
			INSERT INTO stat.TESTNAMES (TITLE, PENALTYPOINTS, ACTIVE) VALUES ('$testname', '$testpenalty', 'Y')
SQL;
			$db->go_query($sql);
		
		}else if($_SESSION['add_or_edit_test'] == 1){ // это редактирование
	
			//print_r($_POST);
			
			$sql = <<<SQL
				UPDATE stat.SOTRUD SET SOTRUD_FAM='$employeesur', SOTRUD_IM='$employeename', SOTRUD_OTCH='$employeepat', DOLJ_K='$type_doljnost', TABEL_KADR='$employeetabel' WHERE 
				SOTRUD.PREDPR_K=10 AND SOTRUD.SOTRUD_K='3809'
SQL;
			$db->go_query($sql);
			
			// обновляем данные в полях
			$_GET['employee_cur'] = $employeesur; // фамилия
			$_GET['employee_name'] = $employeename; // имя
			$_GET['employee_pat'] = $employeepat; // отчество
			$_GET['employee_tabel'] = $employeetabel; // табельный
			$_GET['dolj'] = $type_doljnost; // ID должности
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
	if(isset($_GET['testtype'])){

		if($_GET['testtype'] == 0){ // это добавление нового
		
			$_SESSION['add_or_edit_test'] = 0;
			
			// чистые значения
			$smarty->assign("cur_test_id", '');
			$smarty->assign("cur_test_name", '');
			$smarty->assign("cur_test_penalty", '');
		}else if($_GET['testtype'] == 1){ // это редактирование
	
			$_SESSION['add_or_edit_test'] = 1;
			
			// получаем значения для задания их по умолчанию
			$test_id = $_GET['test_id']; // id теста
			$test_title = $_GET['test_title']; // название
			$test_penalty = $_GET['test_penalty']; // штрафные баллы
			
			$smarty->assign("cur_test_id", '');
			$smarty->assign("cur_test_name", '');
			$smarty->assign("cur_test_penalty", '');
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