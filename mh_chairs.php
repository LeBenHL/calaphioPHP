<?php
require("include/includes.php");
require("include/Calendar.class.php");
require("include/Template.class.php");
Template::print_head(array());
Template::print_body_header('Home', 'NEWS');
if (!$g_user->is_logged_in()) {
	trigger_error("You must be logged in to view the chairing positions.", E_USER_ERROR);
} else {
	echo <<<HEREDOC


<h2 style="font-size: 1.5em; font-weight: bold; margin-bottom: 1em;"> PMP SEMESTER COMMITTEES </h2>


<font color = "black"> 
<font color="black"> Email the ExComm VP or any of the committee chairs to join. </font> <br>
<br>
<h2 style="font-size: 1.3em; font-weight: bold; border-bottom: solid 1px black;">
Presidential: Karen Wu
</h2>

<div style="max-width: 50em; margin-bottom: 3em; line-height: 1.5;">
	<p style="margin: 1.5em 0px;">
<font color="black"> None! BUT, check out some of the other awesome committees below! </font> 

</p>
</div>

<h2 style="font-size: 1.3em; font-weight: bold; border-bottom: solid 1px black;"> Administrative: Audrey Tsai </h2>
<div style="max-width: 50em; margin-bottom: 3em; line-height: 1.5;">
	<p style="margin: 1.5em 0px;">

<font color="black">

<b>Stylus:</b> Stylus Chair: Patrick Chang, Mitchell Lui, Antony Nguyen <br>
Like writing, photography, art, comics, interviews? Submit to our chapter newsletter! <br><br>

<b>FunPack:</b> Lisa Hoang, Claudia Lim <br>
Help us create the end of the semester yearbook! No design experience needed!<br><br>

</font> </p> </div>

<h2 style="font-size: 1.3em; font-weight: bold; border-bottom: solid 1px black;"> Membership: Nicki Bartak </h2>
<div style="max-width: 50em; margin-bottom: 3em; line-height: 1.5;">
<p style="margin: 1.5em 0px;">

<font color="black">
	
<b>Rush:</b> Joseph Gapuz, Ya-An Hsiung, Dian Jiang, Elizabeth Yuen, James Young<br>
Come help us recruit some AWESOME new pledges! <font color "red">Note: This committee is now closed. 

</font><br><br>
</p>
</div>

<h2 style="font-size: 1.3em; font-weight: bold; border-bottom: solid 1px black;"> Service: Alex Quan </h2>
<div style="max-width: 50em; margin-bottom: 3em; line-height: 1.5;">
	<p style="margin: 1.5em 0px;">
	
<b> Halloween Carnival Committee:</b> Elaine Do, Sophia Du, Kelly Luu, Annie Yu <br>
Help us make crafts and design games for local elementary children in our annual Halloween Carnival!<br><br>
<b> Service Project Committee:</b> Elise Hayashi, Dian Jiang, Ramya Rupanagudi, Hannah Schnell  <br>
Help plan informational workshops, fun exercises, and activities for kids for Healthy Heroes Day on Saturday, November 21st!<br><br>	
</p>
</div>


<h2 style="font-size: 1.3em; font-weight: bold; border-bottom: solid 1px black;">
Finance Chairs: Kelsey Chan
</h2>
<div style="max-width: 50em; margin-bottom: 3em; line-height: 1.5;">
	<p style="margin: 1.5em 0px;">
	
<font color = "black"> None! But you can help the Finance department by signing up for fundraisers! </font> 

</p> </div>

<h2 style="font-size: 1.3em; font-weight: bold; border-bottom: solid 1px black;">
Fellowship Committees: Allison Tong
</h2>

<div style="max-width: 50em; margin-bottom: 3em; line-height: 1.5;">
	<p style="margin: 1.5em 0px;">

<font color = "black"> 
<b>Roll Call:</b> Claudia Lim, Antony Nguyen, Alex Quan <br>
Come rep Gamma Gamma at Sectionals by participating in Roll Call! No dance experience necessary!<br><br>

<b>Hot Spot</b>: Christine Fang, Selena Fung, Eugenia Tang, Estelle Yeung, Tiffany Young <br>
Come help us set up the hottest-spot in town! <br><br>

<b> Banquet </b> : Elaine Do, Anqi Li, Eugenia Tang, Annie Yu<br>
Like to decorate? Plan events? BANQUENT COMMITTEE WANTS YOU! <br>

</font> </p> </div>

<h2 style="font-size: 1.3em; font-weight: bold; border-bottom: solid 1px black;">
Pledgemaster Committees: Joseph Gapuz
</h2>
<div style="max-width: 50em; margin-bottom: 3em; line-height: 1.5;">
	<p style="margin: 1.5em 0px;">

<font color="black">Unfortunately, the pledge committee for this semester is full.</font><br>

</p>
</div>

<h2 style="font-size: 1.3em; font-weight: bold; border-bottom: solid 1px black;">
Historian Committees: Moncarol Wang
</h2>
<div style="max-width: 50em; margin-bottom: 3em; line-height: 1.5;">
	<p style="margin: 1.5em 0px;">

<font color = "black"> 
<b>Scrapbook</b>: Allyssa Rodriguez <br>
Help us make the end-of semester Scrapbook! <br><br></font> 

</font> 


</p>
</div>

HEREDOC;
}
Template::print_body_footer();
Template::print_disclaimer();
?>
