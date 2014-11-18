<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

if ((!isset($_SESSION['sotrud_id'])) or (empty($_SESSION['sotrud_id'])))
{
    die('<script>document.location.href= "'.lhost.'/auth.php"</script>'); //Проебал сессию - иди поплачь на главную
}

$db = new db;//Создаем
$db->GetConnect();//ПРоверяем коннект
$error_='';

$date_list = GetPersonalDates($db, $_SESSION['sotrud_id']);

$temp_doljnost_kod = $_SESSION['sotrud_dolj'];
$sql = "SELECT TEXT FROM stat.DOLJNOST WHERE DOLJNOST.KOD='$temp_doljnost_kod'";
$sotrud_dolj_lobby = $db->go_result_once($sql);

if (!empty($_POST)) //Если уже что-то тыкали на странице
{
    $report_type = filter_input(INPUT_POST, 'reptype', FILTER_SANITIZE_NUMBER_INT); //Тип отчета. 1 - предсмертный, 2 - комплексный.
    $report_date = filter_input(INPUT_POST, 'pers_date', FILTER_SANITIZE_STRING); //Если комплексный, то нам нужна дата
    if ($report_type)
    {
        switch ($report_type)
        {
            case 2:
                $results = GetQA($db, $_SESSION['sotrud_id'], $report_date);
                $template = 'personal_result_complex.tpl.html';
                $title= "Комплексный экзаменатор";
                $smarty->assign("modules", $results['modules']);
                $smarty->assign("final_array_txt_questions", $results['txt_questions']);
                $smarty->assign("final_array_txt_answers", $results['txt_answers']);

                $smarty->assign("final_array_sf_questions", $results['sf_questions']);
                $smarty->assign("final_array_sf_answers", $results['sf_answers']);

                $smarty->assign("final_array_sv_questions", $results['sv_questions']);
                $smarty->assign("final_array_sv_answers", $results['sv_answers']);

                $smarty->assign("final_array_cv_basic", $results['cv_basic']);
                $smarty->assign("final_array_cv_questions", $results['cv_questions']);
                $smarty->assign("final_array_cv_answers", $results['cv_answers']);
                break;
            default: $results = GetPEResults($db, $_SESSION['sotrud_id']);
                $template = 'personal_result.tpl.html';
                $smarty->assign("results", $results);
                $title = "Предсменный экзаменатор";
                break;
        }
        
        $smarty->assign("error_", $error_);

        $smarty->assign("sotrud_tabkadr", $_SESSION['sotrud_tabkadr']);
        $smarty->assign("sotrud_fam", $_SESSION['sotrud_fam']);
        $smarty->assign("sotrud_im", $_SESSION['sotrud_im']);
        $smarty->assign("sotrud_otch", $_SESSION['sotrud_otch']);
        $smarty->assign("sotrud_dolj", $sotrud_dolj_lobby['TEXT']);

        $smarty->assign("title", $title);

        $smarty->display($template);
    }
    else
    {
        $type_personal = filter_input(INPUT_POST,'type_personal', FILTER_SANITIZE_NUMBER_INT);//$_POST['type_personal'];
        if ($type_personal)
        {
            switch($type_personal)
            {
                case 1: die('<script>document.location.href= "'.lhost.'/personal_reports.php"</script>'); //Отчеты
                    break;
                default: die('<script>document.location.href= "'.lhost.'/index.php"</script>'); //Назад в ЛК
                    break;
            }
        }

        $type_submit = filter_input(INPUT_POST, 'type_submit_main', FILTER_SANITIZE_NUMBER_INT); //$_POST['type_submit_main']; // по какой кнопке нажали. выбераем раздел.
        if ($type_submit)
        {
            switch($type_submit)
            {
                case 1: die('<script>document.location.href= "'.lhost.'/personal_reports.php?reptype=1"</script>');
                    break; // нормативные документы
                case 2: set_numquestions($db); die('<script>document.location.href= "'.lhost.'/question.php?qtype=2"</script>');
                    break; // контроль компетентности
                case 3: die('<script>document.location.href= "'.lhost.'/documents.php?type_doc=2"</script>');
                    break; // видеоинструктажи
                case 4: die('<script>document.location.href= "'.lhost.'/question.php?qtype=1"</script>');
                    break; // предсменный экзаменатор
                case 5: die('<script>document.location.href= "'.lhost.'/documents.php?type_doc=3"</script>');
                    break; // Компьютерные модели несчастных случаев
                case 6: die('<script>document.location.href= "'.lhost.'/proposals"</script>');
                    break;// Предложения руководству
                case 7: die('<script>document.location.href= "'.lhost.'/personal_data.php"</script>');
                    break;
                default: die('<script>document.location.href= "'.lhost.'/auth.php"</script>');
                    break;
            }
        }    
    }
}
else
{    


    $smarty->assign("error_", $error_);

    $smarty->assign("date_list", $date_list);
    $smarty->assign("sotrud_tabkadr", $_SESSION['sotrud_tabkadr']);
    $smarty->assign("sotrud_fam", $_SESSION['sotrud_fam']);
    $smarty->assign("sotrud_im", $_SESSION['sotrud_im']);
    $smarty->assign("sotrud_otch", $_SESSION['sotrud_otch']);
    $smarty->assign("sotrud_dolj", $sotrud_dolj_lobby['TEXT']);

    $smarty->assign("title", "Отчеты");

    $smarty->display('reports.tpl.html');
}
// --- ФУНКЦИИ ---

function GetPersonalDates($obj, $sid) //История сотрудника по сдаче тестов 
{
    $sql = "SELECT TO_CHAR(DATEBEGIN, 'DD.MM.YYYY HH24:MI:SS') AS DATEBEGIN FROM (SELECT DISTINCT DATEBEGIN FROM stat.ALLHISTORY WHERE SOTRUD_ID='$sid' AND EXAMINERTYPE='2' AND DEL='N') ORDER BY DATEBEGIN";
    $date_list = $obj->go_result($sql);
   
    return $date_list;
}

function GetPEResults($obj,$sid)
{
    $sql = "SELECT TO_CHAR(ALLHISTORY.DATEBEGIN, 'DD.MM.YYYY HH24:MI:SS') AS DATEBEGIN, ALLQUESTIONS.TEXT AS QTEXT, ALLQUESTIONS.SIMPLEPHOTO AS PHOTO, ALLQUESTIONS.SIMPLEVIDEO AS VIDEO, ALLANSWERS.TEXT AS ATEXT, ALLANSWERS.PRICE AS PRICE, ALLANSWERS.COMMENTARY AS COMMENTARY, ALLANSWERS.FACTOR as FACTOR, MODULE.TITLE AS MTITLE
            FROM stat.ALLHISTORY, stat.ALLQUESTIONS, stat.ALLANSWERS, stat.MODULE
            WHERE (SOTRUD_ID='$sid' AND EXAMINERTYPE='1' AND DEL='N') AND ALLQUESTIONS.ID = ALLHISTORY.ALLQUESTIONSID AND ALLANSWERS.ID=ALLHISTORY.ALLANSWERSID AND MODULE.ID=ALLQUESTIONS.MODULEID
            ORDER BY ALLHISTORY.DATEBEGIN, MODULE.ID";
    $PEResults = $obj->go_result($sql);
    return $PEResults;
}

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

function array_end_key($array)
{
    end($array);
    return key($array);
}