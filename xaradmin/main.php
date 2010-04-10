<?php
/**
 * Main administrative function
 *
 * @package modules
 * @copyright (C) 2004-2009 2skies.com
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html} 
 * @link http://xarigami.com/project/xartinymce
 *
 * @subpackage xartinymce module
 * @author Jo Dalle Nogare <icedlava@2skies.com>
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