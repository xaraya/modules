<?php

/**
 * xarmybookmarksapi.php
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage My Bookmarks module
 * @author Scot Gardner
 */


/**
 * Modify a mybookmarks object
 */
function mybookmarks_mybookmarksapi_modify( $args ) 
{

    if (!xarSecurityCheck( 'Editmybookmarks')) return;

    list(
        $itemid
        ,$authid
        ,$itemtype
        ) = xarVarCleanFromInput( 'itemid', 'authid', 'itemtype' );
    extract( $args );

    // Retrieve the object
    $object =& xarModAPIFunc(
        'mybookmarks'
        ,'user'
        ,'get'
        ,array(
             'itemtype'  => 1
            ,'itemid'    => $itemid
        ));
    if ( empty( $object ) ) return;

    $item_title = xarModAPIFunc(
        'mybookmarks'
        ,'user'
        ,'gettitle'
        ,array(
            'object'    =>  $object
            ,'itemtype' =>  $itemtype ));
    $data = mybookmarks_admin_common( 'Modify Bookmarks ' .$item_title );

    // check if authid is set.
    if ( isset( $authid ) ) {

        // check the input values for this object
        $isvalid = $object->checkInput();

        /*
         * We create the preview with the mybookmarks_userapi_viewmybookmarks()
         * function.
         */
        if ( !xarModLoad( 'mybookmarks', 'user' ) ) return;
        $preview = xarModFunc(
            'mybookmarks'
            ,'user'
            ,'display'
            ,array(
                'itemtype'  => '1'
                ,'object'   => $object ));
        if ( !isset( $preview ) ) return;
        $data['preview'] = $preview;

    }



    /*
     * Compose the data for the template
     */
    $data['object'] = $object;
    $data['itemid'] = $itemid;
    $data['action'] = xarModURL(
        'mybookmarks'
        ,'admin'
        ,'modify'
        ,array(
            'itemtype'  => 1
            ,'itemid'   => $itemid ));
    $data['authid'] = xarSecGenAuthKey();
    $data['_bl_template'] = 'mybookmarks';

    return $data;
}


/**
* Update a mybookmarks object
*/
function mybookmarks_mybookmarksapi_update( $args ) 
{

    if (!xarSecurityCheck( 'Editmybookmarks')) return;

    list(
        $itemid
        ,$itemtype
        ) = xarVarCleanFromInput( 'itemid', 'itemtype' );
    extract( $args );

    // Retrieve the object
    $object =& xarModAPIFunc(
        'mybookmarks'
        ,'user'
        ,'get'
        ,array(
             'itemtype'  => 1
            ,'itemid'    => $itemid
        ));
    if ( empty( $object ) ) return;

    // check the input values for this object
    $isvalid = $object->checkInput();

    if ( $isvalid ) {

        /*
         * The object is valid and no preview is wished. Update it
         */
        $itemid = $object->updateItem();
        if (empty( $itemid) ) return; // throw back



        /*
         * Compose the statusmessage
         */
        $item_title = xarModAPIFunc(
            'mybookmarks'
            ,'user'
            ,'gettitle'
            ,array(
                'object'    =>  $object
                ,'itemtype' =>  $itemtype ));

        xarSessionSetVar(
            'mybookmarks_statusmsg'
            ,'Modified Bookmarks ' . $itemid . ' -> ' . $item_title . '.' );

        /*
         * This function generated no output, and so now it is complete we redirect
         * the user to an appropriate page for them to carry on their work
         */
        xarResponseRedirect(
            xarModURL(
                'mybookmarks'
                ,'user'
                ,'display'
                ,array(
                    'itemid'    => $itemid
                    ,'itemtype'  => 1 )));

    } else {

        // Back to modify
        return mybookmarks_mybookmarksapi_modifymybookmarks($args);

    }
}

/**
 * Delete a mybookmarks object.
 *
 */
function mybookmarks_mybookmarksapi_delete( $args ) 
{

    if (!xarSecurityCheck( 'Editmybookmarks')) return;

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    list ( $itemid, $itemtype ) = xarVarCleanFromInput( 'itemid', 'itemtype' );
    extract($args);

    // Retrieve the object
    $object =& xarModAPIFunc(
        'mybookmarks'
        ,'user'
        ,'get'
        ,array(
             'itemtype'  => 1
            ,'itemid'    => $itemid
        ));
    if ( empty( $object ) ) return;

    $item_title = xarModAPIFunc(
        'mybookmarks'
        ,'user'
        ,'gettitle'
        ,array(
            'object'    =>  $object
            ,'itemtype' =>  $itemtype ));
    $data = mybookmarks_admin_common( 'Delete Bookmarks '. $item_title );

    /*
     * The user confirmed the deletion so let's go.
     */
    $itemid = $object->deleteItem();
    if ( empty( $itemid ) ) return;



    /*
     * Set the status message
     */
    xarSessionSetVar(
        'mybookmarks_statusmsg'
        ,'Deleted  Bookmarks '. $itemid .' -> '. $item_title .'!' );

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(
        xarModURL(
            'mybookmarks'
            ,'admin'
            ,'view'
            ,array(
                'itemtype' => 1 )));

}

/**
 * Confirm the deletion of a mybookmarks object.
 *
 */
function mybookmarks_mybookmarksapi_confirmdelete( $args ) 
{

    if (!xarSecurityCheck( 'Editmybookmarks')) return;

    list ( $itemid,  $itemtype ) = xarVarCleanFromInput( 'itemid', 'itemtype' );
    extract($args);

    // Retrieve the object
    $object =& xarModAPIFunc(
        'mybookmarks'
        ,'user'
        ,'get'
        ,array(
             'itemtype'  => 1
            ,'itemid'    => $itemid
        ));
    if ( empty( $object ) ) return;

    $item_title = xarModAPIFunc(
        'mybookmarks'
        ,'user'
        ,'gettitle'
        ,array(
            'object'    =>  $object
            ,'itemtype' =>  $itemtype ));
    $data = mybookmarks_admin_common( 'Delete Bookmarks '. $item_title );



    /*
     * Compose the data for the template
     */
    $data['object'] = $object;
    $data['itemid'] = $itemid;
    $data['action'] = xarModURL(
        'mybookmarks'
        ,'admin'
        ,'delete'
        ,array(
            'itemtype'  => 1
            ,'itemid'   => $itemid ));
    $data['authid'] = xarSecGenAuthKey();
    $data['_bl_template'] = 'mybookmarks';

    return $data;

}


/**
 * Create a mybookmarks object
 *
 */
function mybookmarks_mybookmarksapi_new( $args ) 
{

    if (!xarSecurityCheck( 'Addmybookmarks')) return;

    list ( $authid, $itemtype ) = xarVarCleanFromInput( 'authid', 'itemtype' );
    extract( $args );

    // Retrieve the object via the dynamicdata module api.
    $object = xarModAPIFunc(
        'dynamicdata'
        ,'user'
        ,'getobject'
        ,array(
            'module'     => 'mybookmarks'
            ,'itemtype'  => 1
        ));
    if ( empty($object) ) return;

    /*
     * Initialize the data array();
     */
    $data = mybookmarks_admin_common( 'New Bookmarks ' );

    // These function is called under different contexts.
    // 1. first time ( authid is not set )
    // 2. preview    ( authid is set )
    // 3. Submit with errors ( authid is set )
    if ( isset( $authid ) ) {

        // check the input values for this object
        $isvalid = $object->checkInput();

        /*
         * We create the preview with the mybookmarks_userapi_viewmybookmarks()
         * function.
         */
        if ( !xarModLoad( 'mybookmarks', 'user' ) ) return;
        $preview = xarModFunc(
            'mybookmarks'
            ,'user'
            ,'display'
            ,array(
                'itemtype'  =>  '1'
                ,'object'   => $object ));
        if ( !isset( $preview ) ) return;
        $data['preview'] = $preview;

    }



    /*
     * Compose the data for the template
     */
    $data['object'] = $object;
    $data['action'] = xarModURL(
        'mybookmarks'
        ,'admin'
        ,'new'
        ,array(
            'itemtype'  => 1 ));
    $data['authid'] = xarSecGenAuthKey();
    $data['_bl_template'] = 'mybookmarks';

    return $data;
}


/**
 * Create a mybookmarks object
 *
 */
function mybookmarks_mybookmarksapi_create( $args ) 
{

    if (!xarSecurityCheck( 'Addmybookmarks')) return;

    list ( $itemtype ) = xarVarCleanFromInput( 'itemtype' );
    extract( $args );

    // Retrieve the object via the dynamicdata module api.
    $object = xarModAPIFunc(
        'dynamicdata'
        ,'user'
        ,'getobject'
        ,array(
            'module'     => 'mybookmarks'
            ,'itemtype'  => 1
        ));
    if ( empty($object) ) return;

    // check the input values for this object
    $isvalid = $object->checkInput();

    if ( $isvalid ) {

        /*
         * The object is valid . Create it
         */
        $itemid = $object->createItem();
        if (empty( $itemid) ) return; // throw back



        $item_title = xarModAPIFunc(
            'mybookmarks'
            ,'user'
            ,'gettitle'
            ,array(
                'object'    =>  $object
                ,'itemtype' =>  $itemtype ));

        xarSessionSetVar(
            'mybookmarks_statusmsg'
            ,'Created Bookmarks ' . $itemid .' -> '.  $item_title .'.' );

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponseRedirect(
            xarModURL(
                'mybookmarks'
                ,'admin'
                ,'new'
                ,array(
                    'itemtype' => 1 )));

    } else {

        // Back to new
        return mybookmarks_mybookmarksapi_new( $args );

    }


}

/**
 * // TODO // add description
 */
function mybookmarks_mybookmarksapi_display( $args ) 
{

    // Security check
    if (!xarSecurityCheck( 'Viewmybookmarks')) return;

    // Get parameter from browser
    list( $itemid ,$itemtype ) = xarVarCleanFromInput( 'itemid', 'itemtype' );
    extract( $args );

    // Overload it with the arguments from the admin interface ( if provided )
    $data = array();
    if ( isset( $object ) ) {

        // We need a itemtype to render things properly.
        if ( empty( $itemtype ) ) return 'please provide a itemtype';

    } else {

        // Load the object and provide all tasks which should only be done
        // when we are not rendering a preview ( Menu, Hooks ... )

        // We are called from a browser. To load a object we need a itemtype
        // and a itemid. If there is itemtype let's go to the main page.
        if ( empty( $itemtype ) ) {
            xarResponseRedirect(
                xarModURL(
                    'mybookmarks'
                    ,'user'
                    ,'main' ));
        }

        // If there is no itemid let's go to the itemtypes overview page.
        if ( empty( $itemid ) ) {
            xarResponseRedirect(
                xarModURL(
                    'mybookmarks'
                    ,'user'
                    ,'view'
                    ,array(
                        'itemtype'  =>  $itemtype )));
        }

        // Retrieve the object
        $object =& xarModAPIFunc(
            'mybookmarks'
            ,'user'
            ,'get'
            ,array(
                 'itemtype'  => $itemtype
                ,'itemid'    => $itemid
            ));
        if ( empty( $object ) ) return;

        $item_title = xarModAPIFunc(
            'mybookmarks'
            ,'user'
            ,'gettitle'
            ,array(
                'object'    =>  $object
                ,'itemtype' =>  $itemtype ));
        $data =& mybookmarks_user_common( 'Bookmarks ' . $item_title );


    }

    $data['object_props'] =& $object->getProperties();
    $data['_bl_template'] = 'mybookmarks';
    $data['itemtype'] = $itemtype;
    $data['itemid']   = $itemid;
    return $data;

   return $data;
}

/**
 * // TODO // Add description
 *
 * // TODO // explain that the function is called from admin and user * interface.
 */
function mybookmarks_mybookmarksapi_view( $args ) 
{

    if (!xarSecurityCheck( 'Viewmybookmarks')) return;
     $uid = xarUserGetVar('uid');
    // Get parameter from browser
    list( $type, $startnum, $itemid ,$itemtype ) = xarVarCleanFromInput( 'type', 'startnum', 'itemid', 'itemtype' );
    extract( $args );

    // The itemtype is a must!
    if ( empty( $itemtype ) ) {
        xarResponseRedirect(
            xarModURL(
                'mybookmarks'
                ,'user'
                ,'main' ));
    }

    switch ( $type ) {
        case 'admin':
            $data =& mybookmarks_admin_common( 'View Bookmarks' );
            break;

        default:
            $data =& mybookmarks_user_common( 'View Bookmarks' );
    }

    $itemsperpage = xarModGetVar(
            'mybookmarks'
            ,'itemsperpage.' . $itemtype );

    $objects =& xarModAPIFunc(
        'mybookmarks'
        ,'user'
        ,'getall'
        ,array(
             'itemtype'  => $itemtype
            ,'numitems'  => $itemsperpage
            ,'startnum'  => $startnum
            ,'usrid'     => $uid
            ,'sort'      => array(
                'bm_name')
            ,'fieldlist' => array( )
        ));
    if ( empty($objects) ) return;

    $data['objects_props']  =& $objects->getProperties();
    $data['objects_values'] =& $objects->items;
    $data['itemtype'] = $itemtype;
    $data['_bl_template'] = 'mybookmarks';
    $data['pager'] = xarTplGetPager(
        $startnum
        ,xarModAPIFunc(
            'mybookmarks'
            ,'user'
            ,'count'
            ,array( 'itemtype' => $itemtype ))
        ,xarModURL(
            'mybookmarks'
            ,$type
            ,'view'
            ,array(
                'startnum'  => '%%'
                ,'itemtype' => $itemtype ))
        ,$itemsperpage );

    return $data;

}

/**
 * Administration for the mybookmarks module.
 */
function mybookmarks_mybookmarksapi_config( $args ) 
{

    $data = mybookmarks_admin_common( 'Bookmarks Configuration' );

    list( $itemtype, $authid ) = xarVarCleanFromInput( 'itemtype', 'authid' );
    extract( $args );

    if ( isset( $authid ) ) {

        /*
         * The user confirmed the form. So save the results.
         */

        if (!xarSecConfirmAuthKey()) return;

        $itemsperpage = xarVarCleanFromInput( 'itemsperpage' );

        if ( empty( $itemsperpage ) or !is_numeric( $itemsperpage ) ) {
            $itemsperpage = 10;
        }

        xarModSetVar(
            'mybookmarks'
            ,'itemsperpage.' . $itemtype
            ,$itemsperpage );



        /*
         * Set a status message
         */
        xarSessionSetVar(
            'mybookmarks_statusmsg'
            ,'Updated the Bookmarks configuration!' );

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



    $data['itemtype']       = $itemtype;
    $data['itemtype_label'] = $itemtype;
    $data['itemsperpage']   = xarModGetVar(
        'mybookmarks'
        ,'itemsperpage.' . $itemtype );


    /*
     * Populate the rest of the template
     */
    $data['common']['menu_label'] = 'Configure';
    $data['common']['menu']       = mybookmarks_adminpriv_configmenu();
    $data['action']     = xarModURL( 'mybookmarks', 'admin', 'config' );
    $data['authid']     = xarSecGenAuthKey();
    $data['_bl_template'] = 'mybookmarks';
    return $data;
}


/*
 * END OF FILE
 */
?>