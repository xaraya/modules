<?php
/**
 * @package ie7
 * @copyright (C) 2004 by Ninth Avenue Software Pty Ltd
 * @link http://www.ninthave.net
 * @author Roger Keays <roger.keays@ninthave.net>
 */

/**
 * Insert required javascript for IE browsers.
 */
function ie7_eventapi_OnServerRequest()
{
    /* this value set by sniffer module */
    $browser = xarSessionGetVar('browsername');

    /* just incase it isn't set yet */
    if (empty($browser)) {
        xarModAPIFunc('sniffer', 'user', 'sniff');
        $browser = xarSessionGetVar('browsername');
    }
    if (xarModGetVar('ie7', 'enabled') && !empty($browser) &&
        $browser == 'Microsoft Internet Explorer') {

        /* load the required javascript */
        xarTplAddJavaScript('head', 'src', 
                xarServerGetBaseURL().
                'modules/ie7/xarincludes/ie7/ie7-standard.js');

        /* other config options */
        if (xarModGetVar('ie7', 'css3')) {
            xarTplAddJavaScript('head', 'src', 
                xarServerGetBaseURL().
                'modules/ie7/xarincludes/ie7/ie7-css3.js');
        }
    }
    return true;
}

?>
