<?php
function netquery_admin_flview()
{
    if(!xarSecurityCheck('EditNetquery')) return;
    $data['items'] = array();
    $data['authid'] = xarSecGenAuthKey();
    $flags = xarModAPIFunc('netquery', 'admin', 'getflags');
    if (empty($flags)) {
        $msg = xarML('There are no service flags registered');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    for ($i = 0; $i < count($flags); $i++) {
        $flag = $flags[$i];
        if (xarSecurityCheck('EditNetquery',0)) {
            $flags[$i]['editurl'] = xarModURL('netquery', 'admin', 'flmodify', array('flag_id' => $flag['flag_id']));
        } else {
            $flags[$i]['editurl'] = '';
        }
        $flags[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteNetquery',0)) {
            $flags[$i]['deleteurl'] = xarModURL('netquery', 'admin', 'fldelete', array('flag_id' => $flag['flag_id']));
        } else {
            $flags[$i]['deleteurl'] = '';
        }
        $flags[$i]['deletetitle'] = xarML('Delete');
    }
    $data['items'] = $flags;
    $data['cfglink'] = Array('url'   => xarModURL('netquery', 'admin', 'config'),
                             'title' => xarML('Return to main configuration'),
                             'label' => xarML('Modify Config'));
    $data['flvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'fltview'),
                             'title' => xarML('Edit service/exploit flags'),
                             'label' => xarML('Edit Flags'));
    $data['flalink'] = Array('url'   => xarModURL('netquery', 'admin', 'flnew'),
                             'title' => xarML('Add service/exploit flag'),
                             'label' => xarML('Add Flag'));
    $data['hlplink'] = Array('url'   => xarML('modules/netquery/xardocs/manual.html#admin'),
                             'title' => xarML('Netquery online manual'),
                             'label' => xarML('Online Manual'));
    return $data;
}
?>