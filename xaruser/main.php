<?php
/**
 * Main user function
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */
/**
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.  As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 *
 * @todo Turn str for time to timestamp
 */
function sigmapersonnel_user_main()
{
    if (!xarSecurityCheck('ViewSIGMAPersonnel')) return;

    $data = xarModAPIFunc('sigmapersonnel', 'user', 'menu');
    // Specify some other variables used in the blocklayout template
    $dtasked = time();
    $uid = xarUserGetVar('uid');
    $presenceid = xarModAPIFunc('sigmapersonnel', 'user', 'presencenow', array('uid'=>$uid, 'dtasked' => $dtasked));
    if ($presenceid) {
        $data['currentpresence'] = xarModAPIFunc('sigmapersonnel', 'user', 'getprestype', array('type' => $presenceid));
    } else {
        $data['currentpresence'] = xarML('Unknown');
    }

    // We also may want to change the title of the page for a little
    // better search results from the spiders.
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Your entry to SIGMA Personnel data')));
    // Return the template variables defined in this function
    return $data;
}
?>