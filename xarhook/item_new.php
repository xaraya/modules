<?php 

/**
 * Hook Bridge
 *
 * @copyright   by Michael Cortez
 * @license     GPL (http://www.gnu.org/licenses/gpl.html)
 * @author      Michael Cortez
 * @link        
 *
 * @package     Xaraya eXtensible Management System
 * @subpackage  Hook Bridge
 * @version     $Id$
 *
 */

/**
 * Hook function called for Item New GUI Hook.
 * 
 * @returns string
 * @return  GUI to be displayed
 */

function hookbridge_hook_item_new ( $args ) 
{
    // First check to see if the site admin has enabled hookbridge for this type of hook
    $hookenabled_new = xarModGetVar('hookbridge', 'hookenabled_new' );
    if( !$hookenabled_new )
    {
        return '';
    }


    extract( $args );
    
    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'admin', 'item_new', 'hookbridge');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Security check. Adjust.
    // if (!xarSecurityCheck('SubmitCategoryLink',0,'Link',"$modid:All:All:All")) return '';

    return xarTplModule(
        'hookbridge'
        ,'hook'
        ,'item_new'
        ,array()
        );

}

/*
 * END OF FILE
 */
?>