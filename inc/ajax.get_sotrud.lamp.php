<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
$db = new db;
$db->GetConnect();
$error_='';
$temp_type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_NUMBER_INT);
$sotrud_tabel_spusk = filter_input(INPUT_POST, 'tabel_spusk', FILTER_SANITIZE_NUMBER_INT);
$shift = filter_input(INPUT_POST, 'shift', FILTER_SANITIZE_NUMBER_INT);
$window = filter_input(INPUT_POST, 'window', FILTER_SANITIZE_NUMBER_INT);
$uchast = filter_input(INPUT_POST, 'uchast', FILTER_SANITIZE_NUMBER_INT);
if (filter_input(INPUT_POST, 'date_lamp', FILTER_SANITIZE_STRING))
{
    $date_lamp = date("d.m.Y.", strtotime(filter_input(INPUT_POST, 'date_lamp', FILTER_SANITIZE_STRING)));
}
else
{
    $date_lamp = date("d.m.Y");
}

$uch_query = ($uchast!=-1) ? " AND UCHAST_K='$uchast'" : "";

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

if ($temp_type == 1)
{//тут массив с сотрудниками	

    $sql = "SELECT SOTRUD.TABEL_SPUSK AS TABEL, SOTRUD.SOTRUD_FAM AS FAM, SOTRUD.SOTRUD_IM AS IM, SOTRUD.SOTRUD_OTCH AS OTCH FROM stat.SOTRUD
            WHERE (SOTRUD.SOTRUD_K IN (SELECT SOTRUD_ID FROM stat.ALLHISTORY WHERE ALLHISTORY.DATEBEGIN >= to_date('$current_date_min', 'DD.MM.YYYY HH24:MI:SS') AND ALLHISTORY.DATEBEGIN <= to_date('$current_date_max', 'DD.MM.YYYY HH24:MI:SS') AND 
            EXAMINERTYPE=1)) AND SOTRUD.WINDOW='$window'$uch_query ORDER BY TABEL_SPUSK";

    $array_sotrud = $db->go_result($sql);
    $amount = round(count($array_sotrud)/3);
    $cur_key = 0;

    $smarty->assign("array_sotrud",$array_sotrud);
    $smarty->assign("amount", $amount);
    $smarty->assign("sotrud_tabel_spusk", $sotrud_tabel_spusk);
    
    $smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax.get_sotrud.lamp/sotrud_table.tpl.html");
}
else if ($temp_type == 2)
{//тут проверка табельного
    // получаем табельный и ищем его
    $check_tab_num = filter_input(INPUT_POST, 'check_tab_num', FILTER_SANITIZE_NUMBER_INT);//$_POST['check_tab_num'];

    $sql = "SELECT SOTRUD_K FROM stat.SOTRUD WHERE SOTRUD.TABEL_SPUSK='$check_tab_num' AND PREDPR_K=$predpr_k_glob";
    $bool_sotrud = $db->go_result_once($sql);

    if ($bool_sotrud)
    {
        $temp_sotrud = $bool_sotrud['SOTRUD_K'];

        $sql = "SELECT to_char(MAX(DATEBEGIN), 'DD.MM.YYYY HH24:MI:SS') AS DATEBEGIN FROM stat.ALLHISTORY WHERE ALLHISTORY.SOTRUD_ID='$temp_sotrud' AND EXAMINERTYPE=1";

        $datemax = $db->go_result_once($sql);
        
        if (strtotime($datemax['DATEBEGIN']) >=  strtotime($current_date_min) && strtotime($datemax['DATEBEGIN']) <=  strtotime($current_date_max))
        {
	?>
        <span style="color: #04B404;">№<?=$check_tab_num?> Контроль пройден <?=$datemax['DATEBEGIN']?></span>
        <?php
        }
        else
        {
        ?>
        <span style="color: #E31E24;">№<?=$check_tab_num?> Контроль не пройден</span>
        <?php
        }
    }
    else
    {
    ?>
        <span style="color: #E31E24;">№<?=$check_tab_num?> не существует</span>
    <?php
    }
}
else if ($temp_type == 3)
{ // тут надо получить данные по сотруднику

    $sql = "SELECT SOTRUD_K FROM stat.SOTRUD WHERE SOTRUD.TABEL_SPUSK='$sotrud_tabel_spusk' AND PREDPR_K=$predpr_k_glob";

    $sotrud = $db->go_result_once($sql);

    $temp_sotrud = $sotrud['SOTRUD_K'];

    $sql = "SELECT to_char(MAX(DATEBEGIN), 'DD.MM.YYYY HH24:MI:SS') AS DATEBEGIN FROM stat.ALLHISTORY WHERE ALLHISTORY.SOTRUD_ID='$temp_sotrud' AND EXAMINERTYPE=1";

    $datemax = $db->go_result_once($sql);

    $date = $datemax['DATEBEGIN'];
    die("date_".$date);
}
else
{

}
?>