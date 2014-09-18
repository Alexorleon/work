<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	require_once($_SERVER['DOCUMENT_ROOT']."./PHPExcel/IOFactory.php");
	
	$db = new db;
	$db->GetConnect();
	$error_='';
	 
	// устанавливаем метаданные
	/*$objPHPExcel->getProperties()->setCreator("PHP")
                ->setLastModifiedBy("Алексей")
                ->setTitle("Office 2007 XLSX Тестируем")
                ->setSubject("Office 2007 XLSX Тестируем")
                ->setDescription("Тестовый файл Office 2007 XLSX, сгенерированный PHPExcel.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Тестовый файл");
	$objPHPExcel->getActiveSheet()->setTitle('Демо');*/
	
	// имя файла
	$file_name = iconv("utf-8", "windows-1251", "ГРОЗ Кокс-Майнинг.xlsx");
	die("STOP"); // TODO: предостережение от случайного запуска

	$objPHPExcel = new PHPExcel();
	$objPHPExcel = PHPExcel_IOFactory::load($_SERVER['DOCUMENT_ROOT']."./export/$file_name");
	
	// получаем актуальные уровни компетенции из таблицы
	$sql = <<<SQL
		SELECT PENALTYPOINTS_MIN AS "min", PENALTYPOINTS_MAX AS "max" FROM stat.COMPETENCELEVEL
SQL;
	$array_competence = $db->go_result($sql);
	//print_r($array_competence[1]['min']);
	
	// TODO: РАЗБОР ИДЕТ ТОЛЬКО ТЕКСТОВОГО ТИПА
	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
	
		$highestRow         = $worksheet->getHighestRow(); // или getHighestDataRow
		$highestColumn      = $worksheet->getHighestColumn(); // например, 'F'
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		echo $highestRow." rows";
		//$nrColumns = ord($highestColumn) - 64;
		//echo $nrColumns . ' колонок (A-' . $highestColumn . ') ';
		//echo '<br>Данные: <table border="1"><tr>';
		
		// находим ID теста и модуль из первой строки
		$cell = $worksheet->getCellByColumnAndRow(0, 1);
		$complete_line = $cell->getValue();
		//print_r($complete_line);
		echo "<br />";
		// разбор полученной строки. " - ГРОЗ - Проверка знаний"
		$out_array = preg_split('/ - /', $complete_line);
		
		// получаем ID теста
		$str_testname = iconv("utf-8", "windows-1251", $out_array[0]); // название теста
		//print_r($str_testname);
		//echo "<br />";
		
		$sql = <<<SQL
			SELECT ID FROM stat.TESTNAMES WHERE TITLE='$str_testname'
SQL;
		$s_res = $db->go_result_once($sql);
		$testname_id = $s_res['ID']; // у нас есть специальность
		//print_r($testname_id);
		//echo "<br />";
		
		// получаем ID модуля
		$str_module = iconv("utf-8", "windows-1251", $out_array[1]); // модуль
		
		$sql = <<<SQL
			SELECT ID FROM stat.MODULE WHERE TITLE='$str_module'
SQL;
		$s_res = $db->go_result_once($sql);
		$module_id = $s_res['ID'];
		//print_r($module_id);
	
		// необходимо получить номер последнего ID. Для этого нужно сначало сделать тестовую запись,
		// на тот случай если автоинкремент таблицы уже срабатывал.
		$sql = <<<SQL
			INSERT INTO stat.ALLQUESTIONS (TEXT) VALUES ('TEST')
SQL;
		$db->go_query($sql);
		
		// последний ID
		$sql = <<<SQL
			SELECT Max(ID) AS "max" FROM stat.ALLQUESTIONS
SQL;
		$s_res = $db->go_result_once($sql);
		$count_questions = $s_res['max'];
		
		// теперь удаляем тестовую запись
		$sql = <<<SQL
			DELETE FROM stat.ALLQUESTIONS WHERE ALLQUESTIONS.ID='$count_questions'
SQL;
		$db->go_query($sql);
		
		// увеличиваем счетчик для следующей записи
		$count_questions++;
		
		// TODO: брать кусками
		// TODO: начать транзакцию
		
		// поочередно берем вопросы
		for ($row = 3; $row <= $highestRow; $row = $row + 3){ // 3 - с шагом количества ответов
			
			// получаем сам вопрос
			$cell = $worksheet->getCellByColumnAndRow(1, $row);
			$question = iconv("utf-8", "windows-1251", $cell->getValue());
			//$question = $cell->getValue();
			
			// TODO: как определить какой тип вопроса
			
			$price = 0;
			// определяем максимальный риск для вопроса
			// TODO: это значение можно взять из таблицы TESTNAMES
			for ($i = $row; $i < $row + 3; $i++){
			
				// получаем цену
				$cell = $worksheet->getCellByColumnAndRow(3, $i);
				$pr = $cell->getValue();
				
				// находим максимальную цену
				if($price < $pr){
				
					$price = $pr;
				}
			}
			
			// по цене находим максимальный риск
			if(($price >= $array_competence[3]['min']) && ($price <= $array_competence[3]['max'])){
				
				// id_risk 21
				$riskLevel = 21;
			}elseif(($price >= $array_competence[2]['min']) && ($price <= $array_competence[2]['max'])){
			
				// id_risk 9
				$riskLevel = 9;
			}elseif(($price >= $array_competence[1]['min']) && ($price <= $array_competence[1]['max'])){
			
				// id_risk 8
				$riskLevel = 8;
			}elseif($price >= $array_competence[0]['min']){
			
				// id_risk 7
				$riskLevel = 7;
			}else{
				
				// такого диапазона не существует
			}
			
			// записываем вопрос
			$sql = <<<SQL
				INSERT INTO stat.ALLQUESTIONS (TEXT, TYPEQUESTIONSID, MODULEID, RISKLEVELID) 
				VALUES ('$question', '8', '5', '$riskLevel')
SQL;
			$db->go_query($sql);
			
			// добавляем новую связь теста и вопроса
			$sql = <<<SQL
				INSERT INTO stat.ALLQUESTIONS_B (TESTNAMESID, ALLQUESTIONSID) 
				VALUES ('$testname_id', '$count_questions')
SQL;
			$db->go_query($sql);
			

			// тест - проверить номер последнего ID после вставки
			/*$sql = <<<SQL
				SELECT Max(ID) AS "max" FROM ALLQUESTIONS
SQL;
			$s_res = $db->go_result_once($sql);
			print_r(" posle= ".$s_res['max']);*/
			
			// записываем все ответы
			for ($i = $row; $i < $row + 3; $i++){
			
				// получаем ответ
				$cell = $worksheet->getCellByColumnAndRow(2, $i);
				$answer = iconv("utf-8", "windows-1251", $cell->getValue());
				//$answer = $cell->getValue();
				
				// получаем цену
				$cell = $worksheet->getCellByColumnAndRow(4, $i);
				$price = $cell->getValue();
				
				// получаем комментарий
				$cell = $worksheet->getCellByColumnAndRow(6, $i);
				$commentary = iconv("utf-8", "windows-1251", $cell->getValue());
				//$commentary = $cell->getValue();
			
				// по цене находим компетентность и риск
				if(($price >= $array_competence[3]['min']) && ($price <= $array_competence[3]['max'])){
					
					// id 21 компетентен id_risk 21
					$competence = 21;
					$riskLevel = 21;
				}elseif(($price >= $array_competence[2]['min']) && ($price <= $array_competence[2]['max'])){
				
					// id 41 малокомпетентен id_risk 9
					$competence = 41;
					$riskLevel = 9;
				}elseif(($price >= $array_competence[1]['min']) && ($price <= $array_competence[1]['max'])){
				
					// id 4 некомпетентен id_risk 8
					$competence = 4;
					$riskLevel = 8;
				}elseif($price >= $array_competence[0]['min']){
				
					// id 3 опасно некомпетентен id_risk 7
					$competence = 3;
					$riskLevel = 7;
				}else{
					
					// такого диапазона не существует
				}
				
				// записываем ответ
				$sql = <<<SQL
					INSERT INTO stat.ALLANSWERS (TEXT, ALLQUESTIONSID, COMPETENCELEVELID, COMMENTARY, RISKLEVELID, PRICE) 
					VALUES ('$answer', '$count_questions', '$competence', '$commentary', '$riskLevel', '$price')
SQL;
				$db->go_query($sql);
			}
			
			// увеличиваем счетчик для следующей записи
			$count_questions++;
			
			// TODO: завершить транзакцию
			
			//print_r(" oooo ".$price." oooo ");
			//print_r("!!!!!- ".$question." -!!!!!");
			//echo "<br />";
		}
		//echo '</table>';
	}
		
	/*for ($i = $row; $i < $row + 3; $i++)
			{
				echo '<tr>';
				// берем поочередно ячейки
				for ($col = 1; $col < 8; ++ $col) // highestColumnIndex количество столбцов
				{
					$cell = $worksheet->getCellByColumnAndRow($col, $i);
					
					$val = $cell->getValue();
					//$dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
					echo '<td>' . $val;
				}
				echo '</tr>';
			}*/
	
	
	
	
	
	
	
	
	
	//------------------------------------------------------------
	/*class chunkReadFilter implements PHPExcel_Reader_IReadFilter
	{
		private $_startRow = 0;
		private $_endRow = 0;

		public function setRows($startRow, $chunkSize) {
			$this->_startRow    = $startRow;
			$this->_endRow      = $startRow + $chunkSize;
		}

		public function readCell($column, $row, $worksheetName = '') {
			if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) {
				return true;
			}
			return false;
		}
	}
	
	
	
	if ($_SESSION['startRow']) $startRow = $_SESSION['startRow'];
	else $startRow = 13;

	$inputFileType = 'Excel2007';
	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
	$chunkSize = 20;
	$chunkFilter = new chunkReadFilter();

	while ($startRow <= 65000) {
		$chunkFilter->setRows($startRow,$chunkSize);
		$objReader->setReadFilter($chunkFilter);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load("test.xlsx");
		//Что-то с этими строками делаем
		$startRow += $chunkSize;
		$_SESSION['startRow'] = $startRow; 

		unset($objReader); 

		unset($objPHPExcel);
	}

    echo "The End";
    unset($_SESSION['startRow']);*/



	
	if ($_POST){

		$type_documents = $_POST['type_documents'];

		if ($type_documents == 0){

			// переходим в лобби
			die('<script>document.location.href= "'.lhost.'/index.php"</script>');
		}
	}
	
	$smarty->assign("array_competence", $array_competence);
	$smarty->assign("error_", $error_);

	// TODO: заголовок тоже через иф
	$smarty->assign("title", "Предложения руководству");
	$smarty->display("documents.tpl.html");
?>