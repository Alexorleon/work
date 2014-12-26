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
	$_SESSION['add_or_edit_post'] = 0; // добавление
	
	if (!empty($_POST)){
	
		if(isset($_POST['edit_testparameters'])){
		
			$edit_testparameters = filter_input(INPUT_POST, 'edit_testparameters', FILTER_SANITIZE_SPECIAL_CHARS); // $_POST['status_edit_test'];

			if($edit_testparameters == "save"){
			
				// TODO: начать транзакцию

				$sql = <<<SQL
				UPDATE stat.TESTPARAMETERS SET AMOUNT=0
SQL;
				$db->go_query($sql);
				
				if(isset($_POST['arraytest_id'])){

					$arraytest_id = filter_input(INPUT_POST, 'arraytest_id', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY); // $_POST['arraytest_id'];

					$num = 0;
					foreach($arraytest_id as $key=>$value)
					{
						if(empty($value)){
						
							$num = 0;
						}else{
						
							$num = $value;
						}
						
						$sql = <<<SQL
						UPDATE stat.TESTPARAMETERS SET AMOUNT=$num WHERE TESTPARAMETERS.ID=$key
SQL;
						$db->go_query($sql);
					}
				}
			}
		}
	}
	
	if($_GET){
		
		if($_GET['del_postid']){
		
			if($_GET['del_postid'] != ''){
			
				$del_postid = $_GET['del_postid']; // id должности
				
				// удаляем должность
				$sql = <<<SQL
				DELETE FROM stat.DOLJNOST WHERE DOLJNOST.KOD='$del_postid'
SQL;
				$db->go_query($sql);
				
				//unset($_GET['del_postid']);
			}
		}
	}
	
	// получаем таблицу активности вопросов
	// TODO: модули должны располагаться в БД строго как - знания, умения, опыт, ПП
	$sql = <<<SQL
		SELECT TESTPARAMETERS.ID, TESTPARAMETERS.AMOUNT, TESTPARAMETERS.TYPEQUESTIONSID, TESTPARAMETERS.MODULEID, TYPEQUESTIONS.TITLE 
		FROM stat.TESTPARAMETERS, stat.TYPEQUESTIONS 
		WHERE TESTPARAMETERS.TYPEQUESTIONSID=TYPEQUESTIONS.ID 
		ORDER BY TESTPARAMETERS.TYPEQUESTIONSID, TESTPARAMETERS.MODULEID
SQL;
	$active_modules_questions = $db->go_result($sql);
	
	// получаем список всех должностей.
	$sql = <<<SQL
	SELECT KOD, TEXT FROM stat.DOLJNOST WHERE DOLJNOST.PREDPR_K=$predpr_k_glob
SQL;
	$array_posts = $db->go_result($sql);
	
	$role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);
    
    $smarty->assign("role", $role);
	$smarty->assign("error_", $error_);
	$smarty->assign("curPage", 1);
	$smarty->assign("array_posts", $array_posts);
	$smarty->assign("active_modules_questions", $active_modules_questions);

	$smarty->assign("title", "Список должностей");
	$smarty->display("list_posts.tpl.html");

	// --- ФУНКЦИИ ---

  ?>