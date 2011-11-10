<?php

unset($CONFIG);
$CONFIG = new stdClass;

// include trailing slashes
$CONFIG->homeAddress = "https://localhost/assessment-sc/";
$CONFIG->homePath = "/home/alex/data/websites/assessment-sc/";

// this must be a writable directory
$CONFIG->imagecache = "/tmp/";

$CONFIG->langs = array("en"=>"English", "am"=>"Amharic", "tg"=>"Tigrinya");
$CONFIG->defaultlang = "en";

$CONFIG->dbtype = "mysql";
$CONFIG->dbhost = "localhost";
$CONFIG->dbname = "assessment";
$CONFIG->dbuser = "XXXXXX";
$CONFIG->dbpass = "XXXXXX"; 

$CONFIG->googleanalytics = "";

include_once("setup.php");