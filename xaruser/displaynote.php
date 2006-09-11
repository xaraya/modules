<?php
/**
 * Display a note
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Display a note
 *
 * @param int rnid Release Note ID
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 * @return array with notes info
 */
function release_user_displaynote($args)
{
    extract($args);
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    if (!xarVarFetch('rnid', 'int:1:', $rnid, null)) {return;}
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'basic', XARVAR_NOT_REQUIRED)) return;

    // The user API function is called.
    $item = xarModAPIFunc('release', 'user', 'getnote',
                          array('rnid' => $rnid));

    if ($item == false) return;

    // The user API function is called.
    $id = xarModAPIFunc('release', 'user', 'getid',
                          array('rid' => $item['rid']));


    $getuser = xarModAPIFunc('roles', 'user', 'get',
                              array('uid' => $id['uid']));


    $hooks = xarModCallHooks('item','display', $rnid,
                                    array('itemtype'  => '2',
                                          'returnurl' => xarModURL('release', 'user','displaynote',
                                                                   array('rnid' => $rnid))
                                         )
                                    );
    // TODO: MichelV rewrite hookcall to array
    if (empty($hooks)) {
        $item['hooks'] = '';
    } elseif (is_array($hooks)) {
        $item['hooks'] = join('',$hooks);
    } else {
        $item['hooks'] = $hooks;
    }
    if ($item['certified'] == 2){
        $item['certifiedstatus'] = xarML('Yes');
    } else {
        $item['certifiedstatus'] = xarML('No');
    }
    $stateoptions=array();
    $stateoptions[0] = xarML('Planning');
    $stateoptions[1] = xarML('Alpha');
    $stateoptions[2] = xarML('Beta');
    $stateoptions[3] = xarML('Production/Stable');
    $stateoptions[4] = xarML('Mature');
    $stateoptions[5] = xarML('Inactive');

    foreach ($stateoptions as $key => $value) {
     if ($key==$item['rstate']) {
       $stateoption=$stateoptions[$key];
     }
    }
    $item['stateoption']=$stateoption;
    $item['desc'] = nl2br($id['desc']);
    $item['regname'] = $id['regname'];
    $item['displname'] = $id['displname'];
    $item['type'] = $id['type'];
    $item['class'] = $id['class'];
    $item['contacturl'] = xarModUrl('roles', 'user', 'email', array('uid' => $id['uid']));
    $item['extensionpage']= xarModURL('release','user','display',
                                array('rid' => $item['rid'],
                                      'phase' => 'version',
                                      'tab'  => 'versions'));

    $item['realname'] = $getuser['name'];
    $item['notes'] = nl2br($item['notes']);
    $item['changelog'] = nl2br($item['changelog']);
    return $item;
}

// Begin Docs Portion

?>