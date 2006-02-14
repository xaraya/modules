<?php
/**
 * The main user function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Carts Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Bel Fabien (fab@webu.fr)
 */

/**
 * The main user function
 *
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments. As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 *
 * @author the Example module development team
 */
function carts_user_main()
{

    if (!xarSecurityCheck('ViewCarts')) return;

    $data = xarModAPIFunc('example', 'user', 'menu');
 
    $data['welcome'] = xarML('Welcome to this Example module...');

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Welcome')));

    return $data;

}
?>