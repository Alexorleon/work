<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 

if ((!isset($_SESSION['sotrud_id'])) or (empty($_SESSION['sotrud_id'])))
{
	die('<script>document.location.href= "'.lhost.'/auth.php"</script>');	
}
$db = new db;//Создаем
$db->GetConnect();//ПРоверяем коннект

$smarty->assign("title", "Главная");

$smarty->display('main.tpl.html');
?>