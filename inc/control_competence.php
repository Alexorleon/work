<?php
	//echo "�������� ��������������";
	$typetest = 2;

	// �������� ������� ������������ (������� ��� ���)
	if ($_POST){

		$answer = $_POST['answer'];

		if ($answer == "1"){ // ������� ������������

			//echo "������� ������������";
			//$_SESSION['qtype'] = 1;
			die('<script>document.location.href= "'.lhost.'/question.php?qtype=3"</script>');
		}else{ //������������

			//echo "������������";
			//$_SESSION['qtype'] = 2;
			die('<script>document.location.href= "'.lhost.'/question.php?qtype=4"</script>');
		}
	}

		$smarty->assign("error_", $error_);

		$smarty->assign("typetest", $typetest);
		$smarty->assign("title", "�������� ��������������");
		$smarty->display("questions.tpl.html");
		
		// �������� ���� ����� ������ �����. ����� �������� �� ���� �� ��������.
?>