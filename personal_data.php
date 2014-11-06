<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

$db = new db;
$db->GetConnect();
$error_='';
		
if (!empty($_POST))
{
    $type_personal = filter_input(INPUT_POST,'type_personal', FILTER_SANITIZE_NUMBER_INT);//$_POST['type_personal'];
    if ($type_personal == 0)
    {   // новые документы
        // переходим в лобби
        die('<script>document.location.href= "'.lhost.'/index.php"</script>');
    }
    
    
}
// TODO: получить всю статистику сотрудника
	
$smarty->assign("error_", $error_);

$pers_data = GetPersonalData($db, $_SESSION['sotrud_id']);

$total = GetTotal($db, $pers_data);

$smarty->assign("quest_data", $pers_data);
$smarty->assign("total", $total);
$smarty->assign("title", "Персональные данные");
$smarty->display("personal_data.tpl.html");
        
function GetPersonalData($obj, $sotrud_id)
{
    $result = array();
    $sql = <<<SQL
                SELECT * FROM stat.ALLHISTORY WHERE  SOTRUD_ID='$sotrud_id' AND DEL='N'
SQL;
    
    $answer_results = $obj->go_result($sql);
    //var_dump($answer_results);
    
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
               }
           }
        }
        
        foreach($result as $key=>$value)
        {
            $result[$key]['K'] = ($value['Sum']==0) ? 0 : round($value['K']/$value['Sum'],0);
            
            if ($result[$key]['K']>$value['Danger'])
            {
                $result[$key]['Danger'] = $result[$key]['K'];
            }
            $result[$key]['K'] = -$result[$key]['K'];
            $result[$key]['CompetenceLevel'] = GetCompetenceShortText($obj, $result[$key]['Danger']);
        }
    }
    $result["Ko"] = GetStand($obj, $sotrud_id);
    $result["Ks"] = GetEmpWarnings($obj,  $sotrud_id);
    $result["Kd"] = GetProposes($obj, $sotrud_id);
    foreach($result as $key=>$data_row)
    {
        $result[$key]['CompetenceLevel'] = GetRecommendations($obj, $data_row);
    }
    return $result;
}

function GetStand($obj, $sotrud_id) //Штрафные баллы за опыт работы
{
    $result = array();
    $result['id'] = 5;
    $result['name'] = "Кс – малый стажа работы в подземных условиях";
    $employ_date = "2013-12-12";
    $result['K'] = (strtotime("$employ_date + 1 year")> time()) ? ((strtotime("$employ_date + 6 months")>time())? ((strtotime("$employ_date + 3 months")>time())? 10 : 8) : 6): 0;
    $result['Danger'] = $result['K'];
    $result['CompetenceLevel'] = GetCompetenceShortText($obj, $result['Danger']);
    $result['K'] = -$result['K'];
    return $result;
}

function GetEmpWarnings($obj, $sotrud_id) //Штрафные баллы за нарушения
{
    $result = array();
    $result['id'] = 6;
    $result['name'] = "Кн – низкая дисциплин, наличие нарушений";
    $current_date = date("Y-m-d", time());
    $current_date = date("d.m.Y H:i:s", strtotime("$current_date -3 months"));
    $sql = <<<SQL
            SELECT TOKEN FROM stat.NARUSHIT WHERE PRIKAZ_DATA>= to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS') AND SOTRUD_K='$sotrud_id'
SQL;
    $warns = $obj->go_result($sql);
    
    $result['K'] = 0;
    foreach($warns as $warn)
    {
        $result['K'] = (in_array("Зеленый", $warn)) ? 6 : $result['K'];
        $result['K'] = (in_array("Желтый", $warn)) ? 12 : $result['K'];
        $result['K'] = (in_array("Красный", $warn)) ? 25 : $result['K'];
    }
    $result['Danger'] = $result['K'];
    $result['CompetenceLevel'] = GetCompetenceShortText($obj, $result['K']);
    $result['K'] = -$result['K'];
    return $result;
}

function GetProposes($obj, $sotrud_id) //Рац.предложения сотрудника
{
    $result = array();
    $result['id'] = 7;
    $result['name'] = "Кп - активные действия по снижению или устранению рисков";
    
    $result['K'] = 25;
    $result['Danger'] = 0;
    $result['CompetenceLevel'] = "Нет предложений";
    
    return $result;
}

function GetCTypes($obj) //Возвращает массив всех CompetenceType с MODULE.ID в качестве ключа
{
    $sql_competencetype = <<<SQL
                   SELECT COMPETENCETYPE.ID AS CID, COMPETENCETYPE.TITLE AS CTITLE, MODULE.ID AS MID FROM MODULE, COMPETENCETYPE WHERE COMPETENCETYPE.ID = MODULE.COMPETENCETYPEID
SQL;
    $ctypes = $obj->go_result($sql_competencetype);
    $result = array();
    foreach($ctypes as $ctype)
    {
        $result[$ctype['MID']]['id'] = $ctype['CID'];
        $result[$ctype['MID']]['name'] = $ctype['CTITLE'];
        $result[$ctype['MID']]['K'] = 0;
        $result[$ctype['MID']]['Sum'] = 0;
        $result[$ctype['MID']]['Danger'] = 0;
        $result[$ctype['MID']]['CompetenceLevel'] = "";
    }
    return $result;
}

function GetCompetenceShortText($obj, $level_num)
{
    $sql_competencelevel = <<<SQL
                   SELECT TITLE FROM stat.COMPETENCELEVEL WHERE PENALTYPOINTS_MIN<= '$level_num' AND PENALTYPOINTS_MAX>='$level_num'
SQL;
    $competencelevel = $obj->go_result_once($sql_competencelevel);
    
    if (!empty($competencelevel))
    {
        return $competencelevel['TITLE'];
    }
    return "";
}
function GetRecommendations($obj, $data_row)
{
    $sql = <<<SQL
            SELECT TITLE FROM stat.RECOMMENDATIONSSOTRUD WHERE COMPETENCETYPEID='{$data_row['id']}' AND MIN<={$data_row['Danger']} AND MAX>={$data_row['Danger']}
SQL;
    $result = $obj->go_result_once($sql);
    //var_dump($sql);
    
    $result['TITLE'] = (empty($result)) ? "" : $result['TITLE'];
    return $data_row['CompetenceLevel'].". ".$result['TITLE'];
}

function GetShortRecommendations($obj, $data_row)
{
    $sql = <<<SQL
            SELECT SHORTTITLE FROM stat.RECOMMENDATIONSSOTRUD WHERE COMPETENCETYPEID='{$data_row['id']}' AND MIN<={$data_row['Danger']} AND MAX>={$data_row['Danger']}
SQL;
    $result = $obj->go_result_once($sql);
    //var_dump($sql);
    
    $result['SHORTTITLE'] = (empty($result)) ? "" : $result['SHORTTITLE'];
    return $result['SHORTTITLE'];
}
function GetTotal($obj, $rows)
{
    $result = array();
    $result['name'] = "Кр – персональная компетентность работника";
    $result['Danger'] = 0;
    $result['K'] = 0;
    $result['CompetenceLevel'] = "";
    foreach($rows as $row)
    {
        $result['K'] += $row['K'];
        if ($row['Danger']>$result['Danger'])
        {
            $result['Danger'] = $row['Danger'];
        }
        $result['CompetenceLevel'] .= " ".GetShortRecommendations($obj, $row);
    }
    $result['K'] = round($result['K']/7);
    if ($result['Danger'] < $result['K'])
    {
        $result['Danger'] = $result['K'];
    }
    
    $result['CompetenceLevel'] = GetCompetenceShortText($obj, $result['Danger']).". ".$result['CompetenceLevel'];
    return $result;
}
?>