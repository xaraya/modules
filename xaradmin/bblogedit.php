<?php
function netquery_admin_bblogedit()
{
    if (!xarSecurityCheck('EditNetquery')) return;
    if (!xarVarFetch('startnum', 'isset', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('limit', 'isset', $limit, 30, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('bbmode', 'str:1:100', $bbmode, 'select', XARVAR_NOT_REQUIRED)) return;
    $data['entries'] = array();
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    list($data['buttondir']) = split('[._-]', $data['stylesheet']);
    if (!file_exists($data['buttondir'] = 'modules/netquery/xarimages/'.$data['buttondir'])) $data['buttondir'] = 'modules/netquery/xarimages/blbuttons';
    $data['authid'] = xarSecGenAuthKey();
    $xartable =& xarDBGetTables();
    $spamblockerTable = $xartable['netquery_spamblocker'];
    switch(strtolower($bbmode))
    {
      case 'all':
        if (!xarVarFetch('id', 'int', $id)) return;
        $entry = xarModAPIFunc('netquery', 'admin', 'getbbid', array('id' => $id));
        if ($entry == false) return;
        $data['entry'] = $entry;
        $data['returnlabel'] = xarML('Return to Blocker Admin');
        $data['deletelabel'] = xarML('Delete Record');
        break;
      case 'whoisip':
        if (!xarVarFetch('ip_addr', 'str:1:', $ip_addr)) return;
        $whois_result = xarModAPIFunc('netquery', 'user', 'whoisip', (array('ip_addr' => $ip_addr)));
        $data['ip_addr'] = $ip_addr;
        $data['whois_result'] = $whois_result;
        $data['returnlabel'] = xarML('Return to Blocker Admin');
        break;
      case 'sql':
        if (!xarVarFetch('field', 'str:1:', $field, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('where', 'str:1:', $where, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('search', 'str:1:', $search, '', XARVAR_NOT_REQUIRED)) return;
        if ($field=="" OR $where=="" OR $search=="")
        {
            $bbmode = 'select';
        }
        else
        {
            $entries = xarModAPIFunc('netquery', 'admin', 'getbbsql', array('numitems' => $limit, 'field' => $field, 'where' => $where, 'search' => $search));
        }
      case 'select':
      default:
        if ($bbmode!='sql')
        {
            $entries = xarModAPIFunc('netquery', 'admin', 'getbblog', array('numitems' => $limit));
        }
        $bbmode = "select";
        for ($i = 0; $i < count($entries); $i++)
        {
            $entry = $entries[$i];
            if (xarSecurityCheck('DeleteNetquery',0))
            {
                $entries[$i]['deleteurl'] = xarModURL('netquery', 'admin', 'bbdelid', array('id' => $entry['id']));
            }
            else
            {
                $entries[$i]['deleteurl'] = '';
            }
            $entries[$i]['deletetitle'] = xarML('Drop');
        }
        $data['entries'] = $entries;
        $data['delsellabel'] = xarML('Delete Selected');
        $data['delalllabel'] = xarML('Delete All');
        break;
    }
    $data['bbmode'] = $bbmode;
    $data['bbsettings'] = xarModAPIFunc('netquery', 'user', 'bb2_settings');
    $data['bbstats'] =  xarModAPIFunc('netquery', 'user', 'bb2_stats');
    $data['cfglink'] = Array('url'   => xarModURL('netquery', 'admin', 'config'),
                             'title' => xarML('Return to main configuration'),
                             'label' => xarML('Modify Config'));
    $data['hlplink'] = Array('url'   => 'modules/netquery/xardocs/manual.html#admin',
                             'title' => xarML('Netquery online manual'),
                             'label' => xarML('Online Manual'));
    return $data;
}
?>