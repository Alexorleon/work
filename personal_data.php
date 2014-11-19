<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

$db = new db;
$db->GetConnect();
$error_='';
		
if (!empty($_POST))
{
    $type_personal = filter_input(INPUT_POST,'type_personal', FILTER_SANITIZE_NUMBER_INT);//$_POST['type_personal'];
    switch($type_personal)
    {
        case 1: die('<script>document.location.href= "'.lhost.'/personal_reports.php"</script>'); //Отчеты
            break;
        default: die('<script>document.location.href= "'.lhost.'/index.php"</script>'); //Назад в ЛК
            break;
    }    
}
// TODO: получить всю статистику сотрудника
	
$smarty->assign("error_", $error_);

$pers_data = GetPersonalData($db, $_SESSION['sotrud_id']);

$total = GetTotal($db, $pers_data);
usort($pers_data, function($a, $b) {return $a['weight'] - $b['weight'];});

$smarty->assign("quest_data", $pers_data);
$smarty->assign("total", $total);
$smarty->assign("title", "Персональные данные");
$smarty->display("personal_data.tpl.html");
        
function GetPersonalData($obj, $sotrud_id)
{
    $result = GetCTypes($obj);
    $modules = GetModules($obj);
    $it=0;
    $sql_actual = "SELECT TO_CHAR(MAX(DATEBEGIN), 'DD.MM.YYYY HH24:MI:SS') AS DT FROM stat.ALLHISTORY WHERE EXAMINERTYPE='2' AND SOTRUD_ID='$sotrud_id'";
    $max_date = $obj->go_result_once($sql_actual)['DT'];
   // echo $max_date;
    $sql = "SELECT * FROM stat.ALLHISTORY WHERE SOTRUD_ID='$sotrud_id' AND DEL='N' AND (EXAMINERTYPE='2' AND DATEBEGIN=TO_DATE('$max_date', 'DD.MM.YYYY HH24:MI:SS'))";
    //echo $sql;
    $answer_results = $obj->go_result($sql);
    
    if (count($answer_results)!=0)
    {
        
        foreach($answer_results as $answer_res)
        {
           $current_a_id = $answer_res['ALLANSWERSID'];
           $current_q_id = $answer_res['ALLQUESTIONSID'];
           //echo $current_a_id."<br>";
           $sql_answer = <<<SQL
                        SELECT MODULEID, PRICE, ALLANSWERS.TEXT as TEXT FROM stat.ALLQUESTIONS, stat.ALLANSWERS WHERE ALLANSWERS.ID='$current_a_id' AND ALLQUESTIONS.ID = '$current_q_id'
SQL;
           
           $answer = $obj->go_result_once($sql_answer);
          // echo $it++.$answer['TEXT']." ".$answer['PRICE']." ".$answer['MODULEID']."<br>";
           if (!empty($answer))
           {
               $CTid = $modules[$answer['MODULEID']];
               $result[$CTid]['K']+=$answer['PRICE'];
               $result[$CTid]['Sum']++;
               if ($answer['PRICE']>$result[$CTid]['Danger'])
               {
                   $result[$CTid]['Danger'] = $answer['PRICE'];
               }
           }
        }
        
        foreach($result as $key=>$value)
        {
          //  echo $key." - ".$result[$key]['K']."<br>";
            if (in_array($key, $modules))
            {
                $result[$key]['K'] = ($value['Sum']==0) ? 0 : round($value['K']/$value['Sum'],0);
            
                if ($result[$key]['K']>$value['Danger'])
                {
                    $result[$key]['Danger'] = $result[$key]['K'];
                }
                $result[$key]['K'] = -$result[$key]['K'];
              //  echo $result[$key]['Danger']."<br>";
                $result[$key]['CompetenceLevel'] =GetCompetenceShortText($obj, $result[$key]['Danger']).". ".GetRecommendations($obj, $result[$key], $key);
            }
            else
            {
                $CType = mb_substr($value['KEY'],0);
                //var_dump($CType);
                if ($CType == "ks")
                {
                    $result[$key] = GetStand($obj, $sotrud_id, $result[$key], $key);
                }
                elseif($CType == "kd")
                {
                    $result[$key] = GetEmpWarnings($obj, $sotrud_id, $result[$key], $key);
                }
                elseif($CType == "kp")
                {
                    $result[$key] = GetProposes($obj, $sotrud_id, $result[$key], $key);
                }
            }
            
        }
    }
    
    return $result;
}

function GetStand($obj, $sotrud_id, $result, $stand_id) //Штрафные баллы за опыт работы
{
    $sql =<<<SQL
            SELECT HIRED_DATE FROM stat.SOTRUD WHERE SOTRUD_K='$sotrud_id'
SQL;
    $hd = $obj->go_result_once($sql);
    //($hd);
    
    $employ_date = date("Y-m-d", strtotime($hd['HIRED_DATE']));//"2013-12-12";
    $result['K'] = (strtotime("$employ_date + 1 year")> time()) ? ((strtotime("$employ_date + 6 months")>time())? ((strtotime("$employ_date + 3 months")>time())? 10 : 8) : 6): 0;
    $result['Danger'] = $result['K'];
    $result['CompetenceLevel'] = GetCompetenceShortText($obj, $result['Danger']).". ".GetRecommendations($obj, $result, $stand_id);
    $result['K'] = -$result['K'];
    return $result;
}

function GetEmpWarnings($obj, $sotrud_id, $result, $warn_id) //Штрафные баллы за нарушения
{
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
    $result['CompetenceLevel'] = GetCompetenceShortText($obj, $result['K']).". ".GetRecommendations($obj, $result, $warn_id);
    $result['K'] = -$result['K'];
    return $result;
}

function GetProposes($obj, $sotrud_id, $result, $prop_id) //Рац.предложения сотрудника
{  
    $sql =<<<SQL
            SELECT RATING FROM stat.PROPOSALS WHERE SOTRUDID='$sotrud_id'
SQL;
    $res = $obj->go_result($sql);
    $max = 0;
    foreach($res as $value)
    {
        $max = ($value['RATING']>$max) ? $value['RATING'] : $max;
    }
    $result['K'] = $max;
    $result['Danger'] = $max;
    $result['CompetenceLevel'] = GetRecommendations($obj, $result, $prop_id);
    
    return $result;
}

function GetCTypes($obj) //Возвращает массив всех CompetenceType
{
    $sql_competencetype = <<<SQL
                   SELECT * FROM COMPETENCETYPE
SQL;
    $ctypes = $obj->go_result($sql_competencetype);
    $result = array();
    foreach($ctypes as $ctype)
    {
        $result[$ctype['ID']]['weight'] = $ctype['WEIGHT'];
        $result[$ctype['ID']]['name'] = $ctype['TITLE'];
		$result[$ctype['ID']]['KEY'] = $ctype['KEY'];
        $result[$ctype['ID']]['K'] = 0;
        $result[$ctype['ID']]['Sum'] = 0;
        $result[$ctype['ID']]['Danger'] = 0;
        $result[$ctype['ID']]['CompetenceLevel'] = "";
    }
    return $result;
}

function GetModules($obj) //Массив модулей вида [ID]=>COMPETENCETYPEID
{
    $result = array();
    $sql = <<<SQL
            SELECT ID, COMPETENCETYPEID FROM stat.MODULE
SQL;
    $modules = $obj->go_result($sql);
    
    foreach($modules as $module)
    {
        $result[$module['ID']] = $module['COMPETENCETYPEID'];
    }
    
    return $result;
}

function GetCompetenceShortText($obj, $level_num)
{
    $sql_competencelevel = <<<SQL
                   SELECT TITLE, PENALTYPOINTS_MIN FROM stat.COMPETENCELEVEL ORDER BY PENALTYPOINTS_MAX
SQL;
    $competencelevel = $obj->go_result($sql_competencelevel);
    $result = '';
    foreach ($competencelevel as $cl)
    {
        if ($cl['PENALTYPOINTS_MIN']<=$level_num)
        {
           $result = $cl['TITLE']; 
        }
    }
    return  $result;
}
function GetRecommendations($obj, $data_row, $id)
{

    $sql = <<<SQL
            SELECT TITLE FROM stat.RECOMMENDATIONSSOTRUD WHERE COMPETENCETYPEID='$id' AND MIN<={$data_row['Danger']} AND MAX>={$data_row['Danger']}
SQL;
    $result = $obj->go_result_once($sql);
    //var_dump($sql);
    
    $result['TITLE'] = (empty($result)) ? "" : $result['TITLE'];
    
    return $result['TITLE'];
}

function GetShortRecommendations($obj, $data_row, $id)
{
    $sql = <<<SQL
            SELECT SHORTTITLE FROM stat.RECOMMENDATIONSSOTRUD WHERE COMPETENCETYPEID='$id' AND MIN<={$data_row['Danger']} AND MAX>={$data_row['Danger']}
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
    foreach($rows as $id=>$row)
    {
        $result['K'] += $row['K'];
        if ($row['Danger']>$result['Danger'])
        {
            $result['Danger'] = $row['Danger'];
        }
        $result['CompetenceLevel'] .= " ".GetShortRecommendations($obj, $row, $id);
    }
    $result['K'] = round($result['K']/7);
    if ($result['Danger'] < $result['K'])
    {
        $result['Danger'] = $result['K'];
    }
    
    $result['CompetenceLevel'] = GetCompetenceShortText($obj, $result['Danger']);
    return $result;
}

?>