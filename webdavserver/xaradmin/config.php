<?php 

/**
 * webdavserver
 *
 * @copyright   by Marcel van der Boom
 * @license     GPL (http://www.gnu.org/licenses/gpl.html)
 * @author      Marcel van der Boom
 * @link        
 *
 * @package     Xaraya eXtensible Management System
 * @subpackage  webdavserver
 * @version     $Id$
 *
 */

/**
 * Administration for the webdavserver module.
 */
function webdavserver_admin_config( $args ) 
{

    list( $cancel, $itemtype ) = xarVarCleanFromInput( 'cancel', 'itemtype' );
    extract( $args );

    // check if the user selected cancel
    if ( !empty( $cancel ) ) {

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        return xarResponseRedirect(
            xarModURL(
                'webdavserver'
                ,'admin'
                ,'config'
                ,array(
                    'itemtype' => $itemtype )));

    }

    switch( $itemtype ) {
    

        default:
            return webdavserver_adminpriv_config( $args );
    }

    return xarTplModule(
        'webdavserver'
        ,'admin'
        ,'config'
        ,$data
        ,$itemtype_name );
}

/**
 * Administration for the webdavserver module.
 */
function webdavserver_adminpriv_config( $args ) 
{

    $data = xarModAPIFunc(
        'webdavserver'
        ,'private'
        ,'common'
        ,array(
            'title' => xarML( 'Global Settings' )
            ,'type' => 'admin'
            ));

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
            'webdavserver'
            ,'SupportShortURLs'
            ,$supportshorturls );

        

        /*
         * Set a status message
         */
        xarSessionSetVar(
            'webdavserver_statusmsg'
            ,xarML( 'Updated the global module settings!' ) );

        /*
         * Finished. Back to the sender!
         */
        return xarResponseRedirect(
            xarModURL(
                'webdavserver'
                ,'admin'
                ,'config'
                ,array(
                    'itemtype' => $itemtype )));

    } // Save the changes

    

    $data['common']['menu_label'] = xarML( 'Configure' );
    $data['common']['menu']       = xarModAPIFunc(
        'webdavserver'
        ,'private'
        ,'adminconfigmenu'
        ,0 );

    /*
     * Populate the rest of the template
     */
    $data['action']     = xarModURL(
        'webdavserver'
        ,'admin'
        ,'config' );
    $data['authid']     = xarSecGenAuthKey();
    $data['supportshorturls']   = xarModGetVar(
        'webdavserver'
        ,'SupportShortURLs' );
    return $data;

}

/*
 * END OF FILE
 */
?>
