<?php
/* --------------------------------------------------------------
   $Id: carpinc.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

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

class RSSParser {
	var $insideitem=0;
	var $insidechannel=0;
	var $tag='';
	var $title='';
	var $description='';
	var $link='';
	var $pubdate='';
	var $dcdate='';
	var $ctitle='';
	var $cdescription='';
	var $clink='';
	var $itemcount=0;
	var $top='';
	var $body='';
	var $showit;
	var $tagpairs;
	var $filterin;
	var $filterout;
	var $filterinfield;
	var $filteroutfield;
	var $linktargets=array('',' target="_blank"',' target="_top"');
	
	function CheckFilter($lookfor,$field) {
		if (strlen($field)) {
			if (strpos(strtolower($this->$field),$lookfor)!==false) return 1;
		} else {
			if (strpos(strtolower($this->title.' '.$this->description),$lookfor)!==false) return 1;
		}
		return 0;
	}

	function startElement($parser,$tagName,$attrs) {
		$this->tag=$tagName;
		if ($tagName=="ITEM") $this->insideitem=1;
		if ($tagName=="CHANNEL") $this->insidechannel=1;
		else if ($this->insidechannel&&!(($tagName=="TITLE")||($tagName=="DESCRIPTION")||($tagName=="LINK")||
			($tagName=="PUBDATE")||($tagName=="DC:DATE"))) $this->insidechannel++;
	}

	function endElement($parser,$tagName) {
		global $carpconf;
		if ($tagName=="ITEM") {
			if ($this->itemcount<$carpconf['maxitems']) {
				$filterblock=0;
				if (count($this->filterin)) {
					$filterblock=1;
					for ($i=count($this->filterin)-1;$i>=0;$i--) {
						if ($this->CheckFilter($this->filterin[$i],$this->filterinfield[$i])) {
							$filterblock=0;
							break;
						}
					}
				}
				if (count($this->filterout)&&!$filterblock) {
					for ($i=count($this->filterout)-1;$i>=0;$i--) {
						if ($this->CheckFilter($this->filterout[$i],$this->filteroutfield[$i])) {
							$filterblock=1;
							break;
						}
					}
				}
				if (!$filterblock) {
					$thisitem='';
					$fulltitle=htmlspecialchars($this->title);
					if ($didTrunc=(strlen($this->title)>$carpconf['maxtitle'])) $this->title=substr($this->title,0,$carpconf['maxtitle']-3).'...';
					$this->title=htmlspecialchars(trim($this->title));
					if (!strlen($this->title)) $this->title=$carpconf['defaulttitle'];
					$thisitem.=$carpconf['preitem'].$carpconf['linkdiv'];
					$thisitem.="<a href=\"".trim($this->link)."\"".$this->linktargets[$carpconf['linktarget']].
						(strlen($carpconf['linkclass'])?(' class="'.$carpconf['linkclass'].'"'):'').
						(strlen($carpconf['linkstyle'])?(' style="'.$carpconf['linkstyle'].'"'):'').
						((($carpconf['linktitles']&&$didTrunc)||($carpconf['linktitles']==2))?" title=\"$fulltitle\"":'').
						'>'.$this->title.'</a>'.(strlen($carpconf['linkdiv'])?"</div>\n":($carpconf['showdesc']?"<br>\n":''));
					if ($carpconf['showdesc']) {
						if (strlen($carpconf['desctags'])) $adddesc=trim(preg_replace("#<(?!".$carpconf['desctags'].")(.*?)>#is",($carpconf['removebadtags']?'':"&lt;\\1\\2&gt;"),$this->description));
						else $adddesc=trim(preg_replace("#<(.*?)>#s",($carpconf['removebadtags']?'':"&lt;\\1&gt;"),$this->description));
						if ($carpconf['maxdesc']&&(strlen(preg_replace("/<.*?>/",'',$adddesc))>$carpconf['maxdesc'])) {
							$didTrunc=1;
							for ($gotchars=$i=0,$add='';$gotchars<$carpconf['maxdesc'];) {
								$add.=substr($adddesc,$i,($j=$carpconf['maxdesc']-$gotchars));
								$k=0;
								if ((($fo=strrpos($add,'<'))>($fc=strrpos($add,'>')))||(($fo!==false)&&($fc===false)))
									$add.=substr($adddesc,$i+$j,$k=(1+strpos(substr($adddesc,$i+$j),'>')));
								$i+=$j+$k;
								$gotchars=strlen(preg_replace("/<.*?>/",'',$add));
								
							}
							$adddesc=$add;
						} else $didTrunc=0;
						if ((($fo=strrpos($adddesc,'<'))>($fc=strrpos($adddesc,'>')))||(($fo!==false)&&($fc===false))) $adddesc=substr($adddesc,0,strrpos($adddesc,'<'));
						
						preg_match_all("#<(/{0,1}\w*).*?>#",$adddesc,$matches);
						$opentags=$matches[1];
						for ($i=0;$i<count($opentags);$i++) {
							$tag=strtolower($opentags[$i]);
							if (strcmp(substr($tag,0,1),'/')) {
								$baretag=$tag;
								$isClose=0;
							} else {
								$baretag=substr($tag,1);
								$isClose=1;
							}
							if ($this->tagpairs["$baretag"]!=1) {
								array_splice($opentags,$i,1);
								$i--;
							} else if ($isClose) {
								array_splice($opentags,$i,1);
								$i--;
								for ($j=$i;$j>=0;$j--) {
									if (!strcasecmp($opentags[$j],$baretag)) {
										array_splice($opentags,$j,1);
										$i--;
										$j=-1;
									}
								}
							}
						}
						$thisitem.=$adddesc;
						for ($i=count($opentags)-1;$i>=0;$i--) $thisitem.="</$opentags[$i]>";
						if ($didTrunc) $thisitem.=$carpconf['posttruncdesc'];
					}
					$thisitem.=$carpconf['postitem'];
					$this->itemcount++;
					if ($this->showit) $this->body.=$thisitem."\n";
					else {
						if (strlen($this->pubdate)) $this->dcdate=$this->pubdate;
						if (strlen($this->dcdate)) {
							if (($pubdate=strtotime($this->dcdate)) === -1) $pubdate=0;
						} else $pubdate=0;
						$this->body.="$pubdate:".preg_replace("/\n/",' ',$thisitem)."\n";
					}
				}
				$this->title=$this->description=$this->link=$this->pubdate=$this->dcdate='';
			}
			$this->insideitem=false;
		} else if ($tagName=="CHANNEL") {
			if ($carpconf['showctitle']) {
				if (strlen($this->ctitle)>$carpconf['maxctitle']) $this->ctitle=substr($this->ctitle,0,$carpconf['maxctitle']-3).'...';
				$this->ctitle=htmlspecialchars(trim($this->ctitle));
				if (!strlen($this->ctitle)) $this->ctitle=$carpconf['defaulttitle'];
				$this->top.=$carpconf['cdiv'];
				if ($carpconf['showclink']) $this->top.="<a href=\"".trim($this->clink)."\"".$this->linktargets[$carpconf['linktarget']].
					(strlen($carpconf['cclass'])?(' class="'.$carpconf['cclass'].'"'):'').
					(strlen($carpconf['cstyle'])?(' style="'.$carpconf['cstyle'].'"'):'').
					">".$this->ctitle."</a>";
				else if (strlen($carpconf['cclass'].$carpconf['cstyle'])) $this->top.='<span'.
					(strlen($carpconf['cclass'])?(' class="'.$carpconf['cclass'].'"'):'').
					(strlen($carpconf['cstyle'])?(' style="'.$carpconf['cstyle'].'"'):'').
					'>'.$this->ctitle."</span>";
				else $this->top.=$this->ctitle;
				$this->top.=strlen($carpconf['cdiv'])?"</div>\n":"<br>\n";
				if ($carpconf['showcdesc']) {
					if (strlen($carpconf['desctags'])) $this->top.=trim(preg_replace("#<(?!".$carpconf['desctags'].")(.*?)>#is",($carpconf['removebadtags']?'':"&lt;\\1\\2&gt;"),$this->cdescription))."<br>&nbsp;<br>\n";
					else $this->top.=trim(preg_replace("#<(.*?)>#s",($carpconf['removebadtags']?'':"&lt;\\1&gt;"),$this->cdescription))."<br>&nbsp;<br>\n";
				}
			}
			$this->ctitle=$this->cdescription=$this->clink='';
			$this->insidechannel=0;
		} else if ($this->insidechannel>1) $this->insidechannel--;
	}

	function characterData($parser,$data) {
		if ($this->insideitem) {
			switch ($this->tag) {
			case "TITLE": $this->title.=$data; break;
			case "DESCRIPTION": $this->description.=$data; break;
			case "LINK": $this->link.=$data; break;
			case "PUBDATE": $this->pubdate.=$data; break;
			case "DC:DATE": $this->dcdate.=$data; break;
			}
		} else if ($this->insidechannel==1) {
			switch ($this->tag) {
			case "TITLE": $this->ctitle.=$data; break;
			case "DESCRIPTION": $this->cdescription.=$data; break;
			case "LINK": $this->clink.=$data; break;
			}
		}
	}
	
	function PrepTagPairs($tags) {
		$this->tagpairs=$findpairs=array();
		$temptags=explode('|',strtolower($tags));
		for ($i=count($temptags)-1;$i>=0;$i--) {
			$tag=$temptags[$i];
			if (strcmp(substr($tag,0,1),'/')) {
				$searchpre='/';
				$baretag=$tag;
			} else {
				$searchpre='';
				$baretag=substr($tag,1);
			}
			if ($findpairs["$searchpre$baretag"]==1) {
				$this->tagpairs["$baretag"]=1;
				$findpairs["$baretag"]=$findpairs["/$baretag"]=2;
			} else $findpairs["$tag"]='1';
		}
	}
}

function OpenRSSFeed($url) {
	global $carpconf,$carpversion,$CaRPRedirs;
	
	if (preg_match("#^http://#i",$url)) {
		list($domain,$therest)=explode('/',substr($url,7),2);
		if (preg_match("/\:[0-9]+$/",$domain)) list($domain,$port)=explode(':',$domain,2);
		else $port=80;
		$fp=fsockopen($domain,$port,$errno,$errstr,$carpconf['timeout']);
		if ($fp) {
			$slash=preg_match("#^http://#i",$therest)?'':'/';
			$senddomain=$carpconf['sendhost']?"\r\nHost: $domain":'';
			fputs($fp,"GET $slash$therest HTTP/1.0$senddomain\r\n".
				(strlen($carpconf['proxyauth'])?('Proxy-Authorization: Basic '.base64_encode($carpconf['proxyauth']) ."\r\n"):'').
				(strlen($carpconf['basicauth'])?('Authorization: Basic '.base64_encode($carpconf['basicauth']) ."\r\n"):'').
				"User-Agent: CaRP/$carpversion\r\n\r\n");
			while ((!feof($fp))&&preg_match("/[^\r\n]/",$header=fgets($fp,1000))) {
				if (preg_match("/^Location:/i",$header)) {
					fclose($fp);
					if (count($CaRPRedirs)<$carpconf['maxredir']) {
						$loc=trim(preg_replace("/^Location:/i",'',$header));
						$lochttp=preg_match("#^http://#i",$loc);
						if (!(strlen($slash)||$lochttp)) {
							list($rdomain,$rtherest)=explode('/',substr($therest,strpos($therest,':')+3),2);
							$loc="http://$rdomain$loc";
						}
						if (!(strlen($slash)&&$lochttp)) $loc="http://$domain".(($port==80)?'':":$port").(strlen($slash)?'':'/').$loc;
						for ($i=count($CaRPRedirs)-1;$i>=0;$i--) if (!strcmp($loc,$CaRPRedirs[$i])) {
							CaRPError('Redirection loop detected. Giving up.');
							return 0;
						}
						$CaRPRedirs[count($CaRPRedirs)]=$loc;
						return OpenRSSFeed($loc);
					} else {
						CaRPError('Too many redirects. Giving up.');
						return 0;
					}
				}
			}
		} else CaRPError("$errstr ($errno)");
	} else if (!($fp=fopen($url,'r'))) CaRPError("Failed to open file: $url");
	return $fp;
}

function OpenCacheWrite() {
	global $carpconf;
	$j=0;
	if (!file_exists($carpconf['cachefile'])) touch($carpconf['cachefile']);
	if ($f=fopen($carpconf['cachefile'],'r+')) {
		if ($a=fstat($f)) {
			flock($f,LOCK_EX); // ignore result--doesn't work for all systems and situations
			clearstatcache();
			if ($b=fstat($f)) {
				if ($a['mtime']!=$b['mtime']) {
					flock($f,LOCK_UN);
					fclose($f);
				} else $j=$f;
			} else {
				CarpError("Can't stat cache file (2).");
				fclose($f);
			}
		} else {
			CarpError("Can't stat cache file (1).");
			fclose($f);
		}
	} else CarpError("Can't open cache file.");
	return $j;
}

function CloseCacheWrite($f) {
	ftruncate($f,ftell($f));
	fflush($f);
	flock($f,LOCK_UN);
	fclose($f);
}

function CacheRSSFeed($url) {
	if ($f=OpenRSSFeed($url)) {
		if ($outf=OpenCacheWrite()) {
			while ($l=fread($f,1000)) fwrite($outf,$l);
			CloseCacheWrite($outf);
		} else CaRPError("Unable to create/open RSS cache file.",0);
		fclose($f);
	}
}

function GetRSSFeed($url,$cache,$showit) {
	global $carpconf,$CaRPRedirs;
	$carpconf['desctags']=preg_replace("/\|/",'\b|',$carpconf['descriptiontags']).'\b';
	$xml_parser=xml_parser_create($carpconf['encodingin']);
	if (strlen($carpconf['encodingout'])) xml_parser_set_option($xml_parser,XML_OPTION_TARGET_ENCODING,$carpconf['encodingout']);
	$rss_parser=new RSSParser();
	$rss_parser->showit=$showit;
	//!!! remove the next line eventually
	if (strcmp($carpconf['newwindow'],'0')) $carpconf['linktarget']=$carpconf['newwindow'];
	if (preg_match("/[^0-9]/",$carpconf['linktarget'])) $rss_parser->linktargets[$carpconf['linktarget']]=' target="'.$carpconf['linktarget'].'"';
	$rss_parser->filterinfield=array();
	if (strlen($carpconf['filterin'])) {
		$rss_parser->filterin=explode('|',strtolower($carpconf['filterin']));
		for ($i=count($rss_parser->filterin)-1;$i>=0;$i--) {
			if (strpos($rss_parser->filterin[$i],':')!==false)
				list($rss_parser->filterinfield[$i],$rss_parser->filterin[$i])=explode(':',$rss_parser->filterin[$i],2);
		}
	} else $rss_parser->filterin=array();
	$rss_parser->filteroutfield=array();
	if (strlen($carpconf['filterout'])) {
		$rss_parser->filterout=explode('|',strtolower($carpconf['filterout']));
		for ($i=count($rss_parser->filterout)-1;$i>=0;$i--) {
			if (strpos($rss_parser->filterout[$i],':')!==false)
				list($rss_parser->filteroutfield[$i],$rss_parser->filterout[$i])=explode(':',$rss_parser->filterout[$i],2);
		}
	} else $rss_parser->filterout=array();
	xml_set_object($xml_parser,$rss_parser);
	xml_set_element_handler($xml_parser,"startElement","endElement");
	xml_set_character_data_handler($xml_parser,"characterData");
	$CaRPRedirs=array();
	if ($fp=OpenRSSFeed($url)) {
		$rss_parser->PrepTagPairs($carpconf['descriptiontags']);
		while ($data=preg_replace("/&(?!lt|gt|amp|apos|quot)(.*\b)/is","&amp;\\1\\2",fread($fp,4096))) {
			if (!xml_parse($xml_parser,$data,feof($fp))) {
				CaRPError("XML error: ".xml_error_string(xml_get_error_code($xml_parser))." at line ".xml_get_current_line_number($xml_parser));
				fclose($fp);
				xml_parser_free($xml_parser);
				return;
			}
		}
		fclose($fp);
		if ($showit) CarpOutput($rss_parser->top.$carpconf['preitems'].$rss_parser->body.$carpconf['postitems'].$carpconf['poweredby']);
		if ($cache) {
			if ($cfp=OpenCacheWrite()) {
				fwrite($cfp,($showit?($rss_parser->top.$carpconf['preitems']):'').$rss_parser->body.($showit?($carpconf['postitems'].$carpconf['poweredby']):''));
				CloseCacheWrite($cfp);
			} else CaRPError("Unable to create/open RSS cache file.",0);
		}
		xml_parser_free($xml_parser);
	} else CarpOutput(file($carpconf['cachefile']));
}
?>
