<?php
/**
 * modify an item
 * 
 * @param  $ 'cid' the id of the link to be modified
 */
function censor_admin_modify($args)
{ 
    // Get parameters
    if (!xarVarFetch('cid', 'int:1:', $cid)) return;
    if (!xarVarFetch('obid', 'str:1:', $obid, '', XARVAR_NOT_REQUIRED)) return;

    extract($args);

    if (!empty($obid)) {
        $cid = $obid;
    } 

    $data = xarModAPIFunc('censor',
        'user',
        'get',
        array('cid' => $cid));

    if ($data == false) return;

    // Security Check
    if (!xarSecurityCheck('EditCensor')) return;
    $data['authid'] = xarSecGenAuthKey();
    $data['createlabel'] = xarML('Submit');
    $allowedlocales = xarConfigGetVar('Site.MLS.AllowedLocales');
    foreach($allowedlocales as $locale) {
       $data['locales'][] = array('name' => $locale, 'value' => $locale);
      }
    return $data;

}
?>