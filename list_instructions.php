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
    $_SESSION['add_or_edit_instructions'] = 0; // добавление
		
    if(!empty($_GET))
    {
        if(array_key_exists('del_instructionid', $_GET))
        {
            $del_instructionid = filter_input(INPUT_GET, 'del_instructionid', FILTER_SANITIZE_NUMBER_INT);
            if($del_instructionid != '')
            {
                // удаляем инструкцию
				// TODO: с удалением пока не понятно. физически или тоже только отмечать.
                $sql = <<<SQL
                    DELETE FROM stat.ALLTRAINING WHERE ID='$del_instructionid'
SQL;
                $db->go_query($sql);
                //unset($_GET['del_employeeid']);
            }
        }
    }

    // получаем список всех инструкций
    $sql = 
	"SELECT ALLTRAINING.ID, ALLTRAINING.TITLE, ALLTRAINING.NAME AS NAME, ALLTRAININGTYPE.TITLE AS T_TYPE, ALLTRAININGTYPE.ID AS T_ID FROM stat.ALLTRAINING, stat.ALLTRAININGTYPE 
	WHERE ALLTRAINING.ALLTRAININGTYPEID=ALLTRAININGTYPE.ID";
    $array_instructions = $db->go_result($sql);
    
    $role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);
    
    $smarty->assign("role", $role);
    $smarty->assign("error_", $error_);
    $smarty->assign('curPage', 6);

    $smarty->assign("array_instructions", $array_instructions);

    $smarty->assign("title", "Инструкции");
    $smarty->display("list_instructions.tpl.html");

    // --- ФУНКЦИИ ---
}
  ?>