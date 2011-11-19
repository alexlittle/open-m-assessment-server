<?php
include_once("config.php");
$PAGE = "myquizzes";

include_once("./includes/header.php");
$quizzes = $API->getQuizzesForUser($USER->userid,$_SESSION["session_lang"]);

?>
<h1><?php echo getstring("myquizzes.title");?></h1>
<?php
foreach ($quizzes as $q){
	echo "<div id='".$q->ref."' class='quizlist'>";
	echo "<div class='quiztitle'>".$q->title."</div>";
	echo "<div class='quizattempts'>Attempts: ".$q->noattempts."</div>";
	echo "<div class='quizavg'>Average Score: ".sprintf('%3d',$q->avgscore)."%</div>";
	echo "</div>";
}

include_once("./includes/footer.php");
?>