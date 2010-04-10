<?php
/**
 * Update of Configuration 
 *
 * @package modules
 * @copyright (C) 2002-2008,2009 2skies.com 
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xarigami.com/projects/xartinymce
 *
 * @subpackage xartinymce module
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * Update configuration parameters to relevant vars in database
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function tinymce_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;

    if (!xarVarFetch('activetinymce',   'checkbox', $activetinymce, true,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage',    'str',      $itemsperpage,  30,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('default',         'str',      $default,       'default',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tinyloadmode',    'str:1:',   $tinyloadmode,  'auto',XARVAR_NOT_REQUIRED)) return;    
    
    xarModSetVar('tinymce','activetinymce', $activetinymce);                  
    xarModSetVar('tinymce', 'itemsperpage', $itemsperpage);
    xarModSetVar('tinymce', 'default',      $default); 
    xarModSetVar('tinymce', 'tinyloadmode', $tinyloadmode);   
 
   xarResponseRedirect(xarModURL('tinymce', 'admin', 'modifyconfig'));

    return true;
}

?>