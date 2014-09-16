<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	require_once($_SERVER['DOCUMENT_ROOT']."./PHPExcel/IOFactory.php");
	
	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){
		
	}
	
	
	
	
	//-------------------------парсим
	$objPHPExcel = new PHPExcel();
	$objPHPExcel = PHPExcel_IOFactory::load("posts.xls");

	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
	
		$highestRow         = $worksheet->getHighestRow(); // или getHighestDataRow
		$highestColumn      = $worksheet->getHighestColumn(); // например, 'F'
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		//$nrColumns = ord($highestColumn) - 64;
		//echo $nrColumns . ' колонок (A-' . $highestColumn . ') ';
		echo $highestRow . ' rows.';
		//echo '<br>Данные: <table border="1"><tr>';
		
		// находим ID теста и модуль из первой строки
		/*$cell = $worksheet->getCellByColumnAndRow(0, 2);
		$complete_line = $cell->getValue();
		print_r(iconv("utf-8", "windows-1251", $complete_line));
		echo "<br />";
		die();*/
		// TODO: брать кусками
		// TODO: начать транзакцию
		
		// поочередно берем должности
		for ($row = 2; $row <= $highestRow; $row++){
			
			// получаем саму должность
			$cell = $worksheet->getCellByColumnAndRow(0, $row);
			$post = $cell->getValue();

			// записываем должность
			$sql = <<<SQL
				INSERT INTO stat.DOLJNOST (TEXT, PREDPR_K) 
				VALUES ('$post', '10')
SQL;
			$db->go_query($sql);
			
			// TODO: завершить транзакцию
		}
		//echo '</table>';
	}
	//------------------------------------------------------
	
	
	// получаем список всех должностей
	$sql = <<<SQL
	SELECT KOD, TEXT FROM stat.DOLJNOST WHERE DOLJNOST.PREDPR_K=10
SQL;
	$array_posts = $db->go_result($sql);

	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_posts", $array_posts);

	$smarty->assign("title", "Список должностей");
	$smarty->display("list_posts.tpl.html");

	// --- ФУНКЦИИ ---

  ?>