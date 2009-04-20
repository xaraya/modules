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
function messages_adminapi_create( $args )
{
    extract( $args );

    if (!xarSecurityCheck( 'AddMessages')) return;

    if (!xarVarFetch('itemtype', 'int',    $itemtype, 0,XARVAR_NOT_REQUIRED)) return;

    // Retrieve the object via the dynamicdata module api.
    $object = xarModAPIFunc('dynamicdata', 'user', 'getobject', 
                         array('module' => 'messages','itemtype'  => 1));

    if ( empty($object) ) return;

    // check the input values for this object
    $isvalid = $object->checkInput();

    if ( $isvalid ) {

        /*
         * The object is valid . Create it
         */
        $itemid = $object->createItem();
        if (empty( $itemid) ) return; // throw back

        /*
         * call the hook 'item:create:API'
         */
        $args = array(
            'module'        =>  'messages'
            ,'itemid'       =>  $itemid
            ,'itemtype'     =>  '1' );
        $hooks = xarModCallHooks('item', 'create', $itemid, $args, 'messages' );


        $item_title = xarModAPIFunc('messages', 'user', 'gettitle', 
                      array('object'    =>  $object, 'itemtype' =>  $itemtype ));

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponse::Redirect(
            xarModURL('messages', 'admin', 'new',array('itemtype' => 1 )));

    } else {

        // Back to new
        return messages_adminapi_new( $args );
    }
}
?>