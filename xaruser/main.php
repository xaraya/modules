<?php
/**
 * Main user function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.  As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 */
function accessmethods_user_main()
{
    if (!xarSecurityCheck('ViewDynExample')) return;

    $data = xarModAPIFunc('accessmethods','user','menu');

    // Specify some other variables used in the blocklayout template
    $data['welcome'] = xarML('Welcome to this Dynamic Example module...');
    $data['welcome'] .= '<br /><br />';
    $data['welcome'] .= xarML('You will find 4 different approaches for viewing and displaying dynamic items, as explained in the code and templates.');
    $data['welcome'] .= '<br /><br />';
    $data['welcome'] .= xarML('For your own modules, choose any one of them depending on how much (and where) you want to customize...');

    // Return the template variables defined in this function
    return $data;

}

?>
