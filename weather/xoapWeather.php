<?php
/**
 * Class to grab XML weather feeds from weather.com
 *
 * Implements the weather.com SDK and is derived from :
 * xoapWeather - Copyright (C) 2003 Brian Paulson <spectre013@spectre013.com>						 
 *
 * @package weather
 * @author Roger Raymond <roger@xaraya.com>
 */
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
    var $forecastDays;
    var $error;
    var $units;
    var $location;
    
    /**
     * xoapWeather constructor
     *
     * Setup up the class with default values
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  public
     */
    function xoapWeather()
	{
	    $this->xoapKey = xarModGetVar('weather','license_key');
        $this->xoapPar = xarModGetVar('weather','partner_id');
        $this->defaultLocation = xarModGetVar('weather','default_location');
        $this->defaultUnits = xarModGetVar('weather','units');
        $this->currentCondCache = xarModGetVar('weather','cc_cache_time');
        $this->multiDayforecastCache = xarModGetVar('weather','ext_cache_time');
        $this->forecastDays = xarModGetVar('weather','extdays');
        $this->statusCheck();
        $this->setUnits();
        $this->setLocation();
        $this->setExtraParams();
    }

    /**
     * Sets the location to be used for this instance
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  public
     * @param   string $loc The location we're looking for (US Zip or City Name)
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
    }
    
    /**
     * Sets the units to be used for this instance
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  public
     * @param   string $units (S)tandard or (M)etric
     */
    function setUnits($units=null) 
    {
        if(isset($units)) {
            $this->units = strtolower($units);
        } else {
            if(xarUserIsLoggedIn()) {
                // grab the user's location setting if available
                $units = xarModGetUserVar('weather','units');
            }
            if(!isset($units) || empty($units)) {
                // use the admin drefault location
                $this->units = $this->defaultUnits;
            } else {
                // use the user setting
                $this->units = strtolower($units);
            }
        }
    }

    /**
     * Checks to make sure everything needed to run has been set
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  private
     * @throws  XAR_USER_EXCEPTION
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
	    return true;
    }

    /**
     * Checks for errors in the xml returned from the feed
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  private
     * @throws  XAR_USER_EXCEPTION
     * @returns bool false if no errors set
     */
    function errorCheck(&$data)
	{
	    $tree =& $this->GetXMLTree($data);
	    if(isset($tree['ERROR'])) {
            $this->error['number'] =& $tree['ERROR'][0]['ERR'][0]['ATTRIBUTES']['TYPE'];
	        $this->error['type'] =& $tree['ERROR'][0]['ERR'][0]['VALUE'];

	        if($this->error['type'] != "" or $this->error['number'] != ""){			
		        $this->error['exists'] = true;
                xarErrorSet(
                    XAR_USER_EXCEPTION,
                    $this->error['number'], 
                    $this->error['type']
                );
		    }
        }
        return false; 
	}

    /**
     * Get the Children data from and XML file
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  protected
     * @param   string $vals Array current Nodes in the XML File 
     * @param   integer $i Integer current Increment in the process
     * @return  Return Array Children for Node
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
     * Method parses the xml data feed
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  protected
     * @param   string $data xml data feed
     * @return  array xml data tree
     */
    function &GetXMLTree(&$data)
	{
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
     * Method grabs the xml feed
     *
     * Attempts to grab a local cached weather xml feed from /var/cache/rss
     * otherwise it grabs it from the weather.com site and caches it 
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  protected
     * @param   string $type what we're looking for (current condition [cc] or extended forecast [forecast])
     * @param   string $units (S)tandard or (M)etric
     * @return  string xml data to parse
     */
    function &getFile($type='cc',$units='s') 
    {
        $location = urlencode($this->location);
        if($type == "cc") {
            $setup = "cc=*";
            $refresh = $this->currentCondCache;
        } elseif($type == 'forecast') {
            $setup = "dayf=$this->forecastDays";
            $refresh = $this->multiDayforecastCache;
        }
        $stream = "http://xoap.weather.com/weather/local/$location?$setup&link=xoap&unit=$units&prod=$this->product&par=$this->xoapPar&key=$this->xoapKey";	
        $data = xarModAPIFunc('base','user','getfile',
            array(
                'url'=>$stream,
                'cached'=>true,
                'cachedir'=>'cache/rss',
                'refresh'=>$refresh,
                'extension'=>'.xml',
                'archive'=>false  
            ));
        $this->errorCheck($data);
        return $data;
    }

    /**
     * Get the extended forecast data
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  public
     * @return  array forecast data for the location
     */
    function &forecastData()
	{
	    /*
	    Here we are taking the Array from the XML file and putting it into an manageble array
	    */
	    $xmi =& $this->getFile('forecast',$this->units);
	    $tree =& $this->GetXMLTree($xmi);
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
     * Get the current conditions forecast data
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  public
     * @return  array forecast data for the location
     */
    function &ccData()
	{
		/*
		Grabbing the Current XML data for the Current Condition and the Current Conditions details
		*/ 
		$xmi =& $this->getFile('cc',$this->units);
		$tree =& $this->GetXMLTree($xmi);
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
     * Search for the specified location
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  public
     * @param   string $loc the location to search (US Zip or City)
     * @return  array location data
     */
    function &locData($loc)
	{
	    $stream = "http://xoap.weather.com/search/search?where=".urlencode($loc);
        $data = xarModAPIFunc('base','user','getfile',
            array(
                'url'=>$stream,
                'cached'=>false,
                'extension'=>'.xml',
                'archive'=>false  
            ));
        $this->errorCheck($data);
        if(!empty($data)) {
		    $tree =& $this->GetXMLTree($data);
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
     * Allows the user to override the location and units variables in the url
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  public
     * @return  bool true
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
    
    /**
     * Fixes for the links provided in the weather.com xml feed
     *
     * This makes the links correctly formatted according to the weather.com SDK
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  public
     * @param   string $link the link to format
     * @return  string the correctly formatted link
     */
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