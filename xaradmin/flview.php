<?php
function netquery_admin_flview()
{
    if (!xarSecurityCheck('EditNetquery')) return;
    $data['items'] = array();
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    list($data['buttondir']) = split('[._-]', $data['stylesheet']);
    if (!file_exists($data['buttondir'] = 'modules/netquery/xarimages/'.$data['buttondir'])) $data['buttondir'] = 'modules/netquery/xarimages/blbuttons';
    $data['authid'] = xarSecGenAuthKey();
    $flags = xarModAPIFunc('netquery', 'user', 'getflags');
    if (empty($flags)) {
        $msg = xarML('There are no service flags registered');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    for ($i = 0; $i < count($flags); $i++) {
        $flag = $flags[$i];
        if (xarSecurityCheck('EditNetquery',0)) {
            $flags[$i]['editurl'] = xarModURL('netquery', 'admin', 'flmodify', array('flag_id' => $flag['flag_id']));
            $flags[$i]['edittitle'] = xarML('Edit');
        } else {
            $flags[$i]['editurl'] = '';
            $flags[$i]['edittitle'] = '----';
        }
        if (xarSecurityCheck('DeleteNetquery',0) && $flag['flagnum'] != 99) {
            $flags[$i]['deleteurl'] = xarModURL('netquery', 'admin', 'fldelete', array('flag_id' => $flag['flag_id']));
            $flags[$i]['deletetitle'] = xarML('Delete');
        } else {
            $flags[$i]['deleteurl'] = '';
            $flags[$i]['deletetitle'] = '------';
        }
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
    $data['hlplink'] = Array('url'   => 'modules/netquery/xardocs/manual.html#admin',
                             'title' => xarML('Netquery online manual'),
                             'label' => xarML('Online Manual'));
    return $data;
}
?>