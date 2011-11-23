<?php 
if(isset($q)){
	if(array_key_exists('downloadable',$q->props)){
		$downloadable = $q->props['downloadable'];
	}
	if(array_key_exists('submitable',$q->props)){
		$submitable = $q->props['submitable'];
	}
} else {
	$downloadable = 'true';
	$submitable = 'true';
}
?>

<input type="checkbox" name="props[]" value="downloadable" 
	<?php if ($downloadable == 'true') { 
			echo "checked='checked'"; 
	}?>
/> Allow download<br/>
<input type="checkbox" name="props[]" value="submitable" 
	<?php if ($submitable == 'true') { 
			echo "checked='checked'"; 
	}?>
/> Allow submissions