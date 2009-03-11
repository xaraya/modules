<?php
/**
 * Twitter Module
 *
 * @package modules
 * @copyright (C) 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */

/**
 * Handle requests to TinyURL API, transforms any valid url to a tinyurl
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @param url string url to transform to a TinyURL
 * @return mixed string containing transformed url, bool false on failure
 */
function twitter_utilapi_tinyurl($args)
{
    extract($args);
    if (empty($url)) return false;
    if (function_exists('curl_init')) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $tinyurl = curl_exec($ch);
        curl_close($ch);
    }
    if (empty($tinyurl)) return false;
    return $tinyurl;
}

?>