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
	$sql = <<<SQL
	SELECT ID, TITLE FROM stat.TYPEQUESTIONS ORDER BY ID
SQL;
	$array_typequestions = $db->go_result($sql);
	
	// модуль и риск присутствуют во всех типах
	$sql = <<<SQL
	SELECT ID, TITLE FROM stat.MODULE ORDER BY ID
SQL;
	$array_module = $db->go_result($sql);
	
	$sql = <<<SQL
	SELECT ID, TITLE FROM stat.RISKLEVEL ORDER BY ID
SQL;
	$array_risklevel = $db->go_result($sql);
	
	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_typequestions", $array_typequestions);
	$smarty->assign("array_module", $array_module);
	$smarty->assign("array_risklevel", $array_risklevel);

	// TODO: через ИФ режактирование или создание новой
	$smarty->assign("title", "Редактирование вопросов");
	$smarty->display("edit_questions.tpl.html");

	// --- ФУНКЦИИ ---

  ?>