<?php 

/**
 * hookbridge Utilities
 *
 * @copyright   by Michael Cortez
 * @license     GPL (http://www.gnu.org/licenses/gpl.html)
 * @author      Michael Cortez
 * @link        
 *
 * @package     Xaraya eXtensible Management System
 * @subpackage  HookBridge Utility
 * @version     $Id$
 *
 */

/**
 * Administration for the hookbridge module.
 */
function hookbridge_admin_config( $args ) 
{

    if (!xarVarFetch('itemtype',   'str', $itemtype,   '',     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cancel',     'str', $cancel,     '',     XARVAR_NOT_REQUIRED)) return;
    
    extract( $args );

    // check if the user selected cancel
    if ( !empty( $cancel ) ) {

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        return xarResponseRedirect(
            xarModURL(
                'hookbridge'
                ,'admin'
                ,'config'
                ,array(
                    'itemtype' => 0 )));

    }

    // Get Common Data items
    $data = hookbridge_adminpriv_commondata($args);
    $data['itemtype'] = $itemtype;

    switch( $itemtype ) 
    {
        case 1:
            $itemtype_name = 'createhook';
            
            xarModAPIFunc( 'hookbridge','admin','config_createhook', $args );
            
            $data['hookenabled_create']      = xarModGetVar('hookbridge', 'hookenabled_create' );
            $data['hookfunctions_create']    = unserialize(xarModGetVar('hookbridge', 'hookfunctions_create' ));
			
			$data['available_hook_functions'] = hookbridge_adminpriv_get_available_hook_functions();
			
            break;
        case 2:
            $itemtype_name = 'updatehook';
            
            xarModAPIFunc( 'hookbridge','admin','config_updatehook', $args );
            
            $data['hookenabled_update']      = xarModGetVar('hookbridge', 'hookenabled_update' );
            $data['hookfunctions_update']    = unserialize(xarModGetVar('hookbridge', 'hookfunctions_update' ));
			
			$data['available_hook_functions'] = hookbridge_adminpriv_get_available_hook_functions();
			
            break;
			
        default:
            $itemtype_name = 'Hookbridge Configuration';
            xarModAPIFunc('hookbridge', 'admin', 'config_main', $args);
            
            $data['hookbridge_functionpath'] = xarModGetVar('hookbridge', 'HookBridge_FunctionPath' );
            
    }



    return xarTplModule(
        'hookbridge'
        ,'admin'
        ,'config'
        ,$data
        ,$itemtype_name );
}

function hookbridge_adminpriv_commondata( $args ) 
{
    $data = xarModAPIFunc(
        'hookbridge'
        ,'private'
        ,'common'
        ,array(
            'title' => xarML( 'Global Settings' )
            ,'type' => 'admin'
            ));

    $data['common']['menu_label'] = xarML( 'Configure' );
    $data['common']['menu']       = xarModAPIFunc('hookbridge','private','adminconfigmenu',0 );

    /*
     * Populate the rest of the template
     */
    $data['action']           = xarModURL('hookbridge','admin','config' );
    $data['authid']           = xarSecGenAuthKey();
    $data['supportshorturls'] = xarModGetVar('hookbridge','SupportShortURLs' );

    return $data;

}

function hookbridge_adminpriv_get_available_hook_functions()
{
	$hookbridge_functionpath = xarModGetVar('hookbridge', 'HookBridge_FunctionPath' );

	$available_hook_functions = array();
	if( isset($hookbridge_functionpath) && !empty($hookbridge_functionpath) )
	{
		$dir = $hookbridge_functionpath;
		
		// Open a known directory, and proceed to read its contents
		if (is_dir($dir)) 
		{
			if ($dh = opendir($dir)) 
			{
				while (($file = readdir($dh)) !== false) 
				{
					if( !is_dir($dir."/".$file) )
					{
						$available_hook_functions[$file] = $file;
					}
				}
				closedir($dh);
			}
		}
	}
	return $available_hook_functions;
}

/*
 * END OF FILE
 */
?>