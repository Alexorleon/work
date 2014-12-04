<?php
require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

// проверка доступа к странице
if( !isset($_SESSION['admin_access']) || $_SESSION['admin_access'] !== TRUE)
{
    die('<script>document.location.href= "'.lhost.'/login"</script>');//если не авторизованы, то выкидываем на авторизацию
}
else
{
    $db = new db;
    $db->GetConnect();
    $error_='';
    $role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);
    $question_id = filter_input(INPUT_GET, 'question_id', FILTER_SANITIZE_NUMBER_INT); //ID вопроса
    $dir_photo = $_SERVER['DOCUMENT_ROOT']."/storage/photo_questions/";
    $dir_video = $_SERVER['DOCUMENT_ROOT']."/storage/video_questions/simple_video/";

    $smarty->assign("role", $role);
    
    if (!empty($_POST)) //Сохранение вопроса
    {
        $current_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT); //ID вопроса
        $type_question = filter_input(INPUT_POST, 'type_question', FILTER_SANITIZE_NUMBER_INT); //Тип вопроса
        $module_question = filter_input(INPUT_POST,'module_question', FILTER_SANITIZE_NUMBER_INT); //ID модуля вопроса
        $risklevel_question = filter_input(INPUT_POST,'risklevel_question', FILTER_SANITIZE_NUMBER_INT); //ID уровня риска вопроса
        $testname_question = filter_input(INPUT_POST,'testname_question', FILTER_SANITIZE_NUMBER_INT); //ID теста, к которому прикреплен вопрос

        $text_question = filter_input(INPUT_POST,'text_question', FILTER_SANITIZE_STRING); //Текст вопроса

        $id_answer = filter_input(INPUT_POST, 'id_answer', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY); //Массив ID-шников ответов
        $text_answer = filter_input(INPUT_POST, 'text_answer', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY); //Массив текстов ответов
        $answer_price = filter_input(INPUT_POST, 'answer_price', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY); //Массив штрафов к ответам
        $answer_comment = filter_input(INPUT_POST, 'answer_comment', FILTER_SANITIZE_STRING); //Комментарий к неправильному ответу
        $answer_factor = filter_input(INPUT_POST, 'answer_factor', FILTER_SANITIZE_STRING); //Фактор риска
        if ($current_id) //Сохранение отредактированного вопроса
        {
            $sql_question = "UPDATE stat.ALLQUESTIONS SET
                                TEXT='$text_question',
                                TYPEQUESTIONSID='$type_question',
                                MODULEID='$module_question',
                                RISKLEVELID='$risklevel_question'
                                WHERE ID='$current_id'";
                $db->go_query($sql_question);
        }
        else //Сохранение нового вопроса
        {
            $sql_question = "INSERT INTO stat.ALLQUESTIONS
                            (TEXT, TYPEQUESTIONSID, MODULEID, RISKLEVELID)
                            VALUES
                            ('$text_question', '$type_question', '$module_question', '$risklevel_question')
                            returning ID into :mylastid"; //Запоминаем ID. Поэтому $db->go_query тут заюзать не получится
            $stmt = OCIParse($c, $sql_question);
            oci_bind_by_name($stmt, "mylastid", $current_id, 32, SQLT_INT); //Записываем полученный ID в $current_id
            OCIExecute($stmt);
        }

        for ($ans_iter = 0; $ans_iter<3; $ans_iter++) //3 - патамушта максимум 3 вопроса
        {
            $competencelevel_id = GetCompetenceLevelID($db, $answer_price);

            if ($answer_price[$ans_iter]!=0) //Если ответ неверный - записываем ему фактор и комментарий (общий для всех неправильных ответов)
            {
                $current_comment = $answer_comment;
                $current_factor = $answer_factor;
                $current_risk = $risklevel_question;
            }
            else //Если ответ верный - фактор и риск пустые
            {
                $current_comment = "";
                $current_factor = "";
                $current_risk = 21;
            }

            if ($id_answer[$ans_iter]!=0) //Если ответ уже есть в базе - апдейтим его
            {
                $sql_answer = "UPDATE stat.ALLANSWERS SET
                               TEXT ='{$text_answer[$ans_iter]}',
                               ALLQUESTIONSID='$current_id',
                               COMPETENCELEVELID='$competencelevel_id',
                               COMMENTARY='$current_comment',
                               FACTOR='$current_factor',
                               RISKLEVELID='$current_risk',
                               PRICE='{$answer_price[$ans_iter]}'
                               WHERE ID='{$id_answer[$ans_iter]}'";
            }
            else //Если ответ новый - инсерт в базу
            {
                $sql_answer = "INSERT INTO stat.ALLANSWERS
                               (TEXT, ALLQUESTIONSID, COMPETENCELEVELID, COMMENTARY, FACTOR, RISKLEVELID, PRICE)
                               VALUES
                               ('{$text_answer[$ans_iter]}','$current_id','$competencelevel_id','$current_comment','$current_factor','$current_risk','{$answer_price[$ans_iter]}')";
            }
            $db->go_query($sql_answer);
        }

        $sql_AQB = "SELECT ID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONSID='$current_id'"; //Ищем тест, которому принадлежит вопрос
        $test_id = $db->go_result_once($sql_AQB)['ID'];
        if ($test_id) //Если вопрос уже прикреплен к тесту
        {
            $sql_AQB = "UPDATE stat.ALLQUESTIONS_B SET TESTNAMESID='$testname_question' WHERE ID='$test_id'";
        }
        else
        {
            $sql_AQB = "INSERT INTO stat.ALLQUESTIONS_B (TESTNAMESID, ALLQUESTIONSID) VALUES ('$testname_question', '$current_id')";
        }
        $db->go_query($sql_AQB);
    
        if ((isset($_FILES['download_sf']['tmp_name']))&&(isset($_FILES['download_sf']['name']))) //Сохранение фотографии к простому вопросу
        {
            if (move_uploaded_file($_FILES['download_sf']['tmp_name'], "".$dir_photo."z".$_FILES['download_sf']['name']))
            {
                chmod($dir_photo."z".$_FILES['download_sf']['name'], 0644);
                $ext = pathinfo($_FILES['download_sf']['name'], PATHINFO_EXTENSION);
                $ts=time();
                $ts = "z".$ts.".".$ext;
                $sql = "SELECT SIMPLEPHOTO FROM stat.ALLQUESTIONS WHERE ID='$current_id'";
                $old_img = $db->go_result_once($sql)['SIMPLEPHOTO'];
                rename($dir_photo."z".$_FILES['download_sf']['name'], "".$dir_photo."$ts");
                $sql = "UPDATE stat.ALLQUESTIONS SET SIMPLEPHOTO='$ts' WHERE ID='$current_id'";
                $db->go_query($sql);
                @unlink("".$dir_photo."$old_img");
            }
        }

        if ((isset($_FILES['download_sv']['tmp_name']))&&(isset($_FILES['download_sv']['name']))) //Сохранение видео к простому вопросу
        {
            if (move_uploaded_file($_FILES['download_sv']['tmp_name'], "".$dir_video."z".$_FILES['download_sv']['name']))
            {
                chmod($dir_video."z".$_FILES['download_sv']['name'], 0644);
                $ext = pathinfo($_FILES['download_sv']['name'], PATHINFO_EXTENSION);
                $ts=time();
                $ts = "z".$ts.".".$ext;
                $sql = "SELECT SIMPLEVIDEO FROM stat.ALLQUESTIONS WHERE ID='$current_id'";
                $old_video = $db->go_result_once($sql)['SIMPLEVIDEO'];
                rename($dir_video."z".$_FILES['download_sv']['name'], "".$dir_video."$ts");
                $sql = "UPDATE stat.ALLQUESTIONS SET SIMPLEVIDEO='$ts' WHERE ID='$current_id'";
                $db->go_query($sql);
                @unlink("".$dir_video."$old_video");
            }
        }
        die('<script>document.location.href= "/edit_questions?question_id='.$current_id.'"</script>'); //Все схоронили, мы молодцы, теперь валим на страницу вопроса
    }
    //Собсно, страница вопроса
    $question_data = GetEmptyQuestionArray(); //Забиваем массив пустыми значениями, чтобы нам не ебали мозги нотайсами (да, мы пехопе-перфекционисты)

    if ($question_id) //Редактирование вопроса
    {
        $sql = "SELECT
                ALLQUESTIONS.TYPEQUESTIONSID AS TYPE, ALLQUESTIONS.MODULEID AS MID, ALLQUESTIONS.RISKLEVELID AS RISK, ALLQUESTIONS.TEXT AS TEXT,
                ALLQUESTIONS.SIMPLEPHOTO AS PHOTO, ALLQUESTIONS.SIMPLEVIDEO AS VIDEO
                FROM ALLQUESTIONS
                WHERE ALLQUESTIONS.ID='$question_id'";
        $q_res = $db->go_result_once($sql); //Собираем данные по вопросу

        $sql = "SELECT
                TESTNAMESID
                FROM stat.ALLQUESTIONS_B
                WHERE ALLQUESTIONSID='$question_id'";
        $q_res['TEST'] = $db->go_result_once($sql)['TESTNAMESID']; //Выцепляем ID теста, к которому прикреплен вопрос. Двумя запросами, чтобы все не шло папизде, если вопрос ни к чему не прикреплен

        if ($q_res['TYPE']!=22 && $q_res['TYPE']!=10) //Магические числа, 22 - сложное фото (ваще пока нет), 10 - сложное видео
        {
            $sql = "SELECT ID, TEXT, PRICE, COMMENTARY, FACTOR FROM stat.ALLANSWERS WHERE ALLQUESTIONSID='$question_id'";
            $a_res = $db->go_result($sql); //Выцепляем ответы к вопросу
            foreach($a_res as $answer)
            {
                if ($answer['PRICE']!=0) //Выцепляем комментарий и фактор к вопросу (довольно странная архитектура-с таблиц-с)
                {
                    $question_data['commentary'] = $answer['COMMENTARY'];
                    $question_data['factor'] = $answer['FACTOR'];
                    break; //За такое в моем вузе убивают нахер
                }
            }

            //Собираем данные по вопросу в один массив
            $question_data['id'] = $question_id;
            $question_data['type'] = $q_res['TYPE'];
            $question_data['module'] = $q_res['MID'];
            $question_data['risk'] = $q_res['RISK'];
            $question_data['test'] = $q_res['TEST'];
            $question_data['text'] = $q_res['TEXT'];
            $question_data['photo'] = $q_res['PHOTO'];
            $question_data['video'] = $q_res['VIDEO'];
            $question_data['answers'] = $a_res;
        }
        else //Нам не повезло, это сложный вопрос
        {
            if ($q_res['TYPE']==10) //Нам совсем не повезло, это видеоцепочка
            {
                $sql = "SELECT
                        ALLQUESTIONS.TYPEQUESTIONSID AS TYPE, ALLQUESTIONS.MODULEID AS MID, ALLQUESTIONS.RISKLEVELID AS RISK, ALLQUESTIONS.TEXT AS TEXT,
                        ALLQUESTIONS.PROLOGVIDEO AS PROLOG,  ALLQUESTIONS.CATALOG AS CATALOG, EPILOGVIDEO AS EPILOG
                        FROM ALLQUESTIONS
                        WHERE ALLQUESTIONS.ID=$question_id";
                $q_res = $db->go_result_once($sql);

                $sql = "SELECT
                        TESTNAMESID
                        FROM stat.ALLQUESTIONS_B
                        WHERE ALLQUESTIONSID='$question_id'";
                $q_res['TEST'] = $db->go_result_once($sql)['TESTNAMESID']; //Пока все примерно так же - выцепляем ID теста, все дела

                //Дальше пиздец
                $sql_chain_q = "SELECT ID, POSITION, SIMPLEVIDEO, TITLE
                                FROM COMPLEXVIDEO WHERE COMPLEXVIDEOID='$question_id' ORDER BY POSITION";
                $question_data['chain_questions'] = $db->go_result($sql_chain_q); //Выцепляем подвопросы

                $sql_chain_answers = "SELECT
                                      ID, TEXT, SIMPLEVIDEO, COMPETENCELEVELID, RISKLEVELID, PRICE, COMPLEXVIDEOID, COMMENTARY, FACTOR
                                      FROM ALLANSWERS WHERE COMPLEXVIDEOID IN (SELECT ID AS COMPLEXVIDEOID FROM COMPLEXVIDEO WHERE COMPLEXVIDEOID='$question_id')";
                $answers_chain = $db->go_result($sql_chain_answers); //Выцепляем ответы к подвопросам

                //Пишем данные по ответам к подвопросам в мега-массив
                foreach($question_data['chain_questions'] as $key=>$chained)
                {
                    $question_data['chain_questions'][$key]['answers'] = array(); 
                    foreach ($answers_chain as $ch_answer)
                    {
                        if ($ch_answer['COMPLEXVIDEOID']==$chained['ID'])
                        {
                            $question_data['chain_questions'][$key]['answers'][] = $ch_answer;
                            if ($ch_answer['COMMENTARY']!='')
                            {
                                $question_data['commentary']= $ch_answer['COMMENTARY'];
                            }
                            if ($ch_answer['FACTOR']!='')
                            {
                                $question_data['factor'] = $ch_answer['FACTOR'];
                            }
                        }
                    }
                }

                $question_data['id'] = $question_id;
                $question_data['type'] = 10; //Алярм! Тут тоже магическое число
                $question_data['module'] = $q_res['MID'];
                $question_data['risk'] = $q_res['RISK'];
                $question_data['test'] = $q_res['TEST'];
                $question_data['text'] = $q_res['TEXT'];
                $question_data['prolog'] = $q_res['PROLOG'];
                $question_data['catalog'] = $q_res['CATALOG'];
                $question_data['epilog'] = $q_res['EPILOG'];
            }
        }
    }

    if(array_key_exists('posttype', $_GET))
    {
        $posttype = filter_input(INPUT_GET, 'posttype', FILTER_SANITIZE_NUMBER_INT);
	if($posttype == 0)
        { // это добавление нового
            $_SESSION['add_or_edit_questions'] = 0;
            // чистые значения
            $smarty->assign("text_question", '');
        }
        else if($posttype == 1)
        { // это редактирование
            $_SESSION['add_or_edit_questions'] = 1;
            // получаем значения для задания их по умолчанию
            //$employee_id = filter_input(INPUT_GET, 'employee_id', FILTER_SANITIZE_NUMBER_INT); //$_GET['employee_id']; // id сотрудника
            $smarty->assign("text_question", "-TEST-");
        }
        else
        {
            die("У меня не прописано, что делать");
	}
    }

    // получаем список типов вопросов. в зависимости от выбора, свои настройки составления вопроса.
    $sql = "SELECT ID, TITLE FROM stat.TYPEQUESTIONS ORDER BY ID";
    $array_typequestions = $db->go_result($sql);

    // модуль и риск присутствуют во всех типах
    $sql = "SELECT ID, TITLE FROM stat.MODULE ORDER BY ID";
    $array_module = $db->go_result($sql);

    $sql ="SELECT ID, TITLE FROM stat.RISKLEVEL ORDER BY ID";
    $array_risklevel = $db->go_result($sql);

    $sql ="SELECT ID,TITLE FROM stat.TESTNAMES";
    $array_testnames = $db->go_result($sql);

    $smarty->assign("error_", $error_);
	
    $smarty->assign("question_data", $question_data);
    $smarty->assign("json_data", urlencode(json_encode($question_data, JSON_UNESCAPED_UNICODE)));
    $smarty->assign("array_typequestions", $array_typequestions);
    $smarty->assign("array_module", $array_module);
    $smarty->assign("array_risklevel", $array_risklevel);
    $smarty->assign("array_testnames", $array_testnames);

    if ($question_data['id']!=0) //Выбираем заголовок в зависимости от того, новый вопрос или нет
    {
        $smarty->assign("title", "Редактирование вопроса");
    }
    else
    {
        $smarty->assign("title", "Добавление вопроса");
    }   
    $smarty->display("edit_questions.tpl.html");
}

// --- ФУНКЦИИ ---

function GetCompetenceLevelID($obj, $level_num) //Возвращает ID из таблы COMPETENCELEVEL на основе значения штрафа
{
    $sql_competencelevel = "SELECT ID, PENALTYPOINTS_MIN FROM stat.COMPETENCELEVEL ORDER BY PENALTYPOINTS_MAX";
    $competencelevel = $obj->go_result($sql_competencelevel);
    $result = '';
    foreach ($competencelevel as $cl)
    {
        if ($cl['PENALTYPOINTS_MIN']<=$level_num)
        {
            $result = $cl['ID']; 
        }
    }
        return  $result;
}

function GetEmptyQuestionArray()
{
    $question_data = array();
    $question_data['id'] = 0;
    $question_data['type'] = 0;
    $question_data['module'] = 0;
    $question_data['risk'] = 0;
    $question_data['test'] = 0;
    $question_data['text'] = '';
    $question_data['photo'] = '';
    $question_data['video'] = '';
    $question_data['commentary'] = '';
    $question_data['factor'] = '';
    $question_data['answers'] = array();
    $question_data['answers'][0] = array();
    $question_data['answers'][0]['ID'] = '';
    $question_data['answers'][0]['TEXT'] = '';
    $question_data['answers'][0]['PRICE'] = '';
    $question_data['answers'][1] = array();
    $question_data['answers'][1]['ID'] = '';
    $question_data['answers'][1]['TEXT'] = '';
    $question_data['answers'][1]['PRICE'] = '';
    $question_data['answers'][2] = array();
    $question_data['answers'][2]['ID'] = '';
    $question_data['answers'][2]['TEXT'] = '';
    $question_data['answers'][2]['PRICE'] = '';
    $question_data['chain_questions'] = array();
    $question_data['prolog'] = '';
    $question_data['catalog'] = '';
    $question_data['epilog'] = '';

    return $question_data;
}
?>