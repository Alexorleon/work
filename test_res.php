<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
// проверка доступа к странице
if( !(isset($_SESSION['admin_access']) && $_SESSION['admin_access'] === TRUE))
{
    //если не авторизованы, то выкидываем на ивторизацию
    die('<script>document.location.href= "'.lhost.'/login"</script>');
}
else
{
    $db = new db;
    $db->GetConnect();
    $error_='';

    $role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);

    $smarty->assign("role", $role);
    $smarty->assign("error_", $error_);
    $smarty->assign("title", "Результаты тестирования");
        
    if(array_key_exists('dt', $_GET))
    {
        $test_date = filter_input(INPUT_GET, 'dt', FILTER_SANITIZE_NUMBER_INT);
        $employee_id = filter_input(INPUT_GET, 'sid', FILTER_SANITIZE_NUMBER_INT);
	
        $test_QA = GetQA($db, $employee_id, $test_date);
        
        $employee = GetEmpInfo($db, $employee_id);
        
        $smarty->assign("final_array_txt_questions", $test_QA['txt_questions']);
        $smarty->assign("final_array_txt_answers", $test_QA['txt_answers']);

        $smarty->assign("final_array_sf_questions", $test_QA['sf_questions']);
        $smarty->assign("final_array_sf_answers", $test_QA['sf_answers']);

        $smarty->assign("final_array_sv_questions", $test_QA['sv_questions']);
        $smarty->assign("final_array_sv_answers", $test_QA['sv_answers']);

        $smarty->assign("final_array_cv_basic", $test_QA['cv_basic']);
        $smarty->assign("final_array_cv_questions", $test_QA['cv_questions']);
        $smarty->assign("final_array_cv_answers", $test_QA['cv_answers']);

        $smarty->assign("final_price", $test_QA['price']);
        $smarty->assign("modules", $test_QA['modules']);
        
	$smarty->assign("cur_employee_id", $employee_id);
	$smarty->assign("cur_employee_cur", $employee['SOTRUD_FAM']);
	$smarty->assign("cur_employee_name", $employee['SOTRUD_IM']);
	$smarty->assign("cur_employee_pat", $employee['SOTRUD_OTCH']);
	$smarty->assign("cur_employee_tabel", $employee['TABEL_KADR']);
        
        $smarty->display("test_result.tpl.html");
    }
    else
    {
        $employee_id = filter_input(INPUT_GET, 'sid', FILTER_SANITIZE_NUMBER_INT);
        
        $PEResults = GetPEResults($db, $employee_id);
        $employee = GetEmpInfo($db, $employee_id);
        
        $smarty->assign("results", $PEResults);
        $smarty->assign("cur_employee_id", $employee_id);
	$smarty->assign("cur_employee_cur", $employee['SOTRUD_FAM']);
	$smarty->assign("cur_employee_name", $employee['SOTRUD_IM']);
	$smarty->assign("cur_employee_pat", $employee['SOTRUD_OTCH']);
	$smarty->assign("cur_employee_tabel", $employee['TABEL_KADR']);
        
        $smarty->display("test_PE_result.tpl.html");
    }
    
}

function GetPEResults($obj,$sid)
{
    $sql = "SELECT TO_CHAR(ALLHISTORY.DATEBEGIN, 'DD.MM.YYYY HH24:MI:SS') AS DATEBEGIN, ALLQUESTIONS.TEXT AS QTEXT, ALLANSWERS.TEXT AS ATEXT, ALLANSWERS.PRICE AS PRICE, ALLANSWERS.COMMENTARY AS COMMENTARY, ALLANSWERS.FACTOR as FACTOR, MODULE.TITLE AS MTITLE
            FROM stat.ALLHISTORY, stat.ALLQUESTIONS, stat.ALLANSWERS, stat.MODULE
            WHERE (SOTRUD_ID='$sid' AND EXAMINERTYPE='1' AND DEL='N') AND ALLQUESTIONS.ID = ALLHISTORY.ALLQUESTIONSID AND ALLANSWERS.ID=ALLHISTORY.ALLANSWERSID AND MODULE.ID=ALLQUESTIONS.MODULEID
            ORDER BY ALLHISTORY.DATEBEGIN, MODULE.ID";
    $PEResults = $obj->go_result($sql);
    //var_dump($PEResults);
    return $PEResults;
}
	// --- ФУНКЦИИ ---
function GetQA($obj, $sid, $date) //Все вопросы-ответы сотрудника за тест на определенную дату
{
    $final_array_txt_questions = array();
    $final_array_txt_answers = array();
    $final_array_sf_questions = array();
    $final_array_sf_answers = array();
    $final_array_sv_questions = array();
    $final_array_sv_answers = array();
    $final_array_cv_questions = array();
    $final_array_cv_answers = array();
    $final_array_cv_basic = array();
    
    $sql = "SELECT * FROM stat.ALLHISTORY WHERE SOTRUD_ID='$sid' AND EXAMINERTYPE='2' AND DATEBEGIN=TO_DATE('$date', 'DD.MM.YYYY HH24:MI:SS') AND DEL='N'";
    $date_list = $obj->go_result($sql);
    
    
    foreach ($date_list as $data_answer)
    {
        $sql = "SELECT * FROM stat.ALLQUESTIONS WHERE ID='{$data_answer['ALLQUESTIONSID']}'";
        $question = $obj->go_result_once($sql);
        
        $sql = "SELECT * FROM stat.ALLANSWERS WHERE ID='{$data_answer['ALLANSWERSID']}'";
        $answer = $obj->go_result_once($sql);
        
        $key = $question['ID'];
        switch($question['TYPEQUESTIONSID'])
        {
            case 8: //текст
                $final_array_txt_questions[$key] = array();
                $final_array_txt_answers[$key] = array();
                $final_array_txt_questions[$key]['Text'] = $question['TEXT'];
                $final_array_txt_questions[$key]['Module'] = $question['MODULEID'];
                
                $final_array_txt_answers[$key]['Correct'] = ($answer['RISKLEVELID'] == 21) ? 'T' : 'F';
                $final_array_txt_answers[$key]['Text'] = $answer['TEXT'];
                $final_array_txt_answers[$key]['Comment'] = $answer['COMMENTARY'];
                $final_array_txt_answers[$key]['Price'] = $answer['PRICE'];
            break;
            case 9: //простое видео
                $final_array_sv_questions[$key] = array();
                $final_array_sv_answers[$key] = array();
                $final_array_sv_questions[$key]['Text'] = $question['TEXT'];
                $final_array_sv_questions[$key]['Module'] = $question['MODULEID'];
                
                $final_array_sv_answers[$key]['Correct'] = ($answer['RISKLEVELID'] == 21) ? 'T' : 'F';
                $final_array_sv_answers[$key]['Text'] = $answer['TEXT'];
                $final_array_sv_answers[$key]['Comment'] = $answer['COMMENTARY'];
                $final_array_sv_answers[$key]['Price'] = $answer['PRICE'];
            break;
            case 10: //сложное видео
                if (!array_key_exists($key, $final_array_cv_basic))
                {
                    $final_array_cv_basic[$key] = array();
                    $final_array_cv_basic[$key]['TEXT'] = $question['TEXT'];
                    $final_array_cv_basic[$key]['MID'] = $question['MODULEID'];
                }
                
                if (!array_key_exists($key, $final_array_cv_questions))
                {
                    $final_array_cv_answers[$key] = array();
                    $final_array_cv_questions[$key] = array();
                }
                
                $final_array_cv_answers[$key][] = array();
                $final_array_cv_questions[$key][] = array();
                $subkey = array_end_key($final_array_cv_questions[$key]);
                
                $sql = "SELECT * FROM stat.COMPLEXVIDEO WHERE ID='{$answer['COMPLEXVIDEOID']}'";
                $cv_question = $obj->go_result_once($sql);
                
                $final_array_cv_questions[$key][$subkey]['Text'] = $cv_question['TITLE'];
                $final_array_cv_questions[$key][$subkey]['Video'] = $cv_question['SIMPLEVIDEO'];
                $final_array_cv_questions[$key][$subkey]['Module'] = $question['MODULEID'];
                
                $final_array_cv_answers[$key][$subkey]['Correct'] =  ($answer['RISKLEVELID'] == 21) ? 'T' : 'F';
                $final_array_cv_answers[$key][$subkey]['Text'] = $answer['TEXT'];
                $final_array_cv_answers[$key][$subkey]['Comment'] = $answer['COMMENTARY'];
                $final_array_cv_answers[$key][$subkey]['Price'] = $answer['PRICE'];
            break;
            case 21: //простое фото
                $final_array_sf_questions[$key] = array();
                $final_array_sf_answers[$key] = array();
                $final_array_sf_questions[$key]['Text'] = $question['TEXT'];
                $final_array_sf_questions[$key]['Module'] = $question['MODULEID'];
                
                $final_array_sf_answers[$key]['Correct'] = ($answer['RISKLEVELID'] == 21) ? 'T' : 'F';
                $final_array_sf_answers[$key]['Text'] = $answer['TEXT'];
                $final_array_sf_answers[$key]['Comment'] = $answer['COMMENTARY'];
                $final_array_sf_answers[$key]['Price'] = $answer['PRICE'];
            break;
            case 22: // сложное фото
            break;
        }
    }
    
    $final_price = 0;
    foreach($final_array_txt_answers as $answer)
    {
        $final_price += $answer['Price'];
    }
    foreach($final_array_sf_answers as $answer)
    {
        $final_price += $answer['Price'];
    }
    foreach($final_array_cv_answers as $answer)
    {
        foreach($answer as $subanswer)
        {
            $final_price += $subanswer['Price'];
        }
    }

    $sql_module = "SELECT ID, TITLE FROM stat.MODULE ORDER BY ID";
    $res_module = $obj->go_result($sql_module);

    $sql_compet = "SELECT TITLE, PENALTYPOINTS_MIN FROM stat.COMPETENCELEVEL ORDER BY PENALTYPOINTS_MAX";
    $competencelevels = $obj->go_result($sql_compet);

    foreach ($res_module as $mkey=>$module)
    {
        $module_price = 0;
        foreach($final_array_txt_answers as $key=>$answer)
        {
            if ($final_array_txt_questions[$key]['Module'] == $module['ID'])
            {
                $module_price += $answer['Price'];
                foreach($competencelevels as $clevel)
                {
                    if ($answer['Price']>=$clevel['PENALTYPOINTS_MIN'])
                    {
                        $final_array_txt_answers[$key]['Compet'] = $clevel['TITLE'];
                    }
                }
            }
        }

        foreach($final_array_sf_answers as $key=>$answer)
        {
            if ($final_array_sf_questions[$key]['Module'] == $module['ID'])
            {
                $module_price += $answer['Price'];
                foreach($competencelevels as $clevel)
                {
                    if ($answer['Price']>=$clevel['PENALTYPOINTS_MIN'])
                    {
                        $final_array_sf_answers[$key]['Compet'] = $clevel['TITLE'];
                    }
                }
            }
        }

        foreach($final_array_sv_answers as $key=>$answer)
        {
            if ($final_array_sv_questions[$key]['Module'] == $module['ID'])
            {
                $module_price += $answer['Price'];
                foreach($competencelevels as $clevel)
                {
                    if ($answer['Price']>=$clevel['PENALTYPOINTS_MIN'])
                    {
                        $final_array_sv_answers[$key]['Compet'] = $clevel['TITLE'];
                    }
                }
            }
        }

        foreach($final_array_cv_answers as $key=>$answer)
        {
            foreach($answer as $subkey=>$subanswer)
            {
                if ($final_array_cv_questions[$key][$subkey]['Module'] == $module['ID'])
                {
                    $module_price += $subanswer['Price'];
                    foreach($competencelevels as $clevel)
                    {
                        if ($subanswer['Price']>=$clevel['PENALTYPOINTS_MIN'])
                        {
                            $final_array_cv_answers[$key][$subkey]['Compet'] = $clevel['TITLE'];
                        }
                    }
                }
            }
        }

        $res_module[$mkey]['Price'] = $module_price;
        
        foreach($competencelevels as $clevel)
        {
            if ($module_price>=$clevel['PENALTYPOINTS_MIN'])
            {
                $res_module[$mkey]['Compet'] = $clevel['TITLE'];
            }
        }
}

    $final_array = array();
    $final_array['txt_questions'] = $final_array_txt_questions;
    $final_array['txt_answers'] = $final_array_txt_answers;
    $final_array['sf_questions'] = $final_array_sf_questions;
    $final_array['sf_answers'] = $final_array_sf_answers;
    $final_array['sv_questions'] = $final_array_sv_questions;
    $final_array['sv_answers'] = $final_array_sv_answers;
    $final_array['cv_basic'] = $final_array_cv_basic;
    $final_array['cv_answers'] = $final_array_cv_answers;
    $final_array['cv_questions'] = $final_array_cv_questions;
    $final_array['price'] = $final_price;
    $final_array['modules'] = $res_module;
    return $final_array;
}

function GetEmpInfo($obj, $sid)
{
    $sql = "SELECT * FROM stat.SOTRUD WHERE SOTRUD_K='$sid'";
    $result = $obj->go_result_once($sql);
    
    return $result;
}

function array_end_key($array)
{
    end($array);
    return key($array);
}
  ?>