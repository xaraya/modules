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

    list( $cancel, $itemtype ) = xarVarCleanFromInput( 'cancel', 'itemtype' );
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
			
			hookbridge_adminpriv_config_createhook( $args );
			
	        $data['createhooksenabled']  = xarModGetVar('hookbridge', 'CreateHooksEnabled' );
	        $data['createhooksfunction'] = xarModGetVar('hookbridge', 'CreateHooksFunction' );
			break;
        default:
			$itemtype_name = 'Hookbridge Configuration';
			hookbridge_adminpriv_config( $args );
			
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
    $data['action']     	  = xarModURL('hookbridge','admin','config' );
    $data['authid']     	  = xarSecGenAuthKey();
    $data['supportshorturls'] = xarModGetVar('hookbridge','SupportShortURLs' );

    return $data;

}

/**
 * Administration for the hookbridge module.
 */
function hookbridge_adminpriv_config( $args ) 
{

    list( $itemtype, $authid ) = xarVarCleanFromInput( 'itemtype', 'authid' );
    extract( $args );

    if ( isset( $authid ) ) 
	{

        /*
         * The user confirmed the form. So save the results.
         */

        if (!xarSecConfirmAuthKey()) return;


        $supportshorturls = xarVarCleanFromInput( 'supportshorturls' );

        if ( empty( $supportshorturls ) or !is_numeric( $supportshorturls ) ) 
		{
            $supportshorturls = 0;
        }

        xarModSetVar('hookbridge', 'SupportShortURLs', $supportshorturls );



        $hookbridge_functionpath = xarVarCleanFromInput( 'hookbridge_functionpath' );
        xarModSetVar('hookbridge', 'HookBridge_FunctionPath', $hookbridge_functionpath );
        

        /*
         * Set a status message
         */
        xarSessionSetVar('hookbridge_statusmsg', xarML( 'Updated the global module settings!' ) );

        /*
         * Finished. Back to the sender!
         */
        return xarResponseRedirect(
            xarModURL('hookbridge','admin','config',array(
										                    'itemtype' => $itemtype )
														  ));

    } // Save the changes


}

/**
 * CreateHook config for the hookbridge module.
 */
function hookbridge_adminpriv_config_createhook( $args ) 
{

    list( $itemtype, $authid ) = xarVarCleanFromInput( 'itemtype', 'authid' );
    extract( $args );

    if ( isset( $authid ) ) 
	{

        /*
         * The user confirmed the form. So save the results.
         */

        if (!xarSecConfirmAuthKey()) return;


		$data['createhooksenabled']  = xarModGetVar('hookbridge', 'CreateHooksEnabled' );
		$data['createhooksfunction'] = xarModGetVar('hookbridge', 'CreateHooksFunction' );

        $createhooksenabled  = xarVarCleanFromInput( 'createhooksenabled' );
        $createhooksfunction = xarVarCleanFromInput( 'createhooksfunction' );

        if ( empty( $createhooksenabled ) or !is_numeric( $createhooksenabled ) ) 
		{
            $createhooksenabled = 0;
        }

        xarModSetVar('hookbridge', 'CreateHooksEnabled', $createhooksenabled );
        xarModSetVar('hookbridge', 'CreateHooksFunction', $createhooksfunction );

        

        /*
         * Set a status message
         */
        xarSessionSetVar('hookbridge_statusmsg', xarML( 'Updated the CreateHook module settings!' ) );

        /*
         * Finished. Back to the sender!
         */
        return xarResponseRedirect(
            xarModURL('hookbridge','admin','config',array(
										                    'itemtype' => $itemtype )
														  ));

    } // Save the changes


}

/*
 * END OF FILE
 */
?>
