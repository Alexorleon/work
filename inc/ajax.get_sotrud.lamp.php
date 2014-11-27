<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
$db = new db;
$db->GetConnect();
$error_='';
$temp_type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_NUMBER_INT);
$sotrud_tabel_spusk = filter_input(INPUT_POST, 'tabel_spusk', FILTER_SANITIZE_NUMBER_INT);

$period = time() - (3 * 60 * 60); // TODO: установить нужный период
$current_date = date('d.m.Y H:i:s', $period);
if ($temp_type == 1)
{//тут массив с сотрудниками	

    $sql = "SELECT SOTRUD.TABEL_SPUSK AS TABEL, SOTRUD.SOTRUD_FAM AS FAM, SOTRUD.SOTRUD_IM AS IM, SOTRUD.SOTRUD_OTCH AS OTCH FROM stat.SOTRUD
            WHERE (SOTRUD.SOTRUD_K IN (SELECT SOTRUD_ID FROM stat.ALLHISTORY WHERE ALLHISTORY.DATEBEGIN >= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND 
            EXAMINERTYPE=1)) ORDER BY TABEL_SPUSK";
 
    $array_sotrud = $db->go_result($sql);
    $amount = round(count($array_sotrud)/3);
    $cur_key = 0;
    ?>

<table class="sotrud_lamp_table">
    <tr>
        <th>
            Табельный номер
        </th>
        <th>
            ФИО
        </th>
    </tr>
    <?php
    
    for ($tab_iter=0; $tab_iter<$amount; $tab_iter++)
    {
        $sel = ($sotrud_tabel_spusk == $array_sotrud[$tab_iter]['TABEL']) ? "class='tab_num_selected'" : "";
    ?>
    <tr id="tb_<?=$array_sotrud[$tab_iter]['TABEL']?>" onclick="get_info_tab(<?=$array_sotrud[$tab_iter]['TABEL']?>)" <?=$sel?>>
        <td>
            <?=$array_sotrud[$tab_iter]['TABEL']?>
        </td>
        <td>
            <?=$array_sotrud[$tab_iter]['FAM']?> <?=$array_sotrud[$tab_iter]['IM']?> <?=$array_sotrud[$tab_iter]['OTCH']?>
        </td>
    </tr>
    <?php
    $cur_key++;
    }
    ?>
</table>
<table class="sotrud_lamp_table">
    <tr>
        <th>
            Табельный номер
        </th>
        <th>
            ФИО
        </th>
    </tr>
    <?php
    //$cur_key = key($array_sotrud)+1;
    $iterator_count = $cur_key;
    for ($tab_iter=$iterator_count; $tab_iter<$iterator_count+$amount; $tab_iter++)
    {
        $sel = ($sotrud_tabel_spusk == $array_sotrud[$tab_iter]['TABEL']) ? "class='tab_num_selected'" : "";
    ?>
    <tr id="tb_<?=$array_sotrud[$tab_iter]['TABEL']?>" onclick="get_info_tab(<?=$array_sotrud[$tab_iter]['TABEL']?>)" <?=$sel?>>
        <td>
            <?=$array_sotrud[$tab_iter]['TABEL']?>
        </td>
        <td>
            <?=$array_sotrud[$tab_iter]['FAM']?> <?=$array_sotrud[$tab_iter]['IM']?> <?=$array_sotrud[$tab_iter]['OTCH']?>
        </td>
    </tr>
    <?php
    $cur_key++;
    }
    ?>
</table>
<table class="sotrud_lamp_table">
    <tr>
        <th>
            Табельный номер
        </th>
        <th>
            ФИО
        </th>
    </tr>
    <?php
    $iterator_count = $cur_key;
    //$cur_key = key($array_sotrud)+1;
    for ($tab_iter=$iterator_count; $tab_iter<count($array_sotrud); $tab_iter++)
    {
        $sel = ($sotrud_tabel_spusk == $array_sotrud[$tab_iter]['TABEL']) ? "class='tab_num_selected'" : "";
    ?>
    <tr id="tb_<?=$array_sotrud[$tab_iter]['TABEL']?>" onclick="get_info_tab(<?=$array_sotrud[$tab_iter]['TABEL']?>)" <?=$sel?>>
        <td>
            <?=$array_sotrud[$tab_iter]['TABEL']?>
        </td>
        <td>
            <?=$array_sotrud[$tab_iter]['FAM']?> <?=$array_sotrud[$tab_iter]['IM']?> <?=$array_sotrud[$tab_iter]['OTCH']?>
        </td>
    </tr>
    <?php
    }
    ?>
</table>
    <?php
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