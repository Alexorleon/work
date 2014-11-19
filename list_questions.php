<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
// проверка доступа к странице
if(!isset($_SESSION['admin_access']) || $_SESSION['admin_access'] === FALSE)
{
    //если не авторизованы, то выкидываем на ивторизацию
    die('<script>document.location.href= "'.lhost.'/login"</script>');
}
else
{
    $db = new db;
    $db->GetConnect();
    $error_='';

    // инициализация
    $_SESSION['add_or_edit_employee'] = 0; // добавление
		
    /*if(!empty($_GET))
    {
        if(array_key_exists('del_employeeid', $_GET))
        {
            $del_employeeid = filter_input(INPUT_GET, 'del_employeeid', FILTER_SANITIZE_NUMBER_INT);
            if($del_employeeid != '')
            {
                // удаляем сотрудника
                $sql = <<<SQL
                    DELETE FROM stat.SOTRUD WHERE SOTRUD.SOTRUD_K='$del_employeeid'
SQL;
                $db->go_query($sql);
                //unset($_GET['del_employeeid']);
            }
        }
    }*/

    // получаем список всех вопросов
    $sql = 
	"SELECT ALLQUESTIONS.ID, ALLQUESTIONS.TEXT, MODULE.TITLE AS T_MODULE, RISKLEVEL.TITLE AS T_RISK, TYPEQUESTIONS.TITLE AS T_TYPE FROM stat.ALLQUESTIONS, stat.MODULE, stat.RISKLEVEL, stat.TYPEQUESTIONS 
	WHERE ALLQUESTIONS.MODULEID=MODULE.ID AND ALLQUESTIONS.RISKLEVELID=RISKLEVEL.ID AND ALLQUESTIONS.TYPEQUESTIONSID=TYPEQUESTIONS.ID";
    $array_questions = $db->go_result($sql);
	//print_r($array_questions);
	//die();
    
    $role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);
    
    $smarty->assign("role", $role);
    $smarty->assign("error_", $error_);
	
    $smarty->assign("array_questions", $array_questions);

    $smarty->assign("title", "Список вопросов");
    $smarty->display("list_questions.tpl.html");

    // --- ФУНКЦИИ ---
    
    function GenerateQuery()
    {
        
    }
}
  ?>