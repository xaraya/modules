<?php
/* --------------------------------------------------------------
   $Id: carp.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   CaRP v2.7.5
   Copyright (c) 2002-3 Antone Roundy   http://www.mouken.com/rss/
   Installation & Configuration Manual: http://www.mouken.com/rss/manual/

   Released under the GNU General Public License
   --------------------------------------------------------------*/

$carpversion='2.7.5';

function CarpConfReset() {
	global $carpconf,$carpoptions;
	$carpconf=array(
	'cachepath'=> DIR_FS_ADMIN . 'rss',
	'cacheinterval'=>60,
	'cacheerrorwait'=>30,
	'cachetime'=>'',
	'descriptiontags'=>'b|/b|i|/i|br|p|/p|hr|span|/span|font|/font|img|/img|a|/a',
	'proxyauth'=>'',
	'basicauth'=>'',
	'filterin'=>'',
	'filterout'=>'',
	'linktarget'=>0,
	'showdesc'=>0,
	'maxdesc'=>0,
	'posttruncdesc'=>'<i>... continues</i>',
	'maxitems'=>15,
	'maxtitle'=>80,
	'defaulttitle'=>'(no title)',
	'preitem'=>'',
	'postitem'=>'<br>',
	'preitems'=>'',
	'postitems'=>'',
	'linkclass'=>'',
	'linkstyle'=>'',
	'linkdiv'=>'',
	'showctitle'=>0,
	'showclink'=>0,
	'showcdesc'=>0,
	'maxctitle'=>80,
	'cclass'=>'',
	'cstyle'=>'',
	'cdiv'=>'',
	'encodingin'=>'',
	'encodingout'=>'',
	'linktitles'=>1,
	'maxredir'=>10,
	'timeout'=>15,
	'sendhost'=>1,
	'removebadtags'=>1,
	'outputformat'=>0,

	/* If you modify or override the next line, please post a similar link
	somewhere on your website. If you incorporate CaRP into another product and
	feel the need to change this line, please ensure that there is a similar link
	which will be displayed by default somewhere in your product. Thanks! */

	'poweredby'=>'<br><i><a href="http://www.mouken.com/rss/" target="_blank">Newsfeed display by CaRP</a></i>',

	// replaced by linktarget
	'newwindow'=>0
	);
	
	$carpoptions='|';
	while (list($k,$v)=each($carpconf)) $carpoptions.="$k|";
}

CarpConfReset();

function CarpConf($n,$v) {
	global $carpconf,$carpoptions;
	$n=explode('|',strtolower(preg_replace("/ /",'',$n)));
	for ($i=count($n)-1;$i>=0;$i--) {
		if (strpos($carpoptions,"|$n[$i]|")!==false) $carpconf[$n[$i]]=$v;
		else CarpError("Unknown option ($n[$i]). Please check the spelling of the option name and that the version of CaRP you are using supports this option.",0);
	}
}

function CarpOutput($t) {
	global $carpconf,$carpoutput;
	
	switch ($carpconf['outputformat']) {
	case 1:
		if (!is_array($t)) $t=explode("\n",$t);
		for ($i=0,$j=count($t);$i<$j;$i++) echo 'document.writeln("'.preg_replace('/"/','\"',$t[$i])."\");\n";
		break;
	case 2:
		if (is_array($t)) for ($i=0,$j=count($t);$i<$j;$i++) $carpoutput.=$t[$i];
		else $carpoutput.=$t;
		break;
	default:
		if (is_array($t)) for ($i=0,$j=count($t);$i<$j;$i++) echo $t[$i];
		else echo $t;
	}
}

function CarpError($s,$c=1) {
	global $carpconf;
	CarpOutput("<br>\n[CaRP] $s<br>\n");
	if ($c&&$carpconf['cacheerrorwait']&&strlen($carpconf['cachefile']))
		touch($carpconf['cachefile'],time()+60*($carpconf['cacheerrorwait']-$carpconf['cacheinterval']));
}

function CarpSetCache($cachefile) {
	global $carpconf;
	$cache=0;
	$cachefile=preg_replace("/\.+/",'.',$cachefile);
	$carpconf['cachefile']=$carpconf['cachepath']."/$cachefile";
	if (file_exists($carpconf['cachefile'])) {
		$mtime=filemtime($carpconf['cachefile']);
		$nowtime=time();
		if (strlen($carpconf['cachetime'])) {
			list($hour,$min)=explode(':',$carpconf['cachetime']);
			$limtime=mktime($hour,$min,0);
			$cache=($mtime>$limtime-(($nowtime<$limtime)?86400:0))?1:2;
		} else $cache=(($nowtime-$mtime)<($carpconf['cacheinterval']*60))?1:2;
	} else $cache=2;
	return $cache;
}

// NOTE: These functions are included only for backwards compatibility and will soon be removed.
// Use CarpShow instead.
function ShowRSSPage($url,$cachefile) { CarpShow($url,$cachefile); }
function ShowRSSFeed($url,$cachefile='') { CarpShow($url,$cachefile); }

function CarpFilter($url,$cachefile) { CarpShow($url,$cachefile,0); }

function CarpShow($url,$cachefile='',$showit=1) {
	global $carpconf,$carpoutput;
	$carpoutput='';
	$cache=0;
	if (strlen($cachefile)) $cache=CarpSetCache($cachefile);
	else if (!$showit) {
		CarpError('No cache file indicated when calling CarpFilter or CarpShow with showit=0.',0);
		return 0;
	}
	if ($cache%2==0) {
		require_once dirname(__FILE__).'/carpinc.php';
		GetRSSFeed($url,$cache,$showit);
	} else if ($showit) CarpOutput(file($carpconf['cachefile']));
}

function CarpAggregateSort($a,$b) {
	$na=floor($a);
	$nb=floor($b);
	return ($a==$b)?0:(($a>$b)?-1:1);
}

function CarpAggregate($feeds) {
	global $carpconf,$carpoutput;
	$carpoutput='';
	$fl=explode('|',$feeds);
	$id=array();
	$il=array();
	$j=0;
	for ($i=count($fl)-1;$i>=0;$i--) {
		if ($f=fopen($carpconf['cachepath'].'/'.$fl[$i],'r')) {
			for (;$l=fgets($f,10000);$j++) {
				list($datetime,$il[$j])=explode(':',$l,2);
				$id["$datetime.$j"]=$j;
			}
			fclose($f);
		}
	}
	uksort($id,'CarpAggregateSort');
	CarpOutput($carpconf['preitems']);
	for ($i=0;($i<$carpconf['maxitems'])&&(list($k,$v)=each($id));$i++) CarpOutput($il[$v]);
	CarpOutput($carpconf['postitems'].$carpconf['poweredby']);
}

function CarpCache($url,$cachefile) {
	global $carpconf;
	if (strlen($cachefile)) {
		$cache=CarpSetCache($cachefile);
		if ($cache%2==0) {
			require_once dirname(__FILE__).'/carpinc.php';
			CacheRSSFeed($url);
		}
	} else CarpError('No cache file indicated when calling CarpCache.',0);
}
?>