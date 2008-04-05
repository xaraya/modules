<?php
/**
 * Class to grab XML weather feeds from weather.com
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Weather Module
 * @link http://xaraya.com/index.php/release/662.html
 * @author Weather Module Development Team
 */
 
/**
 * Class to grab XML weather feeds from weather.com
 *
 * Implements the weather.com SDK and is derived from :
 * xoapWeather - Copyright (C) 2003 Brian Paulson <spectre013@spectre013.com>
 *
 * @package weather
 * @author Roger Raymond <roger@xaraya.com>
 */

// use xaraya's Generic XML Parser
sys::import('modules.weather.class.xarXML');

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
        $this->xoapKey = xarModVars::get('weather','license_key');
        $this->xoapPar = xarModVars::get('weather','partner_id');
        $this->defaultLocation = xarModVars::get('weather','default_location');
        $this->defaultUnits = xarModVars::get('weather','units');
        $this->currentCondCache = xarModVars::get('weather','cc_cache_time');
        $this->multiDayforecastCache = xarModVars::get('weather','ext_cache_time');
        $this->forecastDays = xarModVars::get('weather','extdays');
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
                $loc = xarModUserVars::get('weather','default_location');
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
                $units = xarModUserVars::get('weather','units');
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
            $msg = xarML('No Default Location') . xarML('Please enter a valid location id or zip code.');
            throw new Exception($msg);
        }
        if($this->xoapKey == "") {
            $msg = xarML('Your License Key is Invalid!') . xarML('Please enter a valid License Key or Visit http://www.weather.com/services/xmloap.html and sign-up for thier xoapXML Services.');
            throw new Exception($msg);
        }
        if($this->xoapPar == "") {
            $msg = xarML('Your Partner ID is Invalid!') . xarML('Please enter a valid Partner ID or Visit http://www.weather.com/services/xmloap.html and sign-up for their xoapXML Services.');
            throw new Exception($msg);
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
                $msg = $this->error['number'] . $this->error['type'];
                throw new Exception($msg);
            }
        }
        return false;
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
    function &getFile($type='cc',$units='m',$cache=true)
    {
        $location = urlencode($this->location);
        if($type == 'cc') {
            $setup = 'cc=*';
            $refresh = $this->currentCondCache;
        } elseif($type == 'forecast') {
            $setup = "dayf=$this->forecastDays";
            $refresh = $this->multiDayforecastCache;
        }
        $stream = "http://xoap.weather.com/weather/local/{$location}?{$setup}&link=xoap&unit={$units}&prod={$this->product}&par={$this->xoapPar}&key={$this->xoapKey}";
        $data = xarModAPIFunc('base','user','getfile',
            array(
                'url'=>$stream,
                'cached'=>$cache,
                'cachedir'=>'cache/rss',
                'refresh'=>$refresh,
                'extension'=>'.xml',
                'archive'=>false
            )
        );
        return $data;
    }

    /**
     * Get the extended forecast data
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  public
     * @return  array forecast data for the location
     */
    function &forecastData($cache=true)
    {
        /*
        Grabbing the Current XML data for the Current Condition and the Current Conditions details
        */
        $p = new xarXmlParser();
        $xmi =& $this->getFile('forecast',$this->units,$cache);
        if(!$p->parseString($xmi)) {
            // try again
            return $this->forecastData(false);
        } else {
            $tree = $p->tree[0]['children'][0];
        }

        $head =& $tree['children'][0];
        $loc  =& $tree['children'][1];
        
        if ($tree['children'][2]['name'] == 'dayf') {
            $days =& $tree['children'][2]['children'];
        } else {
            $days =& $tree['children'][3]['children'];
        }
        $lnks  =& $tree['children'][2];

        // $days =& $tree['children'][2]['children'];

        $forecast[0]['linkOne']       = $lnks['children'][0]['children'][0]['content'];
        $forecast[0]['titleOne']      = $lnks['children'][0]['children'][1]['content'];
        $forecast[0]['linkTwo']       = $lnks['children'][1]['children'][0]['content'];
        $forecast[0]['titleTwo']      = $lnks['children'][1]['children'][1]['content'];
        $forecast[0]['linkThree']     = $lnks['children'][2]['children'][0]['content'];
        $forecast[0]['titleThree']    = $lnks['children'][2]['children'][1]['content'];
        $forecast[0]['linkFour']      = $lnks['children'][3]['children'][0]['content'];
        $forecast[0]['titleFour']     = $lnks['children'][3]['children'][1]['content'];

        $forecast[0]['unitsTemp']     = $head['children'][2]['content'];
        $forecast[0]['unitsDistance'] = $head['children'][3]['content'];
        $forecast[0]['unitsSpeed']    = $head['children'][4]['content'];
        $forecast[0]['unitsPrecip']   = $head['children'][5]['content'];
        $forecast[0]['tempPressure']  = $head['children'][6]['content'];

        $forecast[0]['location']      = $loc['attributes']['id'];
        $forecast[0]['sunrise']       = $loc['children'][4]['content'];
        $forecast[0]['sunset']        = $loc['children'][5]['content'];
        $forecast[0]['dnam']          = $loc['children'][5]['content'];
        $forecast[0]['lat']           = $loc['children'][2]['content'];
        $forecast[0]['lon']           = $loc['children'][3]['content'];
        $forecast[0]['tm']            = $loc['children'][1]['content'];
        $forecast[0]['zone']          = $loc['children'][6]['content'];

        /*
        With the Current Conditions we have up to 10 days of data that needs to be collected
        we do that here by looping though and grabbing the data
        */
        for($i=0,$c=1,$max=count($days); $c<$max; $i++,$c++) {

            $day =& $days[$c]; // simple reference to our day node
            $forecast[$i]['wkday']                   = $day['attributes']['t'];
            $forecast[$i]['date']                    = $day['attributes']['dt'];

            $children =& $day['children'];
            $forecast[$i]['hi']                      = $children[0]['content'];
            $forecast[$i]['lo']                      = $children[1]['content'];
            $forecast[$i]['sunr']                    = $children[2]['content'];
            $forecast[$i]['suns']                    = $children[3]['content'];

            $daytime =& $children[4];
            $forecast[$i]['part']['d']['icon']       = $daytime['children'][0]['content'];
            $forecast[$i]['part']['d']['cond']       = $daytime['children'][1]['content'];
            $forecast[$i]['part']['d']['windspeed']  = $daytime['children'][2]['children'][0]['content'];
            $forecast[$i]['part']['d']['windgust']   = $daytime['children'][2]['children'][1]['content'];
            $forecast[$i]['part']['d']['winddirdeg'] = $daytime['children'][2]['children'][2]['content'];
            $forecast[$i]['part']['d']['winddir']    = $daytime['children'][2]['children'][3]['content'];
            $forecast[$i]['part']['d']['bt']         = $daytime['children'][3]['content'];
            $forecast[$i]['part']['d']['ppcp']       = $daytime['children'][4]['content'];
            $forecast[$i]['part']['d']['humid']      = $daytime['children'][5]['content'];

            $nighttime =& $children[5];
            $forecast[$i]['part']['n']['icon']       = $nighttime['children'][0]['content'];
            $forecast[$i]['part']['n']['cond']       = $nighttime['children'][1]['content'];
            $forecast[$i]['part']['n']['windspeed']  = $nighttime['children'][2]['children'][0]['content'];
            $forecast[$i]['part']['n']['windgust']   = $nighttime['children'][2]['children'][1]['content'];
            $forecast[$i]['part']['n']['winddirdeg'] = $nighttime['children'][2]['children'][2]['content'];
            $forecast[$i]['part']['n']['winddir']    = $nighttime['children'][2]['children'][3]['content'];
            $forecast[$i]['part']['n']['bt']         = $nighttime['children'][3]['content'];
            $forecast[$i]['part']['n']['ppcp']       = $nighttime['children'][4]['content'];
            $forecast[$i]['part']['n']['humid']      = $nighttime['children'][5]['content'];
            //$forecast[$i]['error'] = $error;
        }
        //var_dump($forecast); die();
        return $forecast;
    }


    /**
     * Get the current conditions forecast data
     *
     * @author  Roger Raymond <roger@xaraya.com>
     * @access  public
     * @return  array forecast data for the location
     */
    function &ccData($cache=true)
    {
        /*
        Grabbing the Current XML data for the Current Condition and the Current Conditions details
        */
        $p = new xarXmlParser();
        $xmi =& $this->getFile('cc',$this->units,$cache);
        if(!$p->parseString($xmi)) {
            // try again
            return $this->ccData(false);
        } else {
            $tree = $p->tree[0]['children'][0];
        }

        $head =& $tree['children'][0];
        $loc  =& $tree['children'][1];

        if ($tree['children'][2]['name'] == 'cc') {
            $cc =& $tree['children'][2];
        } else {
            $cc =& $tree['children'][3];    
        }
        $lnks  =& $tree['children'][2];
        $cc['linkOne']       = $lnks['children'][0]['children'][0]['content'];
        $cc['titleOne']      = $lnks['children'][0]['children'][1]['content'];
        $cc['linkTwo']       = $lnks['children'][1]['children'][0]['content'];
        $cc['titleTwo']      = $lnks['children'][1]['children'][1]['content'];
        $cc['linkThree']     = $lnks['children'][2]['children'][0]['content'];
        $cc['titleThree']    = $lnks['children'][2]['children'][1]['content'];
        $cc['linkFour']      = $lnks['children'][3]['children'][0]['content'];
        $cc['titleFour']     = $lnks['children'][3]['children'][1]['content'];

        $cc['unitsTemp']     = $head['children'][2]['content'];
        $cc['unitsDistance'] = $head['children'][3]['content'];
        $cc['unitsSpeed']    = $head['children'][4]['content'];
        $cc['unitsPrecip']   = $head['children'][5]['content'];
        $cc['tempPressure']  = $head['children'][6]['content'];

        $cc['location']      = $loc['attributes']['id'];
        $cc['sunrise']       = $loc['children'][4]['content'];
        $cc['sunset']        = $loc['children'][5]['content'];
        $cc['dnam']          = $loc['children'][5]['content'];
        $cc['lat']           = $loc['children'][2]['content'];
        $cc['lon']           = $loc['children'][3]['content'];
        $cc['tm']            = $loc['children'][1]['content'];
        $cc['zone']          = $loc['children'][6]['content'];

        $cc['lastUpdate']    = $cc['children'][0]['content'];
        $cc['observStation'] = $cc['children'][1]['content'];
        $cc['temp']          = $cc['children'][2]['content'];
        $cc['feelsLike']     = $cc['children'][3]['content'];
        $cc['conditions']    = $cc['children'][4]['content'];
        $cc['icon']          = $cc['children'][5]['content'];
        $cc['barometer']     = $cc['children'][6]['children'][0]['content'];
        $cc['barometerDesc'] = $cc['children'][6]['children'][1]['content'];
        $cc['wind']          = $cc['children'][7]['children'][0]['content'];
        $cc['windspeed']     = $cc['children'][7]['children'][0]['content'];
        $cc['windGust']      = $cc['children'][7]['children'][1]['content'];
        $cc['winddirdeg']    = $cc['children'][7]['children'][2]['content'];
        $cc['windDirname']   = $cc['children'][7]['children'][3]['content'];
        $cc['humidity']      = $cc['children'][8]['content'];
        $cc['visibility']    = $cc['children'][9]['content'];
        $cc['uv']            = $cc['children'][10]['children'][0]['content'];
        $cc['uvDesc']        = $cc['children'][10]['children'][1]['content'];
        $cc['dewPoint']      = $cc['children'][11]['content'];
        $cc['moonIcon']      = $cc['children'][12]['children'][0]['content'];
        $cc['moonDesc']      = $cc['children'][12]['children'][1]['content'];
        //$cc['error']         = $error;
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
            )
        );

        //$this->errorCheck($data);
        if(empty($data)) {
            return;
        }

        $p = new xarXmlParser();
        if(!$p->parseString($data)) {
            // try again
            return $this->locData($loc);
        } elseif(!isset($p->tree[0]['children'][0]['children'])) {
            // no results
            $null=null;
            return $null;
        } else {
            $tree = $p->tree[0]['children'][0]['children'];
        }

        $i=0;
        foreach($tree as $loc) {
            $info[$i]['zip']  = $loc['attributes']['id'];
            $info[$i]['name'] = $loc['content'];
            $i++;
        }

        return $info;
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