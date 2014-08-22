<?php	
// v 0.0.2
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 

	$db = new db;
	$db->GetConnect();
	$error_='';
	//print_r($_GET);

	// в зависимости от экзаменатора выводим соответствующую инфу
	if(isset($_GET['qtype'])){

	// это предсменный экзаменатор
	if($_GET['qtype'] == 1){
		require_once($_SERVER['DOCUMENT_ROOT']."/inc/pre_shift_examiner.php");
	}else if($_GET['qtype'] == 2){// это контроль компетентности
		require_once($_SERVER['DOCUMENT_ROOT']."/inc/control_competence.php");
	}else if($_GET['qtype'] == 3){// это контроль компетентности - пробное тестирование
		require_once($_SERVER['DOCUMENT_ROOT']."/inc/trial_testing.php");
	}else if($_GET['qtype'] == 4){// это контроль компетентности - просто тестирование
		require_once($_SERVER['DOCUMENT_ROOT']."/inc/testing.php");
	}else{
		//require_once($_SERVER['DOCUMENT_ROOT']."/inc/222.inc.php");
		die("У меня не прописано, что делать=(");
	}


}

	// --- ФУНКЦИИ ---

	// контроль компетентности
	function control_competence(&$obj){

		$sotrud_dolj = $_SESSION['sotrud_dolj'];
		// выбираются вопросы по должности и только те, которые активны в таблице TESTPARAMETERS

		// 1. Посчитать сколько всего вопросов каждого типа
		/*
		чтобы затем не извлекать занова каждый ID вопроса придется убрать COUNT,
		но подсчитать можно и вручную прямо из полученного массива без использования COUNT в запросе.
		*/

		// пример, с подсчетом вопросов каждого типа прямо в запросе.
		/*$sql = <<<SQL
		SELECT TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID, COUNT(TYPEQUESTIONSID) CNT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.TESTNAMESID IN 
		(SELECT SPECIALITY_B.TESTNAMESID FROM stat.SPECIALITY_B, stat.ALLQUESTIONS WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') AND ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE IS NOT NULL) GROUP BY TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID
SQL;*/

		// пример, если нужно ограничить по модулю
		/*$sql = <<<SQL
		SELECT TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID, COUNT(TYPEQUESTIONSID) CNT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.TESTNAMESID IN 
		(SELECT SPECIALITY_B.TESTNAMESID FROM stat.SPECIALITY_B, stat.ALLQUESTIONS WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') AND ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE IS NOT NULL AND TESTPARAMETERS.MODULEID=5) AND ALLQUESTIONS.MODULEID=5 GROUP BY TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID
SQL;*/

		// получаем ID вопроса и другие поля
    	/*$sql = <<<SQL
		SELECT ID, TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.TESTNAMESID IN 
		(SELECT SPECIALITY_B.TESTNAMESID FROM stat.SPECIALITY_B, stat.ALLQUESTIONS WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') AND ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE IS NOT NULL) GROUP BY ID, TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID
SQL;*/
		// получаем ID вопроса случайным образом
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

		// количество вопросов в тесте
		$numq = $_SESSION['numquestions'];

		// извлекаем только типы вопросов
		$onlytype_mass = array();
		for ($i = 0; $i < count($dataRow); $i++) {
			array_push($onlytype_mass, $dataRow[$i]['TYPEQUESTIONSID']);
		}

		// подсчитываем количество вопросов каждого типа
		$countq_mass = array_count_values($onlytype_mass);

		// убераем повторения
		$onlytype_mass = array_unique($onlytype_mass);

		// подсчитываем количество рисков для каждого вопроса
		$sql = <<<SQL
		SELECT TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID, COUNT(RISKLEVELID) CNT FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.TESTNAMESID IN 
		(SELECT SPECIALITY_B.TESTNAMESID FROM stat.SPECIALITY_B, stat.ALLQUESTIONS WHERE SPECIALITY_B.DOLJNOSTKOD='$sotrud_dolj') AND ALLQUESTIONS.TYPEQUESTIONSID IN (SELECT TYPEQUESTIONSID FROM stat.TESTPARAMETERS WHERE TESTPARAMETERS.ACTIVE IS NOT NULL) GROUP BY TESTNAMESID, TYPEQUESTIONSID, MODULEID, RISKLEVELID
SQL;
		//$tempdataRow = $obj->go_result($sql);

		//print_r($tempdataRow);
		echo $obj->debug_show_sql_result($sql);
		echo "<br /";

//--------------------- тестовое оформление
		echo "Всего вопросов: " . count($dataRow);
		echo "<br />";
		echo "Необходимо вопросов: " . $numq;
		echo "<br />";

		echo "-- Количество вопросов --";
		echo "<br />";

		// запоминаем все TITLE
		$c = 0;
		foreach ($onlytype_mass as $element){
		
			$sql = <<<SQL
			SELECT TITLE FROM stat.TYPEQUESTIONS WHERE TYPEQUESTIONS.ID='$element'
SQL;
			$tempdata = $obj->go_result_once($sql);
			
			$title_mass[$c] = $tempdata['TITLE'];
			$c++;
		}

		// запоминаем количество вопросов
		$c = 0;
		foreach ($countq_mass as $element){
		
			$countype_mass[$c] = $element;
			$c++;
		}

		// тестовый вывод
		for($i = 0; $i < count($title_mass); $i++){
			echo $title_mass[$i] . ": " . $countype_mass[$i];
			echo "<br />";
		}
//---------------------

		// извлекаем
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

		// 2. Распределить по заданному критерию

		// 3. Выбрать случайным образом необходимое количество каждого вопроса

		// 4. Проходим по массиву и грузим каждый вопрос и ответы к нему

		// 5. Выводим результаты и записываем их в историю

	}

	// пишем в историю
	function write_history(){

		echo "пишем в историю";
	}

	//$smarty->assign('predpr', $s_res);
  ?>