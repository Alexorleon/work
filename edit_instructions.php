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
		
		if($_SESSION['add_or_edit_instructions'] == 0){ // это добавление нового
		
			
		}else if($_SESSION['add_or_edit_instructions'] == 1){ // это редактирование
	
			$curent_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT); //ID инструкции
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
			}
			else if($posttype == 1)
			{ // это редактирование
			
				$_SESSION['add_or_edit_questions'] = 1;
				
				// получаем значения для задания их по умолчанию
				$instruction_id = filter_input(INPUT_GET, 'instruction_id', FILTER_SANITIZE_NUMBER_INT); //$_GET['employee_id']; // id сотрудника
				$instructiontype_cur = filter_input(INPUT_GET, 'instructiontype_cur', FILTER_SANITIZE_NUMBER_INT); //$_GET['employee_id']; // id сотрудника
				$instructiontitle_cur = filter_input(INPUT_GET, 'instructiontitle_cur', FILTER_SANITIZE_STRING); //$_GET['employee_cur']; // фамилия

				$smarty->assign("cur_instruction_id", $instruction_id);
				$smarty->assign("cur_instruction_type", $instructiontype_cur);
				$smarty->assign("cur_instructiontitle", $instructiontitle_cur);
			}
			else
			{
				die("У меня не прописано, что делать");
		}
    }

    // получаем список типов инструкций.
    $sql = "SELECT ID, TITLE FROM stat.ALLTRAININGTYPE ORDER BY ID";
    $array_alltrainingtype = $db->go_result($sql);

    $smarty->assign("error_", $error_);
	$smarty->assign("array_typeinstructions", $array_alltrainingtype);

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