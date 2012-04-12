<?php 
die;
?>
//
UPDATE question q
inner join language l ON l.langref = q.questiontitleref
SET q.questiontext = l.langtext

//
DELETE FROM language where langref in (SELECT questiontitleref from question)

//
UPDATE response q
inner join language l ON l.langref = q.responsetitleref
SET q.responsetext = l.langtext

//
DELETE FROM language where langref in (SELECT responsetitleref from response)