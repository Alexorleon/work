<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

// проверка доступа к странице
if( !isset($_SESSION['admin_access']) || $_SESSION['admin_access'] !== TRUE)
{
    die('<script>document.location.href= "'.lhost.'/login"</script>');//если не авторизованы, то выкидываем на авторизацию
}
else
{
    $db = new db;
    $db->GetConnect();
    $error_='';
    $role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);
    $instructions_id = filter_input(INPUT_GET, 'instructions_id', FILTER_SANITIZE_NUMBER_INT); //ID инструкции
    $dir_regulations = $_SERVER['DOCUMENT_ROOT']."/storage/regulations/";
    $dir_compmodel = $_SERVER['DOCUMENT_ROOT']."/storage/compmodel/";
    $dir_video_briefings = $_SERVER['DOCUMENT_ROOT']."/storage/video_briefings/";

    $smarty->assign("role", $role);
    
    if (!empty($_POST))
    {
        $type_instruction = filter_input(INPUT_POST, 'type_instruction', FILTER_SANITIZE_NUMBER_INT); //Тип инструкции
        $text_instruction = filter_input(INPUT_POST,'text_instruction', FILTER_SANITIZE_STRING); //Текст инструкции
        $instruction_name = filter_input(INPUT_POST,'instruction_hidden_name', FILTER_SANITIZE_STRING); //Текст инструкции
        $download_file = filter_input(INPUT_POST,'download_file', FILTER_SANITIZE_STRING); //Текст инструкции

		//$exp = substr(strrchr($download_file, '.'), 0);
		
		// смотрим куда положить файл
		$dir_complex = "";
		$exp = "";
		switch ($type_instruction) {
			case 1:
				$dir_complex = $_SERVER['DOCUMENT_ROOT']."/storage/regulations/";
				$exp = ".pdf";
				break;
			case 2:
				$dir_complex = $_SERVER['DOCUMENT_ROOT']."/storage/video_briefings/";
				$exp = ".mp4";
				break;
			case 3:
				$dir_complex = $_SERVER['DOCUMENT_ROOT']."/storage/compmodel/";
				$exp = ".mp4";
				break;
		}
		
		$filename = md5(microtime() . rand(0, 9999));
		$filename = $filename.$exp;
		
		if($_SESSION['add_or_edit_instructions'] == 0){ // это добавление нового
		
			$sql = <<<SQL
				INSERT INTO stat.ALLTRAINING (TITLE, NAME, ALLTRAININGTYPEID) 
				VALUES ('$text_instruction', '$filename', '$type_instruction')
SQL;
				$db->go_query($sql);
		
		
		
		
		// загружам файл
		if (isset($_FILES['download_file']['tmp_name'])&&(isset($_FILES['download_file']['name']))) //Сохраняем пролог вопроса (если таковой есть)
		{
			if (move_uploaded_file($_FILES['download_file']['tmp_name'], "".$dir_complex.$_FILES['download_file']['name']))
			{
				chmod($dir_complex.$_FILES['download_file']['name'], 0644);
				//$ext = pathinfo($_FILES['download_file']['name'], PATHINFO_EXTENSION);

				rename($dir_complex.$_FILES['download_file']['name'], "".$dir_complex.$filename);
			}                
		}
				
		}else if($_SESSION['add_or_edit_instructions'] == 1){ // это редактирование
	
			$curent_id = filter_input(INPUT_POST, 'instruction_hidden_id', FILTER_SANITIZE_NUMBER_INT); //ID инструкции
			
			$sql = <<<SQL
				UPDATE stat.ALLTRAINING SET TITLE='$text_instruction', ALLTRAININGTYPEID='$type_instruction' 
				WHERE ALLTRAINING.ID='$curent_id'
SQL;
				$db->go_query($sql);
				
				// обновляем данные в полях
				$_GET['employee_cur'] = $employeesur; // фамилия
				$_GET['employee_name'] = $employeename; // имя
				$_GET['employee_pat'] = $employeepat; // отчество
				$_GET['employee_tabel'] = $employeetabel; // табельный
				$_GET['dolj'] = $type_doljnost; // ID должности
				$_GET['site_k'] = $type_uchastok; // ID участка
		}else{
			
			die("У меня не прописано, что делать");
		}
	}
	
    if(array_key_exists('posttype', $_GET))
    {
        $posttype = filter_input(INPUT_GET, 'posttype', FILTER_SANITIZE_NUMBER_INT);
		if($posttype == 0)
			{ // это добавление нового
			
				$_SESSION['add_or_edit_questions'] = 0;
				// чистые значения
				$smarty->assign("cur_instruction_id", 0);
				$smarty->assign("cur_instruction_type", 1);
				$smarty->assign("cur_instructiontitle", "");
				$smarty->assign("instructionname_cur", "");
			}
			else if($posttype == 1)
			{ // это редактирование
			
				$_SESSION['add_or_edit_questions'] = 1;
				
				// получаем значения для задания их по умолчанию
				$instruction_id = filter_input(INPUT_GET, 'instruction_id', FILTER_SANITIZE_NUMBER_INT); //$_GET['employee_id']; // id сотрудника
				$instructiontype_cur = filter_input(INPUT_GET, 'instructiontype_cur', FILTER_SANITIZE_NUMBER_INT); //$_GET['employee_id']; // id сотрудника
				$instructiontitle_cur = filter_input(INPUT_GET, 'instructiontitle_cur', FILTER_SANITIZE_STRING); //$_GET['employee_cur']; // фамилия
				$instructionname_cur = filter_input(INPUT_GET, 'instructionname_cur', FILTER_SANITIZE_STRING); //$_GET['employee_cur']; // фамилия

				$smarty->assign("cur_instruction_id", $instruction_id);
				$smarty->assign("cur_instruction_type", $instructiontype_cur);
				$smarty->assign("cur_instructiontitle", $instructiontitle_cur);
				$smarty->assign("instructionname_cur", $instructionname_cur);
			}
			else
			{
				die("У меня не прописано, что делать");
		}
    }

    // получаем список типов инструкций.
    $sql = "SELECT ID, TITLE FROM stat.ALLTRAININGTYPE ORDER BY ID";
    $array_alltrainingtype = $db->go_result($sql);
	
	// список соотношения инструкций и должностей
	$sql = "SELECT KOD, TEXT FROM stat.DOLJNOST WHERE DOLJNOST.PREDPR_K='$predpr_k_glob'";
    $array_doljnost = $db->go_result($sql);

    $smarty->assign("error_", $error_);
	$smarty->assign("array_typeinstructions", $array_alltrainingtype);
	$smarty->assign("array_doljnost", $array_doljnost);
	$smarty->assign("add_or_edit_instructions", $_SESSION['add_or_edit_instructions']);

    if ($_SESSION['add_or_edit_instructions'] == 1)
    {
        $smarty->assign("title", "Редактирование инструкции");
    }
    else
    {
        $smarty->assign("title", "Добавление инструкции");
    }
	
    $smarty->display("edit_instructions.tpl.html");
}

// --- ФУНКЦИИ ---

?>