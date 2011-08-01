<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

function uploads_user_errors()
{
    if (!xarSecurityCheck('ViewUploads')) return;
    
    if (!xarVarFetch('layout','str:1:100',$data['layout'],'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxallowed','str:1:100',$data['maxallowed'],'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('location','str:1:100',$data['location'],'',XARVAR_NOT_REQUIRED)) return;

    return $data;
}
?>