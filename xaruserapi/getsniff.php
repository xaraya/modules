<?php
function netquery_userapi_sniff($arg)
{
  $browserinfo = xarSessionGetVar('browserinfo');
  if (is_string($browserinfo) && is_object(unserialize($browserinfo)))
  {
    return true;
  }
  else
  {
    if (file_exists('modules/sniffer/class/xarSniff.php'))
    {
      include_once('modules/sniffer/class/xarSniff.php');
      $client = new xarSniff();
    }
    elseif (file_exists('modules/netquery/xarincludes/nqSniff.class.php'))
    {
      include_once('modules/netquery/xarincludes/nqSniff.class.php');
      $client = new nqSniff();
    }
    else
    {
      return false;
    }
    xarSessionSetVar('browserinfo', serialize($client));
    return true;
  }
  return false;
}
function netquery_userapi_getsniff()
{
  $result = false;
  $browserinfo = xarSessionGetVar('browserinfo');
  if (is_string($browserinfo) && is_object($info = unserialize($browserinfo)))
  {
    $result = $info;
  }
  else
  {
    $uas = xarSessionGetVar('uaid');
    $success = netquery_userapi_sniff($uas);
    if ($success)
    {
      $browserinfo = xarSessionGetVar('browserinfo');
      if (is_string($browserinfo) && is_object($info = unserialize($browserinfo)))
      {
        $result = $info;
      }
    }
  }
  return $result;
}
function netquery_userapi_property($args)
{
  extract($args);
  if (!isset($property_name)) return;
  $result = netquery_userapi_getsniff();
  return $result->property($property_name);
}
function netquery_userapi_has_feature($args)
{
  extract($args);
  if (!isset($feature)) return;
  $result = netquery_userapi_getsniff();
  return $result->has_feature($feature);
}
function netquery_userapi_has_quirk($args)
{
  extract($args);
  if (!isset($quirk)) return;
  $result = netquery_userapi_getsniff();
  return $result->has_quirk($quirk);
}
?>