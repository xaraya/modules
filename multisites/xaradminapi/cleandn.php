<?php
//CleanDN function - to clean all extensions that people may want to use
// The multisite config has already setup modvar called DNexts. Let's use this
function multisites_adminapi_cleandn($sitedn)
{

  if (!isset($sitedn) || empty($sitedn)){
    $msg = xarML("Could not clean ".$sitedn);
            xarExceptionSet(XAR_USER_EXCEPTION, 'ERROR-API CLEANDN', new DefaultUserException($msg));
            return $msg;
  }

  $siteext =xarModGetVar('multisites','DNexts');

  $ext_array = explode(',',$siteext);
  // sort so for examp .com.au is before .com
  usort ($ext_array,'lengthcmp');
  // get rid of www prefix and all dn extensions
  $sitedn = str_replace('www.','',$sitedn);
  foreach ($ext_array as $key => $ext) {
    $sitedn = str_replace($ext,'',$sitedn);
  }

return $sitedn;
}
function lengthcmp ($a, $b) 
{
    if (strlen($a) > strlen($b)) return 0;
    return ($a > $b) ? -1 : 1;
}
?>
