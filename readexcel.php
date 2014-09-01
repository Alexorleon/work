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
	
	// Специальность	Вопросы	Ответы	Цена	Компетентность	Риск	Фактор
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel = PHPExcel_IOFactory::load("test.xlsx");
	
	
	/*
	берем название должности из первой строки.
	берем первую строку.
	если в должности не пусто, запоминаем ее.
	находим соответствие теста к этой долности. - у нас есть id 9.
	каждый файл который мы открыли относится к определенному типу вопроса. - у нас есть 8.
	берем из ячейки уровень риска.
	берем из ячейки модуль. - у нас 5
	
	SELECT Max(ID) FROM ALLQUESTIONS
	*/
			
	// TODO: РАЗБОР ИДЕТ ТОЛЬКО ТЕКСТОВОГО ТИПА
	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
	
		$highestRow         = $worksheet->getHighestRow(); // или getHighestDataRow
		$highestColumn      = $worksheet->getHighestColumn(); // например, 'F'
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		echo "---".$highestRow."---";
		//$nrColumns = ord($highestColumn) - 64;
		//echo $nrColumns . ' колонок (A-' . $highestColumn . ') ';
		echo $highestRow . ' строк.';
		echo '<br>Данные: <table border="1"><tr>';
		
		// находим ID специальности из первой строки
		$cell = $worksheet->getCellByColumnAndRow(0, 1);
		$risk = $cell->getValue();
		// TODO: брать кусками
		
		// берем первый вопрос
		for ($row = 2; $row <= $highestRow; $row = $row + 3){ // 3 - количество ответов
		
			// берем поочередно 3 строки из этого вопроса
			// формируем сам вопрос
			
			// определяем название теста TODO: если тот же, то запрос повторять не нужно
			// получаем должность
			$cell = $worksheet->getCellByColumnAndRow(1, $row);
			$dolj_id = $cell->getValue();
			
			$sql = <<<SQL
				SELECT TESTNAMESID FROM stat.SPECIALITY_B WHERE DOLJNOSTKOD='$dolj_id'
SQL;
			$s_res = $db->go_result_once($sql);

			$test_id = $s_res['TESTNAMESID']; // у нас есть специальность - 9
			print_r($test_id."    ");
			
			// получаем сам вопрос
			$cell = $worksheet->getCellByColumnAndRow(2, $row);
			$question = iconv("utf-8", "windows-1251", $cell->getValue());
						
			// тест - проверить номер последнего ID
			$sql = <<<SQL
				SELECT Max(ID) AS "max" FROM stat.ALLQUESTIONS
SQL;
			$s_res = $db->go_result_once($sql);
			print_r("do= ".$s_res['max']);
			
			// получаем уровень риска
			$cell = $worksheet->getCellByColumnAndRow(6, $row);
			$risk = $cell->getValue();
			if($risk == 0) $risk = 21;
			print_r("    ".$risk."    ");
			print_r("!!!!!- ".$question);
				
			// записываем вопрос
			$sql = <<<SQL
				INSERT INTO stat.ALLQUESTIONS (TEXT, TESTNAMESID, TYPEQUESTIONSID, RISKLEVELID, MODULEID) 
				VALUES ('$question', '$test_id', '8', '$risk', '5')
SQL;
			$db->go_query($sql);			
			
			// тест - проверить номер последнего ID после вставки
			$sql = <<<SQL
				SELECT Max(ID) AS "max" FROM ALLQUESTIONS
SQL;
			$s_res = $db->go_result_once($sql);
			print_r(" posle= ".$s_res['max']);
			
			
		}
		echo '</table>';
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

				
	


	
	/*if ($_POST){

		$type_documents = $_POST['type_documents'];

		if ($type_documents == 0){

			// переходим в лобби
			die('<script>document.location.href= "'.lhost.'/index.php"</script>');
		}
	}*/
	
	// TODO: получить историю отправленных сообщений (и показать прочитанные)
	
	$smarty->assign("error_", $error_);

	// TODO: заголовок тоже через иф
	$smarty->assign("title", "Предложения руководству");
	$smarty->display("documents.tpl.html");
?>