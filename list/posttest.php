<?php
header("Content-type:text/plain;Charset:UTF-8");

$questions = array();

$q1r1 = array(
			'refid'=> "a",
			'orderno'=> 1,
			'text'=>'It shortens the duration of the third stage of labor',
			'score'=>0	
);
$q1r2 = array(
			'refid'=> "b",
			'orderno'=> 2,
			'text'=>'It decreases the blood loss during delivery',
			'score'=>0	
);
$q1r3 = array(
			'refid'=> "c",
			'orderno'=> 3,
			'text'=>'It is a recommended practice for all deliveries by the World Health Organization',
			'score'=>0	
);
$q1r4 = array(
			'refid'=> "d",
			'orderno'=> 4,
			'text'=>'All of the above',
			'score'=>10	
);

$q1r = array($q1r1,$q1r2,$q1r3,$q1r4);

$q1 = array(
			'refid'=>'q1',
			'orderno'=> 1,
			'text'=>'Which one of the following is true about active management of the third stage of labor?',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q1r
);


$q2r1 = array(
			'refid'=> "a",
			'orderno'=> 1,
			'text'=>'Use of a uterotonic',
			'score'=>0	
);
$q2r2 = array(
			'refid'=> "b",
			'orderno'=> 2,
			'text'=>'Cord clamping',
			'score'=>10	
);
$q2r3 = array(
			'refid'=> "c",
			'orderno'=> 3,
			'text'=>'Uterine massage',
			'score'=>0	
);
$q2r4 = array(
			'refid'=> "d",
			'orderno'=> 4,
			'text'=>'Controlled cord traction',
			'score'=>0	
);

$q2r = array($q2r1,$q2r2,$q2r3,$q2r4);

$q2 = array(
			'refid'=>'q2',
			'orderno'=> 2,
			'text'=>'Which one of the following is NOT a component of the Active Management of Third Stage Labor (AMTSL)?',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q2r
);

$q3r1 = array(
			'refid'=> "a",
			'orderno'=> 1,
			'text'=>'Misoprostol',
			'score'=>0	
);
$q3r2 = array(
			'refid'=> "b",
			'orderno'=> 2,
			'text'=>'Oxytocin',
			'score'=>10	
);
$q3r3 = array(
			'refid'=> "c",
			'orderno'=> 3,
			'text'=>'Ergometrine',
			'score'=>0	
);
$q3r4 = array(
			'refid'=> "d",
			'orderno'=> 4,
			'text'=>'All of the above',
			'score'=>0	
);

$q3r = array($q3r1,$q3r2,$q3r3,$q3r4);

$q3 = array(
			'refid'=>'q3',
			'orderno'=> 3,
			'text'=>'Which uterotonic can be used for the active management of the third stage of labor?',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q3r
);

$q4r1 = array(
			'refid'=> "a",
			'orderno'=> 1,
			'text'=>'Uterine atony',
			'score'=>0	
);
$q4r2 = array(
			'refid'=> "b",
			'orderno'=> 2,
			'text'=>'Genital trauma/laceration',
			'score'=>0	
);
$q4r3 = array(
			'refid'=> "c",
			'orderno'=> 3,
			'text'=>'Congenital anomalies of the baby',
			'score'=>10	
);
$q4r4 = array(
			'refid'=> "d",
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
			'refid'=> "a",
			'orderno'=> 1,
			'text'=>'It is the first line of therapy for the prevention of postpartum hemorrhage',
			'score'=>0	
);
$q5r2 = array(
			'refid'=> "b",
			'orderno'=> 2,
			'text'=>'It is heat stable',
			'score'=>10	
);
$q5r3 = array(
			'refid'=> "c",
			'orderno'=> 3,
			'text'=>'It requires injection',
			'score'=>0
);
$q5r4 = array(
			'refid'=> "d",
			'orderno'=> 4,
			'text'=>'It can be used by both intravenous or intramuscular routes',
			'score'=>0	
);

$q5r = array($q5r1,$q5r2,$q5r3,$q5r4);

$q5 = array(
			'refid'=>'q5',
			'orderno'=> 5,
			'text'=>'Which one of the following is NOT correct for oxytocin?',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q5r
);

$q6r1 = array(
			'refid'=> "a",
			'orderno'=> 1,
			'text'=>'Intradermal',
			'score'=>10	
);
$q6r2 = array(
			'refid'=> "b",
			'orderno'=> 2,
			'text'=>'Oral',
			'score'=>0	
);
$q6r3 = array(
			'refid'=> "c",
			'orderno'=> 3,
			'text'=>'Sublingual',
			'score'=>0
);
$q6r4 = array(
			'refid'=> "d",
			'orderno'=> 4,
			'text'=>'Rectal',
			'score'=>0	
);

$q6r = array($q6r1,$q6r2,$q6r3,$q6r4);

$q6 = array(
			'refid'=>'q6',
			'orderno'=> 6,
			'text'=>'Which one of the following is NOT a route for using misoprostol?',
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

$q9r1 =  array(
			'refid'=> "afterpl",
			'orderno'=> 1,
			'text'=>'After placenta is delivered',
			'score'=>0	
);
$q9r2 = array(
			'refid'=> "immediate",
			'orderno'=> 2,
			'text'=>'Immediately after the delivery of the baby without waiting for the placenta to be delivered',
			'score'=>10	
);
$q9r3 = array(
			'refid'=> "same",
			'orderno'=> 3,
			'text'=>'At the same time as uterine massage is applied',
			'score'=>0
);
$q9r4 = array(
			'refid'=> "afterma",
			'orderno'=> 4,
			'text'=>'After uterine massage is done',
			'score'=>0
);

$q9r = array($q9r1,$q9r2,$q9r3,$q9r4);

$q9 = array(
			'refid'=>'q9',
			'orderno'=> 9,
			'text'=>'When should you use misoprostol for prevention of post-partum haemorrhage?',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q9r
);


$q10r1 =  array(
			'refid'=> "a",
			'orderno'=> 1,
			'text'=>'The most common side effects are shivering and temperature elevations',
			'score'=>0	
);
$q10r2 = array(
			'refid'=> "b",
			'orderno'=> 2,
			'text'=>'Nausea and vomiting can be caused by the administration of misoprostol',
			'score'=>0	
);
$q10r3 = array(
			'refid'=> "c",
			'orderno'=> 3,
			'text'=>'Side effects usually last up to 24 hours',
			'score'=>10
);
$q10r4 = array(
			'refid'=> "afterma",
			'orderno'=> 4,
			'text'=>'Most side effects do not require treatment and resolve spontaneously',
			'score'=>0
);

$q10r = array($q10r1,$q10r2,$q10r3,$q10r4);

$q10 = array(
			'refid'=>'q10',
			'orderno'=> 10,
			'text'=>'Which one of the following statements is NOT correct about the side effects of misoprostol?',
			'score'=>10,
			'type'=>'select1',
			'r'=>$q10r
);

$questions = array ($q1,$q2,$q3,$q4,$q5,$q6,$q7,$q8,$q9,$q10);

$json = array (
					'refid'=>"PostTest",
					'title'=>"PPH Post Test",
					'maxscore'=>100,
					'q'=>$questions);

echo json_encode($json);