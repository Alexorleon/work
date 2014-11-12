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
	
	// инициализация
	$_SESSION['add_or_edit_test'] = 0; // добавление
	
	if($_GET){
		
		if($_GET['del_testid']){
		
			if($_GET['del_testid'] != ''){
			
				$del_testid = $_GET['del_testid']; // id теста
				
				// удаляем тест
				$sql = <<<SQL
				DELETE FROM stat.TESTNAMES WHERE TESTNAMES.ID='$del_testid'
SQL;
				$db->go_query($sql);
				
				//unset($_GET['del_testid']);
			}
		}
	}
	
	// получаем список всех тестов
	$sql = <<<SQL
	SELECT ID, TITLE, PENALTYPOINTS FROM stat.TESTNAMES
SQL;
	$array_tests = $db->go_result($sql);
	
        $role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);
    
    $smarty->assign("role", $role);
	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_tests", $array_tests);

	$smarty->assign("title", "Список тестов");
	$smarty->display("list_tests.tpl.html");

	// --- ФУНКЦИИ ---

  ?>