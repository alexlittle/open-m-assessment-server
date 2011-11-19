<?php
	include_once '../config.php';
?>

function addQuestion(){
	
	var qno = $('#questions > div').size()+1;
	
	var fb = $("<div class='formblock'></div>");
	var fl = $("<div class='formlabel'></div>").text("<?php echo getstring('quiz.new.question'); ?> " +qno);
	fb.append(fl);
	var ff = $("<div class='formfield'></div>");
	ff.append("<input type='text' name='q"+qno+"' value='' size='60'></input>");
	ff.append("<h3><?php echo getstring('quiz.new.responses'); ?></h3>");
	for(i=1; i<5 ; i++){
		ff.append("<input type='text' name='q"+qno+"r"+i+"' value='' size='40'></input>");
		ff.append("<input type='text' name='q"+qno+"m"+i+"' value='0' size='5'></input><br/>");
	}
	fb.append(ff);
	$('#questions').append(fb);
		
	$('#noquestions').val(qno);
}	