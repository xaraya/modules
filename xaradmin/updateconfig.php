<?php
/**
 * Xaraya Google Search
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Google Search Module
 * @link http://xaraya.com/index.php/release/809.html
 * @author John Cox
 */
/**
 * update configuration
 */
function googlesearch_admin_updateconfig()
{
    //if (!xarVarFetch('itemsperpage','int:1:',$itemsperpage,'20',XARVAR_NOT_REQUIRED)) return;
  if (!xarVarFetch('license-key', 'str:1:', $licensekey, 'Enter your license key', XARVAR_NOT_REQUIRED)) return;
  if (!xarVarFetch('maxQueries', 'int', $maxQueries, '1000', XARVAR_NOT_REQUIRED)) return;
  if (!xarVarFetch('reset', 'array', $reset, 'array()', XARVAR_NOT_REQUIRED)) return;

  //if (!xarVarFetch('filter', 'checkbox', $filter, false, XARVAR_NOT_REQUIRED)) return;
  //if (!xarVarFetch('safesearch', 'checkbox', $safesearch, false, XARVAR_NOT_REQUIRED)) return;
  //if (!xarVarFetch('restrict', 'checkbox', $restrict, false, XARVAR_NOT_REQUIRED)) return;
  //if (!xarVarFetch('lr', 'checkbox', $lr, false, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    // Security Check
  if(!xarSecurityCheck('Admingooglesearch')) return;
    //xarModSetVar('googlesearch', 'itemsperpage', $itemsperpage);
    xarModSetVar('googlesearch', 'license-key', $licensekey);
    xarModSetVar('googlesearch', 'maxQueries', $maxQueries);

    isset($reset['cacheQuery']) ? xarModSetVar('googlesearch', 'cacheQuery', '') : '';
    isset($reset['cacheGoogleSearchResponse']) ? xarModSetVar('googlesearch', 'cacheGoogleSearchResponse', serialize(array())) : '';
    isset($reset['cacheGoogleSearchPage']) ? xarModSetVar('googlesearch', 'cacheGoogleSearchPage', '') : '';
    isset($reset['cacheLinks']) ? xarModSetVar('googlesearch', 'cacheLinks', serialize(array())) : '';
    isset($reset['cacheRetrievedPages']) ? xarModSetVar('googlesearch', 'cacheRetrievedPages', serialize(array())) : '';
    isset($reset['cacheRemoteURL']) ? xarModSetVar('googlesearch', 'cacheRemoteURL', '') : '';
    isset($reset['cacheRemoteFiles']) ? xarModSetVar('googlesearch', 'cacheRemoteFiles', serialize(array())) : '';
    isset($reset['cachePageIndex']) ? xarModSetVar('googlesearch', 'cachePageIndex', '') : '';
    isset($reset['cachePageHash']) ? xarModSetVar('googlesearch', 'cachePageHash', '') : '';

    //xarModSetVar('googlesearch', 'filter', $filter);
    //xarModSetVar('googlesearch', 'safesearch', $safesearch);
    //xarModSetVar('googlesearch', 'restrict', $restrict);
    //xarModSetVar('googlesearch', 'lr', $lr);
    xarModCallHooks('module','updateconfig','googlesearch', array('module' => 'googlesearch'));
    xarResponseRedirect(xarModURL('googlesearch', 'admin', 'modifyconfig'));
    return true;
}
?>