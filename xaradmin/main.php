<?php
/**
 * Email authentication module. Allows you to login with your email address
 * instead of username.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * 
 * @subpackage Authemail Module
 * @link http://xaraya.com/index.php/release/10513.html
*/

/**
 * The main administration function
 *
 * @author jojodee
 * @access public
 * @return Specify your return type here
 */
function authemail_admin_main()
{
   xarResponseRedirect(xarModURL('authemail', 'admin', 'overview'));

    /* success so return true */
    return true;
}
?>