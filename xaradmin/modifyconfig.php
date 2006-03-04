<?php
/**
 * Modify the configuration options
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
/**
 * Modify Configuration
 * @return array
 */
function encyclopedia_admin_modifyconfig()
{
    if (!xarSecurityCheck('AdminEncyclopedia')) {return;}

    if(!xarVarFetch('welcome',   'str', $data['welcome']   ,xarModGetVar('encyclopedia', 'welcome'), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('upload',   'int', $data['upload']   ,xarModGetVar('encyclopedia', 'upload'), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('imagewidth',   'int', $data['imagewidth']   ,xarModGetVar('encyclopedia', 'imagewidth'), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('imageheight',   'int', $data['imageheight']   ,xarModGetVar('encyclopedia', 'imageheight'), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('autolinks',   'int', $data['autolinks']   ,xarModGetVar('encyclopedia', 'autolinks'), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('imageheight',   'str', $data['imageheight']   ,xarModGetVar('encyclopedia', 'imageheight'), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('longdisplay',   'int', $data['longdisplay']   ,xarModGetVar('encyclopedia', 'longdisplay'), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('columns',   'int', $data['columns']   ,xarModGetVar('encyclopedia', 'columns'), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('itemsperpage',   'int', $data['itemsperpage']   ,xarModGetVar('encyclopedia', 'itemsperpage'), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('allowsearch',   'int', $data['allowsearch']   ,xarModGetVar('encyclopedia', 'allowsearch'), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('allowletters',   'int', $data['allowletters']   ,xarModGetVar('encyclopedia', 'allowletters'), XARVAR_NOT_REQUIRED)) {return;}
    $data['authid'] = xarSecGenAuthKey();

    return $data;
}
?>