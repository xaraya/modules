<?php
/**
 * The main administration function
 *
 * @package modules
 * @copyright (C) 2006-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage JpGraph Module
 * @link http://xaraya.com/index.php/release/819.html
 * @author JpGraph Module Development Team
 */

/**
 * The main administration function
 *
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments. As such it can
 * be used for a number of things, but most commonly it either just
 * shows the module menu and returns or calls whatever the module
 * designer feels should be the default function (often this is the
 * view() function)
 *
 * @author JpGraph Module Development Team
 * @access public
 * @return bool true
 */
function jpgraph_admin_main()
{
    /* Security check
     */
    if (!xarSecurityCheck('EditJpGraph')) return;
       /* If you want to go directly to some default function, instead of
         * having a separate main function, you can simply call it here, and
         * use the same template for admin-main.xd as for admin-view.xd
         * return xarModFunc('jpgraph','admin','view');
         */
        xarResponseRedirect(xarModURL('jpgraph', 'admin', 'overview'));

    /* success so return true */
    return true;
}
?>