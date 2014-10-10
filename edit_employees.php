<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){
		
		$employeesur = $_POST['employeesur']; // id должности
		$employeename = $_POST['employeename']; // id должности
		$employeepat = $_POST['employeepat']; // id должности
		$type_doljnost = $_POST['type_doljnost']; // id должности
		$employeetabel = $_POST['employeetabel']; // id должности
		
		// определяем нужный запрос в зависимости от статуса. добавляем или редактируем
		if($_SESSION['add_or_edit_employee'] == 0){ // это добавление нового
		
			// проверяем табельный номер
			$sql = <<<SQL
			SELECT SOTRUD_K FROM stat.SOTRUD WHERE SOTRUD.TABEL_KADR='$employeetabel'
SQL;
			$check_employees_tabel = $db->go_result_once($sql);

			if(empty($check_employees_tabel)){ // если пусто, то все гуд, такого табельного нет. добовляем.
				
				$sql = <<<SQL
				INSERT INTO stat.SOTRUD (SOTRUD_FAM, SOTRUD_IM, SOTRUD_OTCH, PREDPR_K, DOLJ_K, TABEL_KADR) 
				VALUES ('$employeesur', '$employeename', '$employeepat', 10, '$type_doljnost', '$employeetabel')
SQL;
				$db->go_query($sql);
			
			}else{ // иначе говорим что такой табельный уже есть
			
			}
		
		}else if($_SESSION['add_or_edit_employee'] == 1){ // это редактирование
	
			
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
	if(isset($_GET['posttype'])){

		if($_GET['posttype'] == 0){ // это добавление нового
		
			$_SESSION['add_or_edit_employee'] = 0;
			
			// чистые значения
			//$smarty->assign("cur_post_kod", );
			//$smarty->assign("cur_post_name", '');
		}else if($_GET['posttype'] == 1){ // это редактирование
	
			$_SESSION['add_or_edit_employee'] = 1;
			
			// получаем значения для задания их по умолчанию
			/*$post_kod = $_GET['post_kod']; // id должности
			$post_name = $_GET['post_name']; // название должности
			
			$smarty->assign("cur_post_kod", $post_kod);
			$smarty->assign("cur_post_name", $post_name);*/
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
	// получаем список всех должностей. 10 - кокс-майнинг
	$sql = <<<SQL
	SELECT KOD, TEXT FROM stat.DOLJNOST WHERE DOLJNOST.PREDPR_K=10
SQL;
	$array_posts = $db->go_result($sql);	
	
	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_posts", $array_posts);

	// TODO: через ИФ режактирование или создание новой
	$smarty->assign("title", "Редактирование сотрудников");
	$smarty->display("edit_employees.tpl.html");

	// --- ФУНКЦИИ ---

  ?>