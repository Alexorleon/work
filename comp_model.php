<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
	
	// ����� ������ ��������, �������� ���� �� � ������� ��������� �� ������.
	if(isset($_GET['type_comp'])){

		// ������� ������ ����� ���������
		if($_GET['type_comp'] == 1){
		
			$typecomp = "new";
			// TODO: 
		}elseif ($_GET['type_comp'] == 2){ // ������� ��� ���������
		
			$typecomp = "all";
			// TODO: 
		}else{
			$typecomp = '';
		}
	}else{
		$typecomp = '';
	}

	// �������� ������� (����� ��� ���)
	if ($_POST){

		$type_compmodel = $_POST['type_compmodel'];

		if ($type_compmodel == 1){ // ����� ���������


			die('<script>document.location.href= "'.lhost.'/comp_model.php?type_comp=1"</script>');
		}elseif ($type_compmodel == 2){ // ��� ���������


			die('<script>document.location.href= "'.lhost.'/comp_model.php?type_comp=2"</script>');
		}else{
			
			if($_GET['type_comp'] != 0){

				// ��������� ������� � ������ ����������
				die('<script>document.location.href= "'.lhost.'/comp_model.php?type_comp=0"</script>');
			}else{
			
				// ��������� � �����
				die('<script>document.location.href= "'.lhost.'/index.php"</script>');
			}
		}
	}
	
	// TODO: ��������� � �� ���� �� ����� ���������. �.�. ��� �� �����������.
	
	$smarty->assign("error_", $error_);

	$smarty->assign("typecomp", $typecomp);
	$smarty->assign("title", "������������ ������ ���������� �������");
	$smarty->display("comp_model.tpl.html");
?>