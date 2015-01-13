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
		$employeesur = filter_input(INPUT_POST, 'employeesur', FILTER_SANITIZE_SPECIAL_CHARS);
		//$employeesur = iconv("utf-8", "windows-1251", $employeesur); // фамилия
                
        $employeename = filter_input(INPUT_POST, 'employeename', FILTER_SANITIZE_SPECIAL_CHARS);
		//$employeename = iconv("utf-8", "windows-1251", $employeename); // имя
                
        $employeepat = filter_input(INPUT_POST, 'employeepat', FILTER_SANITIZE_SPECIAL_CHARS);
		//$employeepat = iconv("utf-8", "windows-1251", $employeepat); // отчество
                
		$type_doljnost = filter_input(INPUT_POST, 'type_doljnost', FILTER_SANITIZE_SPECIAL_CHARS);//$_POST['type_doljnost']; // должность
		
		$type_uchastok = filter_input(INPUT_POST, 'type_uchastok', FILTER_SANITIZE_SPECIAL_CHARS);//$_POST['type_uchastok']; // участок
		
        $employeetabel = filter_input(INPUT_POST, 'employeetabel', FILTER_SANITIZE_NUMBER_INT);//$_POST['employeetabel']; // табельный
		
		$type_numwindows = filter_input(INPUT_POST, 'type_numwindows', FILTER_SANITIZE_NUMBER_INT);//$_POST['type_numwindows']; // окно
		
		// определяем нужный запрос в зависимости от статуса. добавляем или редактируем
		if($_SESSION['add_or_edit_employee'] == 0){ // это добавление нового
		
			// проверяем табельный номер
			$sql = <<<SQL
			SELECT SOTRUD_K FROM stat.SOTRUD WHERE PREDPR_K='$predpr_k_glob' AND SOTRUD.TABEL_SPUSK='$employeetabel'
SQL;
			$check_employees_tabel = $db->go_result_once($sql);

			if(empty($check_employees_tabel)){ // если пусто, то такого табельного нет. добовляем.
				$error_='';//нулим ошибку,если повторно будет
				$smarty->assign("employeename", "");
				$sql = <<<SQL
				INSERT INTO stat.SOTRUD (SOTRUD_FAM, SOTRUD_IM, SOTRUD_OTCH, PREDPR_K, DOLJ_K, TABEL_SPUSK, UCHAST_K, WINDOW) 
				VALUES ('$employeesur', '$employeename', '$employeepat', '$predpr_k_glob', '$type_doljnost', '$employeetabel', '$type_uchastok', '$type_numwindows')
SQL;
				$db->go_query($sql);
			
			}else{ // иначе говорим что такой табельный уже есть
				
				//Во первых, нужно вывести ошибку, точнее текст ошибки
				$error_ = "Такой табельный уже есть!";
			}
		
		}else if($_SESSION['add_or_edit_employee'] == 1){ // это редактирование
	
			$employee_id_hidden = filter_input(INPUT_POST, 'employee_hidden_id', FILTER_SANITIZE_NUMBER_INT);//$_POST['employee_hidden_id'];
			//print_r($_POST);
			//die();
			
			// проверяем табельный номер. но если это тот же, то все норм.
			if($_SESSION['check_employee_tabel'] != $employeetabel){

				$sql = <<<SQL
				SELECT SOTRUD_K FROM stat.SOTRUD WHERE PREDPR_K='$predpr_k_glob' AND SOTRUD.TABEL_SPUSK='$employeetabel'
SQL;
				$check_employees_tabel = $db->go_result_once($sql);

				if(empty($check_employees_tabel)){ // если пусто, то такого табельного нет. добовляем.

					$sql = <<<SQL
					UPDATE stat.SOTRUD SET SOTRUD_FAM='$employeesur', SOTRUD_IM='$employeename', SOTRUD_OTCH='$employeepat', DOLJ_K='$type_doljnost', UCHAST_K='$type_uchastok', TABEL_SPUSK='$employeetabel', WINDOW='$type_numwindows' WHERE 
					SOTRUD.PREDPR_K='$predpr_k_glob' AND SOTRUD.SOTRUD_K='$employee_id_hidden'
SQL;
					$db->go_query($sql);
					
					// обновляем данные в полях
					$_GET['employee_cur'] = $employeesur; // фамилия
					$_GET['employee_name'] = $employeename; // имя
					$_GET['employee_pat'] = $employeepat; // отчество
					$_GET['employee_tabel'] = $employeetabel; // табельный
					$_GET['dolj'] = $type_doljnost; // ID должности
					$_GET['site_k'] = $type_uchastok; // ID участка
					$_GET['numwindow'] = $type_numwindows; // ID участка
					
					// запоминаем новый табельный
					$_SESSION['check_employee_tabel'] = $employeetabel;
				}else{ // иначе говорим что такой табельный уже есть
					
					//Во первых, нужно вывести ошибку, точнее текст ошибки
					$error_ = "Такой табельный уже есть!";
				}
			}else{
			
				$sql = <<<SQL
					UPDATE stat.SOTRUD SET SOTRUD_FAM='$employeesur', SOTRUD_IM='$employeename', SOTRUD_OTCH='$employeepat', DOLJ_K='$type_doljnost', UCHAST_K='$type_uchastok', TABEL_SPUSK='$employeetabel', WINDOW='$type_numwindows' WHERE 
					SOTRUD.PREDPR_K='$predpr_k_glob' AND SOTRUD.SOTRUD_K='$employee_id_hidden'
SQL;
				$db->go_query($sql);
				
				// обновляем данные в полях
				$_GET['employee_cur'] = $employeesur; // фамилия
				$_GET['employee_name'] = $employeename; // имя
				$_GET['employee_pat'] = $employeepat; // отчество
				$_GET['employee_tabel'] = $employeetabel; // табельный
				$_GET['dolj'] = $type_doljnost; // ID должности
				$_GET['numwindow'] = $type_numwindows; // ID участка
			}
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	$role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);
    
    $smarty->assign("role", $role);
	if(array_key_exists('posttype', $_GET)){
	
        $posttype = filter_input(INPUT_GET, 'posttype', FILTER_SANITIZE_NUMBER_INT);
		
		if($posttype == 0){ // это добавление нового
		
			$_SESSION['add_or_edit_employee'] = 0;
			
			// чистые значения
            $smarty->assign("date_list", array());
			$smarty->assign("cur_employee_cur", '');
			$smarty->assign("cur_employee_name", '');
			$smarty->assign("cur_employee_pat", '');
			$smarty->assign("cur_employee_tabel", '');
			$smarty->assign("cur_dolj_kod", '');
			$smarty->assign("cur_site_kod", '');
			
			$smarty->assign("count_pt", 0);
			$smarty->assign("numwindow", 1);
		}else if($posttype == 1){ // это редактирование
	
			$_SESSION['add_or_edit_employee'] = 1;
			
			// получаем значения для задания их по умолчанию
			$employee_id = filter_input(INPUT_GET, 'employee_id', FILTER_SANITIZE_NUMBER_INT); //$_GET['employee_id']; // id сотрудника
			$employee_cur = filter_input(INPUT_GET, 'employee_cur', FILTER_SANITIZE_STRING); //$_GET['employee_cur']; // фамилия
			$employee_name = filter_input(INPUT_GET, 'employee_name', FILTER_SANITIZE_STRING); //$_GET['employee_name']; // имя
			$employee_pat = filter_input(INPUT_GET, 'employee_pat', FILTER_SANITIZE_STRING); //$_GET['employee_pat']; // отчество
			$employee_tabel = filter_input(INPUT_GET, 'employee_tabel', FILTER_SANITIZE_NUMBER_INT); //$_GET['employee_tabel']; // табельный
			$dolj_kod = filter_input(INPUT_GET, 'dolj', FILTER_SANITIZE_NUMBER_INT); //$_GET['dolj']; // ID должности
			$site_kod = filter_input(INPUT_GET, 'site_k', FILTER_SANITIZE_NUMBER_INT); //$_GET['dolj']; // ID участка
			$numwindow = filter_input(INPUT_GET, 'numwindow', FILTER_SANITIZE_NUMBER_INT); //$_GET['dolj']; // ID участка

            // получаем участок сотрудника
			$sql = <<<SQL
			SELECT UCHAST_K FROM stat.SOTRUD WHERE SOTRUD.SOTRUD_K='$employee_id'
SQL;
			$site_k = $db->go_result_once($sql);
			$site_kod = $site_k['UCHAST_K'];
 
			// запоминаем табельный
			$_SESSION['check_employee_tabel'] = $employee_tabel;

			$date_list = GetTestDates($db, $employee_id);
			$count_pt = CountPT($db, $employee_id);
			//var_dump($dl);
			$smarty->assign("date_list", $date_list);
			$smarty->assign("count_pt", $count_pt);

			$smarty->assign("cur_employee_id", $employee_id);
			$smarty->assign("cur_employee_cur", $employee_cur);
			$smarty->assign("cur_employee_name", $employee_name);
			$smarty->assign("cur_employee_pat", $employee_pat);
			$smarty->assign("cur_employee_tabel", $employee_tabel);
			$smarty->assign("cur_dolj_kod", $dolj_kod);
			$smarty->assign("cur_site_kod", $site_kod);
			$smarty->assign("numwindow", $numwindow);
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
	// получаем список всех должностей.
	$sql = <<<SQL
	SELECT KOD, TEXT FROM stat.DOLJNOST WHERE DOLJNOST.PREDPR_K='$predpr_k_glob'
SQL;
	$array_posts = $db->go_result($sql);
	
	// получаем список всех участков
	$sql = <<<SQL
	SELECT UCHAST_K, UCHAST_NAIM FROM stat.UCHAST WHERE UCHAST.PREDPR_K='$predpr_k_glob'
SQL;
	$array_site = $db->go_result($sql);
	
	// текущее количество окон
	// TODO: разделение по предприятиям. пока временно через магическое число
	$sql = <<<SQL
		SELECT NUMWINDOWS FROM stat.ADMININFO WHERE ADMININFO.ID='21'
SQL;
	$value_numwindows = $db->go_result_once($sql)['NUMWINDOWS'];
	
	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_posts", $array_posts);
	$smarty->assign("array_site", $array_site);
	$smarty->assign("value_numwindows", $value_numwindows);
    $smarty->assign("curPage", 2);
	// TODO: через ИФ режактирование или создание новой
	$smarty->assign("title", "Редактирование сотрудников");
	$smarty->display("edit_employees.tpl.html");

	// --- ФУНКЦИИ ---
function GetTestDates($obj, $sid) //История сотрудника по сдаче тестов 
{
    $sql = "SELECT TO_CHAR(DATEBEGIN, 'DD.MM.YYYY HH24:MI:SS') AS DATEBEGIN FROM (SELECT DISTINCT DATEBEGIN FROM stat.ALLHISTORY WHERE SOTRUD_ID='$sid' AND EXAMINERTYPE='2' AND DEL='N') ORDER BY DATEBEGIN";
    $date_list = $obj->go_result($sql);
   
    return $date_list;
}

function CountPT($obj, $sid, $isntDel=false)
{
    $delSQL = ($isntDel) ? " AND DEL='N'" : "";
    $sql = "SELECT COUNT(ID) AS COUNT FROM stat.ALLHISTORY WHERE SOTRUD_ID='$sid' AND EXAMINERTYPE='1'$delSQL";

    $result = $obj->go_result_once($sql);
    
    return $result['COUNT'];
}
  ?>