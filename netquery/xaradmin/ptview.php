<?php
function netquery_admin_ptview()
{
    if(!xarSecurityCheck('EditNetquery')) return;
    if (!xarVarFetch('portnum', 'int:1:100000', $portnum, '80', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    $data['items'] = array();
    $data['authid'] = xarSecGenAuthKey();
    $portdata = xarModAPIFunc('netquery', 'admin', 'getportdata', array('port' => $portnum));
    if (empty($portdata)) {
        $msg = xarML('There are no port services registered');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    for ($i = 0; $i < count($portdata); $i++) {
        $port = $portdata[$i];
        if (xarSecurityCheck('EditNetquery',0)) {
            $portdata[$i]['editurl'] = xarModURL('netquery', 'admin', 'ptmodify', array('port_id' => $port['port_id']));
        } else {
            $portdata[$i]['editurl'] = '';
        }
        $portdata[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteNetquery',0)) {
            $portdata[$i]['deleteurl'] = xarModURL('netquery', 'admin', 'ptdelete', array('port_id' => $port['port_id']));
        } else {
            $portdata[$i]['deleteurl'] = '';
        }
        $portdata[$i]['deletetitle'] = xarML('Delete');
    }
    $data['items'] = $portdata;
    $data['portnumlabel'] = 'Port #';
    $data['portnum'] = $portnum;
    $data['cfglink'] = Array('url'   => xarModURL('netquery', 'admin', 'config'),
                             'title' => xarML('Return to main configuration'),
                             'label' => xarML('Modify Config'));
    $data['ptvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'ptview'),
                             'title' => xarML('Edit port services data'),
                             'label' => xarML('Edit Ports'));
    $data['ptalink'] = Array('url'   => xarModURL('netquery', 'admin', 'ptnew', array('portnum' => $portnum)),
                             'title' => xarML('Add port service data'),
                             'label' => xarML('Add Port'));
    $data['hlplink'] = Array('url'   => xarML('modules/netquery/xardocs/manual.html#admin'),
                             'title' => xarML('Netquery online manual'),
                             'label' => xarML('Online Manual'));
    return $data;
}
?>