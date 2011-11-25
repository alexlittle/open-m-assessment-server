<?php
include_once("../config.php");
$PAGE = "mydownloads";

include_once("../includes/header.php");

printf("<h1>%s</h1>", getstring("mydownloads.title"));

// stuff here to add any new quizzes to download queue (from url)
$quizref = optional_param("quizref","",PARAM_TEXT);
if($quizref != ""){
	$quiz = $API->getQuiz($quizref);
	if ($quiz != null){
		$API->addQuizToDownloadQueue($USER->userid,$quiz->quizid);
		echo "<div class='info'>";
		echo getstring("mydownloads.queue.added",array($quiz->title));
		echo "<p>This quiz will be downloaded to you phone after you next run the mQuiz phone application.</p>";
		echo "</div>";
	}
}

$queue = $API->getUserDownloadQueue($USER->userid);
$history = $API->getUserDownloadHistory($USER->userid);

printf("<h2>%s</h2>", getstring("mydownloads.queue.title"));

if (count($queue) == 0){
	printf("<p>%s</p>", getstring("mydownloads.queue.empty"));
} else {
	foreach ($queue as $q){
		echo "<div class='quizlist'>";
		echo "<div class='quiztitle'><a href='".$CONFIG->homeAddress."quiz/view.php?ref=".$q->quizref."'>".$q->quiztitle."</a></div>";
		echo "<div class='quiztitle'>Added: ".date('H:i d M Y',strtotime($q->queuedate))."</div>";
		echo "<div style='clear:both'></div>";
		echo "</div>";
	}
}

printf("<h2>%s</h2>", getstring("mydownloads.history.title"));

if (count($history) == 0){
	printf("<p>%s</p>", getstring("mydownloads.history.empty"));
} else {
	foreach ($history as $h){
		echo "<div class='quizlist'>";
		echo "<div class='quiztitle'><a href='".$CONFIG->homeAddress."quiz/view.php?ref=".$h->quizref."'>".$h->quiztitle."</a></div>";
		echo "<div class='quiztitle'>".date('H:i d M Y',strtotime($h->historydate))."</div>";
		echo "<div style='clear:both'></div>";
		echo "</div>";
	}
}
include_once("../includes/footer.php");
?>