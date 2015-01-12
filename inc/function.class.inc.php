<?php
class db {
// данные (свойства):
var $user = db_user;
var $pass = db_pass;
var $db = db_db;
var $connect = false;

// методы:
//проверка подключения
 function GetConnect() {
 if (!@$c = OCILogon($this->user, $this->pass, $this->db, 'CL8MSWIN1251')) {
	$err = OCIError(); 
	$this->connect = false;
	die("Oracle Connect Error [".$err['message']."]");
  }else{
	$this->connect = true;
  }
 } 
 
 //Порвать коннект
 function CloseConnect() {
	oci_free_statement($s);
	oci_close($c);
	$this->connect = false;  
 }
 
//результат по запросу принт_р таблицей
function debug_show_sql_result($sql_in){
global $c;
if (!$this->connect) $this->GetConnect();
	$i = 0;
	$s = OCIParse($c, $sql_in);		
	if (!OCIExecute($s, OCI_DEFAULT)) {
		$e = oci_error($s);
		die("Oracle Error [".$e['message']."]");
	}
while ($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) {
    $out .= "<tr>\n";
    foreach ($row as $key=>$item) {
	if ($i == 0) $head .= "<td>" . $key . "</td>\n";
        $item = iconv(mb_detect_encoding($item), 'utf-8', $item);
        $out .= "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "0") . "</td>\n";
    }
    $out .= "</tr>\n";
	$i++;
}
	$ret .= "<table border='1'>\n";
	$ret .= "<tr>".$head."</tr>\n";
	$ret .= $out;
	$ret .= "</table>\n";
	
return $ret;
}

//результат по запросу в массив 
function go_result($sql_in){
global $c;
if (!$this->connect) $this->GetConnect();
	$s = OCIParse($c, $sql_in);		
	if (!OCIExecute($s, OCI_DEFAULT)) {
		$e = oci_error($s);
		//print_r($e);
		$this->error = $e['message'];
		//die("Oracle Error [".$e['message']."]");
	}
	$out = Array();
	$i = 0;
while ($res = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($res as $key=>$item) {
		$item = iconv(mb_detect_encoding($item), 'utf-8', $item);
		$out[$i][$key] = ($item !== null ? htmlentities($item, ENT_QUOTES) : "0");						
    }
		$i++;		
}
	return $out;
}

//результат по запросу в переменные
function go_result_once($sql_in){
global $c;
if (!$this->connect) $this->GetConnect();
	$s = OCIParse($c, $sql_in);		
	if (!OCIExecute($s, OCI_DEFAULT)) {
		$e = oci_error($s);
		die("Oracle Error [".$e['message']."] [--$sql_in--]");
	}
	$out = Array();
	if ($res = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)){
		foreach ($res as $key=>$item) {
			$item = iconv(mb_detect_encoding($item), 'utf-8', $item);
			$out[$key] = ($item !== null ? htmlentities($item, ENT_QUOTES) : "0");						
		}	
	}
	return $out;
}

//просто квери
function go_query($sql_in){
global $c;
if (!$this->connect) $this->GetConnect();
	$s = OCIParse($c, $sql_in);	
	if (!OCIExecute($s, OCI_DEFAULT)) {
		$e = oci_error($s);
		die("Oracle Error [".$e['message']."]");
	}
	return OCICommit($c);
}
 
//end
}
?>