<?php
/**
 * Get an IP address from current user
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Get an IP Address
 * @author jojodee
 * @return $userip - a 'best guess' IP address for the current user
 */
function sitecontact_adminapi_getcurrentip()
{
   if(!xarSecurityCheck('ReadSiteContact', 0)) return;

    $data=array(); //initialize our data array
    $proxyip=''; //proxy ip if it exists
    $trueip=''; //true ip if it exists
    $isanip=0; //is this an ip

    $remote_addr = xarServerGetVar('REMOTE_ADDR');
    $x_forwarded_for= xarServerGetVar('HTTP_X_FORWARDED_FOR');
    $x_forwarded= xarServerGetVar('HTTP_X_FORWARDED');
    $forwarded_for= xarServerGetVar('HTTP_FORWARDED_FOR');
    $forwarded= xarServerGetVar('HTTP_FORWARDED');
    $x_comingfrom=xarServerGetVar('HTTP_X_COMING_FROM');
    $comingfrom=xarServerGetVar('HTTP_COMING_FROM');
    $httpvia=xarServerGetVar('HTTP_VIA');
    /* Gets the ip sent by the user */
    if (!empty($remote_addr )) {
        $trueip = $remote_addr;
    }
    
    /* Gets the proxy ip if it exists and  sent */

    if (!empty($x_fowarded_for)){
        $proxyip = $x_forwarded_for;
    } elseif (!empty($x_forwarded)) {
        $proxyip = $x_forwarded;
    } elseif (!empty($forwarded_for)) {
        $proxyip = $forwarded_for;
    } elseif (!empty($forwarded)) {
        $proxyip = $forwarded;
    }elseif (!empty($httpvia)) {
        $proxyip = $httpvia;
    } elseif (!empty($x_comingfrom)) {
        $proxyip = $x_comingfrom;
    } elseif (!empty($comingfrom)) {
        $proxyip = $comingfrom;
    }
    /* watch out for  more than one ... */
    $multi_proxyip = explode(";", $proxyip);
    $proxyip = $multi_proxyip[0]; //take the first

    if (empty($proxyip)) {
       $userip= $trueip;
    } else {
        /* check the ip */
        $results=0;
        $isanip = ereg('^([0-9]{1,3}.){3,3}[0-9]{1,3}', $proxyip, $results);

             if ($isanip && (count($results) > 0)) {
                 $userip=$results[0];
             } else {
                 // hmm not much we can do?
                 $userip='';
             }
    }
    return $userip;
}

?>