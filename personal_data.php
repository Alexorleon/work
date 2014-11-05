<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

$db = new db;
$db->GetConnect();
$error_='';
		
if ($_POST)
{
    $type_personal = $_POST['type_personal'];
    if ($type_personal == 0)
    {   // новые документы
        // переходим в лобби
        die('<script>document.location.href= "'.lhost.'/index.php"</script>');
    }
    
    
}
// TODO: получить всю статистику сотрудника
	
$smarty->assign("error_", $error_);

$quest_data = GetPersonalData($db, $_SESSION['sotrud_id']);

/*

$smarty->assign("Kz", $quest_data['Kz']);
$smarty->assign("Ko", $quest_data['Ko']);
$smarty->assign("Ku", $quest_data['Ku']);
$smarty->assign("Kz_danger", $Kz_danger);
$smarty->assign("Ku_danger", $Ku_danger);
$smarty->assign("Ko_danger", $Ko_danger);
*/
$smarty->assign("quest_data", $quest_data);
$smarty->assign("title", "Персональные данные");
$smarty->display("personal_data.tpl.html");
        
function GetPersonalData($obj, $pers_id)
{
    $sql_competencelevel = <<<SQL
                   SELECT TITLE, PENALTYPOINTS_MIN,PENALTYPOINTS_MAX FROM stat.COMPETENCELEVEL
SQL;
    $competencelevels = $obj->go_result($sql_competencelevel);
    
    $sql = <<<SQL
                SELECT * FROM stat.ALLHISTORY WHERE  SOTRUD_ID='$pers_id' AND DEL='N'
SQL;
    
    $answer_results = $obj->go_result($sql);
    //var_dump($answer_results);
    $result = array();
    if (count($answer_results)!=0)
    {
        $result = GetCTypes($obj);
        foreach($answer_results as $answer_res)
        {
           $current_a_id = $answer_res['ALLANSWERSID'];
           //echo $current_a_id;
           $sql_answer = <<<SQL
                        SELECT MODULEID, PRICE FROM stat.ALLQUESTIONS, stat.ALLANSWERS WHERE ALLANSWERS.ID='$current_a_id' AND ALLQUESTIONS.ID = ALLANSWERS.ALLQUESTIONSID
SQL;

           $answer = $obj->go_result_once($sql_answer);
           //var_dump($answer);
           

           if (!empty($answer))
           {
               $result[$answer['MODULEID']]['K']+=$answer['PRICE'];
               $result[$answer['MODULEID']]['Sum']++;
               if ($answer['PRICE']>$result[$answer['MODULEID']]['Danger'])
               {
                   $result[$answer['MODULEID']]['Danger'] = $answer['PRICE'];
                   foreach ($competencelevels as $cl)
                   {
                       if ($cl['PENALTYPOINTS_MIN']<=$answer['PRICE'] && $cl['PENALTYPOINTS_MAX']>=$answer['PRICE'])
                       {
                           $result[$answer['MODULEID']]['CompetenceLevel'] = $cl['TITLE'];
                           break;
                       }
                   }
               }
           }
        }
        
        foreach($result as $key=>$value)
        {
            $result[$key]['K'] = ($value['Sum']==0) ? 0 : -round($value['K']/$value['Sum'],0);
            if ($value['Sum'] == 0)
            {
               $result[$key]['CompetenceLevel'] = "Нет данных"; 
            }
            if ($value['K']>$value['Danger'])
            {
                $result[$key]['Danger'] = $value['K'];
                   foreach ($competencelevels as $cl)
                   {
                       if ($cl['PENALTYPOINTS_MIN']<=$value['K'] && $cl['PENALTYPOINTS_MAX']>=$value['K'])
                       {
                           $result[$key]['CompetenceLevel'] = $cl['TITLE'];
                           break;
                       }
                   }
            }
        }
    }

    return $result;
}

function GetCTypes($obj) //Возвращает массив всех CompetenceType с MODULE.ID в качестве ключа
{
    $sql_competencetype = <<<SQL
                   SELECT COMPETENCETYPE.TITLE AS CTITLE, MODULE.ID AS MID FROM MODULE, COMPETENCETYPE WHERE COMPETENCETYPE.ID = MODULE.COMPETENCETYPEID
SQL;
    $ctypes = $obj->go_result($sql_competencetype);
    $result = array();
    foreach($ctypes as $ctype)
    {
        $result[$ctype['MID']]['name'] = $ctype['CTITLE'];
        $result[$ctype['MID']]['K'] = 0;
        $result[$ctype['MID']]['Sum'] = 0;
        $result[$ctype['MID']]['Danger'] = 0;
        $result[$ctype['MID']]['CompetenceLevel'] = "";
    }
    return $result;
}
?>