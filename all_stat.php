<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
// проверка доступа к странице
if( !(isset($_SESSION['admin_access']) && $_SESSION['admin_access'] === TRUE))
{
    //если не авторизованы, то выкидываем на ивторизацию
    die('<script>document.location.href= "'.lhost.'/login"</script>');
}
else
{
    $db = new db;
    $db->GetConnect();
    $error_='';

    $role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);
    
    $smarty->assign("role", $role);
    $smarty->assign("error_", $error_);
    $smarty->assign("title", "Результаты тестирования по предприятию");
    
    
    
    function GetPEResults($obj)
    {
    
    $sql = "SELECT TO_CHAR(ALLHISTORY.DATEBEGIN, 'DD.MM.YYYY HH24:MI:SS') AS DATEBEGIN,
            ALLANSWERS.PRICE AS PRICE
            FROM stat.ALLHISTORY, stat.ALLANSWERS
            WHERE (EXAMINERTYPE='1' AND DEL='N') AND ALLANSWERS.ID=ALLHISTORY.ALLANSWERSID
            ORDER BY ALLHISTORY.DATEBEGIN";
    $PEResults = $obj->go_result($sql);
    
    //var_dump($PEResults);
    return $PEResults;
    }
}