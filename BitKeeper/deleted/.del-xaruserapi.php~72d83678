<?php
/**
 * xaruserapi.php
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage My Bookmarks module
 * @author Scot Gardner
 * @version     $Id$
 */

/**
 * Utility function to pass individual menu items to the main menu.
 *
 * This function is invoked by the core to retrieve the items for the
 * usermenu.
 *
 * @returns array
 * @return  array containing the menulinks for the main menu items
 */

function mybookmarks_userapi_getmenulinks ( $args ) 
{


    // First we need to do a security check to ensure that we only return menu items
    // that we are suppose to see.  It will be important to add for each menu item that
    // you want to filter.  No sense in someone seeing a menu link that they have no access
    // to edit.  Notice that we are checking to see that the user has permissions, and
    // not that he/she doesn't.


    if (xarSecurityCheck('Viewmybookmarks')) {

        // The main menu will look for this array and return it for a tree
        // view of the module. We are just looking for three items in the
        // array, the url, which we need to use the xarModURL function, the
        // title of the link, which will display a tool tip for the module
        // url, in order to keep the label short, and finally the exact label
        // for the function that we are displaying.

        $menulinks[] = array(
            'url'       => xarModURL(
                'mybookmarks'
                ,'user'
                ,'view'
                ,array(
                    'itemtype' => 1 ))
            ,'title'    => 'Look at the Bookmarks'
            ,'label'    => 'View Bookmarks' );


    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.
    return $menulinks;

}

/**
 * This function is called when xarModURL is invoked and Short URL Support is
 * enabled.
 *
 * The parameters are passed in $args.
 *
 * Some hints:
 *
 * o If you want to get rid of the modulename. Look at xarModGetAlias() and
 *   xarModSetAlias().
 * o
 *
 */
function mybookmarks_userapi_encode_shorturl( $args ) 
{

    $func       = NULL;
    $module     = NULL;
    $itemid     = NULL;
    $itemtype   = NULL;
    $rest       = array();

    foreach( $args as $name => $value ) {

        switch( $name ) {

            case 'module':
                $module = $value;
                break;

            case 'itemtype':
                $itemtype = $value;
                break;

            case 'objectid':
            case 'itemid':
                $itemid = $value;
                break;

            case 'func':
                $func = $value;
                break;

            default:
                $rest[] = $value;

       }
    }

    // kind of a assertion :-))
    if( isset( $module ) and $module != 'mybookmarks' ) {
        return;
    }

    /*
     * LETS GO. We start with the module.
     */
    $path = '/mybookmarks';

    if ( empty( $func ) )
        return;

    /*
     * We only provide support for display and view and main
     */
    if ( $func != 'display' and $func != 'view' and $func != 'main' )
        return;

    /*
     * Now add the itemtype if possible
     */
    if ( isset( $itemtype ) ) {

        switch ( $itemtype ) {

            case 1:
                $itemtype_name = 'mybookmarks';
                break;


        default:
            // Unknown itemtype?
            return;
        }

        $path = $path . '/' . $itemtype_name;

        /*
         * And last but not least the itemid
         */
        If ( isset( $itemid ) ) {
                $path = $path . '/' . $itemid;
        }
    }

    /*
     * ADD THE REST !!!! THIS HAS TO BE DONE EVERYTIME !!!!!
     */
    $add = array();
    foreach ( $rest as $argument ) {
        if ( isset( $rest['argument'] ) ) {
            $add[] =  $argument . '=' . $rest[$argument];
        }
    }

    if ( count( $add ) > 0 ) {
        $path = $path . '?' . implode( '&', $add );
    }

    return $path;

}

/**
 * This function is called when xarModURL is invoked and Short URL Support is
 * enabled.
 *
 * The parameters are passed in $args.
 *
 * Some hints:
 *
 * o If you want to get rid of the modulename. Look at xarModGetAlias() and
 *   xarModSetAlias().
 * o
 *
 */
function mybookmarks_userapi_decode_shorturl( $params ) 
{


    if ( $params[0] != 'mybookmarks' )
        return;

    /*
     * Check for the itemtype
     */
    if ( empty( $params[1] ) )
        return array( 'main', array() );

    switch ( $params[1] ) {

        case 'mybookmarks':
            $itemtype = 1;
            break;


        default:
            return array( 'main', array() );
    }

    if ( !isset( $params[2] ) )
        return array(
            'view'
            ,array(
                'itemtype' => $itemtype ));

    return array(
        'display'
        ,array(
            'itemid'    => $params[2]
            ,'itemtype' => $itemtype ));

}

/**
 * Generic function to retrieve the number of objects stored in database of
 * itemtype $itemtype;
 *
 * @param array( 'itemtype' => <itemtype> )
 * @return number of items
 */
function mybookmarks_userapi_count( $args ) 
{

    extract( $args );

    // Retrieve all objects via the dynamicdata module api.
    $numitems =& xarModAPIFunc(
        'dynamicdata'
        ,'user'
        ,'countitems'
        ,array(
            'module'     => 'mybookmarks'
            ,'itemtype'  => $itemtype
        ));

    return $numitems;
}

/**
 *
 * @param array( 'itemtype' => <itemtype> )
 * @return number of items
 *
 * @param $args['startnum'] starting article number
 * @param $args['numitems'] number of articles to get
 * @param $args['sort'] sort order ('date','title','hits','rating',...)
 * @param $args['fields'] array with all the fields to return per article
 *                        Default list is : 'aid','title','summary','authorid',
 *                        'pubdate','pubtypeid','notes','status','body'
 *                        Optional fields : 'cids','author','counter','rating','dynamicdata'
 */
function mybookmarks_userapi_getall( $args ) 
{

    extract( $args );

    if ( empty($startnum) ) {
        $startnum = NULL;
    }

    if ( empty($numitems) ) {
        $numitems = NULL;
    }

    if ( empty($sort) ) {
        $sort = NULL;
    }

    if ( empty($fieldlist) ) {
        $fieldlist = NULL;
    }

    if ( empty($itemids) ) {
        $itemids = NULL;
    }

    // Retrieve all objects via the dynamicdata module api.
      $usrid = xarUserGetVar('uid'); 
    $objects =& xarModAPIFunc(
        'dynamicdata'
        ,'user'
        ,'getitems'
        ,array(
            'module'     => 'mybookmarks'
            ,'itemtype'  => $itemtype
            ,'numitems'  => $numitems
            ,'startnum'  => $startnum
            ,'status'    => 1
            ,'where'     => "user_name = $usrid"
            ,'sort'      => $sort
            ,'getobject' => 1
            ,'itemids'   => $itemids
            ,'fieldlist' => $fieldlist
        ));

    return $objects;
}

/**
 *
 * @param array( 'itemtype' => <itemtype> )
 * @return number of items
 *
 * @param $args['itemid'] starting article number
 * @param $args['numitems'] number of articles to get
 * @param $args['sort'] sort order ('date','title','hits','rating',...)
 * @param $args['fields'] array with all the fields to return
 * @param $args['fields'] array with all the fields to return
 */
function mybookmarks_userapi_get( $args ) 
{

    extract( $args );

    // Retrieve the object via the dynamicdata module api.
    $object = xarModAPIFunc(
        'dynamicdata'
        ,'user'
        ,'getitem'
        ,array(
            'module'     => 'mybookmarks'
            ,'itemtype'  => $itemtype
            ,'itemid'    => $itemid
            ,'status'    => 1
            ,'getobject' => 1
        ));
    if ( empty($object) ) return;

    return $object;
}

/**
 *
 * @param array( 'itemtype' => <itemtype> )
 * @return number of items
 *
 * @param $args['item'] item
 * @param $args['itemtype'] itemtyp
 */
function mybookmarks_userapi_gettitle( $args ) 
{

    extract( $args );

    if ( empty( $itemtype ) ) return 'Itemtype missing';

    if ( isset( $item ) ) {
        switch ( $itemtype ) {

            case 1:
                return $item['user_name'] .
                       ', ' . $item['bm_url'];
                break;

        }

    } else if ( isset( $object ) ) {

        switch ( $itemtype ) {

            case 1:
                return $object->properties['user_name']->getValue() .
                       ', ' . $object->properties['bm_url']->getValue();
                break;

        }
    }

    return 'Unknown Itemtype';
}

/**
 * Utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function mybookmarks_userapi_getitemlinks ( $args ) 
{

    extract($args);

    if (empty($itemtype)) {
        return;
    }

    $itemlinks = array();
    $objects =& xarModAPIFunc(
        'mybookmarks'
        ,'user'
        ,'getall'
        ,array(
             'itemids'   => $itemids
            ,'itemtype'  => $itemtype
#            ,'fieldlist' => array( )
        ));
    if ( empty($objects) ) return;

    $data =& $objects->items;

    foreach( $data as $id => $object ) {

        $title = xarModAPIFunc(
            'mybookmarks'
            ,'user'
            ,'gettitle'
            ,array(
                'itemtype'  =>  $itemtype
                ,'item'     =>  & $object
                ));

        $itemlinks[$id] = array(
            'url'   =>  xarModURL(
                'mybookmarks'
                ,'user'
                ,'display'
                ,array(
                    'itemid'    => $id
                    ,'itemtype' => $itemtype
                    ))
            ,'title'    =>  $title
            ,'label'    =>  $title
            );
    }

    return $itemlinks;
}

/*
 * END OF FILE
 */
?>
