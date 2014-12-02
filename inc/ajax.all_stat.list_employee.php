<?php

require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");	
$db = new db;
$db->GetConnect();
$error_='';

$smarty->assign("Results", GetPEResults($db));
$smarty->assign("EmployeesCount", GetEmployeesCount($db));
$smarty->assign("PassedCount", GetPassedEmployees($db));
$smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax/all_stat.tpl.html");
function GetPEResults($obj)
{
    $sql = "SELECT TO_CHAR(ALLHISTORY.DATEBEGIN, 'DD.MM.YYYY HH24:MI:SS') AS DATEBEGIN,
            ALLANSWERS.PRICE AS PRICE
            FROM stat.ALLHISTORY, stat.ALLANSWERS
            WHERE (EXAMINERTYPE='1' AND DEL='N') AND ALLANSWERS.ID=ALLHISTORY.ALLANSWERSID
            ORDER BY ALLHISTORY.DATEBEGIN";
    $PEResults = $obj->go_result($sql);

    return $PEResults;
}

function GetEmployeesCount($obj)
{
    global $predpr_k_glob;
    $sql = "SELECT COUNT(SOTRUD_K) AS C FROM stat.SOTRUD WHERE PREDPR_K='$predpr_k_glob'";
    
    return $obj->go_result_once($sql)['C'];
}

function GetPassedEmployees($obj)
{
    $sql ="SELECT DISTINCT COUNT(SOTRUD_ID) AS C FROM stat.ALLHISTORY";
    return $obj->go_result_once($sql)['C'];
}