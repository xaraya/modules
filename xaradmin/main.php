<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xartinymce
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * The main administration function
 */
function tinymce_admin_main()
{
    if (!xarSecurityCheck('AdminTinyMCE')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0) {
           xarResponseRedirect(xarModURL('tinymce', 'admin', 'overview'));
    } else {
        xarResponseRedirect(xarModURL('tinymce', 'admin', 'modifyconfig'));
    }
    /* success */
    return true;
}

?>