<?php 
include_once("../../config.php");
$PAGE = "import";
include_once("../../includes/header.php");
global $IMPORT_INFO;
$IMPORT_INFO = array();

echo "<h1>".getstring("import.title")."</h1>";

$submit = optional_param("submit","",PARAM_TEXT);
$title = optional_param("title","",PARAM_TEXT);
$content = optional_param("content","",PARAM_TEXT);
$format = optional_param("format","",PARAM_TEXT);

$supported_qtypes = array('truefalse','multichoice','essay');
if ($submit != ""){
	
	if($title == ""){
		array_push($MSG,getstring('import.quiz.error.notitle'));
	}
	if($content == ""){
		array_push($MSG,getstring('import.quiz.error.nocontent'));
	}
	$questions_to_import = array();
	
	if($format == 'gift'){
		include_once('./gift/import.php');
		$import = new qformat_gift();
			
		$lines = explode("\n",$content);
		$questions = $import->readquestions($lines);
			
		foreach($questions as $q){
			/*echo "<h2>".$q->questiontext.":".$q->qtype."</h2>";
			echo "<pre>";
			//print_r($q);
			echo "</pre>";*/
			if (in_array($q->qtype, $supported_qtypes)){
				array_push($questions_to_import,$q);
			} else {
				if($q->qtype != 'category'){
					array_push($IMPORT_INFO, $q->qtype." question type not yet supported ('".$q->questiontext."')");
				}
			}	
		}
	}
	
	if(count($questions_to_import) == 0){
		array_push($MSG,getstring('import.quiz.error.nosuppportedquestions'));
	}
	
	if(count($MSG) == 0){
		// now do the actual import
		if($format == 'gift'){
			// setup quiz with default props
			$quizid = $API->addQuiz($title);
			$API->setProp('quiz',$quizid,'downloadable','true');
			$API->setProp('quiz',$quizid,'submitable','true');
			$API->setProp('quiz',$quizid,'generatedby','import');
			$API->setProp('quiz',$quizid,'content',$content);
			$importer = new GIFTImporter();
			$importer->quizid = $quizid;
			$importer->import($questions_to_import);
			
			$API->setProp('quiz', $quizid, 'maxscore', $importer->quizmaxscore);
		}
	
		$q = $API->getQuizById($quizid);
		printf("<div class='info'>%s</div>", getstring("quiz.new.saved"));
		if(!empty($IMPORT_INFO)){
			echo "<div class='info'>Some of your questions were not imported:<ul>";
			foreach ($IMPORT_INFO as $info){
				echo "<li>".$info."</li>";
			}
			echo "</ul></div>";
		}
		// send mail to owner
		$m = new Mailer();
		$m->sendQuizCreated($USER->email,$USER->firstname, $title, $q->ref);
		include_once("../../includes/footer.php");
		die;
	}
}

if(!empty($MSG)){
	echo "<div class='warning'><ul>";
	foreach ($MSG as $err){
		echo "<li>".$err."</li>";
    }
    echo "</ul></div>";
}
?>
<div class="info">The import facility is still under development, currently only true/false and multichoice questions can be imported - more coming soon though!</div>

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