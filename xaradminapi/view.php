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
function messages_adminapi_view( $args )
{

    if (!xarSecurityCheck( 'ViewMessages')) return;

    // Get parameter from browser
    list( $type, $startnum, $itemid ,$itemtype ) = xarVarCleanFromInput( 'type', 'startnum', 'itemid', 'itemtype' );
    extract( $args );

    // The itemtype is a must!
    if ( empty( $itemtype ) ) {
        xarResponseRedirect(
            xarModURL(
                'messages'
                ,'user'
                ,'main' ));
    }

    switch ( $type ) {
        case 'admin':
            $data =& messages_admin_common( 'View Admin Messages' );
            break;

        default:
            $data =& messages_user_common( 'View Messages' );
    }

    $itemsperpage = xarModGetVar(
            'messages'
            ,'itemsperpage.' . $itemtype );

    $uid = xarUserGetVar('uid');
    $objects = xarModAPIFunc(
        'messages'
        ,'user'
        ,'getall'
        ,array(
             'itemtype'  => $itemtype
            ,'numitems'  => $itemsperpage
            ,'startnum'  => $startnum
            ,'sort'      => array(
                'msg_id')
            ,'fieldlist' => array( 'subject', 'from_userid', 'to_userid', 'msg_time', 'read_msg')
        ));

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


    $data['pager'] = xarTplGetPager(
        $startnum
        ,xarModAPIFunc(
            'messages'
            ,'user'
            ,'count'
            ,array( 'itemtype' => $itemtype ))
        ,xarModURL(
            'messages'
            ,$type
            ,'view'
            ,array(
                'startnum'  => '%%'
                ,'itemtype' => $itemtype ))
        ,$itemsperpage );

        $numitems = xarModAPIFunc(
        'messages'
        ,'user'
        ,'counttotal'
        ,array(
            'module'     => 'messages'
            ,'itemtype'  => $itemtype
        ));
         $data['totalin'] = $numitems;

         $numitems = xarModAPIFunc(
        'messages'
        ,'user'
        ,'counttotalout'
        ,array(
            'module'     => 'messages'
            ,'itemtype'  => $itemtype
        ));

         $data['totalout'] =$numitems;
         $numitems = xarModAPIFunc(
        'messages'
        ,'user'
        ,'countunread'
        ,array(
            'module'     => 'messages'
            ,'itemtype'  => $itemtype
        ));


         $data['unread'] = $numitems;
         $data['indicatorinboxyes'] = '<img src="modules/messages/xarimages/green.gif" />';
         $data['indicatorinboxno'] = '<img src="modules/messages/xarimages/red.gif" />';
         $data['indicatorunreadyes'] = '<img src="modules/messages/xarimages/green.gif" />';
         $data['indicatorunreadno'] = '<img src="modules/messages/xarimages/red.gif" />';
         $data['indicatoroutboxyes'] = '<img src="modules/messages/xarimages/green.gif" />';
         $data['indicatoroutboxno'] = '<img src="modules/messages/xarimages/red.gif" />';
         $data['messageslogo'] = '<img src="modules/messages/xarimages/xaraya_logo.gif" width="202" height="69"/>';
         $data['limitinbox'] = '10';
         $data['outbox'] = '10';
         $data['limitreached1'] = ' ';
         $data['limitreached2'] = ' ';
         $data['read_msg'] = $objects->properties['read_msg']->getValue();
         $data['imageread'] = '<img src="modules/messages/xarimages/check_read.gif" border="0"/>';
         $data['imageunread'] = '<img src="modules/messages/xarimages/check_unread.gif" border="0"/>';

    return $data;
}

?>