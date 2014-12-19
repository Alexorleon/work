<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
$db = new db;
$db->GetConnect();
$error_='';

$current = filter_input(INPUT_POST, 'current', FILTER_SANITIZE_NUMBER_INT);
$weekago = time() - (60 * 60 * 24 * 7);
$weekago = date("d.m.Y", $weekago);
$sql = "SELECT UCHAST_K, UCHAST_NAIM FROM stat.UCHAST WHERE
                PREDPR_K='$predpr_k_glob' AND
                UCHAST_K IN (SELECT DISTINCT SOTRUD.UCHAST_K FROM stat.SOTRUD WHERE
                            SOTRUD_K IN (SELECT DISTINCT SOTRUD_ID FROM stat.ALLHISTORY WHERE DATEBEGIN>to_date('$weekago', 'DD.MM.YYYY HH24:MI:SS')))";

    $array_uchast = $db->go_result($sql);
    
    $smarty->assign("array_uchast",$array_uchast);
    $smarty->assign("current", $current);
    
    $smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax.get_sotrud.lamp/ajax.get_uchast.tpl.html");
?>