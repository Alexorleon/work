<?php
	 // ������������
	
	/*$db = new db;
	$db->GetConnect();
	$error_='';*/
	
	�ontrol_competence($db);

	$smarty->assign("error_", $error_);

	//$smarty->assign("typetest", 3);
	$smarty->assign("title", "�������� ��������������");
	$smarty->display("questions.tpl.html");
	
	
	// --- ������� ---

	// �������� ��������������
	function �ontrol_competence(&$obj){

		$sotrud_dolj = $_SESSION['sotrud_dolj'];
		// ���������� ������� �� ��������� � ������ ��, ������� ������� � ������� TESTPARAMETERS

		// 1. ��������� ������� ����� �������� ������� ����
		/*
		����� ����� �� ��������� ������ ������ ID ������� �������� ������ COUNT,
		�� ���������� ����� � ������� ����� �� ����������� ������� ��� ������������� COUNT � �������.
		*/

		// ������, � ��������� �������� ������� ���� ����� � �������.
		/*$sql = <<<SQL
		SELECT TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID, COUNT(TYPEQUESTIONSID) CNT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.TESTNAMESID IN 
		(SELECT SPECIALITY_B.TESTNAMESID FROM stat.SPECIALITY_B, stat.ALLQUESTIONS WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') AND ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE IS NOT NULL) GROUP BY TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID
SQL;*/

		// ������, ���� ����� ���������� �� ������
		/*$sql = <<<SQL
		SELECT TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID, COUNT(TYPEQUESTIONSID) CNT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.TESTNAMESID IN 
		(SELECT SPECIALITY_B.TESTNAMESID FROM stat.SPECIALITY_B, stat.ALLQUESTIONS WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') AND ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE IS NOT NULL AND TESTPARAMETERS.MODULEID=5) AND ALLQUESTIONS.MODULEID=5 GROUP BY TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID
SQL;*/

		// �������� ID ������� � ������ ����
    	/*$sql = <<<SQL
		SELECT ID, TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.TESTNAMESID IN 
		(SELECT SPECIALITY_B.TESTNAMESID FROM stat.SPECIALITY_B, stat.ALLQUESTIONS WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') AND ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE IS NOT NULL) GROUP BY ID, TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID
SQL;*/
		// �������� ID ������� ��������� �������
		/*$sql = <<<SQL
		SELECT ID, TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID FROM
		(SELECT ID, TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.TESTNAMESID IN 
		(SELECT SPECIALITY_B.TESTNAMESID FROM stat.SPECIALITY_B, stat.ALLQUESTIONS WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') AND ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE IS NOT NULL) GROUP BY ID, TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID ORDER BY dbms_random.value) WHERE rownum<=10
SQL;*/
$sql = <<<SQL
		SELECT ID, TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID FROM
		(SELECT ID, TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.TESTNAMESID IN 
		(SELECT SPECIALITY_B.TESTNAMESID FROM stat.SPECIALITY_B, stat.ALLQUESTIONS WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') AND ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE IS NOT NULL) GROUP BY ID, TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID ORDER BY dbms_random.value) WHERE RISKLEVELID=9 AND TYPEQUESTIONSID=8
SQL;

		$dataRow = $obj->go_result($sql);

		//print_r($dataRow);
		echo $obj->debug_show_sql_result($sql);

		// ���������� �������� � �����
		$numq = $_SESSION['numquestions'];

		// ��������� ������ ���� ��������
		$onlytype_mass = array();
		for ($i = 0; $i < count($dataRow); $i++) {
			array_push($onlytype_mass, $dataRow[$i]['TYPEQUESTIONSID']);
		}

		// ������������ ���������� �������� ������� ����
		$countq_mass = array_count_values($onlytype_mass);

		// ������� ����������
		$onlytype_mass = array_unique($onlytype_mass);

		// ������������ ���������� ������ ��� ������� �������
		$sql = <<<SQL
		SELECT TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID, COUNT(RISKLEVELID) CNT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.TESTNAMESID IN 
		(SELECT SPECIALITY_B.TESTNAMESID FROM stat.SPECIALITY_B, stat.ALLQUESTIONS WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') AND ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE IS NOT NULL) GROUP BY TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID
SQL;
		//$tempdataRow = $obj->go_result($sql);

		//print_r($tempdataRow);
		echo $obj->debug_show_sql_result($sql);
		echo "<br /";

//--------------------- �������� ����������
		echo "����� ��������: " . count($dataRow);
		echo "<br />";
		echo "���������� ��������: " . $numq;
		echo "<br />";

		echo "-- ���������� �������� --";
		echo "<br />";

		// ���������� ��� TITLE
		$c = 0;
		foreach ($onlytype_mass as $element){
		
			$sql = <<<SQL
			SELECT TITLE FROM stat.TYPEQUESTIONS WHERE TYPEQUESTIONS.ID='$element'
SQL;
			$tempdata = $obj->go_result_once($sql);
			
			$title_mass[$c] = $tempdata['TITLE'];
			$c++;
		}

		// ���������� ���������� ��������
		$c = 0;
		foreach ($countq_mass as $element){
		
			$countype_mass[$c] = $element;
			$c++;
		}

		// �������� �����
		for($i = 0; $i < count($title_mass); $i++){
			echo $title_mass[$i] . ": " . $countype_mass[$i];
			echo "<br />";
		}
//---------------------

		// ���������
		for ($i = 0; $i < count($dataRow); $i++) {

    		echo $dataRow[$i]['TESTNAMESID'];
    		echo ',';
    		echo $dataRow[$i]['TYPEQUESTIONSID'];
    		echo ',';
    		echo $dataRow[$i]['MODULEID'];
    		echo ',';
    		echo $dataRow[$i]['RISKLEVELID'];
    		echo "---";    		
		}
		/*
		$stack = array("orange", "banana");
		array_push($stack, "apple", "raspberry");
		*/

		// 2. ������������ �� ��������� ��������

		// 3. ������� ��������� ������� ����������� ���������� ������� �������

		// 4. �������� �� ������� � ������ ������ ������ � ������ � ����

		// 5. ������� ���������� � ���������� �� � �������

	}

	// ����� � �������
	function write_history(){

		echo "����� � �������";
	}
?>