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
function messages_adminapi_view( $args )
{
    if (!xarSecurityCheck( 'ViewMessages')) return;

    extract( $args );

    if (!xarVarFetch('type', 'isset', $type, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemtype', 'int:0:', $itemtype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid', 'id', $itemid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'int:0:', $startnum, 10, XARVAR_NOT_REQUIRED)) return;

    // The itemtype is a must!
    if (empty( $itemtype) ) {
        xarResponseRedirect(
            xarModURL('messages', 'user', 'main' ));
    }

    switch ( $type ) {
        case 'admin':
            $data =& messages_admin_common('View Admin Messages');
            break;

        default:
            $data =& messages_user_common('View Messages');
    }

    $itemsperpage = xarModVars::get('messages', 'itemsperpage.' . $itemtype );

    $role_id = xarSession::getVar('role_id');
    $objects = xarModAPIFunc('messages', 'user', 'getall',
             array('itemtype'  => $itemtype, 'numitems'  => $itemsperpage,
                   'startnum'  => $startnum, 'sort'=> array('msg_id'),
                   'fieldlist' => array( 'subject', 'from_userid', 'to_userid', 'msg_time', 'read_msg')));

    if ( empty($objects) ) return;
    $object_props =& $objects->getProperties();
    $data['objects_props']  =& $objects->getProperties();
    $data['objects_values'] =& $objects->items;
    $data['subject'] = $object_props['subject'];
    $data['from'] = $object_props['from_userid'];
    $data['time'] = $object_props['msg_time'];
//    $data['attachment'] = $object_props['attachment'];  TODO
    $data['itemtype'] = $itemtype;
    $data['_bl_template'] = 'messages';
    $data['numitems'] = & $objects->numitems;


    $data['pager'] = xarTplGetPager($startnum, xarModAPIFunc('messages', 'user', 'count', array( 'itemtype' => $itemtype )),
                   xarModURL('messages', $type, 'display',
                   array('startnum'  => '%%', 'itemtype' => $itemtype )),$itemsperpage );

        $numitems = xarModAPIFunc('messages', 'user', 'counttotal',
                  array('module'     => 'messages', 'itemtype'  => $itemtype));

         $data['totalin'] = $numitems;

         $numitems = xarModAPIFunc('messages', 'user', 'counttotalout',
                   array('module'     => 'messages', 'itemtype'  => $itemtype));

         $data['totalout'] =$numitems;
         $numitems = xarModAPIFunc('messages', 'user', 'countunread',
                   array('module' => 'messages', 'itemtype'  => $itemtype));

         $data['unread'] = $numitems;
         $data['indicatorinboxyes'] = '<img src="'.xarTplGetImage('green.gif','messages').'" alt="inbox yes" />';
         $data['indicatorinboxno'] = '<img src="'.xarTplGetImage('red.gif','messages').'" alt="inbox no" />';
         $data['indicatorunreadyes'] = '<img src="'.xarTplGetImage('green.gif','messages').'" alt="unread yes" />';
         $data['indicatorunreadno'] = '<img src="'.xarTplGetImage('red.gif','messages').'" alt="unread no" />';
         $data['indicatoroutboxyes'] = '<img src="'.xarTplGetImage('green.gif','messages').'" alt="outbox yes" />';
         $data['indicatoroutboxno'] = '<img src="'.xarTplGetImage('red.gif','messages').'" alt="outbox no" />';
         $data['messageslogo'] = '<img src=""'.xarTplGetImage('xaraya_logo.jpg','messages').'" width="150" alt="xaraya"/>';
         $data['limitinbox'] = '10';
         $data['outbox'] = '10';
         $data['limitreached1'] = ' ';
         $data['limitreached2'] = ' ';
         $data['read_msg'] = $objects->properties['read_msg']->getValue();
         $data['imageread'] = '<img src="'.xarTplGetImage('check_read.gif','messages').'" alt="check read"/>';
         $data['imageunread'] = '<img src="'.xarTplGetImage('check_unread.gif','messages').'" alt="check unread"/>';

    return $data;
}

?>