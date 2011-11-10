<?php 
$receipts_dir = "./receipts/";
$handler = opendir($receipts_dir);
header("Content-type:text/html;Charset:utf-8");
$list = array();
// open directory and walk through the filenames
while ($file = readdir($handler)) {
	// if file isn't this directory or its parent, add it to the results
	if ($file != "." && $file != "..") {
		$ctime = filectime($receipts_dir . $file) . ',' . $file;
		$list[$ctime] = $file;
	}
}
closedir($handler);

krsort($list);

foreach ($list as $k=>$v){
	echo "<a href='".$receipts_dir.$v."'>".$v."</a><br/>\n";
}



?>