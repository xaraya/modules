<?php
/**
 * Figlet Module
 *
 * @package modules
 * @subpackage figlet module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Lucas Baltes, John Cox
 */

// the hook function
//
function figlet_userapi_transform($args) 
{

    extract($args);
    require("modules/figlet/xarclass/phpfiglet_class.php");

    // Argument check
    if ((!isset($objectid)) ||
        (!isset($extrainfo))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = figlet_transform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        foreach ($extrainfo as $text) {
            $result[] = figlet_transform($text);
        }
    } else {
        $result = figlet_transform($text);
    }

    return $result;
}

// the wrapper for a string var (simple up to now)
//
function figlet_transform($text) 
{
    // pad it with a space so we can match things at the start of the 1st line.
    $ret = " " . $text;

    $phpFiglet = new phpFiglet();

    $font = xarModGetUserVar('figlet', 'defaultfont');

    if (!empty($ret)){

        if ($phpFiglet->loadFont("modules/figlet/xarfonts/$font")) {
            $ret = $phpFiglet->display("$ret");
        }
    }

    // Remove our padding..
    $message = substr($ret, 1);

    $messageformat = "<pre>$message</pre>";
    return $messageformat;
}
?>
