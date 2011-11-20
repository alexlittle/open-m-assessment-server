<?php
include_once("config.php");
$PAGE = "index";
include_once("./includes/header.php");
$top10popular = $API->get10PopularQuizzes();
$top10recent = $API->get10MostRecentQuizzes();
?>

<div id="start1" class="homestart">
<h3>1. Create a quiz online</h3> 
</div>

<div id="start2" class="homestart">
<h3>2. Take the quiz on your smartphone</h3> 
</div>

<div id="start3" class="homestart">
<h3>3. View and track results online</h3> 
</div>

<div style="clear:both;">
<div id="top10quizzes" class="homewidget">
<h3>10 Most Popular Quizzes</h3>
<ol>
<?php 	
	foreach ($top10popular as $t){
		echo "<li>";
		printf("<a href='./quiz/view.php?ref=%s'>%s</a>",$t->ref,$t->title);
		echo "<br/><small>(".$t->noattempts." attempts)</small>";
		echo "</li>";
	}
?>
</ol></div>

<div id="scoreboard" class="homewidget">
<h3>Leaderboard</h3> 
</div>

<div id="newquizzes" class="homewidget">
<h3>10 Most Recent Quizzes</h3>
<ol>
<?php 
	foreach ($top10recent as $t){
		echo "<li>";
		printf("<a href='./quiz/view.php?ref=%s'>%s</a>",$t->ref,$t->title);
		echo "<br/><small>(added on  ".date('d M Y',strtotime($t->createdon)).")</small>";
		echo "</li>";
	}
?>
</ol>
</div>
</div>
<?php 
include_once("./includes/footer.php");
