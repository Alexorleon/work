<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
$db = new db;
$db->GetConnect();
$error_='';
$temp_type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_NUMBER_INT);
$sotrud_tabel_spusk = filter_input(INPUT_POST, 'tabel_spusk', FILTER_SANITIZE_NUMBER_INT);
$window = filter_input(INPUT_POST, 'window', FILTER_SANITIZE_NUMBER_INT);

$current_hour = date("G");

if ($current_hour>=8 && $current_hour<16)
{
    $current_date = date("d.m.Y")." 05:00:00";
}
elseif ($current_hour>=16 && $current_hour<24)
{
    $current_date = date("d.m.Y")." 13:00:00";
}
elseif($current_hour>=0 && $current_hour<9)
{
    $current_date = date("d.m.Y", time() - 60 * 60 * 24)." 21:00:00";
}
else
{
    echo "Вы находитесь не на планете Земля, у вас в сутках часов больше 24. Если атмосфера текущей планеты не пригодна для дыхания, рекомендуется немедленно вернуться на Землю.";
}

if ($temp_type == 1)
{//тут массив с сотрудниками	

    $sql = "SELECT SOTRUD.TABEL_SPUSK AS TABEL, SOTRUD.SOTRUD_FAM AS FAM, SOTRUD.SOTRUD_IM AS IM, SOTRUD.SOTRUD_OTCH AS OTCH FROM stat.SOTRUD
            WHERE (SOTRUD.SOTRUD_K IN (SELECT SOTRUD_ID FROM stat.ALLHISTORY WHERE ALLHISTORY.DATEBEGIN >= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND 
            EXAMINERTYPE=1)) AND SOTRUD.WINDOW='$window' ORDER BY TABEL_SPUSK";
 
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
        if (strtotime($datemax['DATEBEGIN']) >=  strtotime($current_date))
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