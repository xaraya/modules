<?php
/**
 * Overview function
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
function tinymce_admin_overview()
{
    if (!xarSecurityCheck('AdminTinyMCE')) return;

    $data=array();
    //common admin menu
    $data['menulinks'] = xarModAPIFunc('tinymce','admin','getmenulinks');

    return xarTplModule('tinymce', 'admin', 'main', $data);
}

?>