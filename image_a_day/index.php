<?php
/************************************************************************/
/* Postnuke: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/* Changelog */
/************************************************************************/
/* Babe a Day module  with content from CoolPick.com                    */
/************************************************************************/

if (!eregi("modules.php", $PHP_SELF)) {
    die ("You can't access this file directly...");
}
if (!isset($mainfile)) { 
    include("mainfile.php"); 
}
include("header.php");
?>
<?php
    OpenTable();
 echo "<div align=\"center\"><a href=\"http://coolpick.com/way/cool/syn/babe.html";
  $url = "http://coolpick.com/way/cool/syn/babe.html";
    $lines_array = file($url);
  $lines_string = implode('', $lines_array);
    eregi("7babpol.shtml(.*)<TD ALIGN=center VALIGN=center>", $lines_string, $head);
    $lines = split("\n", $head[0]);
  $x = count($lines);
    for ($i=0;$i<$x;$i++) {
          $again=eregi_replace("</td>", " ", $lines[$i]); 
          $again=eregi_replace("7babpol.shtml", " ", $again);
          $again=eregi_replace("\" >", " ", $again);
//          echo eregi_replace("<IMG src=\"", "<img src=\"".$iurl, $again);
	echo $again;
//    echo $lines[$i]; 
    }
?>
<?php
 echo "</a><br><a href=\"http://coolpick.com/way/cool/syn/babe.html\"> CoolPick.com </></div>";
    CloseTable();
include("footer.php");
?>