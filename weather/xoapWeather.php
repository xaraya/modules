<?php
#######################################################################################
#xoapWeather - Process XML feeds from weather.com for display on a website            #
#			   keeping with in weather.com's standards for cacheing requests and links#
#Copyright (C) 2003  Brian Paulson <spectre013@spectre013.com>						  #
#																					  #
#This program is free software; you can redistribute it and/or 						  #
#modify it under the terms of the GNU General Public License                          #
#as published by the Free Software Foundation; either version 2                       #
#of the License, or (at your option) any later version.                               #
#																					  #
#This program is distributed in the hope that it will be useful,                      #
#but WITHOUT ANY WARRANTY; without even the implied warranty of                       #
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                        #
#GNU General Public License for more details.                                         # 
# 																					  #
#You should have received a copy of the GNU General Public License                    #
#along with this program; if not, write to the Free Software                          #
#Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.          #
#######################################################################################

/**
 *  This file was modified by Roger Raymond for use in Xaraya
 */


########################################################
# VERSION 1.1										   #
########################################################
class xoapWeather
{
    var $xoapKey;
    var $xoapPar;
    var $product = 'xoap';
    var $currentCondCache;
    var $multiDayforecastCache;
    var $defaultLocation;
    var $defaultUnits;
    var $sitePath;
    var $ccFile = 'cc.xml';
    var $forecastFile = 'forecast.xml';
    var $cacheDir;
    var $forecastDays;
    var $error;
    var $units;
    var $location;
    //var $Administrator = 'webmaster@chieftain.com';
    
    // file name holders
    var $ccs;
    var $forecasts;
    var $ccm;
    var $forecastm; 
    
    /**
    *	Perform Setup Actions for class
    *	@access	Private
    *	@param	None
    *	@return	None
    */
    function xoapWeather()
	{
	    $this->xoapKey = xarModGetVar('weather','license_key');
        $this->xoapPar = xarModGetVar('weather','partner_id');
        $this->defaultLocation = xarModGetVar('weather','default_location');
        $this->defaultUnits = xarModGetVar('weather','units');
        $this->currentCondCache = xarModGetVar('weather','cc_cache_time');
        $this->multiDayforecastCache = xarModGetVar('weather','ext_cache_time');
        $this->cacheDir = xarModGetVar('weather','cache_dir');
        $this->forecastDays = xarModGetVar('weather','extdays');
        $this->statusCheck();
        $this->setUnits();
        $this->setLocation();
    }

    /**
    *   Sets the Zip code that the program should use	
    *	@access Private
    *	@param	None
    *	@return None
    */
    function setLocation($loc=null)
	{
		if(isset($loc)) {
            $this->location =& $loc;
        } else {
            if(xarUserIsLoggedIn()) {
                // grab the user's location setting if available
                $loc = xarModGetUserVar('weather','default_location');
            }
            if(!isset($loc) || empty($loc)) {
                // use the admin drefault location
                $this->location =& $this->defaultLocation;
            } else {
                // use the user setting
                $this->location =& $loc;
            }
        }
        
        // we need to trigger these after the location is set
        $this->reinit();
    }

    function setUnits($units=null) 
    {
        if(isset($units)) {
            $this->units =& $units;
        } else {
            if(xarUserIsLoggedIn()) {
                // grab the user's location setting if available
                $units = xarModGetUserVar('weather','units');
            }
            if(!isset($units) || empty($units)) {
                // use the admin drefault location
                $this->units =& $this->defaultUnits;
            } else {
                // use the user setting
                $this->units =& $units;
            }
        }
        $this->reinit();
    }


    function reinit()
    {
        $this->setFiles();
        $this->cacheControl();
    }

    /**
    *   Checks the status of certain Variables that are needed to execute	
    *	@access Private
    *	@param	None
    *	@return None
    */
    function statusCheck()
	{
	    if($this->defaultLocation == "") {
		    xarErrorSet(
                XAR_USER_EXCEPTION,
                xarML('No Default Location'),
                xarML('Please enter a valid location id or zip code.')
                );
		}
	    if($this->xoapKey == "") {
		    xarErrorSet(
                XAR_USER_EXCEPTION,
                xarML('Your License Key is Invalid!'),
                xarML('Please enter a valid License Key or Visit http://www.weather.com/services/xmloap.html and sign-up for thier xoapXML Services.')
                );
        }
	    if($this->xoapPar == "") {
		    xarErrorSet(
                XAR_USER_EXCEPTION,
                xarML('Your Partner ID is Invalid!'),
                xarML('Please enter a valid Partner ID or Visit http://www.weather.com/services/xmloap.html and sign-up for thier xoapXML Services.')
                );
		}
	    if($this->cacheDir == "") {
		    xarErrorSet(
                XAR_USER_EXCEPTION,
                xarML('Your Cache Directory is not defined.'),
                xarML('Please enter a valid Cache Directory and make sure it is writeable by your webserver.')
                );
        }
	    
        return true;
    }

    /**
    *	Sets the paths and filenames of for Proper cacheing.	
    *	@access Private
    *	@param	None
    *	@return	None
    */
    function setFiles()
	{
	    $sep = DIRECTORY_SEPARATOR;
        $this->sitePath = $_SERVER['DOCUMENT_ROOT'];
	    $this->ccs = $this->sitePath.$sep.$this->cacheDir.$sep.$this->location.'s'.$this->ccFile;
	    $this->forecasts = $this->sitePath.$sep.$this->cacheDir.$sep.$this->location.'s'.$this->forecastFile;
        $this->ccm = $this->sitePath.$sep.$this->cacheDir.$sep.$this->location.'m'.$this->ccFile;
	    $this->forecastm = $this->sitePath.$sep.$this->cacheDir.$sep.$this->location.'m'.$this->forecastFile;
	}

    /**
    *	Performs all cacheing for the application and file creation	
    *	@access	Private
    *	@param	None
    *	@return None
    */
    function cacheControl()
	{
	    $this->error = false;
        // check metric files
        if(!is_file($this->ccm)) {
			if($this->error != 1) {
			    $this->getXMLdata($this->location,'ccm'); 
			}
			if($this->error != 1) {
			    $this->getXMLdata($this->location,'forecastm');
			}
		} else {
		    $ccFiletime = filemtime($this->ccm);
		    $forecastFiletime = filemtime($this->forecastm);
		    $cccache = time() - ($this->currentCondCache);
		    $forecastCache = time() - ($this->multiDayforecastCache);
	
		    if($ccFiletime <= $cccache or $this->error == true) {
			    $this->getXMLdata($this->location,'ccm'); 
			}
		
		    if($forecastFiletime <= $forecastCache or $this->error == True) {
			    $this->getXMLdata($this->location,'forecastm');
			}
		}
        
        // get standard files
        if(!is_file($this->ccs)) {
			if($this->error != 1) {
			    $this->getXMLdata($this->location,'ccs'); 
			}
			if($this->error != 1) {
			    $this->getXMLdata($this->location,'forecasts');
			}
		} else {
		    $ccFiletime = filemtime($this->ccs);
		    $forecastFiletime = filemtime($this->forecasts);
		    $cccache = time() - ($this->currentCondCache);
		    $forecastCache = time() - ($this->multiDayforecastCache);
	
		    if($ccFiletime <= $cccache or $this->error == true) {
			    $this->getXMLdata($this->location,'ccs'); 
			}
		
		    if($forecastFiletime <= $forecastCache or $this->error == true) {
			    $this->getXMLdata($this->location,'forecasts');
			}
		}
        
	    $this->cleanCache();
	}	

    /**
    *   Checks for cache Files that are older then 24 Hours old and removes them	
    *	@access	Private
    *	@param None
    *	@return None
    */
    function cleanCache()
	{
	    $sep = DIRECTORY_SEPARATOR;
        $dir = $this->sitePath.$sep.$this->cacheDir;
	    $open = opendir($dir);
	    while($file = readdir($open)) {
		    if($file == "." or $file == "..") {
			
			} else {
			    $Filetime = filemtime($dir.$sep.$file);
			    $Purge = mktime(date("H")-24,date("i"),0,date("m"),date("d"),date("Y"));
			    if($Purge > $Filetime) {
				    unlink($dir.$sep.$file);
				} 
			}
	 	}
	}

    /**
    *	Retrives XML data from weather.com's website 	
    *	@access Public
    *	@param	$location string Current Zipcode
    *	@param	$type string Either 'cc' or 'forecast' 
    *	@return None
    */
    function getXMLdata($location,$type)
    {
        //print_r($location); die();
        if($type == "ccs") {
            $setup = "cc=*";
            $file = $this->ccs;
            $units = 's';
        } elseif($type == 'forecasts') {
            $setup = "dayf=$this->forecastDays";
            $file = $this->forecasts;
            $units = 's';
        } elseif($type == "ccm") {
            $setup = "cc=*";
            $file = $this->ccm;
            $units = 'm';
        } elseif($type == "forecastm") {
            $setup = "dayf=$this->forecastDays";
            $file = $this->forecastm;
            $units = 'm';   
        }
        
		$location = urlencode($location);
        $stream = "http://xoap.weather.com/weather/local/$location?$setup&link=xoap&unit=$units&prod=$this->product&par=$this->xoapPar&key=$this->xoapKey";	
		$data = file($stream);
		$this->errorCheck($data);
		if($this->error['number'] == 2) {
	        $location = $this->locData($location);
		} else {
		    $xmi=join('',$data);
		    if($xmi != "") {
			    $open = fopen($file,"w");
				fputs($open,$xmi,strlen($xmi));
				fclose($open);
			}	
		}
        
    }

    /**
    *	Checks for errors in the XML file and handles them accordingly	
    *	@access	Public
    *	@param	$file string filename of the xml file that we are currenly loading 
    *	@return	$error string True or NULL for detecting errors in the XML Feed
    */
    function errorCheck($file)
	{
	    /*
	    if there is as problem with the XML file you request The weather channel returns an errror XML File
	    here we take the errors and Display.
	    */
	    $tree = $this->GetXMLTree($file);
	    if(isset($tree['ERROR'])) {
            $this->error['number'] = $tree['ERROR'][0]['ERR'][0]['ATTRIBUTES']['TYPE'];
	        $this->error['type'] = $tree['ERROR'][0]['ERR'][0]['VALUE'];

	        if($this->error['type'] != "" or $this->error['number'] != ""){			
		        $this->error['exists'] = true;
		    }
            return true;
        } else {
            return false;
        }
	}

    /**
    *	Get the Children data from and XML file
    *	@access	Public
    *	@param	$vals Array current Nodes in the XML File 
    *	@param	$i Integer current Increment in the process
    *	@return	Return Array Children for Node
    */

    function GetChildren($vals, &$i)
	{
 	    $children = array();     // Contains node data

  	    /* Node has CDATA before it's children */
  	    if (isset($vals[$i]['value'])) {
  	        $children['VALUE'] = $vals[$i]['value'];
        }

  	    /* Loop through children */
  	    $max = count($vals);
        while (++$i < $max) {
  	        switch ($vals[$i]['type']) {
    	        /* Node has CDATA after one of it's children
     	        (Add to cdata found before if this is the case) */
     	        case 'cdata':
      	            if (isset($children['VALUE']))
         	            $children['VALUE'] .= $vals[$i]['value'];
      	            else
         	            $children['VALUE'] = $vals[$i]['value'];
       	            break;
      	        /* At end of current branch */
     	        case 'complete':
       	            if (isset($vals[$i]['attributes'])) {
       	 	            $children[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
        	            $index = count($children[$vals[$i]['tag']])-1;

       	                if (isset($vals[$i]['value']))
                            $children[$vals[$i]['tag']][$index]['VALUE'] = $vals[$i]['value'];
                        else
                            $children[$vals[$i]['tag']][$index]['VALUE'] = '';
                    } else {
                        if (isset($vals[$i]['value']))
                            $children[$vals[$i]['tag']][]['VALUE'] = $vals[$i]['value'];
                        else
                            $children[$vals[$i]['tag']][]['VALUE'] = '';
                    }
                    break;
                /* Node has more children */
                case 'open':
                    if (isset($vals[$i]['attributes'])) {
                        $children[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
                        $index = count($children[$vals[$i]['tag']])-1;
                        $children[$vals[$i]['tag']][$index] = array_merge($children[$vals[$i]['tag']][$index],$this->GetChildren($vals, $i));
                    } else {
                        $children[$vals[$i]['tag']][] = $this->GetChildren($vals, $i);
                    }
                    break;
                /* End of node, return collected data */
                case 'close':
                    return $children;
            }
        }
    }

    /**
    *	Function will attempt to open the xmlloc as a local file, on fail it will attempt to open it as a web link	
    *	@access	Public
    *	@param	string XML File to load
    *	@return	$tree array of data in the XML file
    */
    function GetXMLTree($xmlloc)
	{
	    if(is_array($xmlloc)) {
		    $data = implode('', $xmlloc);
        } else {
		    if(file_exists($xmlloc)) {
			    $data = implode('', file($xmlloc));
            }
		}
        if(!isset($data)) { 
            // chances are we have a non-existent location or an error
            xarErrorSet(
                    XAR_USER_EXCEPTION,
                    xarML('An Error Occured while Processing Your Request'),
                    xarML('A likely cause for this is that the location you specified was not found')
                );
        }
  	    $parser = xml_parser_create('ISO-8859-1');
 	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
  	    xml_parse_into_struct($parser, $data, $vals, $index);
 	    xml_parser_free($parser);

  	    $tree = array();
  	    $i = 0;

  	    if (isset($vals[$i]['attributes'])) {
            $tree[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
            $index = count($tree[$vals[$i]['tag']])-1;
            $tree[$vals[$i]['tag']][$index] =  array_merge($tree[$vals[$i]['tag']][$index], $this->GetChildren($vals, $i));
  		} else {
    	    $tree[$vals[$i]['tag']][] = $this->GetChildren($vals, $i);
		}
        return $tree;
    }

    /**
    *	Parses forecast XML file 		
    *	@access Public
    *	@param	None
    *	@return	$forecast Array Contains and array with of the data in the forecast XML file
    */
    function &forecastData()
	{
	    /*
	    Here we are taking the Array from the XML file and putting it into an manageble array
	    */
	    $xmi = $this->getFile('forecast',$this->units);
	    $tree = $this->GetXMLTree($xmi);
	    $days = $tree['WEATHER'][0]['DAYF'][0]['DAY'];
	    $error = $this->errorCheck($xmi);

	    $forecast[0]['loc'] = $tree['WEATHER'][0]['LOC'][0]['DNAM'][0]['VALUE'];
	    $forecast[0]['lsup'] = $tree['WEATHER'][0]['DAYF'][0]['LSUP'][0]['VALUE'];
	    $forecast[0]['unitsTemp'] = $tree['WEATHER'][0]['HEAD'][0]['UT'][0]['VALUE'];                  
	    $forecast[0]['unitsDistance'] = $tree['WEATHER'][0]['HEAD'][0]['UD'][0]['VALUE'];
	    $forecast[0]['unitsSpeed'] = $tree['WEATHER'][0]['HEAD'][0]['US'][0]['VALUE'];
	    $forecast[0]['unitsPrecip'] = $tree['WEATHER'][0]['HEAD'][0]['UP'][0]['VALUE'];
	    $forecast[0]['tempPressure'] = $tree['WEATHER'][0]['HEAD'][0]['UR'][0]['VALUE'];
	    $forecast[0]['linkOne'] = $this->formatLink($tree['WEATHER'][0]['LNKS'][0]['LINK'][0]['L'][0]['VALUE']);
	    $forecast[0]['titleOne'] = $tree['WEATHER'][0]['LNKS'][0]['LINK'][0]['T'][0]['VALUE'];
	    $forecast[0]['linkTwo'] = $this->formatLink($tree['WEATHER'][0]['LNKS'][0]['LINK'][1]['L'][0]['VALUE']);
	    $forecast[0]['titleTwo'] = $tree['WEATHER'][0]['LNKS'][0]['LINK'][1]['T'][0]['VALUE'];
	    $forecast[0]['linkThree'] = $this->formatLink($tree['WEATHER'][0]['LNKS'][0]['LINK'][2]['L'][0]['VALUE']);
	    $forecast[0]['titleThree'] = $tree['WEATHER'][0]['LNKS'][0]['LINK'][2]['T'][0]['VALUE'];
	    $forecast[0]['linkFour'] = $this->formatLink($tree['WEATHER'][0]['LNKS'][0]['LINK'][3]['L'][0]['VALUE']);
	    $forecast[0]['titleFour'] = $tree['WEATHER'][0]['LNKS'][0]['LINK'][3]['T'][0]['VALUE'];
	    /*
	    With the Current Conditions we have up to 10 days of data that needs to be collected
	    we do that here by looping though and grabbing the data
	    */
	    for($i=0; $i<count($days); $i++) {
	        $forecast[$i]['wkday'] = $days[$i]['ATTRIBUTES']['T'];
	        $forecast[$i]['date'] = $days[$i]['ATTRIBUTES']['DT'];
	        $forecast[$i]['hi'] = $days[$i]['HI'][0]['VALUE'];
	        $forecast[$i]['lo'] = $days[$i]['LOW'][0]['VALUE'];
	        $forecast[$i]['sunr'] = $days[$i]['SUNR'][0]['VALUE'];
	        $forecast[$i]['suns'] = $days[$i]['SUNS'][0]['VALUE'];
	        $forecast[$i]['part']['d']['icon'] = $days[$i]['PART'][0]['ICON'][0]['VALUE'];
	        $forecast[$i]['part']['d']['cond'] = $days[$i]['PART'][0]['T'][0]['VALUE'];
	        $forecast[$i]['part']['d']['windspeed'] = $days[$i]['PART'][0]['WIND'][0]['S'][0]['VALUE'];
	        $forecast[$i]['part']['d']['windgust'] = $days[$i]['PART'][0]['WIND'][0]['GUST'][0]['VALUE'];
	        $forecast[$i]['part']['d']['winddir'] = $days[$i]['PART'][0]['WIND'][0]['T'][0]['VALUE'];
	        $forecast[$i]['part']['d']['ppcp'] = $days[$i]['PART'][0]['PPCP'][0]['VALUE'];
	        $forecast[$i]['part']['d']['humid'] = $days[$i]['PART'][0]['HMID'][0]['VALUE'];
	        $forecast[$i]['part']['n']['icon'] = $days[$i]['PART'][1]['ICON'][0]['VALUE'];
	        $forecast[$i]['part']['n']['cond'] = $days[$i]['PART'][1]['T'][0]['VALUE'];
	        $forecast[$i]['part']['n']['windspeed'] = $days[$i]['PART'][1]['WIND'][0]['S'][0]['VALUE'];
	        $forecast[$i]['part']['n']['windgust'] = $days[$i]['PART'][1]['WIND'][0]['GUST'][0]['VALUE'];
	        $forecast[$i]['part']['n']['winddir'] = $days[$i]['PART'][1]['WIND'][0]['T'][0]['VALUE'];
	        $forecast[$i]['part']['n']['ppcp'] = $days[$i]['PART'][1]['PPCP'][0]['VALUE'];
	        $forecast[$i]['part']['n']['humid'] = $days[$i]['PART'][1]['HMID'][0]['VALUE'];
	        $forecast[$i]['error'] = $error;
	    }
	    return $forecast;	
    }

    /**
    *	Gets the Current Conditions data from the XML file and puts it into and aarry	
    *	@access	Public
    *	@param	None
    *	@return	$cc Array Current Conditions Data
    */
    function getFile($type='cc',$units='s') 
    {
        if($type=='cc') {
            switch($units) {
                case 's':
                    return $this->ccs;
                    break;
                case 'm':
                    return $this->ccm;
                    break;
            }
        } elseif($type=='forecast') {
            switch($units) {
                case 's':
                    return $this->forecasts;
                    break;
                case 'm':
                    return $this->forecastm;
                    break;
            }
        }
    }

    function &ccData()
	{
		/*
		Grabbing the Current XML data for the Current Condition and the Current Conditions details
		*/ 
		$xmi = $this->getFile('cc',$this->units);
		$tree = $this->GetXMLTree($xmi);
		$error = $this->errorCheck($xmi);
		$cc['linkOne'] = $this->formatLink($tree['WEATHER'][0]['LNKS'][0]['LINK'][0]['L'][0]['VALUE']);
		$cc['titleOne'] = $tree['WEATHER'][0]['LNKS'][0]['LINK'][0]['T'][0]['VALUE'];
		$cc['linkTwo'] = $this->formatLink($tree['WEATHER'][0]['LNKS'][0]['LINK'][1]['L'][0]['VALUE']);
		$cc['titleTwo'] = $tree['WEATHER'][0]['LNKS'][0]['LINK'][1]['T'][0]['VALUE'];
		$cc['linkThree'] = $this->formatLink($tree['WEATHER'][0]['LNKS'][0]['LINK'][2]['L'][0]['VALUE']);
		$cc['titleThree'] = $tree['WEATHER'][0]['LNKS'][0]['LINK'][2]['T'][0]['VALUE'];
		$cc['linkFour'] = $this->formatLink($tree['WEATHER'][0]['LNKS'][0]['LINK'][3]['L'][0]['VALUE']);
		$cc['titleFour'] = $tree['WEATHER'][0]['LNKS'][0]['LINK'][3]['T'][0]['VALUE'];
		$cc['unitsTemp'] = $tree['WEATHER'][0]['HEAD'][0]['UT'][0]['VALUE'];                  
		$cc['unitsDistance'] = $tree['WEATHER'][0]['HEAD'][0]['UD'][0]['VALUE'];
		$cc['unitsSpeed'] = $tree['WEATHER'][0]['HEAD'][0]['US'][0]['VALUE'];
		$cc['unitsPrecip'] = $tree['WEATHER'][0]['HEAD'][0]['UP'][0]['VALUE'];
		$cc['tempPressure'] = $tree['WEATHER'][0]['HEAD'][0]['UR'][0]['VALUE'];
		$cc['location'] = $tree['WEATHER'][0]['LOC'][0]['ATTRIBUTES']['ID'];
		$cc['sunrise'] = $tree['WEATHER'][0]['LOC'][0]['SUNR'][0]['VALUE'];
		$cc['sunset'] = $tree['WEATHER'][0]['LOC'][0]['SUNS'][0]['VALUE'];
		$cc['lastUpdate'] = $tree['WEATHER'][0]['CC'][0]['LSUP'][0]['VALUE'];
		$cc['observStation'] = $tree['WEATHER'][0]['CC'][0]['OBST'][0]['VALUE'];
		$cc['temp'] = $tree['WEATHER'][0]['CC'][0]['TMP'][0]['VALUE'];
		$cc['feelsLike'] = $tree['WEATHER'][0]['CC'][0]['FLIK'][0]['VALUE'];
		$cc['conditions'] = $tree['WEATHER'][0]['CC'][0]['T'][0]['VALUE'];
		$cc['icon'] = $tree['WEATHER'][0]['CC'][0]['ICON'][0]['VALUE'];
		$cc['barometer'] = $tree['WEATHER'][0]['CC'][0]['BAR'][0]['R'][0]['VALUE'];
		$cc['barometerDesc'] = $tree['WEATHER'][0]['CC'][0]['BAR'][0]['D'][0]['VALUE'];
		$cc['wind'] = $tree['WEATHER'][0]['CC'][0]['WIND'][0]['S'][0]['VALUE'];
		$cc['windGust'] = $tree['WEATHER'][0]['CC'][0]['WIND'][0]['GUST'][0]['VALUE'];
		$cc['windDirdeg'] = $tree['WEATHER'][0]['CC'][0]['WIND'][0]['D'][0]['VALUE'];
		$cc['windDirname'] = $tree['WEATHER'][0]['CC'][0]['WIND'][0]['T'][0]['VALUE'];
		$cc['humidity'] = $tree['WEATHER'][0]['CC'][0]['HMID'][0]['VALUE'];
		$cc['visibility'] = $tree['WEATHER'][0]['CC'][0]['VIS'][0]['VALUE'];
		$cc['uv'] = $tree['WEATHER'][0]['CC'][0]['UV'][0]['I'][0]['VALUE'];
		$cc['uvDesc'] = $tree['WEATHER'][0]['CC'][0]['UV'][0]['T'][0]['VALUE'];
		$cc['dewPoint'] = $tree['WEATHER'][0]['CC'][0]['DEWP'][0]['VALUE'];
		$cc['error'] = $error;
		
	    return $cc;
	}	
    
    /**
     *  Searches for a location and returns the possible matches
     */
    function &locData($loc)
	{
	    $stream = "http://xoap.weather.com/search/search?where=".urlencode($loc);
	    $data = file($stream);
        if(!empty($data)) {
		    $tree = $this->GetXMLTree($data);
		}
	    if(isset($tree['SEARCH'][0]['LOC'])) {
            $max = count($tree['SEARCH'][0]['LOC']);
            for($i=0; $i<$max; $i++) {
			    $info[$i]['zip'] = $tree['SEARCH'][0]['LOC'][$i]['ATTRIBUTES']['ID'];
			    $info[$i]['name'] = $tree['SEARCH'][0]['LOC'][$i]['VALUE'];
		    }
	        return $info;
        } else {
            // we don't have any results
            return false;
        }
        return true;
	}
    
    /**
     *  This lets us read from the $_GET or $_POST vars and set some stuff
     */
    function setExtraParams()
    {
        xarVarFetch('xwunits','str::',$units,null,XARVAR_NOT_REQUIRED);
        $units = strtolower($units);
        if(isset($units) && ($units=='s' || $units=='m')) {
            $this->setUnits($units);
        }
        xarVarFetch('xwloc','str::',$loc,null,XARVAR_NOT_REQUIRED);
        if(isset($loc) && !empty($loc)) {
            $this->setLocation($loc);
        }
        return true;
    }
    
    function formatLink($link) 
    {
        if(stristr($link,'par=xoap')) {
            // weather.com's feed is wrong so we'll fix it to comply with the TOS
            $link = str_replace('par=xoap','prod=xoap&amp;par='.$this->xoapPar,$link);
        } elseif(stristr($link,'prod=xoap')) {
            // the feed is correct, append the partner id
            $link .= '&amp;par='.$this->xoapPar;
            
        }
        return $link;
    }
    
}
?>