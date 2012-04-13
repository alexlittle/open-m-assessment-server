<?php
include_once("config.php");
global $PAGE,$MSG,$API;
$PAGE = "reset";
$submit = optional_param("submit","",PARAM_TEXT);
$email = optional_param("email","",PARAM_TEXT);

include_once("./includes/header.php");
echo "<h1>".getstring("reset.title")."</h1>";

if ($submit != ""){
	
}

echo "<p>".getstring("reset.text")."</h1>";
?>

<form method="post" action="">
	<div class="formblock">
		<div class="formlabel"><?php echo getstring('register.email'); ?></div>
		<div class="formfield"><input type="text" name="email" value=""></input></div>
	</div>
	<div class="formblock">
		<div class="formlabel">&nbsp;</div>
		<div class="formfield">
			<input type="submit" name="submit" value="<?php echo getstring("reset.submit.button"); ?>"></input>
		</div>
	</div>
</form>

<?php 
include_once("./includes/footer.php");
?>