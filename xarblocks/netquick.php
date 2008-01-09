<?php
function netquery_netquickblock_init()
{
    return array(
        'nocache'     => 0,
        'pageshared'  => 1,
        'usershared'  => 1,
        'cacheexpire' => null
    );
}
function netquery_netquickblock_info()
{
    return array(
        'text_type'      => 'netquick',
        'module'         => 'netquery',
        'text_type_long' => xarML('Netquery Quick Options'),
        'allow_multiple' => true,
        'form_content'   => false,
        'form_refresh'   => false,
        'show_preview'   => true
    );
}
function netquery_netquickblock_display($blockinfo)
{
    // MichelV: cannot use this when there are no instances created.
  //  if (!xarSecurityCheck('ReadNetqueryBlock', 0, 'Block', $blockinfo['title'])) {return;}
    include_once "modules/netquery/xarincludes/nqSniff.class.php";
    if (!is_array($blockinfo['content']))
    {
        $vars = @unserialize($blockinfo['content']);
    }
    else
    {
        $vars = $blockinfo['content'];
    }
    if (!isset($vars['blockquery'])) $vars['blockquery'] = 'clientinfo';
    $data = array();
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    $data['buttondir'] = ((list($testdir) = split('[._-]', $data['stylesheet'])) && (!empty($testdir)) && (file_exists('modules/netquery/xarimages/'.$testdir))) ? 'modules/netquery/xarimages/'.$testdir : 'modules/netquery/xarimages/blbuttons';
    $data['bbsettings'] = xarModAPIFunc('netquery', 'user', 'bb2_settings');
    $data['bbstats'] = xarModAPIFunc('netquery', 'user', 'bb2_stats');
    $data['browserinfo'] = new nqSniff();
    $data['geoip'] = xarModAPIFunc('netquery', 'user', 'getgeoip', array('ip' => $data['browserinfo']->property('ip')));
    $data['topcountries_limit'] = xarModGetVar('netquery', 'topcountries_limit');
    $data['countries'] = xarModAPIFunc('netquery', 'user', 'getcountries', array('numitems' => $data['topcountries_limit']));
    $data['mapping_site'] = xarModGetVar('netquery', 'mapping_site');
    $data['whois_default'] = xarModGetVar('netquery', 'whois_default');
    $data['links'] = xarModAPIFunc('netquery','user','getlinks');
    $data['clientip'] = $_SERVER['REMOTE_ADDR'];
    $data['email'] = 'someone@'.gethostbyaddr($_SERVER['REMOTE_ADDR']);
    $data['httpurl'] = 'http://'.$_SERVER['SERVER_NAME'];
    if (countedB() != 'yes')
    {
      $data['bbstart'] = xarModAPIFunc('netquery', 'user', 'bb2_load');
      if (xarSessionGetVar('NQcounted') != 'yes' || $data['bbsettings']['display_stats'] == 'pagehits')
      {
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $geocctable = $xartable['netquery_geocc'];
        $query = "UPDATE $geocctable SET users = users + 1 WHERE cc = ?";
        $bindvars = array($data['geoip']['cc']);
        $result =& $dbconn->Execute($query,$bindvars);
        xarSessionSetVar('NQcounted', 'yes');
      }
    }
    $data['vars'] = $vars;
    $blockinfo['content'] = $data;
    return $blockinfo;
}
function &countedB()
{
    static $counted;
    if (isset($counted)) $counted = 'yes';
    else $counted = 'no';
    return $counted;
}
?>