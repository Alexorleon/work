<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 		
$db = new db;
$db->GetConnect();
$error_='';

$question_id = filter_input(INPUT_POST, 'current_id', FILTER_SANITIZE_NUMBER_INT);
if ($question_id!=0)
{
    $sql = "DELETE FROM stat.COMPLEXVIDEO WHERE ID='$question_id'";

    if ($res = $db->go_query($sql))
    {
        echo "1";
    }
    else
    {
        echo $question_id;
    }
}
else
{
    echo "1";
}