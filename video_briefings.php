<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
	
	// �������� ������� (����� ��� ���)
	if ($_POST){

		$type_regulations = $_POST['type_regulations'];

		if ($type_regulations == 1){ // ����� ���������


			die('<script>document.location.href= "'.lhost.'/regulations.php?type_reg=1"</script>');
		}elseif ($type_regulations == 2){ // ��� ���������


			die('<script>document.location.href= "'.lhost.'/regulations.php?type_reg=2"</script>');
		}else{
			
			// ��������� ����� � �����
			die('<script>document.location.href= "'.lhost.'/index.php"</script>');
		}
	}
	
	// TODO: ��������� � �� ���� �� ����� ���������. �.�. ��� �� �����������.
	
	// ����� ������ ��������, �������� ���� �� � ������� ��������� �� ������.
	if(isset($_GET['type_reg'])){

		// ������� ��� ���������
		if($_GET['type_reg'] == 1){
		
			echo "New doc";
		}elseif ($_GET['type_reg'] == 2){ // ������� ������ ����� ���������
		
			echo "All doc";
		}else{}
	}

	$smarty->assign("error_", $error_);

	//$smarty->assign("typetest", $typetest);
	$smarty->assign("title", "����������� ���������");
	$smarty->display("regulations.tpl.html");
?>