<?php
//echo "����������� �����������";
			$typetest = 1;

			if ($_POST){

				$answer = $_POST['answer'];

				// �������� ������� ������
				if ($answer == "21"){ // 21 ��� ID � ������� ������� �����������
					//echo "good"; ���������
					$_SESSION['transitionOption'] = 1;

					$dateBegin = $_SESSION['DATEBEGIN'];
					$dateEnd = date('d.m.y H:i:s');
					$tempID = $_SESSION['sotrud_id'];
					$tempqu = $_SESSION['ID_question'];
					$tempans = $_SESSION['answer_attempt'];

					// �������� ���������, ���������� ��� � ������� TODO: ����������
					$sql = <<<SQL
					INSERT INTO stat.ALLHISTORY (SOTRUD_ID, ALLQUESTIONSID, DATEBEGIN, DATEEND, ATTEMPTS, EXAMINERTYPE, DEL) VALUES ($tempID, $tempqu, to_date('$dateBegin', 'DD.MM.YYYY HH24:MI:SS'), to_date('$dateEnd', 'DD.MM.YYYY HH24:MI:SS'), $tempans, 'pred', 'N')
SQL;
					$db->go_query($sql);

					die('<script>document.location.href= "'.lhost.'/commentAnswer.php?type_exam=1"</script>'); // type_exam=1 �������� ����������� ����������� pre_shift_examiner
				}else{
					//�� ���������
					//echo "not good";
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

					$_SESSION['DATEBEGIN'] = date('d.m.y H:i:s');
				}

				$sotrud_dolj = $_SESSION['sotrud_dolj'];
				// �� ���� ������ ����� ������ � ������������ ����������.
				// ����� �� �������� ����� ������ ���� ��������� ������. ������ �� ������ ������, ������ ��������� � ������� ����������� � ��������� ������.
				$sql = <<<SQL
				SELECT ID, TEXT FROM
				(SELECT ID, TEXT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.MODULEID='5' AND ALLQUESTIONS.TYPEQUESTIONSID='8' AND ALLQUESTIONS.TESTNAMESID IN 
				(SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') ORDER BY dbms_random.value) WHERE rownum=1
SQL;

				$s_res = $db->go_result_once($sql);

				// ���������� ID ������� ���� ����������� �������� �� ���� �����
				$_SESSION['ID_question'] = $s_res['ID'];

				$temp_id = $_SESSION['ID_question'];
				
				//$question_text = $s_res['TEXT']; TODO: ����� � �� �����
			}
			
			// ����� ������ � ����� �������
			$sql = <<<SQL
			SELECT ID, TEXT, COMPETENCELEVELID FROM stat.ALLANSWERS WHERE ALLANSWERS.ALLQUESTIONSID='$temp_id'
SQL;
			
			$array_answers = $db->go_result($sql);
			
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

			$smarty->assign("typetest", $typetest);
			$smarty->assign("title", "����������� �����������");
			$smarty->display("questions.tpl.html");
			
?>