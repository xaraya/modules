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
//Psspl: modifided the code for post anonymously 
function messages_userapi_create( $args )
{
    extract($args);

    if (!isset($subject)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'subject', 'userapi', 'create', 'messages');
        throw new Exception($msg);
    }

    if (!isset($body)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'body', 'userapi', 'create', 'messages');
        throw new Exception($msg);
    }

    if (!isset($recipient)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'recipient', 'userapi', 'create', 'messages');
        throw new Exception($msg);
    }

    if (!isset($draft) || $draft != true) {
        $draft = false;
    }

    // check the authorisation key
    if (!xarSecConfirmAuthKey()) return; // throw back

    //Psspl:Modifided the code for postanon_to field.   
    if(!isset($postanon_to)){
        $postanon_to = 0;
    }       
    $id =  xarModAPIFunc('comments',
                         'user',
                         'add',
                          array('modid'       => xarMod::getID('messages'),
                                'objectid'    => $recipient,
                                'title'       => $subject,
                                'comment'     => $body,
                                'author'      => xarSession::getVar('role_id'),
                                'postanon_to' => $postanon_to,                                 
                                 'postanon' => $postanon,));

    if($id !== false && $draft == true) {
        xarModAPIFunc('comments',
                         'user',
                         'deactivate',
                          array('id'       => $id));
    
    }


    return $id;
}

?>
