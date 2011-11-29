<?php 
include_once("../../config.php");
$PAGE = "import";
include_once("../../includes/header.php");
echo "<h1>".getstring("import.title")."</h1>";

$submit = optional_param("submit","",PARAM_TEXT);
$title = optional_param("title","",PARAM_TEXT);
$content = optional_param("content","",PARAM_TEXT);
$format = optional_param("format","",PARAM_TEXT);

$supported_qtypes = array('truefalse','multichoice');
if ($submit != ""){
	
	if($format == 'gift'){
		include_once('./gift/extras.php');
		include_once('./gift/format.php');
		include_once('./gift/gift_format.php');
		
		$import = new qformat_gift();
		//echo "<h2>GIFT Object</h2><pre>";
		//print_r($import);
		//echo "</pre>";
		//print("<hr/>");
		
		$lines = explode("\n",$content);
		$questions = $import->readquestions($lines);
		
		foreach($questions as $q){
			echo "<h2>".$q->questiontext."</h2><pre>";
			echo $q->qtype."\n";
			if (in_array($q->qtype, $supported_qtypes)){
				print_r($q);
			} else {
				echo "question type not supported";
			}
			echo "</pre>";
			print("<hr/>");
			//	echo $import->writequestion($q);
		
		}
	}
	
}



?>
<form method="post" action="">
	<div class="formblock">
		<div class="formlabel"><?php echo getstring('import.quiz.title'); ?></div>
		<div class="formfield"><input type="text" name="title" size="60" value="<?php echo $title; ?>"></input></div>
	</div>
	<div class="formblock">
		<div class="formlabel"><?php echo getstring("import.quiz.content"); ?></div>
		<div class="formfield"><textarea name="content" cols="100" rows="20"><?php echo $content; ?></textarea></div>
	</div>
	<div class="formblock">
		<div class="formlabel"><?php echo getstring("import.quiz.format"); ?></div>
		<div class="formfield"><input type="radio" name="format" value="gift" checked="checked"/><?php echo getstring("import.quiz.format.gift"); ?></div>
	</div>
	<div class="formblock">
		<div class="formlabel">&nbsp;</div>
		<div class="formfield"><input type="submit" name="submit" value="<?php echo getstring("import.quiz.add.button"); ?>"></input></div>
	</div>
	
</form>


<?php 
include_once("../../includes/footer.php");
?>