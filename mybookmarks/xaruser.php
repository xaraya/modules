<?php
/**
 * xaruser.php
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage My Bookmarks module
 * @author Scot Gardner
 * @version     $Id$
 */

function mybookmarks_user_main() 
{

    // Security Check
    // It is important to do this as early as possible to avoid potential
    // security holes or just too much wasted processing.  For the main
    // function we want to check that the user has at least edit privilege for
    // some item within this component, or else they won't be able to do
    // anything and so we refuse access altogether.  The lowest level of
    // access for administration depends on the particular module, but it is
    // generally either 'edit' or 'delete'.
    if (!xarSecurityCheck( 'Viewmybookmarks')) return;

    $data = mybookmarks_user_common( 'My Bookmarks' );

    return $data;
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
function mybookmarks_user_common( $title = 'Undefined' ) 
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


    // Initilaize the title
    $common['pagetitle'] = $title;

    return array( 'common' => $common );
}

/**
 * Standard interface for displaying objects
 *
 * This is a generic display() function for DD handled itemtype's. The
 * itemtype specific parts are separated to a function
 * userpriv_displaytable().
 *
 *
 */
function mybookmarks_user_display( $args ) 
{

    $itemtype = xarVarCleanFromInput( 'itemtype' );
    extract( $args );

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc(
                'mybookmarks'
                ,'mybookmarks'
                ,'display'
                , $args );


        default:
            xarSessionSetVar(
                'mybookmarks_statusmsg'
                ,'Error: Itemtype not specified or invalid. Redirected you to main page!' );

            xarResponseRedirect(
                xarModURL(
                    'mybookmarks'
                    ,'user'
                    ,'main' ));
    }
}

/**
 * Standard interface for view item lists
 *
 * This is a generic display() function for DD handled itemtype's. The
 * itemtype specific parts are separated to a function
 * userpriv_viewtable().
 *
 */
function mybookmarks_user_view( $args ) 
{
    if(!xarSecurityCheck( 'Addmybookmarks',0,$data= "You need to login before you can add or view your Bookmarks")) return $data;

    $itemtype = xarVarCleanFromInput( 'itemtype' );
    extract( $args );

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc(
                'mybookmarks'
                ,'mybookmarks'
                ,'view'
                , $args );


        default:
            xarSessionSetVar(
                'mybookmarks_statusmsg'
                ,'Error: Itemtype not specified or invalid. Redirected you to main page!' );

            xarResponseRedirect(
                xarModURL(
                    'mybookmarks'
                    ,'user'
                    ,'main' ));
    }

}

/*
 * END OF FILE
 */
?>