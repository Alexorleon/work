<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){
		
		$id_specialty = $_POST['type_specialty']; // id теста
		$postname = $_POST['postname']; // id должности
		
		//print_r($_POST);
		//die();
		
		// определяем нужный запрос в зависимости от статуса. добавляем или редактируем
		if($_SESSION['add_or_edit_post'] == 0){ // это добавление нового
		
			$sql = <<<SQL
			INSERT INTO stat.DOLJNOST (TEXT, PREDPR_K) VALUES ('$postname', 10)
SQL;
		$db->go_query($sql);
		
		}else if($_SESSION['add_or_edit_post'] == 1){ // это редактирование
	
			
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
	if(isset($_GET['posttype'])){

		if($_GET['posttype'] == 0){ // это добавление нового
		
			$_SESSION['add_or_edit_post'] = 0;
		}else if($_GET['posttype'] == 1){ // это редактирование
	
			$_SESSION['add_or_edit_post'] = 1;
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
	// получаем список всех специальностей
	$sql = <<<SQL
	SELECT ID, TITLE FROM stat.TESTNAMES WHERE TESTNAMES.ACTIVE='Y'
SQL;
	$array_testnames = $db->go_result($sql);
	
	
	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_testnames", $array_testnames);

	// TODO: через ИФ режактирование или создание новой
	$smarty->assign("title", "Редактирование должности");
	$smarty->display("edit_post.tpl.html");

	// --- ФУНКЦИИ ---

  ?>