<?php 
if(isset($q->props)){
	if(array_key_exists('downloadable',$q->props)){
		$downloadable = $q->props['downloadable'];
	}
} else {
	$downloadable = 'true';
}
?>

<input type="checkbox" name="props[]" value="downloadable" 
	<?php if ($downloadable == 'true') { 
			echo "checked='checked'"; 
	}?>
/> Allow download (untick to save this as draft only)<br/>