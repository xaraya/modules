<?php // $Id$

echo '<html><head>
<style type="text/css">
td.cal   { font-size:11pt; }
a:link    { font-weight:bold; color:#FFFF00; }
a:visited { font-weight:bold; color:#FFFF00; }
a:active  { font-style:italic; color:#FFFF00; }
TD   { font-family : Times New Roman,Times,Arial; font-size : 11pt; }
body {  margin-top:0px;  margin-bottom:0px;  font-family : Times New Roman,Times,Arial;}
p,ul,ol,li,div,td { font-family:Arial, Helvetica, sans-serif; }
</style></head>';

$clang=pnUserGetLang();
$ModName = $GLOBALS['name'];
$path_clang = "modules/".pnVarPrepForOS($ModName)."/pnlang/".$clang."/calendar.php";
$path_elang = "modules/".pnVarPrepForOS($ModName)."/pnlang/eng/calendar.php";

if (file_exists($path_clang)) {
    include $path_clang;
} elseif (file_exists($path_elang)) {
    include $path_elang;
}

print "<body bgcolor=\"".pnModGetVar('todolist', 'BACKGROUND_COLOR'). "\">";

/**
 * creates the HTML for the calender (direct output)
 * 
 * @param $Monat int The month to display
 * @param $Jahr  int The year to display
 */
function Kalender($Monat,$Jahr)
{
    $modurl="modules.php?op=modload&name=todolist&file=calendar";

    $Monatsname = explode(' ', _TODOLIST_MONTH_NAMES);
    $Tag = explode(' ', _TODOLIST_SHORT_DAY_NAMES);
    $KSchrArt = "Verdana,Arial"; /* Schriftart Kalenderkopf */
    $KSchrGroesse = 2;       /* Schriftgroesse 1-7 Kalenderkopf */
    $KSchrFarbe = "#FFFF00";     /* Schriftfarbe Kalenderkopf */
    $Khgrund = "#000066";    /* Hintergrundfarbe Kalenderkopf */
    $TSchrArt = "Verdana,Arial"; /* Schriftart Tagesanzeige */
    $TSchrGroesse = 1;       /* Schriftgroesse 1-7 Tagesanzeige */
    $TSchrFarbe = "#000000";     /* Schriftfarbe Tagesanzeige */
    $Thgrund = "#D0F0F0";    /* Hintergrundfarbe Tagesanzeige */
    $SoFarbe = "#E00000";    /* Schriftfarbe f. Sonntage */
    $Ahgrund = "#FFFFFF";    /* Hintergrundfarbe f. heutigen Tag */

    $jetzt = getdate(time());
    $DieserMonat = $jetzt[mon];
    $DiesesJahr = $jetzt[year];
    $DieserTag = $jetzt[mday];


    $Zeit = mktime(0,0,0,$Monat,1,$Jahr);
    $bla = getdate($Zeit);
    $Start = $bla[wday];
    if($Start > 0) $Start--;
    else $Start = 6;
    $Stop = 31;
    if($Monat==4 ||$Monat==6 || $Monat==9 || $Monat==11 ) --$Stop;
    if($Monat==2)
    {
        $AnzTage=-3;
        $Stop=$Stop + $AnzTage;
        if($Jahr%4==0) $Stop++;
        if($Jahr%100==0) $Stop--;
        if($Jahr%400==0) $Stop++;
    }
    echo "<table border=3 cellpadding=1 cellspacing=1>";
    $Monatskopf = $Monatsname[$Monat-1] . " " . $Jahr;
    $Monatskopf = $Monatskopf . "<BR><a class=cal href=".$modurl."&m=" . ($Monat-1) ."&dj=" . $Jahr .
          ">&#60;&#60;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class=cal href=".
          $modurl."&m=".($Monat+1) ."&dj=" . $Jahr . ">&#62;&#62;</a>";
    SchreibeKopf($Monatskopf,$Khgrund,$KSchrFarbe,$KSchrGroesse,$KSchrArt);
    $Tageszahl = 1;
    for($i=0;$i<=5;$i++)
    {
        echo "<tr>";
        for($j=0;$j<=5;$j++)
        {
            if(($i==0)&&($j < $Start))
                SchreibeZelle("&#160;",$Thgrund,$TSchrFarbe,$TSchrGroesse,$TSchrArt);
            else
            {
                if($Tageszahl > $Stop)
                    SchreibeZelle("&#160;",$Thgrund,$TSchrFarbe,$TSchrGroesse,$TSchrArt);
                else
                {
                    if(($Jahr==$DiesesJahr)&&($Monat==$DieserMonat)&&($Tageszahl==$DieserTag))
                        SchreibeZelle($Tageszahl,$Ahgrund,$TSchrFarbe,$TSchrGroesse,$TSchrArt);
                    else
                        SchreibeZelle($Tageszahl,$Thgrund,$TSchrFarbe,$TSchrGroesse,$TSchrArt);
                    $Tageszahl++;
                }
            }
        }
        if($Tageszahl > $Stop)
            SchreibeZelle("&#160;",$Thgrund,$SoFarbe,$TSchrGroesse,$TSchrArt);
        else
        {
            if(($Jahr==$DiesesJahr)&&($Monat==$DieserMonat)&&($Tageszahl==$DieserTag))
                SchreibeZelle($Tageszahl,$Ahgrund,$SoFarbe,$TSchrGroesse,$TSchrArt);
            else
                SchreibeZelle($Tageszahl,$Thgrund,$SoFarbe,$TSchrGroesse,$TSchrArt);
            $Tageszahl++;
        }
        echo "</tr>";
    }
    echo "</table>";
}

/**
 * writes the head for the calender (direct output)
 *
 * @param $Monatstitel    The name of the month
 * @param $HgFarbe        Background-color
 * @param $SchrFarbe    Font-color
 * @param $SchrGroesse    Font-size
 * @param $SchrArt        Font
 */
function SchreibeKopf($Monatstitel,$HgFarbe,$SchrFarbe,$SchrGroesse,$SchrArt)
{
    $Tag = explode(' ', _TODOLIST_SHORT_DAY_NAMES);
    echo "<tr>";
    echo "<td class=\"cal\" align=center colspan=7 valign=middle bgcolor=" . $HgFarbe . ">";
    echo "<font size=" . $SchrGroesse . " color=" . $SchrFarbe . " face=" . $SchrArt . "><b>";
    echo $Monatstitel;
    echo "</b></font></td></tr>";
    echo "<tr>";
    for($i=0;$i<=6;$i++)
        SchreibeZelle($Tag[$i],$HgFarbe,$SchrFarbe,$SchrGroesse,$SchrArt);
    echo "</tr>";
}

/**
 * writes one cell of the calender-table
 *
 * @param $Inhalt        The content of the cell
 * @param $HgFarbe        Background-color
 * @param $SchrFarbe    Font-color
 * @param $SchrGroesse    Font-size
 * @param $SchrArt        Font
 */
function SchreibeZelle($Inhalt,$HgFarbe,$SchrFarbe,$SchrGroesse,$SchrArt)
{
    echo "<td align=center valign=middle bgcolor=" . $HgFarbe . " class=cal >";
    echo "<font size=" . $SchrGroesse . " color=" . $SchrFarbe . " face=" . $SchrArt . "><b>";
    echo $Inhalt;
    echo "</b></font></td>";
}

// $d = getdate(time());
// $dm = $jetzt[mon] + 1;
// $dj = $jetzt[year];
if ($m == 0) {
    $dj--;
    $m = 12;
}
if ($m > 12) {
    $dj += $m % 12;
    $m = (int)($m / 12);
}
Kalender($m,$dj);

echo '</body></html>';
?>