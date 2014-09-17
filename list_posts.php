<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	require_once($_SERVER['DOCUMENT_ROOT']."./PHPExcel/IOFactory.php");
	
	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){
		
	}
	
	// получаем список всех должностей
	$sql = <<<SQL
	SELECT KOD, TEXT FROM stat.DOLJNOST WHERE DOLJNOST.PREDPR_K=10
SQL;
	$array_posts = $db->go_result($sql);
	
	//-------------------------парсим
	$objPHPExcel = new PHPExcel();
	$objPHPExcel = PHPExcel_IOFactory::load("employees.xls");

	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
	
		$highestRow         = $worksheet->getHighestRow(); // или getHighestDataRow
		$highestColumn      = $worksheet->getHighestColumn(); // например, 'F'
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		//$nrColumns = ord($highestColumn) - 64;
		//echo $nrColumns . ' колонок (A-' . $highestColumn . ') ';
		echo $highestRow . ' rows.';
		//echo '<br>Данные: <table border="1"><tr>';
		
		/*$cell = $worksheet->getCellByColumnAndRow(0, 2);
		$complete_line = $cell->getValue();
		$FIO = iconv("utf-8", "windows-1251", $complete_line);
		$out_array = preg_split('/ /', $FIO);
		print_r($out_array[0]);
		echo "<br />";
		print_r($out_array[1]);
		echo "<br />";
		print_r($out_array[2]);
		die();*/
		
		// получаем фио
		/*$cell = $worksheet->getCellByColumnAndRow(0, 2);
		$complete_line = $cell->getValue();
		$FIO = iconv("utf-8", "windows-1251", $complete_line);
		print_r($FIO);
		echo "<br />";
		// должность
		$cell = $worksheet->getCellByColumnAndRow(2, 2);
		$complete_line = $cell->getValue();
		$post = iconv("utf-8", "windows-1251", $complete_line);
		print_r($post);
		
		$tab_num = 0;
		// находим должность к этому сотруднику
		for($rr = 2; $rr < count($array_posts); $rr++){
		
			if($post == iconv("utf-8", "windows-1251", $array_posts[$rr]['TEXT'])){
			
				$tab_num = $array_posts[$rr]['KOD'];
				echo "<br />";
				print_r($post);
				break;
			}
		}
		echo "<br />";
		// табельный
		print_r($tab_num);
		die();*/
		// TODO: брать кусками
		// TODO: начать транзакцию
		
		// поочередно берем сотрудника
		for ($row = 2; $row <= $highestRow; $row++){
			
			// получаем ФИО
			$cell = $worksheet->getCellByColumnAndRow(0, $row);
			$FIO = iconv("utf-8", "windows-1251", $cell->getValue());

			// делим на ФИО
			$out_array = preg_split('/ /', $FIO);
			
			// получаем табельный
			$cell = $worksheet->getCellByColumnAndRow(1, $row);
			$tab_num_xls = iconv("utf-8", "windows-1251", $cell->getValue());
			
			// получаем должность из файла, которая должна относиться к этому сотруднику
			$cell = $worksheet->getCellByColumnAndRow(2, $row);
			$post_xls = iconv("utf-8", "windows-1251", $cell->getValue());
		
			// находим ID должности к этому сотруднику
			$is_null = 0;
			for($rr = 2; $rr < count($array_posts); $rr++){
			
				if($post_xls == iconv("utf-8", "windows-1251", $array_posts[$rr]['TEXT'])){
				
					$post_id = $array_posts[$rr]['KOD'];
					
					// записываем сотрудника
					$sql = <<<SQL
					INSERT INTO stat.SOTRUD (SOTRUD_FAM, SOTRUD_IM, SOTRUD_OTCH, PREDPR_K, DOLJ_K, TABEL_KADR) 
					VALUES ('$out_array[0]', '$out_array[1]', '$out_array[2]', '10', '$post_id', $tab_num_xls)
SQL;
					$db->go_query($sql);
					$is_null = 1;
					break;
				}
			}
			
			if($is_null == 0){
			
				// записываем сотрудника
				$sql = <<<SQL
				INSERT INTO stat.SOTRUD (SOTRUD_FAM, SOTRUD_IM, SOTRUD_OTCH, PREDPR_K, DOLJ_K, TABEL_KADR) 
				VALUES ('$out_array[0]', '$out_array[1]', '$out_array[2]', '10', '0', $tab_num_xls)
SQL;
				$db->go_query($sql);
			}
			
			// TODO: завершить транзакцию
		}
		//die();
	}
	//------------------------------------------------------

	
	$smarty->assign("error_", $error_);
	
	$smarty->assign("array_posts", $array_posts);

	$smarty->assign("title", "Список должностей");
	$smarty->display("list_posts.tpl.html");

	// --- ФУНКЦИИ ---

  ?>