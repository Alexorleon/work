<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 		
$db = new db;
$db->GetConnect();
$error_='';

$question_count = filter_input(INPUT_POST, 'question_count', FILTER_SANITIZE_NUMBER_INT);
$sql ="SELECT ID, TITLE FROM stat.RISKLEVEL ORDER BY ID";
$array_risklevel = $db->go_result($sql); //В данном случае нам нужен списочек уровней риска

if ($question_count<5)
{
    $smarty->assign('last', $question_count);
    $smarty->assign('array_risklevel', $array_risklevel);
    $smarty->display($_SERVER['DOCUMENT_ROOT']."/templates/ajax/add_chain_question.tpl.html");
}
else
{
    echo "В цепочке находится максимальное количество вопросов";
}