<?php
/**
 * The main administration function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 * @link http://xaraya.com/index.php/release/46.html
 * @author Jason Judge
 */
/**
 * The main administration function
 *
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments.  As such it can
 * be used for a number of things, but most commonly it either just
 * shows the module menu and returns or calls whatever the module
 * designer feels should be the default function (often this is the
 * view() function)
 *
 * @author MichelV <michelv@xaraya.com>
 * @author Lists Module Development Team
 * @TODO MichelV: <1> Security
 */
function lists_admin_main()
{
    //if (!xarSecurityCheck('EditLists')) return;
    xarResponseRedirect(xarModURL('lists', 'admin', 'view'));
    /* success so return true */
    return true;
}
?>