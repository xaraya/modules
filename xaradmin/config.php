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

    if (!xarVarFetch('authid',   'str', $authid,   '',     XARVAR_NOT_REQUIRED)) return;
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

    $itemtypes = $data['common']['itemtype_array'];

    if( array_key_exists( $itemtype, $itemtypes ) )
    {
        // Get info for this item type
        $itvar  = $itemtypes[$itemtype]['var'];
        $itname = $itemtypes[$itemtype]['name'];
        
        $tplNameExtension = $itvar.'hook';
        
        $data['hookenabled']      = xarModGetVar('hookbridge', 'hookenabled_'.$itvar );
        $hookfunctionsStr         = xarModGetVar('hookbridge', 'hookfunctions_'.$itvar );
        if( isset($hookfunctionStr) && !empty($hookfunctionStr) )
        {
            $data['hookfunctions']    = xarModGetVar('hookbridge', 'hookfunctions_'.$itvar );
        } else {
            $data['hookfunctions']    = array();
        }

        $data['available_hook_functions'] = hookbridge_adminpriv_get_available_hook_functions();

        // Check if the user submitted a config form. If so, save the results.
        if ( isset( $authid ) && !empty($authid)  ) 
        {
            if (!xarSecConfirmAuthKey()) return;

            if (!xarVarFetch('hookenabled',   'str',   $hookenabled,   '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('hookfunctions', 'array', $hookfunctions, '', XARVAR_NOT_REQUIRED)) return;

            if ( empty( $hookenabled ) or !is_numeric( $hookenabled ) ) 
            {
                $hookenabled = 0;
            }

            xarModSetVar('hookbridge', 'hookenabled_'.$itvar,   $hookenabled );
            xarModSetVar('hookbridge', 'hookfunctions_'.$itvar, serialize($hookfunctions) );

            // Set a status message
            xarSessionSetVar('hookbridge_statusmsg', xarML( 'Updated the '.$itname.' settings!' ) );

            // Config saved, bounce back to hookbridge config screen
            return xarResponseRedirect(
                xarModURL('hookbridge','admin','config',array(
                                                                'itemtype' => $itemtype )
                                                              ));
        }

    } else {
        $tplNameExtension = 'mainconfig';
        xarModAPIFunc('hookbridge', 'admin', 'config_main', $args);
        
        $data['hookbridge_functionpath'] = xarModGetVar('hookbridge', 'HookBridge_FunctionPath' );
    }

    return xarTplModule(
        'hookbridge'
        ,'admin'
        ,'config'
        ,$data
        ,$tplNameExtension );
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
    $data['common']['menu']       = xarModAPIFunc('hookbridge','private','adminconfigmenu',array('itemtype'=>0,'itemtype_array'=>$data['common']['itemtype_array']) );

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
