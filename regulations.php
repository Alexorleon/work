<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
	
	// ����� ������ ��������, �������� ���� �� � ������� ��������� �� ������.
	if(isset($_GET['type_reg'])){

		// ������� ������ ����� ���������
		if($_GET['type_reg'] == 1){
		
			$typedoc = "new";
			// TODO: 
		}elseif ($_GET['type_reg'] == 2){ // ������� ��� ���������
		
			$typedoc = "all";
			// TODO: 
		}else{
			$typedoc = '';
		}
	}else{
		$typedoc = '';
	}

	// �������� ������� (����� ��� ���)
	if ($_POST){

		$type_regulations = $_POST['type_regulations'];

		if ($type_regulations == 1){ // ����� ���������


			die('<script>document.location.href= "'.lhost.'/regulations.php?type_reg=1"</script>');
		}elseif ($type_regulations == 2){ // ��� ���������


			die('<script>document.location.href= "'.lhost.'/regulations.php?type_reg=2"</script>');
		}else{
			
			if($_GET['type_reg'] != 0){

				// ��������� ������� � ������ ����������
				die('<script>document.location.href= "'.lhost.'/regulations.php?type_reg=0"</script>');
			}else{
			
				// ��������� � �����
				die('<script>document.location.href= "'.lhost.'/index.php"</script>');
			}
		}
	}
	
	// TODO: ��������� � �� ���� �� ����� ���������. �.�. ��� �� �����������.

	
	$smarty->assign("error_", $error_);

	$smarty->assign("typedoc", $typedoc);
	$smarty->assign("title", "����������� ���������");
	$smarty->display("regulations.tpl.html");
?>