<?php 
die;
?>

// move quiz title from lang table
UPDATE quiz q
inner join language l ON l.langref = q.quiztitleref
SET q.quiztitle = l.langtext


// delete lang DELETE FROM language where langref in (SELECT quiztitleref from quiz)