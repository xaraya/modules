<?php


if (!defined("LOADED_AS_MODULE")) {
         die ("You can't access this file directly...");
     }

include 'mainfile.php';
//$index = 1;


$ModName = basename( dirname( __FILE__ ) );

modules_get_language();

if (!$user){
    	$username="";
    	
    	}else{
    	getusrinfo($user);
	
	}

global $textcolor1,$textcolor2, $user, $cookie, $adminmail, $nukeurl, $sitename, $user, $pntable, $dbconn, $multilingual, $currentlang, $bgcolor2 ;
include("header.php");
include("config.php");
OpenTable();

echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"400\" align=\"center\">";
echo " <tr><td valign=\"top\"><img src=\"".WHERE_IS_PERSO."images\logo.gif\" align=\"right\" alt=\"\" /><div class=\"pn-logo-small\">"._CONTACTQUESTION." $sitename. <br />"._CONTACTQUESTION2."<br />"._CONTACTQUESTION3."<br />"._CONTACTQUESTION4."</div></td>";
echo "</tr>";
echo "<tr><td><form action=\"modules.php?op=modload&amp;name=$ModName&amp;file=pn_contact\" method=\"post\">";
echo "<table align=\"left\" width=\"350\">";
echo "<tr><td width=\"120\" align=\"right\">"._CONTACTNAME."*</td>";
echo "<td align=\"right\"><input name=\"custname\" size=\"30\" value=\"$userinfo[name]\" /></td>";
echo "</tr>";


echo "<tr><td width=\"120\" align=\"right\">"._CONTACTSUBJECT."*</td>";
echo "<td align=\"right\"><select name=\"subjectrequest\">";
echo " 	  <option value=\"General\" selected=\"selected\">General suggestion</option>";
//echo " 	  <option value=\"Contribution request\" selected=\"Upgrade to contributor status\">Upgrade to contributor status</option>";
echo " 	  <option value=\"Service\">Service request</option>";
echo "    <option value=\"Information\">Information request</option>";
//echo " 	  <option value=\"Billing\">Billing</option>";
//echo " 	  <option value=\"Link Request\">Link Request</option>";
echo " 	  <option value=\"General Assistance\">General Assistance</option>";
//echo " 	  <option value=\"WebSite\">Website Issues</option>";
//echo " 	  <option value=\"Advertising\">Advertising</option>";
//echo " 	  <option value=\"Lost Password Request\">Lost Password Request</option>";
//echo " 	  <option value=\"General Issue\">General Issue</option>";
echo " 	  <option value=\"Thanks, $sitename\">Thanks, $sitename !!</option>";
echo " <option value=\"Abuse / Spam Report\">Abuse / Spam Report</option>";
echo " 	    <option value=\"Complaint\">Complaint</option>";
echo "</select>";
echo "</td></tr>";


// echo "<tr><td width=\"120\" align=\"right\">"._CONTACTDEPARTMENT."*</td>";
// echo "<td align=\"right\"><select name=\"subject\">";
// echo " 
// echo " 				  <option value=\"Sales\" selected=\"selected\">Sales</option>";
// echo " 				  <option value=\"Billing\">Billing</option>";
// echo "                   <option value=\"Support\">Support</option>";
// echo "                   <option value=\"Billing\">Billing</option>";
// echo "                   <option value=\"Billing\">Management</option>";
// echo "</select>";
// echo "</td></tr>";



echo "<tr><td width=\"120\" align=\"right\">"._CONTACTEMAIL."*</td>";
echo "<td align=\"right\"><input name=\"email\" size=\"30\" value=\"$userinfo[email]\" /></td>";
echo "</tr>";
echo "<tr><td width=\"120\" align=\"right\">"._CONTACTURL."</td>";
echo "<td align=\"right\"><input name=\"URL\" size=\"30\" value=\"$userinfo[url]\" /></td>";
echo "</tr>";
echo "<tr><td width=\"120\" align=\"right\">"._CONTACTLOCATION."</td>";
echo "<td align=\"right\"><input name=\"location\" size=\"30\" value=\"$userinfo[user_from]\" /></td>";
echo "</tr>";
echo "<tr><td width=\"120\" valign=\"top\" align=\"right\">"._CONTACTCOMPANY."</td>";
echo "<td align=\"right\"><input name=\"company\" size=\"30\" value=\"\" /></td>";
echo "</tr>";
echo "<tr><td colspan=\"2\" align=\"left\">"._CONTACTCOMMENTS."*<br />";
echo "<textarea name=\"Comments\" cols=\"55\" rows=\"10\"></textarea></td>";
echo "</tr>";
echo "<tr><td>* "._CONTACTFIELDS."</td>";
echo "<td> ";
################# CHECKING IP ADRESS ########### Sonntag, 18. Februar 2001
echo "<input type=\"hidden\" name=\"ipadress\" value=\"$REMOTE_ADDR\" />";
echo "<input type=\"hidden\" name=\"referer\" value=\"$HTTP_REFERER\" />";
echo "<p align=\"right\">";
$today = getdate(); 
$month = $today[month]; 
$mday = $today[mday]; 
$year = $today[year]; 
echo "$month $mday, $year"; 
echo "</p><p><input type=\"submit\" value=\""._CONTACTSEND."\" />&nbsp;";
echo "<input type=\"reset\" value=\""._CONTACTCANCEL."\" /><br />";
echo "</p></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
echo "</td></tr></table>";

CloseTable();
 include("footer.php");
?>
