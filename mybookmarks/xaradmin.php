<?php
/**
 * xaradmin.php
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage My Bookmarks module
 * @author Scot Gardner
 * @version     $Id$
 */

/*
 * The main ( default ) administration view.
 */

function mybookmarks_admin_main() 
{

    if (!xarSecurityCheck( 'Editmybookmarks')) return;

    // Check if we should show the overview page
    // The admin system looks for a var to be set to skip the introduction
    // page altogether.  This allows you to add sparse documentation about the
    // module, and allow the site admins to turn it on and off as they see fit.
    if (xarModGetVar('adminpanels', 'overview') == 0) {

        // Yes we should
        $data = mybookmarks_admin_common( 'Overview' );
        return $data;

    }

    // No we shouldn't. So we redirect to the admin_view() function.
    xarResponseRedirect(
        xarModURL(
            'mybookmarks'
            ,'admin'
            ,'view' ));
    return true;

}

/**
 * Show a overview of all available administration options.
 *
 * This is the main page if the admin 'Disabled Module Overview' in
 * 'adminpanels - configurations - configure overview'.
 */

function mybookmarks_admin_view($args) 
{

    list( $itemtype ) = xarVarCleanFromInput('itemtype' );

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc(
                'mybookmarks'
                ,'mybookmarks'
                ,'view' );


        default:
            return mybookmarks_admin_common('Main Page'); }
}



/**
 * This function provides information to the templates which are common to all
 * pageviews.
 *
 * It provides the following informations:
 *
 *      'menu'      => Array with information about the module menu
 *      'statusmsg' => Status message if set
 */
function mybookmarks_admin_common( $title = 'Undefined' ) 
{

    $common = array();

    $common['menu'] = array();

    // Initialize the statusmessage
    $statusmsg = xarSessionGetVar( 'mybookmarks_statusmsg' );
    if ( isset( $statusmsg ) ) {
        xarSessionDelVar( 'mybookmarks_statusmsg' );
        $common['statusmsg'] = $statusmsg;
    }


    // Set the page title
    xarTplSetPageTitle($title);


    // Initialize the title
    $common['pagetitle'] = $title;
    $common['type']      = 'MyBookmarks Administration';

    return array( 'common' => $common );
}


/**
 * Standard interface for the creation of objects.
 *
 * We just forward to the appropiate mybookmarks_adminapi_create<table>()
 * function.
 *
 */
function mybookmarks_admin_new( $args ) 
{

    list( $authid, $preview, $itemtype, $cancel ) =
        xarVarCleanFromInput( 'authid', 'preview', 'itemtype', 'cancel' );
    extract( $args );

    /*
     * Return to the itemtype's view page if
     *  -> If the user decided to cancel the action
     *  -> There is no itemtype ( will go to main view )
     */
    if ( !empty( $cancel ) or empty( $itemtype ) ) {

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponseRedirect(
            xarModURL(
                'mybookmarks'
                ,'admin'
                ,'view'
                ,array(
                    'itemtype' => $itemtype )));

    }

    // These function is called under different contexts.
    // 1. first time ( authid is not set )
    // 2. preview    ( authid is set, preview is set )
    // 3. Submit     ( authid is set )
    if ( isset( $authid ) ) {

        // Confirm the authorization key
        if (!xarSecConfirmAuthKey()) return;

        if ( empty($preview) ) {

            switch( $itemtype ) {

                case 1:
                    return xarModAPIFunc(
                        'mybookmarks'
                        ,'mybookmarks'
                        ,'create'
                        ,$args );

                default:
                    // TODO // Add statusmessage
                    xarResponseRedirect(
                        xarModURL(
                            'mybookmarks'
                            ,'admin'
                            ,'view' ));
            }
        }

    }

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc(
                'mybookmarks'
                ,'mybookmarks'
                ,'new'
                ,$args );

        default:
            // TODO // Add statusmessage
            xarResponseRedirect(
                xarModURL(
                    'mybookmarks'
                    ,'admin'
                    ,'view' ));
    }
}


/**
 * Standard interface for the modification of objects.
 *
 * We just forward to the appropiate mybookmarks_admin_modify<table>()
 * function.
 *
 */
function mybookmarks_admin_modify( $args ) 
{

    list( $itemtype, $itemid, $cancel, $authid, $preview ) =
        xarVarCleanFromInput('itemtype', 'itemid', 'cancel', 'authid', 'preview' );
    extract( $args );

    /*
     * Return to the itemtype's view page if
     *  -> If the user decided to cancel the action
     *  -> There is no itemid to modify
     *  -> There is no itemtype ( will go to main view )
     */
    if ( !empty( $cancel ) or empty( $itemid ) or empty( $itemtype ) ) {

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponseRedirect(
            xarModURL(
                'mybookmarks'
                ,'admin'
                ,'view'
                ,array(
                    'itemtype' => $itemtype )));

    }

    // check if authid is set.
    if ( isset( $authid ) ) {

        // Confirm the authorization key
        if (!xarSecConfirmAuthKey()) return;

        // Check if a preview is wished
        if ( !isset( $preview ) ) {

            switch( $itemtype ) {

                case 1:
                    return xarModAPIFunc(
                        'mybookmarks'
                        ,'mybookmarks'
                        ,'update'
                        ,$args );

                default:
                    // TODO // Add statusmessage
                    xarResponseRedirect(
                        xarModURL(
                            'mybookmarks'
                            ,'admin'
                            ,'view' ));
            }
        }
    }

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc(
                'mybookmarks'
                ,'mybookmarks'
                ,'modify'
                ,$args );

        default:
            // TODO // Add statusmessage
            xarResponseRedirect(
                xarModURL(
                    'mybookmarks'
                    ,'admin'
                    ,'view' ));
    }

}


/**
 * Standard interface for the deletion of objects.
 *
 * We just forward to the appropiate mybookmarks_adminapi_delete<table>()
 * function.
 *
 */
function mybookmarks_admin_delete( $args ) 
{

    list( $authid, $confirm, $itemtype, $cancel, $itemid ) =
        xarVarCleanFromInput( 'authid', 'confirm', 'itemtype', 'cancel', 'itemid' );
    extract( $args );

    /*
     * Return to the itemtype's view page if
     *  -> If the user decided to cancel the action
     *  -> There is no itemid to delete
     *  -> There os no itemtype ( will go to main view )
     */
    if ( !empty( $cancel ) or empty( $itemid ) or empty( $itemtype ) ) {

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponseRedirect(
            xarModURL(
                'mybookmarks'
                ,'admin'
                ,'view'
                ,array(
                    'itemtype' => $itemtype )));

    }

    // These function is called under different contexts.
    // 1. first time ( authid is not set )
    // 2. confirm    ( authid is set )
    if ( isset( $authid ) ) {

        // Confirm the authorization key
        if (!xarSecConfirmAuthKey()) return;

        // Check if the user selected Delete
        if ( isset( $confirm ) ) {

            switch( $itemtype ) {

                case 1:
                    return xarModAPIFunc(
                        'mybookmarks'
                        ,'mybookmarks'
                        ,'delete'
                        ,$args );


                default:
                    // TODO // Add statusmessage
                    xarResponseRedirect(
                        xarModURL(
                            'mybookmarks'
                            ,'admin'
                            ,'view' ));
            }

        }
    }

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc(
                'mybookmarks'
                ,'mybookmarks'
                ,'confirmdelete'
                , $args );

        default:
            // TODO // Add statusmessage
            xarResponseRedirect(
                xarModURL(
                    'mybookmarks'
                    ,'admin'
                    ,'view' ));
    }
}

/**
 * Administration for the mybookmarks module.
 */
function mybookmarks_admin_config( $args ) 
{

    list( $cancel, $itemtype ) = xarVarCleanFromInput( 'cancel', 'itemtype' );
    extract( $args );

    // check if the user selected cancel
    if ( !empty( $cancel ) ) {

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponseRedirect(
            xarModURL(
                'mybookmarks'
                ,'admin'
                ,'config'
                ,array(
                    'itemtype' => $itemtype )));

    }

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc(
                'mybookmarks'
                ,'mybookmarks'
                ,'config'
                ,$args );


        default:
            return mybookmarks_adminpriv_config( $args );
    }
}

/**
 * Administration for the mybookmarks module.
 */
function mybookmarks_adminpriv_config( $args ) 
{

    $data = mybookmarks_admin_common( 'Module Configuration' );

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
            'mybookmarks'
            ,'SupportShortURLs'
            ,$supportshorturls );



        /*
         * Set a status message
         */
        xarSessionSetVar(
            'mybookmarks_statusmsg'
            ,'Updated the modules configuration!' );

        /*
         * Finished. Back to the sender!
         */
        xarResponseRedirect(
            xarModURL(
                'mybookmarks'
                ,'admin'
                ,'config'
                ,array(
                    'itemtype' => $itemtype )));

    } // Save the changes



    $data['common']['menu_label'] = 'Configure';
    $data['common']['menu']       = mybookmarks_adminpriv_configmenu();


    /*
     * Populate the rest of the template
     */
    $data['action']     = xarModURL(
        'mybookmarks'
        ,'admin'
        ,'config' );
    $data['authid']     = xarSecGenAuthKey();
    $data['supportshorturls']   = xarModGetVar(
        'mybookmarks'
        ,'SupportShortURLs' );
    return $data;

}

/**
 * Create a little submenu for the configuration screen.
 */
function mybookmarks_adminpriv_configmenu() 
{

    /*
     * Build the configuration submenu
     */
    $menu = array(
        array(
            'label' =>  'Config',
            'url'   =>  xarModURL(
                'mybookmarks',
                'admin',
                'config' )));


    $menu[] = array(
            'label' =>  'Bookmarks',
            'url'   =>  xarModURL(
                'mybookmarks',
                'admin',
                'config'
                ,array( 'itemtype' => '1' )));


    return $menu;

}

/*
 * END OF FILE
 */
?>