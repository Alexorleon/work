<?php
	// ����������� �����������;
	$typetest = 1;

	if ($_POST){

		$answer = $_POST['comp_lvl_id'];
		$idans = $_POST['answ_id'];
		
		// �������� ������� ������
		if ($answer == 21){ // ���������� ����� 21 ��� ID � ������� ������� ����������� // TODO: ����� ����� ����� ��������
			// ���������
			$_SESSION['transitionOption'] = 1;

			$dateBegin = $_SESSION['DATEBEGIN'];
			$dateEnd = date('d.m.Y H:i:s');
			$tempID = $_SESSION['sotrud_id'];
			$tempqu = $_SESSION['ID_question'];
			$tempans = $_SESSION['answer_attempt'];
			
			$tempAnsID = 0;
			// ���� ����� �������� ��������� �� ����� �����, ����� ����� ������ ������� ������
			if($_SESSION['answer_attempt'] == 0){
				$tempAnsID = $idans;
			}else{
			
				$tempAnsID = $_SESSION['first_answerid'];
			}

			// �������� ���������, ���������� ��� � �������
			// TODO: ����������
			$sql = <<<SQL
			INSERT INTO stat.ALLHISTORY (SOTRUD_ID, ALLQUESTIONSID, DATEBEGIN, DATEEND, ATTEMPTS, EXAMINERTYPE, DEL, ALLANSWERSID) VALUES 
			($tempID, $tempqu, to_date('$dateBegin', 'DD.MM.YYYY HH24:MI:SS'), to_date('$dateEnd', 'DD.MM.YYYY HH24:MI:SS'), 
			'$tempans', 1, 'N', '$tempAnsID')
SQL;
			$db->go_query($sql);
			
			die('<script>document.location.href= "'.lhost.'/commentAnswer.php?type_exam=1"</script>'); // type_exam=1 �������� ����������� ����������� pre_shift_examiner
		}else{
			//�����������
			// ���������� ������ ������� ������ ���� ����������� �������� �������
			if($_SESSION['answer_attempt'] == 0){
				$_SESSION['first_answerid'] = $idans;
			}
			
			$_SESSION['transitionOption'] = 0;
			
			die('<script>document.location.href= "'.lhost.'/commentAnswer.php?type_exam=1"</script>');
		}
	}

	$temp_id = $_SESSION['ID_question']; // ��� ��� 0

	// ��������� ��� �� ������ ��� ����� �����
	if($_SESSION['transitionOption'] == 0){ // ���� ������� ��� �������� �� ���������

		$sql = <<<SQL
		SELECT TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$temp_id'
SQL;
		$s_res = $db->go_result_once($sql);
		//$question_text = $s_res['TEXT']; TODO: ����� � �� �����

		// ����������� ������� �������
		$_SESSION['answer_attempt'] = $_SESSION['answer_attempt'] + 1;
	
	}else{// �������� ������� ��� � ������� ��� �������� ���������

		// �������� ������ ���� ������ ����, ��������� ��� �������� �������
		if($_SESSION['answer_attempt'] == 0){

			$_SESSION['DATEBEGIN'] = date('d.m.Y H:i:s');
		}

		$sotrud_dolj = $_SESSION['sotrud_dolj'];

		// 1. �������� ��� ����� ��� ������������ ���������.
		// 2. �������� ��� ������� �� ��������� ������.
		// 3. �������� ���� ��������� ������ �� ������ ������ �� ��������� ��������.
		//AND ALLQUESTIONS.TYPEQUESTIONSID='8'
		$sql = <<<SQL
		SELECT ID, TEXT, TYPEQUESTIONSID, SIMPLEPHOTO FROM 
		(SELECT ID, TEXT, TYPEQUESTIONSID, SIMPLEPHOTO FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.ID IN 
		(SELECT ALLQUESTIONSID FROM stat.ALLQUESTIONS_B WHERE ALLQUESTIONS_B.TESTNAMESID IN 
		(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj')) ORDER BY dbms_random.value) WHERE rownum=1
SQL;
		$s_res = $db->go_result_once($sql);

		if(empty($s_res)){
		
			die('<script>document.location.href= "'.lhost.'/auth.php"</script>');
		}
		
		// ���������� ID ������� ���� ����������� �������� �� ���� �����
		$_SESSION['ID_question'] = $s_res['ID'];
		
		// ���������� ��� �������
		$_SESSION['type_question'] = $s_res['TYPEQUESTIONSID'];
		
		// ���������� ��� ��������
		$_SESSION['simplephoto'] = $s_res['SIMPLEPHOTO'];
		
		//$question_text = $s_res['TEXT']; TODO: ����� � �� �����
	}

	$temp_id = $_SESSION['ID_question'];
	
	// ����� ������ � ����� �������
	$sql = <<<SQL
	SELECT ID, TEXT, COMPETENCELEVELID FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_id'
SQL;
	$array_answers = $db->go_result($sql);
	
	// �������� �������� ���������
	$temp_doljkod = $_SESSION['sotrud_dolj'];
	$sql = <<<SQL
	SELECT TEXT FROM stat.DOLJNOST WHERE DOLJNOST.KOD='$temp_doljkod'
SQL;
	$sm_sotrud_dolj = $db->go_result_once($sql);
	
	// �������� ���������
	$temp_sotrud_id = $_SESSION['sotrud_id'];
	$sql = <<<SQL
	SELECT TABEL_KADR FROM stat.SOTRUD WHERE SOTRUD.PREDPR_K='$predpr_k_glob' AND SOTRUD.SOTRUD_K='$temp_sotrud_id'
SQL;
	$sm_sotrud_tabel = $db->go_result_once($sql);
	
	/*������
	$sql = <<<SQL
	SELECT ID, TEXT FROM
	(SELECT ID, TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='8' AND ALLQUESTIONS.TESTNAMESID IN 
	(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') ORDER BY dbms_random.value) WHERE rownum=1
SQL;*/

	shuffle($array_answers);

	$smarty->assign("error_", $error_);

	$smarty->assign("question", $s_res);//������
	$smarty->assign("array_answers", $array_answers);//������
	
	// FIO
	$smarty->assign("sm_sotrud_fam", $_SESSION['sotrud_fam']);
	$smarty->assign("sm_sotrud_im", $_SESSION['sotrud_im']);
	$smarty->assign("sm_sotrud_otch", $_SESSION['sotrud_otch']);	
	$smarty->assign("sm_sotrud_dolj", $sm_sotrud_dolj);
	$smarty->assign("sm_sotrud_tabel", $sm_sotrud_tabel);
	$smarty->assign("sm_ID_question", $_SESSION['ID_question']);
	
	$smarty->assign("type_question", $_SESSION['type_question']);
	$smarty->assign("simplephoto", $_SESSION['simplephoto']);

	$smarty->assign("typetest", $typetest);
	$smarty->assign("title", "����������� �����������");
	$smarty->display("questions.tpl.html");
	
?>