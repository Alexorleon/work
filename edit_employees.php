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
		
		$employeesur = iconv("utf-8", "windows-1251", $_POST['employeesur']); // фамилия
		$employeename = iconv("utf-8", "windows-1251", $_POST['employeename']); // имя
		$employeepat = iconv("utf-8", "windows-1251", $_POST['employeepat']); // отчество
		$type_doljnost = $_POST['type_doljnost']; // должность
		$employeetabel = $_POST['employeetabel']; // табельный
		
		// определяем нужный запрос в зависимости от статуса. добавляем или редактируем
		if($_SESSION['add_or_edit_employee'] == 0){ // это добавление нового
		
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
		
		}else if($_SESSION['add_or_edit_employee'] == 1){ // это редактирование
	
			$employee_id_hidden = $_POST['employee_hidden_id'];
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
	}
	
	if(isset($_GET['posttype'])){

		if($_GET['posttype'] == 0){ // это добавление нового
		
			$_SESSION['add_or_edit_employee'] = 0;
			
			// чистые значения
			$smarty->assign("cur_employee_cur", '');
			$smarty->assign("cur_employee_name", '');
			$smarty->assign("cur_employee_pat", '');
			$smarty->assign("cur_employee_tabel", '');
			$smarty->assign("cur_dolj_kod", '');
		}else if($_GET['posttype'] == 1){ // это редактирование
	
			$_SESSION['add_or_edit_employee'] = 1;
			
			// получаем значения для задания их по умолчанию
			$employee_id = $_GET['employee_id']; // id сотрудника
			$employee_cur = $_GET['employee_cur']; // фамилия
			$employee_name = $_GET['employee_name']; // имя
			$employee_pat = $_GET['employee_pat']; // отчество
			$employee_tabel = $_GET['employee_tabel']; // табельный
			$dolj_kod = $_GET['dolj']; // ID должности
			
			// запоминаем табельный
			$_SESSION['check_employee_tabel'] = $employee_tabel;
			
			$smarty->assign("cur_employee_id", $employee_id);
			$smarty->assign("cur_employee_cur", $employee_cur);
			$smarty->assign("cur_employee_name", $employee_name);
			$smarty->assign("cur_employee_pat", $employee_pat);
			$smarty->assign("cur_employee_tabel", $employee_tabel);
			$smarty->assign("cur_dolj_kod", $dolj_kod);
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
	// получаем список всех должностей. 10 - кокс-майнинг
	$sql = <<<SQL
	SELECT KOD, TEXT FROM stat.DOLJNOST WHERE DOLJNOST.PREDPR_K='$predpr_k_glob'
SQL;
	$array_posts = $db->go_result($sql);	
	
	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_posts", $array_posts);

	// TODO: через ИФ режактирование или создание новой
	$smarty->assign("title", "Редактирование сотрудников");
	$smarty->display("edit_employees.tpl.html");

	// --- ФУНКЦИИ ---

  ?>