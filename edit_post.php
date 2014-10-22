<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
	$db = new db;
	$db->GetConnect();
	$error_='';
	$array_test_added = Array();	
	$smarty->assign("cur_post_name", '');
	
	if ($_POST){
		
		//print_r($_POST);
		//die();
		
		$postname = $_POST['postname']; // название должности
		
		// определяем нужный запрос в зависимости от статуса. добавляем или редактируем
		if($_SESSION['add_or_edit_post'] == 0){ // это добавление нового
		
			$sql = <<<SQL
			INSERT INTO stat.DOLJNOST (TEXT, PREDPR_K) VALUES ('$postname', 10)
SQL;
			$db->go_query($sql);
		
		}else if($_SESSION['add_or_edit_post'] == 1){ // это редактирование
			
			$dolj_id = $_POST['dolj_id']; // название должности
			
			$sql = <<<SQL
				UPDATE stat.DOLJNOST SET TEXT='$postname' WHERE 
				DOLJNOST.PREDPR_K=10 AND DOLJNOST.KOD='$dolj_id'
SQL;
			$db->go_query($sql);
			
			$_GET['post_name'] = $postname;
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
	if($_GET){

		if($_GET['posttype'] == 0){ // это добавление нового
		
			$_SESSION['add_or_edit_post'] = 0;
			
			// чистые значения
			//$smarty->assign("cur_post_kod", );
			$smarty->assign("cur_post_name", '');			
		}else if($_GET['posttype'] == 1){ // это редактирование
	
			$_SESSION['add_or_edit_post'] = 1;
				
			if(isset($_GET['post_kod']) && !empty($_GET['post_kod']) && $_GET['post_kod'] && isset($_GET['post_name']) && !empty($_GET['post_name']) && $_GET['post_name']){

				// получаем значения для задания их по умолчанию
				$post_kod = $_GET['post_kod']; // id должности
				$post_name = $_GET['post_name']; // название должности
				
				// получаем список тестов к должности
				$sql = <<<SQL
				SELECT SPECIALITY_B.ID AS ID, SPECIALITY_B.TESTNAMESID AS TESTID, TESTNAMES.TITLE AS TITLE 
				FROM stat.SPECIALITY_B, stat.TESTNAMES WHERE 
				SPECIALITY_B.DOLJNOSTKOD='$post_kod' AND SPECIALITY_B.TESTNAMESID=TESTNAMES.ID
SQL;
				$array_test_added = $db->go_result($sql);
			}else{
			
				$post_kod = 0; // id должности
				$post_name = ''; // название должности
				
				// получаем список тестов к должности
				$sql = <<<SQL
				SELECT SPECIALITY_B.ID AS ID, SPECIALITY_B.TESTNAMESID AS TESTID, TESTNAMES.TITLE AS TITLE 
				FROM stat.SPECIALITY_B, stat.TESTNAMES WHERE 
				SPECIALITY_B.DOLJNOSTKOD='$post_kod' AND SPECIALITY_B.TESTNAMESID=TESTNAMES.ID
SQL;
				$array_test_added = $db->go_result($sql);
				print_r("fsdfsdfdfsadf");
			}
			
			$smarty->assign("array_test_added", $array_test_added);
			
			$smarty->assign("cur_post_kod", $post_kod);
			$smarty->assign("cur_post_name", $post_name);
		}else{
			
			die("У меня не прописано, что делать");
		}
		
		if(isset($_GET['del_testid']) && !empty($_GET['del_testid']) && $_GET['del_testid']){

			$del_testid = $_GET['del_testid']; // id теста
			
			// удаляем должность
			$sql = <<<SQL
			DELETE FROM stat.SPECIALITY_B WHERE SPECIALITY_B.ID='$del_testid'
SQL;
			$db->go_query($sql);
			
			die('<script>document.location.href= "'.lhost.'/edit_post"</script>');
			//unset($_GET['del_testid']);
		}
	}
	
	// получаем список всех тестов
	$sql = <<<SQL
	SELECT ID, TITLE FROM stat.TESTNAMES WHERE TESTNAMES.ACTIVE='Y'
SQL;
	$array_testnames = $db->go_result($sql);
	
	/*if($_SESSION['add_or_edit_post'] == 1){
		
		// получаем список тестов к должности
		$sql = <<<SQL
		SELECT SPECIALITY_B.ID AS ID, SPECIALITY_B.TESTNAMESID AS TESTID, TESTNAMES.TITLE AS TITLE 
		FROM stat.SPECIALITY_B, stat.TESTNAMES WHERE 
		SPECIALITY_B.DOLJNOSTKOD='$post_kod' AND SPECIALITY_B.TESTNAMESID=TESTNAMES.ID
SQL;
		$array_test_added = $db->go_result($sql);
		
		$smarty->assign("array_test_added", $array_test_added);
	}else{
		
	}*/
	
	$smarty->assign("error_", $error_);
	$smarty->assign("array_test_added", $array_test_added);	
	$smarty->assign("array_testnames", $array_testnames);
	
	$smarty->assign("add_or_edit_post", $_SESSION['add_or_edit_post']);

	// TODO: через ИФ режактирование или создание новой
	$smarty->assign("title", "Редактирование должности");
	$smarty->display("edit_post.tpl.html");

	// --- ФУНКЦИИ ---

  ?>