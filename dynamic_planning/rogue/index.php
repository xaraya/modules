<?php

////////////////////////////////////////////////////////////////////
//File:	$Id: s.index.php 1.1 02/06/25 17:44:31-00:00 clnelson $	
//
//This one's for Post Nuke -- Rogue !
//Now a module -- change from 4.4 and mutant
//Now 4.01 Transitional
//Removed most tables (except for ones that make sense)
//Now has MS capability (thanks to Tony Sherman)
//
/*////////////////// Quick Outline //////////////////////////////////

This module depends on two tables 'tracks' and 'tasks'.

Main page list Track and track leaders -- admin is allowed
to add tracks and associate tracks with corresponding categories
and users.

Track page for each track lists tasks and completion information.
The track leader or designate or admin is allowed to edit and add
tasks.  This is handled by the Dynamic:: Authorisation.

Dynamic Planning sheets are created for each task.  Priviledged users
(see above) may edit sheets.

Printable formats are available for most screens.

Dynamic Planning module was created to assist Project Management
teams at the Alaska Division of Public Assistance.

//////////////////////////////////////////////////////////////////////*/

if (!defined("LOADED_AS_MODULE")) {
         die ("You can't access this file directly...");
     }
if(!IsSet($mainfile)) { include ("mainfile.php"); }
$index = 0;

// ADS - These 3 vaiables are needed to allow multiple occurances in Rogue .703 (works on multi-sites)

global $tracks , $tasks ;
$tracks = $pnconfig['prefix']."_tracks" ;
$tasks = $pnconfig['prefix']."_tasks" ;
$local_stories = $pnconfig['prefix']."_stories" ;


//Initialize message
$trackmsg = "Editing";

//Main Project Page -- default
function viewproject() 
{
 global $tracks , $tasks , $local_stories ;
 include("header.php");

  $myts = new myTextSanitizer;
  $result = mysql_query("SELECT trackid, trackname, tracklead, tracktext, trackstatus, trackcat FROM $tracks ORDER BY trackname");
  if(!$result) {
    echo mysql_errno(). ": ".mysql_error(). "<br /><br />"; exit();
  }

  echo "<div style=\"text-align: left;\">";
  echo "<p class=\"pn-title\">Project Track Page</p>";	
  if (authorised(0,"Dynamic::",0,ACCESS_ADMIN)) echo "<p class=\"pn-normal\"><a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=adminproject\">[Edit Project Info]</a></p>";
  echo "<p class=\"pn-normal\">Print Project <a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=projectcomplete\">Summary</a></p>";	
  while (list($trackid, $trackname, $tracklead, $tracktext, $trackstatus, $trackcat) = mysql_fetch_row($result)) {
    $brtt = nl2br($tracktext);
// MOD to use local prefixes	ADS
	
    $result2 = mysql_query("SELECT hometext FROM $local_stories WHERE catid=$trackcat ORDER BY time DESC LIMIT 1");
   list($tracknews) = mysql_fetch_row($result2);
   $tracknews = $myts->makeTareaData4Show($tracknews);
    echo "<p class=\"pn-title\"><a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=view&amp;trackid=$trackid\">$trackname</a></p>
    <div style=\"padding: 2px; border-style: solid; border-width: thin; background-color: white;\">
    <p class=\"pn-normal\" style=\"margin-left: 20px;\">Track Leader: <a class=\"pn-normal\" href=\"user.php?op=userinfo&amp;uname=$tracklead\">$tracklead</a>
     | <a class=\"pn-normal\" href=\"index.php?catid=$trackcat\">Read News</a>
     | <a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=view&amp;trackid=$trackid\">View Tasks</a>
     | <a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=viewsheet&amp;trackid=$trackid\">View Planning Sheets</a>
     | <a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=Submit_News&amp;file=index\">Post News</a>"; 
    if (authorised(0,"Dynamic::","$trackname::",ACCESS_EDIT)) echo " | <a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=edit&amp;trackid=$trackid\">Edit Tasks</a>";
    if (authorised(0,"Dynamic::","$trackname::",ACCESS_EDIT)) echo " | <a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=editsheet&amp;trackid=$trackid\">Edit Sheets</a>";
    echo "</p>
    <p class=\"pn-normal\" style=\"margin-left: 40px;\"><i>$brtt</i></p>
    <p class=\"pn-title\" style=\"margin-left: 20px;\">Most Recent News:</p>
    <p class=\"pn-normal\" style=\"margin-left: 20px;\">$tracknews</p>
    <p class=\"pn-title\" style=\"text-align: right;\">Updated:<span class=\"pn-normal\"> $trackstatus</span>
    <br />Print View: <a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=tracksummary&amp;trackid=$trackid\">Summary</a> |  <a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=trackcomplete&amp;trackid=$trackid\">Planning Sheets</a></p>
    </div><br />";
  }
  echo "<p class=\"pn-normal\">Print Project <a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=projectcomplete\">Summary</a></p>";

  echo "</div>";
  include("footer.php");
	
}

function viewtrack($trackid) 
{
global $tracks , $tasks ;

  include("header.php");

  $result1 = mysql_query("SELECT taskid, title, text, DATE_FORMAT(startdate, '%b %d, %y'), DATE_FORMAT(enddate, '%b %d, %y'), DATE_FORMAT(lastdate, '%b %d, %y'), percent FROM $tasks where trackid='$trackid' ORDER BY startdate ASC");
  if(!$result1) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  $result2 = mysql_query("SELECT trackname, tracklead, tracktext, trackstatus FROM $tracks WHERE trackid='$trackid'");
  if(!$result2) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  list($trackname, $tracklead, $tracktext, $trackstatus) = mysql_fetch_row($result2);

  echo "<div style=\"text-align: left;\">";
  echo "<p class=\"pn-title\"><a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=main\">Project Page</a>: $trackname Track Page</p>";
  if (authorised(0,"Dynamic::","$trackname::",ACCESS_EDIT)) echo "<p class=\"pn-normail\"><a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=edit&amp;trackid=$trackid\">[Edit Track Info]</a></p>";
  $brtt = nl2br($tracktext);
  echo "<p class=\"pn-title\">Track Leader: <a class=\"pn-normal\"href=\"user.php?op=userinfo&amp;uname=$tracklead\">$tracklead</a></p>
  <p class=\"pn-title\">Track Goal and Description:</p><p class=\"pn-normal\"style=\"padding: 2px; border-style: solid; border-width: thin; background-color: white;\">$brtt</p>
  <p class=\"pn-title\">Last Updated: <span class=\"pn-normal\">$trackstatus</span></p>
  <p class=\"pn-title\">Tasks:</p>";
  echo "<table><tr><td>-Task-</td><td>-Start-</td><td>-End-</td><td>-Status-</td></tr>";
  while (list($taskid, $title, $text, $start, $end, $last, $percent) = mysql_fetch_row($result1)) {

    echo "<tr style=\"background-color: white;\">
	  <td><a class=\"pn-normal\"href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=viewsheet&amp;trackid=$trackid#$taskid\">$title</a></td><td>$start</td><td>$end</td><td>$percent %</td>
	  </tr>";
  }
  echo "</table>";
  echo "<p class=\"pn-title\">View Task Definition Sheet: <a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=viewsheet&amp;trackid=$trackid\"> [ click here ]</a></p>";
  echo "<p class=\"pn-title\">Print Track <a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=tracksummary&amp;trackid=$trackid\">[ Summary ]</a> | <a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=trackcomplete&amp;trackid=$trackid\">[ With Planning Sheets ]</a></p>";

  echo "</div>";
  include("footer.php");

}

function edittrack($trackid) 
{
global $tracks , $tasks , $trackmsg;
  include("header.php");

  $result1 = mysql_query("SELECT taskid, title, text, DATE_FORMAT(startdate,'%y-%m-%d'), DATE_FORMAT(enddate,'%y-%m-%d'), DATE_FORMAT(lastdate,'%y-%m-%d'), percent FROM $tasks where trackid='$trackid' ORDER BY startdate ASC");
  if(!$result1) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }

  $result2 = mysql_query("SELECT trackname, tracklead, tracktext, trackstatus FROM $tracks WHERE trackid='$trackid'");
  if(!$result2) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  list($trackname, $tracklead, $tracktext, $trackstatus) = mysql_fetch_row($result2);

  if (authorised(0,"Dynamic::","$trackname::",ACCESS_EDIT)) {
  echo "<div style=\"text-align: left;\">";
  echo "<p class=\"pn-title\"><a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=main\">Project Page</a>: <a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=view&amp;trackid=$trackid\">$trackname Track Page</a> : $trackmsg</p>
  <form action=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=revise&amp;trackid=$trackid\" method=\"post\">
  <p class=\"pn-title\">Track Leader: $tracklead</p>
  <p class=\"pn-title\">Track Goal and Description:</p>
  <p class=\"pn-normal\"><textarea name=\"tracktext\" cols=\"60\" rows=\"6\">$tracktext</textarea></p>
  <p class=\"pn-title\">Last Updated: <span class=\"pn-normal\">$trackstatus</span></p>
  <p class=\"pn-title\"><input type=\"submit\" value=\"Update Description / Time Stamp \"></p>
  </form>";
  echo "<p class=\"pn-title\">Track Tasks</p>
  <table>
  <tr><td>-title-</td><td>-start-</td><td>-end-</td><td>-%-</td><td>-update-</td></tr>\n";
  while (list($taskid, $title, $text, $start, $end, $last, $percent) = mysql_fetch_row($result1)) {

    echo "<form action=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=update&amp;trackid=$trackid\" method=\"post\">
    <tr>
    <input type=\"hidden\" name=\"taskid\" value=\"$taskid\">
    <td><input type=\"text\" name=\"title\"  size=\"26\" value=\"$title\"></td>
    <td><input type=\"text\" name=\"start\" size=\"8\" value=\"$start\"></td>
    <td><input type=\"text\" name=\"end\" size=\"8\" value=\"$end\"></td>
    <td><input type=\"text\" name=\"percent\" size=\"3\" value=\"$percent\"></td>
    <td><input type=\"submit\" value=\"Update\"></td>
    </tr>
    </form>";
  }
  echo "<form action=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=add&amp;trackid=$trackid\" method=\"post\">
  <tr>
  <td><input type=\"text\" name=\"title\" size=\"26\" value=\"\"></td>
  <td><input type=\"text\" name=\"start\" size=\"8\" value=\"yy-mm-dd\"></td>
  <td><input type=\"text\" name=\"end\" size=\"8\" value=\"yy-mm-dd\"></td>
  <td><input type=\"text\" name=\"percent\" size=\"3\" value=\"##\"></td>
  <td><input type=\"submit\" value=\"Add\"></td>
  </tr>
  </form>
  </table>";
  echo "<p class=\"pn-sub\">Note: All dates are YY-MM-DD.</p>
  <p class=\"pn-title\">Edit Task Definition Sheet: <a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=editsheet&amp;trackid=$trackid\">click here</a></p>
  <p class=\"pn-normal\">Please contact site admin if you have any questions.</p>
  <p class=\"pn-normal\"><a class=\"pn-normal\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=view&amp;trackid=$trackid\">[Return to Track Page]</a></p>";

  echo "</div>";
  } else { echo "<p>You are not authorised to view this page.</p>";}
  include("footer.php");

}

function updatetask($trackid) 
{

  global $trackmsg, $taskid, $title, $start, $end, $percent, $tracks , $tasks ;
  if (authorised(0,"Dynamic::","::",ACCESS_EDIT)) {
  mysql_query("UPDATE $tasks SET title='$title', startdate='$start', enddate='$end', percent='$percent' WHERE taskid=$taskid");
  mysql_query("UPDATE $tracks SET trackstatus=now() WHERE trackid=$trackid");
  $trackmsg = "Task Updated!";
  } else { echo "<p>You are not authorised to view this page.</p>";}
}


function addtask($trackid) 
{

  global $trackmsg, $title, $start, $end, $percent, $tracks , $tasks ;
  
  if (authorised(0,"Dynamic::","::",ACCESS_EDIT)) {
  mysql_query("INSERT into $tasks values(NULL, '$trackid', '$title', NULL, '$start', '$end', NULL, '$percent', NULL, NULL)");
  mysql_query("UPDATE $tracks SET trackstatus=now() WHERE trackid=$trackid");
  $trackmsg = "Task Added!";
  } else { echo "<p>You are not authorised to update this page.</p>";}
}

function updatetrack($trackid) 
{

  global $trackmsg, $tracktext, $trackstatus, $tracks , $tasks ;
  if (authorised(0,"Dynamic::","::",ACCESS_EDIT)) {
  mysql_query("UPDATE $tracks SET tracktext='$tracktext', trackstatus=now() WHERE trackid=$trackid");
  $trackmsg = "Track Updated!";
  } else { echo "<p>You are not authorised to update this page.</p>";}
}

function viewsheet($trackid) 
{
global $tracks , $tasks ;

  include("header.php");

  $result1 = mysql_query("SELECT taskid, title, text, DATE_FORMAT(startdate, '%b %d, %y'), DATE_FORMAT(enddate, '%b %d, %y'), DATE_FORMAT(lastdate, '%b %d, %y'), percent, steps, team FROM $tasks where trackid='$trackid' ORDER BY startdate ASC");
  if(!$result1) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  $result2 = mysql_query("SELECT trackname, tracklead, tracktext, trackstatus FROM $tracks WHERE trackid='$trackid'");
  if(!$result2) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  list($trackname, $tracklead, $tracktext, $trackstatus) = mysql_fetch_row($result2);

  echo "<div style=\"text-align: left;\">";
  echo "<p class=\"pn-title\"><a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=main\">Project Page</a>: <a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=view&amp;trackid=$trackid\">$trackname Track Page</a>: Dynamic Planning Sheet</p>";
  if (authorised(0,"Dynamic::","$trackname::",ACCESS_EDIT)) echo "<p class=\"pn-normal\"><a class=\"pn-normal\"href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=editsheet&amp;trackid=$trackid\">[Edit Sheet Info]</a></p>";
  echo "<p class=\"pn-title\">Print Track <a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=tracksummary&amp;trackid=$trackid\">[ Summary ]</a> | <a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=trackcomplete&amp;trackid=$trackid\">[ With Planning Sheets ]</a></p>";
  while (list($taskid, $title, $text, $start, $end, $last, $percent, $steps, $team) = mysql_fetch_row($result1)) {
    $brtxt = nl2br($text); $brss = nl2br($steps); $brst = nl2br($team);
    echo "<p class=\"pn-title\">Task: $title</p><a name=\"$taskid\"></a>
    <div style=\"padding: 2px; border-style: solid; border-width: thin; background-color: white;\">
    <p class=\"pn-title\">Task Result - What will be accomplished as a result of completing this task?</p>
    <p class=\"pn-normal\"style=\"margin-left: 20px;\">$brtxt</p>
    <p class=\"pn-title\">Brief summary of steps to complete task:</p>
    <p class=\"pn-normal\" style=\"margin-left: 20px;\">$brss</p>
    <p class=\"pn-title\">Team:</p>
    <p class=\"pn-normal\" style=\"margin-left: 20px;\">$brst</p>
    </div>";
  }
  echo "</div>";
  include("footer.php");
}

function editsheet($trackid) 
{

  global $trackmsg, $tracks , $tasks ;
  include("header.php");
  $result1 = mysql_query("SELECT taskid, title, text, DATE_FORMAT(startdate,'%y-%m-%d'), DATE_FORMAT(enddate,'%y-%m-%d'), DATE_FORMAT(lastdate,'%y-%m-%d'), percent, steps, team FROM $tasks where trackid='$trackid' ORDER BY startdate ASC");
  if(!$result1) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  $result2 = mysql_query("SELECT trackname, tracklead, tracktext, trackstatus FROM $tracks WHERE trackid='$trackid'");
  if(!$result2) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  list($trackname, $tracklead, $tracktext, $trackstatus) = mysql_fetch_row($result2);

  if (authorised(0,"Dynamic::","$trackname::",ACCESS_EDIT)) {
  echo "<div style=\"text-align: left;\">";
  echo "<p class=\"pn-title\"><a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=main\">Project Page</a>: <a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=view&amp;trackid=$trackid\">$trackname Track Page</a>: <a class=\"pn-title\" href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=viewsheet&amp;trackid=$trackid\">Dynamic Planning Sheet</a> : $trackmsg</p>";
  while (list($taskid, $title, $text, $start, $end, $last, $percent, $steps, $team) = mysql_fetch_row($result1)) {
    echo "<p class=\"pn-title\">Task: $title</p>
    <form action=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=updatesheet&amp;trackid=$trackid\" method=\"post\">
    <input type=\"hidden\" name=\"taskid\" value=\"$taskid\">
    <p class=\"pn-title\">Task Result - What will be accomplished as a result of completing this task?</p>
    <p class=\"pn-normal\"><textarea name=\"text\" cols=\"60\" rows=\"6\">$text</textarea></p>
    <p class=\"pn-title\">Brief summary of steps to complete task:</p>
    <p class=\"pn-normal\"><textarea name=\"steps\" cols=\"60\" rows=\"8\">$steps</textarea></p>
    <p class=\"pn-title\">Team:</p>
    <p class=\"pn-normal\"><textarea name=\"team\" cols=\"60\" rows=\"3\">$team</textarea></p>
    <p class=\"pn-normal\"><input type=\"submit\" value=\"Update Task Item\"></p>
    </form>";
  }

  echo "</div>";
  } else { echo "<p>You are not authorised to view this page.</p>";}
  include("footer.php");
}

function updatesheet($trackid) 
{

  global $trackmsg, $taskid, $text, $steps, $team, $tracks , $tasks ;
  
  if (authorised(0,"Dynamic::","::",ACCESS_EDIT)) {
  mysql_query("UPDATE $tasks SET text='$text', steps='$steps', team='$team' WHERE taskid=$taskid");
  mysql_query("UPDATE $tracks SET trackstatus=now() WHERE trackid=$trackid");
  $trackmsg = "Sheet Updated!";
  } else { echo "<p>You are not authorised to update this page.</p>";}
}

function adminproject() 
{

  global $trackmsg, $tracks , $tasks ;
  include("header.php");
  if (authorised(0,"Dynamic::",0,ACCESS_ADMIN)) {
  $result = mysql_query("SELECT trackid, trackname, tracklead, trackcat FROM $tracks");
  if(!$result) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  echo "<div style=\"text-align: left;\">";
  echo "<h3><a href=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=main\">Project Page</a> : $trackmsg</h3>";
  echo"<div style=\"padding: 2px; border-style: solid; border-width: thin; background-color: white;\">
       <p>This page allows you to create new tracks for your project.  You also will want to associate a track with the corresponding category ID number.  Finally, chose username to be displayed for the project lead (this should be a UID).</p>
       <p>For starters, only the admin can create or add tracks, tasks, or do any editting.  You should create group or user permissions for each track leader.  For instance, I have given each track leader EDIT permission, Component: Dynamic::, and Instance: Track Name::.  This allows each track leader to edit their own track only.<p>
       <p>I hope your find this script useful.</p></div>";
  while (list($trackid, $trackname, $tracklead, $trackcat) = mysql_fetch_row($result)) {
    echo "		
    <form action=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=updateproject&amp;trackid=$trackid\" method=\"post\">
    <input type=\"hidden\" name=\"trackid\" value=\"$trackid\"></p>
    <p>Track Name: <input type=\"text\" name=\"trackname\" size=\"20\" value=\"$trackname\"></p>
    <p>Track Lead: <input type=\"text\" name=\"tracklead\" size=\"20\" value=\"$tracklead\"></p>
    <p>News Category ID #: <input type=\"text\" name=\"trackcat\" size=\"6\" value=\"$trackcat\"></p>
    <p><input type=\"submit\" value=\"Update Track\"></p>
    </form>";
  }
  echo "<h3>Add Track:</h3>
  <form action=\"modules.php?op=modload&amp;name=NS-Dynamic_Planning&amp;file=index&amp;func=addtrack\" method=\"post\">
  <p>Track Name: <input type=\"text\" name=\"trackname\" size=\"20\" value=\"\"></p>
  <p>Track Lead: <input type=\"text\" name=\"tracklead\" size=\"20\" value=\"\"></p>
  <p>News Category ID #: <input type=\"text\" name=\"trackcat\" size=\"6\" value=\"\"></p>
  <p><input type=\"submit\" value=\"Add Track\"></p>";

  echo "</div>";
  } else { echo "<p>You are not authorised to view this page.</p>";}
  include("footer.php");
}

function updateproject($trackid) 
{

  global $trackmsg, $trackid, $trackname, $tracklead, $trackcat, $tracks , $tasks ;
  if (authorised(0,"Dynamic::",0,ACCESS_ADMIN)) {
  mysql_query("UPDATE $tracks SET trackname='$trackname', tracklead='$tracklead', trackcat='$trackcat' WHERE trackid=$trackid");
  $trackmsg = "Track Updated!";
  } else { echo "<p>You are not authorised to update this page.</p>";}
}

function addtrack() 
{

  global $trackmsg, $trackname, $tracklead, $trackcat, $tracks , $tasks ;
  if (authorised(0,"Dynamic::",0,ACCESS_ADMIN)) {
  mysql_query("INSERT into $tracks values(NULL, '$trackname', '$tracklead', NULL, NULL, '$trackcat')");
  $trackmsg = "Track Added!";
  } else { echo "<p>You are not authorised to edit this page.</p>";}
}

function printtracksummary($trackid) 
{
global $tracks , $tasks ;

  $result1 = mysql_query("SELECT taskid, title, text, DATE_FORMAT(startdate, '%b %d, %y'), DATE_FORMAT(enddate, '%b %d, %y'), DATE_FORMAT(lastdate, '%b %d, %y'), percent FROM $tasks where trackid='$trackid' ORDER BY startdate ASC");
  if(!$result1) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  $result2 = mysql_query("SELECT trackname, tracklead, tracktext, trackstatus FROM $tracks WHERE trackid='$trackid'");
  if(!$result2) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  list($trackname, $tracklead, $tracktext, $trackstatus) = mysql_fetch_row($result2);
  $brtt = nl2br($tracktext);
  echo "
  <!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
  <html>
  <head>
  <title>$trackname Track Page</title>
  </head>
  <body>
  <div style=\"width: 650px; padding: 2px; border-style: solid; border-width: thin;\">
  <h3>Track: $trackname</h3>
  <h3>Track Leader: $tracklead</h3>
  <h3>Track Goal and Description:</h3>
  <p style=\"margin-left: 20px;\">$brtt</p>
  <p>Last Updated: $trackstatus</p>
  <h3>Tasks:</h3>";
  echo "
  <table width=\"100%\"><tr><td>-Task-</td><td>-Start-</td><td>-End-</td><td>-%-</td></tr>
  ";
  while (list($taskid, $title, $text, $start, $end, $last, $percent) = mysql_fetch_row($result1)) {
    echo "
    <tr><td>$title</td><td>$start</td><td>$end</td><td>$percent %</td></tr>
    ";
  }
  echo "</table>";
  echo "</div></body></html>";
}

function printtrackcomplete($trackid) 
{
global $tracks , $tasks ;

  echo "
  <!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
  <html>
  <head>
  <title>Print Track Page</title>
  </head>
  <body>
  <div style=\"width: 650px; padding: 2px; border-style: solid; border-width: thin;\">
  ";
  $result1 = mysql_query("SELECT taskid, title, text, DATE_FORMAT(startdate, '%b %d, %y'), DATE_FORMAT(enddate, '%b %d, %y'), DATE_FORMAT(lastdate, '%b %d, %y'), percent FROM $tasks where trackid='$trackid' ORDER BY startdate ASC");
  if(!$result1) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  $result2 = mysql_query("SELECT trackname, tracklead, tracktext, trackstatus FROM $tracks WHERE trackid='$trackid'");
  if(!$result2) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  $result3 = mysql_query("SELECT taskid, title, text, DATE_FORMAT(startdate, '%b %d, %y'), DATE_FORMAT(enddate, '%b %d, %y'), DATE_FORMAT(lastdate, '%b %d, %y'), percent, steps, team FROM $tasks where trackid='$trackid' ORDER BY startdate ASC");
  if(!$result1) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  list($trackname, $tracklead, $tracktext, $trackstatus) = mysql_fetch_row($result2);

  $brtt = nl2br($tracktext);
  echo "
  <h3>Track: $trackname</h3>
  <h3>Track Leader: $tracklead</h3>
  <h3>Track Goal and Description:</h3>
  <p style=\"margin-left: 20px;\">$brtt</p>
  <p>Last Updated: $trackstatus</p>
  <h3>Tasks:</h3>
  ";
  echo "<table width=\"100%\"><tr><td>-Task-</td><td>-Start-</td><td>-End-</td><td>-%-</td></tr>
  ";
  while (list($taskid, $title, $text, $start, $end, $last, $percent) = mysql_fetch_row($result1)) {
    echo "
    <tr><td>$title</td><td>$start</td><td>$end</td><td>$percent %</td></tr>
    ";
  }
  echo "</table>";
  while (list($taskid, $title, $text, $start, $end, $last, $percent, $steps, $team) = mysql_fetch_row($result3)) {
    $brtxt = nl2br($text); $brss = nl2br($steps); $brst = nl2br($team);
    echo "<h3>Task: $title</h3>
    <div style=\"padding: 2px; border-style: solid; border-width: thin; background-color: white;\">
    <p>Task Result - What will be accomplished as a result of completing this task?</p>
    <p style=\"margin-left: 20px;\">$brtxt</p>
    <p>Brief summary of steps to complete task:</p>
    <p style=\"margin-left: 20px;\">$brss</p>
    <p>Team:</p>
    <p style=\"margin-left: 20px;\">$brst</p>
    </div>";
  }
  echo "
  </div></body></html>
  ";
}

function printprojectcomplete() 
{
	
  global $sitename, $tracks , $tasks ;
  $result = mysql_query("SELECT trackid, trackname, tracklead, trackstatus FROM $tracks ORDER BY trackname");
  if(!$result) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  echo "
  <!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
  <html>
  <head>
  <title>$sitename Project Summary -- with Tasks</title>
  </head>
  <body>
  <div style=\"width: 650px; padding: 2px; border-style: solid; border-width: thin;\">
  <h3>$sitename Project Summary -- with Tasks</h3>";
  while (list($trackid, $trackname, $tracklead, $trackstatus) = mysql_fetch_row($result)) {
    echo "<div style=\"padding: 10px; border-style: none; border-width: thin; background-color: white;\">
    <h3>Track: $trackname</h3>
    <h3>Track Leader: $tracklead</h3>
    <p>Last Updated: $trackstatus</p>";
    $result1 = mysql_query("SELECT taskid, title, text, DATE_FORMAT(startdate, '%b %d, %y'), DATE_FORMAT(enddate, '%b %d, %y'), DATE_FORMAT(lastdate, '%b %d, %y'), percent FROM $tasks where trackid='$trackid' ORDER BY startdate ASC");
    if(!$result1) {
      echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
    }
    echo"<h3>Tasks:</h3>";
    echo "<table width=\"100%\"><tr><td>-Task-</td><td>-Start-</td><td>-End-</td><td>-%-</td></tr>";
    while (list($taskid, $title, $text, $start, $end, $last, $percent) = mysql_fetch_row($result1)) {
      echo "<tr><td>$title</td><td>$start</td><td>$end</td><td>$percent %</td></tr>";
    }
    echo "</table>";
    echo "</div>";
  }
  echo "</div></body></html>";
}

function printprojectsummary() 
{
  //This function isn't really used anymore
	
  global $sitename, $tracks , $tasks ;
  $result = mysql_query("SELECT trackname, tracklead, trackstatus FROM $tracks ORDER BY trackname");
  if(!$result) {
    echo mysql_errno(). ": ".mysql_error(). "<br />"; exit();
  }
  echo "
  <!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
  <html>
  <head>
  <title>$sitename Project Summary</title>
  </head>
  <body>
  <div style=\"width: 650px; padding: 2px; border-style: solid; border-width: thin;\">
  <h3>$sitename Project Summary</h3>";
  while (list($trackname, $tracklead, $trackstatus) = mysql_fetch_row($result)) {
    echo "<div style=\"padding: 10px; border-style: none; border-width: thin; background-color: white;\">
    <h3>Track: $trackname</h3>
    <h3>Track Leader: $tracklead</h3>
    <p>Last Updated: $trackstatus</p>
    </div>";
  }
  echo "</div></body></html>";
}

switch($func) {

  case "main":
    viewproject();
    break;

  case "view":
    if ($trackid == 0 OR $trackid == "") {
      Header("Location: index.php");
    }
    viewtrack($trackid);
    break;
    
  case "edit":
    if ($trackid == 0 OR $trackid == "") {
      Header("Location: index.php");
    }
    edittrack($trackid);
    break;
    
  case "update":
    if ($trackid == 0 OR $trackid == "") {
      Header("Location: index.php");
    }
    updatetask($trackid);
    edittrack($trackid);
    break;

  case "add":
    if ($trackid == 0 OR $trackid == "") {
      Header("Location: index.php");
    }
    addtask($trackid);
    edittrack($trackid);
    break;

  case "revise":
    if ($trackid == 0 OR $trackid == "") {
      Header("Location: index.php");
    }
    updatetrack($trackid);
    edittrack($trackid);
    break;

  case "editsheet":
    if ($trackid == 0 OR $trackid == "") {
      Header("Location: index.php");
    }
    editsheet($trackid);
    break;

  case "updatesheet":
    if ($trackid == 0 OR $trackid == "") {
      Header("Location: index.php");
    }
    updatesheet($trackid);	
    editsheet($trackid);
    break;

  case "viewsheet":
    if ($trackid == 0 OR $trackid == "") {
      Header("Location: index.php");
    }
    viewsheet($trackid);
    break;

  case "adminproject":
    adminproject();
    break;

  case "updateproject":
    if ($trackid == 0 OR $trackid == "") {
      Header("Location: index.php");
    }
    updateproject($trackid);	
    adminproject();
    break;

  case "addtrack":
    addtrack();
    adminproject();
    break;

  case "tracksummary":
    if ($trackid == 0 OR $trackid == "") {
      Header("Location: index.php");
    }
    printtracksummary($trackid);
    break;

  case "trackcomplete":
    if ($trackid == 0 OR $trackid == "") {
      Header("Location: index.php");
    }
    printtrackcomplete($trackid);
    break;

  case "projectsummary":
    printprojectsummary();
    break;

  case "projectcomplete":
    printprojectcomplete();
    break;

  default:
    viewproject();
    break;
}
?>