<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
function uploads_user_errors()
{
    if (!xarSecurityCheck('ViewUploads')) return;
    
    if (!xarVarFetch('layout','str:1:100',$data['layout'],'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxallowed','str:1:100',$data['maxallowed'],'',XARVAR_NOT_REQUIRED)) return;

    return $data;
}
?>