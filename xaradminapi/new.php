<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_adminapi_new( $args )
{
    if (!xarSecurityCheck( 'AddMessages')) return;

    list ( $authid, $itemtype ) = xarVarCleanFromInput( 'authid', 'itemtype' );
    extract( $args );

    // Retrieve the object via the dynamicdata module api.
    $object = xarModAPIFunc(
        'dynamicdata'
        ,'user'
        ,'getobject'
        ,array(
            'module'     => 'messages'
            ,'itemtype'  => 1
        ));
    if ( empty($object) ) return;

    /*
     * Initialize the data array();
     */
    $data = messages_admin_common( 'New Messages ' );

    // These function is called under different contexts.
    // 1. first time ( authid is not set )
    // 2. preview    ( authid is set )
    // 3. Submit with errors ( authid is set )
    if ( isset( $authid ) ) {

        // check the input values for this object
        $isvalid = $object->checkInput();

        /*
         * We create the preview with the mymessages_userapi_viewmymessages()
         * function.
         */
        if ( !xarModLoad( 'messages', 'user' ) ) return;
        $preview = xarModFunc(
            'messages'
            ,'user'
            ,'display'
            ,array(
                'itemtype'  =>  '1'
                ,'object'   => $object ));
        if ( !isset( $preview ) ) return;
        $data['preview'] = $preview;

    }


    /*
     * call the hook 'module:modifyconfig:GUI'
     */
    $args = array(
        'module'        =>  'messages'
        ,'itemid'       =>  NULL
        ,'itemtype'     =>  '1' );
    $data['hooks'] = xarModCallHooks(
        'item'
        ,'new'
        ,NULL
        ,$args
        ,'messages' );


    /*
     * Compose the data for the template
     */
    $data['object'] = $object;
    $data['action'] = xarModURL(
        'messages'
        ,'admin'
        ,'new'
        ,array(
            'itemtype'  => 1 ));
    $data['authid'] = xarSecGenAuthKey();
    $data['_bl_template'] = 'messages';

    return $data;
}
?>