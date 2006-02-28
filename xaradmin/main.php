<?php
/**
 * Main administrative function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xartinymce module
 * @link http://xaraya.com/index.php/release/63.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * The main administration function
 */
function tinymce_admin_main()
{
    if (!xarSecurityCheck('AdminTinyMCE')) return;

        xarResponseRedirect(xarModURL('tinymce', 'admin', 'modifyconfig'));

    /* success */
    return true;
}

?>