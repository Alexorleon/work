<?php

require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 		
$db = new db;
$db->GetConnect();
$error_='';

$ids = filter_input(INPUT_POST, 'emp_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);

$quoted = function($value){return "'".$value."'";};
$ids_string = implode(",", array_map($quoted, $ids));
$wrongs = 0;
$passed = 0;
$passed_emps = 0;
if ($ids)
{
    if (count($ids)<=500)
    {
        $sql = "SELECT PRICE FROM stat.ALLHISTORY, stat.ALLANSWERS WHERE ALLHISTORY.SOTRUD_ID IN ($ids_string) AND ALLANSWERS.ID=ALLHISTORY.ALLANSWERSID";
        $result_prices = $db->go_result($sql);
        
        foreach($result_prices as $price)
        {
            if ($price['PRICE']!=0)
            {
                $wrongs++;
            }
        }
        $passed = count($result_prices);
        
        $sql = "SELECT COUNT(DISTINCT SOTRUD_ID) AS C FROM stat.ALLHISTORY WHERE ALLHISTORY.SOTRUD_ID IN ($ids_string)";
        $passed_emps = $db->go_result_once($sql)['C'];
    }
    else
    {
        $ids_strings = array_chunk($ids, 500);
        $result_part = array();
        $result_prices = array();
        foreach($ids_strings as $id_part)
        {
            $ids_string = implode(",", array_map($quoted, $id_part));
            $sql = "SELECT PRICE FROM stat.ALLHISTORY, stat.ALLANSWERS WHERE ALLHISTORY.SOTRUD_ID IN ($ids_string) AND ALLANSWERS.ID=ALLHISTORY.ALLANSWERSID";
            $result_part[] = $db->go_result($sql);
            $sql = "SELECT COUNT(DISTINCT SOTRUD_ID) AS C FROM stat.ALLHISTORY WHERE ALLHISTORY.SOTRUD_ID IN ($ids_string)";
            $passed_emps += $db->go_result_once($sql)['C'];
        }

        foreach($result_part as $part)
        {
            foreach($part as $current_part)
            {
                $passed++;
                if ($current_part["PRICE"]!=0)
                {
                    $wrongs++;
                }
            }
        }
    }
    
    
    $smarty->assign("wrongs", $wrongs);
    $smarty->assign("total", count($ids));
    $smarty->assign("passed", $passed);
    $smarty->assign("passed_emps", $passed_emps);
    $smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax.PE_stats.list_employees/PE_stats.tpl.html");
}