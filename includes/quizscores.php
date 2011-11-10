<?php 
$quizzes = $API->getQuizzes();



foreach ($quizzes as $q){
	$scores = $API->getQuizScores($q);
?>	
	<script type="text/javascript">
	google.load("visualization", "1", {
		packages:["corechart"]});
		google.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Score');
			data.addColumn('number', 'NoScores');
			data.addRows(<?php echo count($scores)?>);

			<?php
				$c = 0;
				foreach ($scores as $k=>$v){
					printf("data.setValue(%d, 0, 'scored %d %%');", $c,$k);
					printf('data.setValue(%d, 1, %d);', $c,$v);
					$c++;
				}
				
			?>
	
			var chart = new google.visualization.PieChart(document.getElementById('chart_div<?php echo $q; ?>'));
			chart.draw(data, {
				width: 450, height: 300, title: '<?php echo $q; ?> Mark distribution'});
		}
		</script>
		<div id="chart_div<?php echo $q; ?>"></div>
		
		<?php 
			$avgscores = $API->getQuizAvgResponseScores($q);
		?>
		<script type="text/javascript">
		google.load("visualization", "1", {
			packages:["corechart"]});
			google.setOnLoadCallback(drawChart);
			function drawChart() {
				var data = new google.visualization.DataTable();
				data.addColumn('string', 'Question Ref');
				data.addColumn('number', 'Average Score');
				data.addRows(<?php echo count($avgscores); ?>);

				<?php
						$c = 0;
						foreach ($avgscores as $k=>$v){
							printf("data.setValue(%d, 0, '%s');", $c,$k);
							printf('data.setValue(%d, 1, %d);', $c,$v);
							$c++;
						}
						
					?>

		
				var chart = new google.visualization.BarChart(document.getElementById('bar_div<?php echo $q; ?>'));
				chart.draw(data, {
					width: 400, height: 240, title: 'Average Score per question',
					vAxis: {
						title: 'Question', titleTextStyle: {
							color: 'red'}
					}
				});
			}
			</script>
			<div id="bar_div<?php echo $q; ?>"></div>
				
<?php 	
}
?>



