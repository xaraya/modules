<?php
/**
 * Main user function
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage downloads
 * @link http://www.xaraya.com/index.php/release/19741.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.  As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 */
function downloads_user_main()
{

    if(!xarVarFetch('startnum', 'isset', $data['startnum'], NULL, XARVAR_DONT_SET)) {return;} 

    $data['seccheck'] = FALSE;

    if (!xarSecurityCheck('ViewDownloads')) {
        return;
    } else {
        $data['seccheck'] = TRUE;
    }

    // Return the template variables defined in this function
    return $data;

}

?>