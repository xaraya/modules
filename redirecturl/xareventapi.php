<?php
/**
 * Redirect URL
 *
 * @copyright (C) 2003-2005 by Envision Net, Inc.
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 * @link http://www.envisionnet.net
 * @author Brian McGilligan <brian@envisionnet.net>
 *
 * @package Xaraya eXtensible Management System
 * @subpackage Redirect URL module
*/

/**
    Function takes over Redirects for "valid" module/function pairs
*/
function redirecturl_eventapi_OnResponseRedirect($arg)
{
    $module         =  xarRequestGetVar('module');
    $func           =  xarRequestGetVar('func');
    $validRedirects =& redirecturl_eventapi_getValidRedirects();

    // find the redirect url
    $returnUrl = xarSessionGetVar("redirecturl");
    xarSessionDelVar("redirecturl");
     
    // Time to redirect
    if( !empty($returnUrl) )
    {         
        if( isset($validRedirects[$module]) && in_array($func, $validRedirects[$module]) )
        {
            $returnUrl = str_replace('&amp;', '&', $returnUrl);
            header("Location: " . $returnUrl);
            exit();
        }
    }

    return true;
}

/**
    Saves last "invalid" Redirect
    (It's save point)
*/
function redirecturl_eventapi_OnServerRequest($arg)
{   
    $module         =  xarRequestGetVar('module');
    $func           =  xarRequestGetVar('func');
    $validRedirects =& redirecturl_eventapi_getValidRedirects();

    if( isset($validRedirects[$module]) && !in_array($func, $validRedirects[$module]) && xarCurrentErrorType() == XAR_NO_EXCEPTION  )
    {
        xarSessionSetVar("redirecturl", xarServerGetCurrentURL());
    }
    return true;
}

/**
    Returns list of valid module/func pairs to save
    NOTE: I'm not using this currently as all non Redirets 
          I want to save, but it could be for more control
*/
function redirecturl_eventapi_getValidSavePoints()
{
    $valid['articles']   = array();
    $valid['ui']         = array();
    $valid['myarticles'] = array();
    $valid['roles']      = array();

    return $valid;
}

/**
    Returns list of valid module/func pairs to override ResponseRedirects
*/
function redirecturl_eventapi_getValidRedirects()
{
    $valid['articles']   = array('new', 'modify', 'create', 'update');
    $valid['ui']         = array('new', 'modify', 'create', 'update');
    $valid['myarticles'] = array();
    $valid['roles']      = array('login');

    return $valid;
}
?>
