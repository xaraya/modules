<?php
/**
 * Administration for the mybookmarks module.
 */
function messages_admin_config( $args ) 
{

    list( $cancel, $itemtype ) = xarVarCleanFromInput( 'cancel', 'itemtype' );
    extract( $args );

    // check if the user selected cancel
    if ( !empty( $cancel ) ) {

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponseRedirect(
            xarModURL(
                'messages'
                ,'admin'
                ,'config'
                ,array(
                    'itemtype' => $itemtype )));

    }

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc(
                'messages'
                ,'messages'
                ,'config'
                ,$args );


        default:
            return messages_adminpriv_config( $args );
    }
}

/**
 * Administration for the mybookmarks module.
 */
function messages_adminpriv_config( $args ) 
{

//    $data = messages_admin_common( 'Module Configuration' );

    list( $itemtype, $authid ) = xarVarCleanFromInput( 'itemtype', 'authid' );
    extract( $args );

    if ( isset( $authid ) ) {

        /*
         * The user confirmed the form. So save the results.
         */

        if (!xarSecConfirmAuthKey()) return;

        $supportshorturls = xarVarCleanFromInput( 'supportshorturls' );

        if ( empty( $supportshorturls ) or !is_numeric( $supportshorturls ) ) {
            $supportshorturls = 0;
        }

        xarModSetVar(
            'messages'
            ,'SupportShortURLs'
            ,$supportshorturls );



        /*
         * Set a status message
         */
        xarSessionSetVar(
            'messages_statusmsg'
            ,'Updated the modules configuration!' );

        /*
         * Finished. Back to the sender!
         */
        xarResponseRedirect(
            xarModURL(
                'messages'
                ,'admin'
                ,'config'
                ,array(
                    'itemtype' => $itemtype )));

    } // Save the changes



    $data['common']['menu_label'] = 'Configure';
    $data['common']['menu']       = messages_adminpriv_configmenu();


    /*
     * Populate the rest of the template
     */
    $data['action']     = xarModURL(
        'messages'
        ,'admin'
        ,'config' );
    $data['authid']     = xarSecGenAuthKey();
    $data['supportshorturls']   = xarModGetVar(
        'messages'
        ,'SupportShortURLs' );
    return $data;

}

function messages_adminpriv_configmenu() 
{

    /*
     * Build the configuration submenu
     */
    $menu = array(
        array(
            'label' =>  'Config',
            'url'   =>  xarModURL(
                'messages',
                'admin',
                'config' )));


    $menu[] = array(
            'label' =>  'Messages',
            'url'   =>  xarModURL(
                'messages',
                'admin',
                'config'
                ,array( 'itemtype' => '1' )));


    return $menu;

}
?>
