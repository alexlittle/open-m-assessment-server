<?php
include_once("../config.php");
$PAGE = "developer";
include_once("../includes/header.php");
?>


<h1>Developer Info</h1>

<p>All the code used for the phone app client and the server is available for download.</p>

<p>You can download the code from GitHub:</p>

<ul>
<li><a href="https://github.com/alexlittle/open-m-assessment-client">Phone client application</a></li>
<li><a href="https://github.com/alexlittle/open-m-assessment-server">Server application</a></li>
</ul>


<h2>Future developments</h2>

<p>Here is a list of the features we'd like to work on:</p>

<ul>
<li>Create client apps for other phone operating systems (perhaps use <a href="http://phonegap.com/">PhoneGap</a>?)</li>
<li>Support for more question types, such as multi-select</li>
<li>Support for images/media as part of the questions and/or response options</li>
<li>Import/export to other quiz formats (such as <a href="http://microformats.org/wiki/gift">GIFT</a>)</li>
<li>Private quizzes - so you can select who else is allowed to answer</li>
<li>Better sharing of quizzes - so you're able to email/sms a quiz to your friends</li>
</ul>

<p>Any help with the development is much appreciated!</p>
<?php 
include_once("../includes/footer.php");
?>