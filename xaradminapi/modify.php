<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_adminapi_modify( $args )
{
    if (!xarSecurityCheck( 'ViewMessages')) return;

    extract( $args );

    if (!xarVarFetch('from_userid', 'int:1:', $from_userid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid', 'id', $itemid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('subject','str:1:', $subject, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype', 'int:0:', $itemtype, 0, XARVAR_NOT_REQUIRED)) return;

    // Retrieve the object
    $object = xarModAPIFunc('messages', 'user', 'get',
            array('itemtype'  => 1, 'itemid'    => $itemid));

    if ( empty( $object ) ) return;

    $item_title = xarModAPIFunc('messages', 'user', 'gettitle',
                array('object' => $object, 'itemtype' =>  $itemtype ));

    $data = messages_admin_common( 'Reply Messages ' .$item_title . $from_userid );
    $data['subject'] = $subject;

    // check if authid is set.
    if ( isset( $authid ) ) {

        // check the input values for this object
        $isvalid = $object->checkInput();

        /*
         * We create the preview with the messages_userapi_viewmessages()
         * function.
         */
        if ( !xarModLoad( 'messages', 'user' ) ) return;
        $preview = xarModFunc('messages', 'user', 'display',
                   array('itemtype'  => '1', 'object'   => $object ));

        if ( !isset( $preview ) ) return;
        $data['preview'] = $preview;
    }


    /*
     * call the hook 'item:modify:GUI'
     */
    $args = array(
        'module'        =>  'messages'
        ,'itemid'       =>  $itemid
        ,'itemtype'     =>  '1' );
    $data['hooks'] = xarModCallHooks('item', 'modify', $itemid, $args, 'messages' );

    /*
     * Compose the data for the template
     */
    $data['object'] = $object;
    $data['itemid'] = $itemid;
    $data['action'] = xarModURL('messages', 'admin', 'modify', 
                    array('itemtype'  => 1, 'itemid'   => $itemid ));

    $data['authid'] = xarSecGenAuthKey();
    $data['_bl_template'] = 'messages';
    $role_id = xarSession::getVar('role_id');
    $to_userid = $object->properties['from_userid']->getValue();
    $object->properties['to_userid']->setValue($to_userid);
    $subject = $object->properties['subject']->getValue();
    $subject = xarML('Re:') . $subject;
    $object->properties['subject']->setValue($subject);
    $object->properties['from_userid']->setValue($role_id);
    $message = $object->properties['msg_text']->getValue();
    $message = add_quoting($message);
    $object->properties['msg_text']->setValue($message);
    $replied = 1;
    $object->properties['replied']->setValue($replied);
      $itemid = $object->updateItem();
        if (empty( $itemid) ) return; // throw back

    return $data;
}

function add_quoting($string, $pattern = '> ')
{
         // add quoting patern to mark text quoted in your reply
         return $pattern.str_replace("\n", "\n$pattern", $string);
}
?>