<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
$db = new db;
$db->GetConnect();

$tabnum = filter_input(INPUT_POST, 'tabnum', FILTER_SANITIZE_NUMBER_INT);//$_POST['tabnum']; //получаем пост переменную табельного номера

$sql = "select SOTRUD_K from stat.sotrud where TABEL_SPUSK='$tabnum' and DEL IS NULL and predpr_k=$predpr_k_glob";
$s_res = $db->go_result_once($sql);

if((empty($s_res)))
{
    echo 0;
}
else
{
    echo 1;
}