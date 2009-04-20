<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_admin_delete( $args )
{
    extract($args);

    if (!xarVarFetch('itemid',   'id', $itemid)) return;
    if (!xarVarFetch('itemtype', 'int:1:', $itemtype, 0, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck( 'AddMessages')) return;

    // Retrieve the object
    $object = xarModAPIFunc('messages', 'user', 'get', array('itemtype'  => 1, 'itemid'=> $itemid));
    if ( empty( $object ) ) return;

    $item_title = xarModAPIFunc('messages', 'user', 'gettitle', 
                          array('object'=>$object,'itemtype' =>  $itemtype ));

    $data = messages_admin_common( 'Delete Message '. $item_title );

    /*
     * The user confirmed the deletion so let's go.
     */
    $itemid = $object->deleteItem();
    if ( empty( $itemid ) ) return;

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponse::Redirect(
        xarModURL('messages', 'user', 'display', array('itemtype' => 1 )));

}

/**
 * Confirm the deletion of a mybookmarks object.
 *
 */
function messages_admin_confirmdelete( $args )
{
    if (!xarSecurityCheck( 'AddMessages')) return;

    extract($args);

    if (!xarVarFetch('itemid',   'id', $itemid)) return;
    if (!xarVarFetch('itemtype', 'int:1:', $itemtype, 0, XARVAR_NOT_REQUIRED)) return;

    // Retrieve the object
    $object = xarModAPIFunc('messages', 'user', 'get'
        ,array('itemtype'  => 1, 'itemid'    => $itemid));
    if ( empty( $object ) ) return;

    $item_title = xarModAPIFunc('messages', 'user', 'gettitle', 
                          array('object'    =>  $object, 'itemtype' =>  $itemtype ));
    $data = messages_admin_common( 'Delete Messages '. $item_title );

    /*
     * Compose the data for the template
     */
    $data['object'] = $object;
    $data['itemid'] = $itemid;
    $data['action'] = xarModURL('messages', 'admin', 'delete', 
                           array('itemtype'  => 1,'itemid'   => $itemid ));
    $data['authid'] = xarSecGenAuthKey();
    $data['_bl_template'] = 'messages';

    return $data;
}

?>