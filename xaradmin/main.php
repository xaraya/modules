<?php
/**
 * Window Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Window Module
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Window Module Development Team
 */
function window_admin_main()
{
    if (!xarSecurityCheck('AdminWindow')) return;
        return xarResponseRedirect(xarModURL('window', 'admin', 'newurl'));

}
?>