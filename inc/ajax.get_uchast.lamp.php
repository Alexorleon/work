<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
$db = new db;
$db->GetConnect();
$error_='';

$current = filter_input(INPUT_POST, 'current', FILTER_SANITIZE_NUMBER_INT);
$shift = filter_input(INPUT_POST, 'shift', FILTER_SANITIZE_NUMBER_INT);
$window = filter_input(INPUT_POST, 'window', FILTER_SANITIZE_NUMBER_INT);

if (filter_input(INPUT_POST, 'date_lamp', FILTER_SANITIZE_STRING))
{
    $date_lamp = date("d.m.Y.", strtotime(filter_input(INPUT_POST, 'date_lamp', FILTER_SANITIZE_STRING)));
}
else
{
    $date_lamp = date("d.m.Y");
}

switch ($shift)
{
    case 1:
        $current_date_min = $date_lamp." 01:00:00";
        $current_date_max = $date_lamp." 09:00:00";
    break;
    case 2:
        $current_date_min = $date_lamp." 9:00:00";
        $current_date_max = $date_lamp." 17:00:00";
    break;
    case 3:
        $current_date_min = date("d.m.Y", (strtotime($date_lamp) - 60 * 60 * 24))." 17:00:00";
        $current_date_max = $date_lamp." 01:00:00";
    break;
    default:
        $current_date_min = $date_lamp." 00:00:00";
        $current_date_max = $date_lamp." 23:59:59";
    break;
}

$sql = "SELECT UCHAST_K, UCHAST_NAIM FROM stat.UCHAST WHERE
                PREDPR_K='$predpr_k_glob' AND
                UCHAST_K IN (SELECT DISTINCT SOTRUD.UCHAST_K FROM stat.SOTRUD WHERE
                            WINDOW='$window' AND
                            SOTRUD_K IN (SELECT DISTINCT SOTRUD_ID FROM stat.ALLHISTORY WHERE DATEBEGIN>=to_date('$current_date_min', 'DD.MM.YYYY HH24:MI:SS') AND
                                DATEBEGIN<to_date('$current_date_max', 'DD.MM.YYYY HH24:MI:SS')))";

    $array_uchast = $db->go_result($sql);
    
    $smarty->assign("array_uchast",$array_uchast);
    $smarty->assign("current", $current);
    
    $smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax.get_sotrud.lamp/ajax.get_uchast.tpl.html");
?>