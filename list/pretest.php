<?php
header("Content-type:text/plain;Charset:UTF-8");

$questions = array();

$q1r1 = array(
			'refid'=> "true",
			'orderno'=> 1,
			'text'=>'True',
			'score'=>10	
		);	
$q1r2 = array(
			'refid'=> "false",
			'orderno'=> 2,
			'text'=>'False',
			'score'=>0	
		);

$q1r = array($q1r1,$q1r2);

$q1 = array(
			'refid'=>'q1',
			'orderno'=> 1,
			'text'=>'A woman can die within two hours after the onset of postpartum hemorrhage (PPH) if she does not receive proper treatment.',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q1r
			);

$q2r1 = array(
			'refid'=> "true",
			'orderno'=> 1,
			'text'=>'True',
			'score'=>10	
);
$q2r2 = array(
			'refid'=> "false",
			'orderno'=> 2,
			'text'=>'False',
			'score'=>0	
);

$q2r = array($q2r1,$q2r2);

$q2 = array(
			'refid'=>'q2',
			'orderno'=> 2,
			'text'=>'PPH is the single most important direct cause of maternal death.',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q2r
);


$q3r1 = array(
			'refid'=> "true",
			'orderno'=> 1,
			'text'=>'True',
			'score'=>0	
);
$q3r2 = array(
			'refid'=> "false",
			'orderno'=> 2,
			'text'=>'False',
			'score'=>10	
);

$q3r = array($q3r1,$q3r2);

$q3 = array(
			'refid'=>'q3',
			'orderno'=> 3,
			'text'=>'The Active Management of Third Stage Labor (AMTSL) consists of three components which are: administration of a uterotonic; uterine massage; and repair of genital tears.',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q3r
);

$q4r1 = array(
			'refid'=> "ut",
			'orderno'=> 1,
			'text'=>'Uterine atony',
			'score'=>0	
);
$q4r2 = array(
			'refid'=> "gen",
			'orderno'=> 2,
			'text'=>'Genital trauma/laceration',
			'score'=>0	
);
$q4r3 = array(
			'refid'=> "ab",
			'orderno'=> 3,
			'text'=>'Congenital anomalies of the baby',
			'score'=>10	
);
$q4r4 = array(
			'refid'=> "rp",
			'orderno'=> 4,
			'text'=>'Retained placenta',
			'score'=>0	
);

$q4r = array($q4r1,$q4r2,$q4r3,$q4r4);

$q4 = array(
			'refid'=>'q4',
			'orderno'=> 4,
			'text'=>'Which one of the following is NOT one of the main causes of post-partum haemorrhage?',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q4r
);


$q5r1 = array(
			'refid'=> "true",
			'orderno'=> 1,
			'text'=>'True',
			'score'=>0	
);
$q5r2 = array(
			'refid'=> "false",
			'orderno'=> 2,
			'text'=>'False',
			'score'=>10	
);

$q5r = array($q5r1,$q5r2);

$q5 = array(
			'refid'=>'q5',
			'orderno'=> 5,
			'text'=>'Misoprostol is a uterotonic in tablet form which needs refrigeration and can be administered without requiring an injection',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q5r
);

$q6r1 = array(
			'refid'=> "immediate",
			'orderno'=> 1,
			'text'=>'Immediately after the delivery of the baby without waiting for the placenta to be delivered',
			'score'=>10	
);
$q6r2 = array(
			'refid'=> "afterpl",
			'orderno'=> 2,
			'text'=>'After placenta is delivered',
			'score'=>0	
);
$q6r3 = array(
			'refid'=> "same",
			'orderno'=> 3,
			'text'=>'At the same time as uterine massage is applied',
			'score'=>0
);
$q6r4 = array(
			'refid'=> "afterma",
			'orderno'=> 4,
			'text'=>'After uterine massage is done',
			'score'=>0
);

$q6r = array($q6r1,$q6r2,$q6r3,$q6r4);

$q6 = array(
			'refid'=>'q6',
			'orderno'=> 6,
			'text'=>'When should you use misoprostol for prevention of post-partum haemorrhage?',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q6r
);

$q7r1 = array(
			'refid'=> "oral400",
			'orderno'=> 1,
			'text'=>'400 micrograms (2 tablets) - oral',
			'score'=>0	
);
$q7r2 = array(
			'refid'=> "sub600",
			'orderno'=> 2,
			'text'=>'600 micrograms (3 tablets) - sublingual',
			'score'=>0	
);
$q7r3 = array(
			'refid'=> "oral600",
			'orderno'=> 3,
			'text'=>'600 micrograms (3 tablets) - oral',
			'score'=>10
);
$q7r4 = array(
			'refid'=> "rec800",
			'orderno'=> 4,
			'text'=>'800 micrograms (4 tablets) - rectal',
			'score'=>0
);

$q7r = array($q7r1,$q7r2,$q7r3,$q7r4);

$q7 = array(
			'refid'=>'q7',
			'orderno'=> 7,
			'text'=>'What is the recommended dose and route of administration of misoprostol for prevention of post-partum haemorrhage?',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q7r
);

$q8r1 = array(
			'refid'=> "a",
			'orderno'=> 1,
			'text'=>'1000 mcg rectal dose is effective for treatment of PPH',
			'score'=>0	
);
$q8r2 = array(
			'refid'=> "b",
			'orderno'=> 2,
			'text'=>'800 mcg sublingual dose is effective for treatment of PPH',
			'score'=>0	
);
$q8r3 = array(
			'refid'=> "c",
			'orderno'=> 3,
			'text'=>'Postpartum hemorrhage treatment should be started when the woman loses &gt;=500 mL blood',
			'score'=>0
);
$q8r4 = array(
			'refid'=> "all",
			'orderno'=> 4,
			'text'=>'All of the above',
			'score'=>10
);

$q8r = array($q8r1,$q8r2,$q8r3,$q8r4);

$q8 = array(
			'refid'=>'q8',
			'orderno'=> 8,
			'text'=>'Which one is correct about the postpartum treatment regimen with Misoprostol?',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q8r
);


$q9r1 = array(
			'refid'=> "true",
			'orderno'=> 1,
			'text'=>'True',
			'score'=>10	
);
$q9r2 = array(
			'refid'=> "false",
			'orderno'=> 2,
			'text'=>'False',
			'score'=>0	
);

$q9r = array($q9r1,$q9r2);

$q9 = array(
			'refid'=>'q9',
			'orderno'=> 9,
			'text'=>'Misoprostol is a very safe and effective uterotonic to be used for the prophylaxis of postpartum hemorrhage when oxytocin is not available.',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q9r
);

$q10r1 = array(
			'refid'=> "true",
			'orderno'=> 1,
			'text'=>'True',
			'score'=>0	
);
$q10r2 = array(
			'refid'=> "false",
			'orderno'=> 2,
			'text'=>'False',
			'score'=>10	
);

$q10r = array($q10r1,$q10r2);

$q10 = array(
			'refid'=>'q10',
			'orderno'=> 10,
			'text'=>'For all deliveries, women should be monitored for blood loss, and health care providers are usually very accurate with their estimates of blood loss.',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q10r
);


$questions = array ($q1,$q2,$q3,$q4,$q5,$q6,$q7,$q8,$q9,$q10);

$json = array (	'refid'=>"PreTest",
					'title'=>"PPH Pre Test",
					'maxscore'=>100,
					'q'=>$questions);

echo json_encode($json);