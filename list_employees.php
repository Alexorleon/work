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
		
    if(!empty($_GET))
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
    }

    // получаем список всех сотрудников
    $sql = 
	"SELECT SOTRUD.SOTRUD_K, SOTRUD.SOTRUD_FAM, SOTRUD.SOTRUD_IM, SOTRUD.SOTRUD_OTCH, DOLJNOST.TEXT AS TEXT, DOLJNOST.KOD AS KOD, SOTRUD.TABEL_SPUSK, SOTRUD.WINDOW 
	FROM stat.SOTRUD, stat.DOLJNOST WHERE SOTRUD.PREDPR_K='$predpr_k_glob' AND SOTRUD.DOLJ_K=DOLJNOST.KOD";
    $array_employees = $db->go_result($sql);
    
    $role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);
    
    $smarty->assign("role", $role);
    $smarty->assign("error_", $error_);
	
    $smarty->assign("array_employees", $array_employees);
    $smarty->assign("curPage", 2);
    $smarty->assign("title", "Список сотрудников");
    $smarty->display("list_employees.tpl.html");

    // --- ФУНКЦИИ ---
    
    function GenerateQuery()
    {
        
    }
}
  ?>