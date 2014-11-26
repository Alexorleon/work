<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
	// проверка доступа к странице
	if( isset($_SESSION['admin_access']) && $_SESSION['admin_access'] === TRUE){
	}else{
		//если не авторизованы, то выкидываем на ивторизацию
		die('<script>document.location.href= "'.lhost.'/login"</script>');
	}
	
	$db = new db;
	$db->GetConnect();
	$error_='';
	$question_id = filter_input(INPUT_GET, 'question_id', FILTER_SANITIZE_NUMBER_INT); //ID вопроса
        $dir_photo = $_SERVER['DOCUMENT_ROOT']."/storage/photo_questions/";
        $dir_video = $_SERVER['DOCUMENT_ROOT']."/storage/video_questions/simple_video/";
        
        if (!empty($_POST))
        {
            $current_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $type_question = filter_input(INPUT_POST, 'type_question', FILTER_SANITIZE_NUMBER_INT);
            $module_question = filter_input(INPUT_POST,'module_question', FILTER_SANITIZE_NUMBER_INT);
            $risklevel_question = filter_input(INPUT_POST,'risklevel_question', FILTER_SANITIZE_NUMBER_INT);
            $testname_question = filter_input(INPUT_POST,'testname_question', FILTER_SANITIZE_NUMBER_INT);
            
            $text_question = filter_input(INPUT_POST,'text_question', FILTER_SANITIZE_STRING);
            $text_question = iconv(mb_detect_encoding($text_question), "windows-1251", $text_question);
            
            $id_answer = filter_input(INPUT_POST, 'id_answer', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
            $text_answer = filter_input(INPUT_POST, 'text_answer', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
            $answer_price = filter_input(INPUT_POST, 'answer_price', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
            $answer_comment = filter_input(INPUT_POST, 'answer_comment', FILTER_SANITIZE_STRING);
            $answer_factor = filter_input(INPUT_POST, 'answer_factor', FILTER_SANITIZE_STRING);
            if ($current_id)
            {
                $sql_question = "UPDATE stat.ALLQUESTIONS SET
                        TEXT='$text_question',
                        TYPEQUESTIONSID='$type_question',
                        MODULEID='$module_question',
                        RISKLEVELID='$risklevel_question'
                        WHERE ID='$current_id'";
                $db->go_query($sql_question);
            }
            else
            {
                $sql_question = "INSERT INTO stat.ALLQUESTIONS
                                (TEXT, TYPEQUESTIONSID, MODULEID, RISKLEVELID)
                                VALUES ('$text_question', '$type_question', '$module_question', '$risklevel_question')
                                  returning ID into :mylastid";
                $stmt = OCIParse($c, $sql_question);
                oci_bind_by_name($stmt, "mylastid", $current_id, 32, SQLT_INT);
                OCIExecute($stmt);
            }
            
            for ($ans_iter = 0; $ans_iter<3; $ans_iter++)
            {
                $competencelevel_id = GetCompetenceLevelID($db, $answer_price);
                $text_answer[$ans_iter] = iconv(mb_detect_encoding($text_answer[$ans_iter]), "windows-1251", $text_answer[$ans_iter]);
                if ($answer_price[$ans_iter]!=0)
                {
                    $current_comment = iconv(mb_detect_encoding($answer_comment), "windows-1251", $answer_comment);
                    $current_factor = iconv(mb_detect_encoding($answer_factor), "windows-1251", $answer_factor);
                    $current_risk = $risklevel_question;
                }
                else
                {
                    $current_comment = "";
                    $current_factor = "";
                    $current_risk = 21;
                }
                if ($id_answer[$ans_iter]!=0)
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
                else
                {
                    $sql_answer = "INSERT INTO stat.ALLANSWERS
                        (TEXT, ALLQUESTIONSID, COMPETENCELEVELID, COMMENTARY, FACTOR, RISKLEVELID, PRICE)
                        VALUES
                        ('{$text_answer[$ans_iter]}','$current_id','$competencelevel_id','$current_comment','$current_factor','$current_risk','{$answer_price[$ans_iter]}')";
                }
                $db->go_query($sql_answer);
            }
            
            $sql_AQB = "SELECT ID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONSID='$current_id'";
            $test_id = $db->go_result_once($sql_AQB)['ID'];
            if ($test_id)
            {
                $sql_AQB = "UPDATE stat.ALLQUESTIONS_B SET TESTNAMESID='$testname_question' WHERE ID='$test_id'";
            }
            else
            {
                $sql_AQB = "INSERT INTO stat.ALLQUESTIONS_B (TESTNAMESID, ALLQUESTIONSID) VALUES ('$testname_question', '$current_id')";
            }
            $db->go_query($sql_AQB);
            
            if ((isset($_FILES['download_sf']['tmp_name']))&&(isset($_FILES['download_sf']['name'])))
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
            if ((isset($_FILES['download_sv']['tmp_name']))&&(isset($_FILES['download_sv']['name'])))
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
            die('<script>document.location.href= "/edit_questions?question_id='.$current_id.'"</script>');
        }
        
        $question_data = array();
        if ($question_id)
        {
            $sql = "SELECT
                    ALLQUESTIONS.TYPEQUESTIONSID AS TYPE, ALLQUESTIONS.MODULEID AS MID, ALLQUESTIONS.RISKLEVELID AS RISK, ALLQUESTIONS.TEXT AS TEXT,
                    ALLQUESTIONS.SIMPLEPHOTO AS PHOTO, ALLQUESTIONS.SIMPLEVIDEO AS VIDEO,
                    ALLQUESTIONS_B.TESTNAMESID AS TEST
                    FROM ALLQUESTIONS, ALLQUESTIONS_B
                    WHERE ALLQUESTIONS.ID=$question_id AND ALLQUESTIONS_B.ALLQUESTIONSID=ALLQUESTIONS.ID";
            $q_res = $db->go_result_once($sql);
            $sql = "SELECT ID, TEXT, PRICE, COMMENTARY, FACTOR FROM stat.ALLANSWERS WHERE ALLQUESTIONSID='$question_id'";
            $a_res = $db->go_result($sql);
            foreach($a_res as $answer)
            {
                if ($answer['PRICE']!=0)
                {
                    $question_data['commentary'] = $answer['COMMENTARY'];
                    $question_data['factor'] = $answer['FACTOR'];
                    break;
                }
            }
            $question_data['id'] = $question_id;
            $question_data['type'] = $q_res['TYPE'];
            $question_data['module'] = $q_res['MID'];
            $question_data['risk'] = $q_res['RISK'];
            $question_data['test'] = $q_res['TEST'];
            $question_data['text'] = $q_res['TEXT'];
            $question_data['photo'] = $q_res['PHOTO'];
            $question_data['video'] = $q_res['VIDEO'];
          //  $question_data['commentary'] = $a_res[0]['COMMENTARY'];
          //  $question_data['factor'] = $a_res[0]['FACTOR'];
            $question_data['answers'] = $a_res;
            }
        else
        {
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
        }
        

	$role = filter_input(INPUT_COOKIE, 'role', FILTER_SANITIZE_NUMBER_INT);

	$smarty->assign("role", $role);
	if(array_key_exists('posttype', $_GET)){

		$posttype = filter_input(INPUT_GET, 'posttype', FILTER_SANITIZE_NUMBER_INT);
		if($posttype == 0){ // это добавление нового

			$_SESSION['add_or_edit_questions'] = 0;

			// чистые значения
			$smarty->assign("text_question", '');

		}else if($posttype == 1){ // это редактирование

			$_SESSION['add_or_edit_questions'] = 1;

			// получаем значения для задания их по умолчанию
			//$employee_id = filter_input(INPUT_GET, 'employee_id', FILTER_SANITIZE_NUMBER_INT); //$_GET['employee_id']; // id сотрудника

			$smarty->assign("text_question", "-TEST-");
		}else{
			
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
	$smarty->assign("array_typequestions", $array_typequestions);
	$smarty->assign("array_module", $array_module);
	$smarty->assign("array_risklevel", $array_risklevel);
        $smarty->assign("array_testnames", $array_testnames);
	// TODO: через ИФ режактирование или создание новой
	$smarty->assign("title", "Редактирование вопросов");
	$smarty->display("edit_questions.tpl.html");

	// --- ФУНКЦИИ ---
function GetCompetenceLevelID($obj, $level_num)
{
    $sql_competencelevel = <<<SQL
                   SELECT ID, PENALTYPOINTS_MIN FROM stat.COMPETENCELEVEL ORDER BY PENALTYPOINTS_MAX
SQL;
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
  ?>