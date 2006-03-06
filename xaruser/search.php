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
function googlesearch_user_search()
{
    //xarVarFetch('startnum', 'id', $startnum, '1', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);
    xarVarFetch('q', 'str:0:', $q, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);

    // Security Check
    if(!xarSecurityCheck('Overviewgooglesearch')) return;

/*
    $args['params']['key']          = xarModGetUserVar('googlesearch', 'license-key');
    $args['params']['q']            = 'xaraya';
    $args['params']['start']        = 0;
    $args['params']['maxResults']   = xarModGetUserVar('googlesearch', 'itemsperpage');
    $args['params']['filter']       = xarModGetUserVar('googlesearch', 'filter');
    $args['params']['restrict']     = xarModGetUserVar('googlesearch', 'restrict');
    $args['params']['safeSearch']   = xarModGetUserVar('googlesearch', 'safesearch');
    $args['params']['lr']           = xarModGetUserVar('googlesearch', 'lr');
    $args['params']['ie']           = '';
    $args['params']['oe']           = '';
    $endpoint['site']       = 'http://api.google.com';
    $endpoint['path']       = 'GoogleSearch.wsdl';
    $soap['methodname']     = 'doGoogleSearch';
    $soap['namespace']      = 'urn:GoogleSearch';
    $soap['soapaction']     = 'urn:GoogleSearch';
    $soap['headers']        = 'Constants.NS_URI_SOAP_ENC';

    // The user API function is called
    $links = xarModAPIFunc('soapserver',
                           'user',
                           'callsoap',
                           array('params' => $args,
                                 'site' => $endpoint['site'],
                                 'path' => $endpoint['path'],
                                 'methodname' => $soap['methodname'],
                                 'namespace'    => $soap['namespace'],
                                 'soapaction'   => $soap['soapaction'],
                                 'headers'      => $soap['headers'],
                                 'usewsdl'      => true));
    if (!$links) return;
*/
    $data = array();
    include_once('modules/googlesearch/xarclass/nusoap.php');
    if (!empty($q)){
        $queryDay = xarModGetVar('googlesearch', 'queryCountDay');
        $queryCount = xarModGetVar('googlesearch', 'queryCount');
        $maxQueries = xarModGetVar('googlesearch', 'maxQueries');
        $midnight = mktime(0,0,0, date('m'), date('d'), date('Y'));
        if ($queryDay < $midnight) {
          $queryDay = $midnight;
          $queryCount = 0;
          xarModSetVar('googlesearch', 'queryCountDay', $midnight);
          xarModSetVar('googlesearch', 'queryCount', 0);
        }// if

        if ($queryCount < $maxQueries) {

          $args['key']          = xarModGetUserVar('googlesearch', 'license-key');
          $args['q']            = $q;
          $args['start']        = 0;
          $args['maxResults']   = 10;   //xarModGetUserVar('googlesearch', 'itemsperpage');
          $args['filter']       = true; //xarModGetUserVar('googlesearch', 'filter');
          $args['restrict']     = '';   //xarModGetUserVar('googlesearch', 'restrict');
          $args['safeSearch']   = true; //xarModGetUserVar('googlesearch', 'safesearch');
          $args['lr']           = '';   //xarModGetUserVar('googlesearch', 'lr');
          $args['ie']           = '';
          $args['oe']           = '';

          $soapclient = new soapclient("http://api.google.com/search/beta2");
          $result = $soapclient->call("doGoogleSearch", $args, "urn:GoogleSearch");
          xarModSetVar('googlesearch', 'queryCount', $queryCount+1);

          if (!empty($result['faultcode'])) {
            $data['message']                    = $result['faultstring'];
            $data['links']                      = '';
          } else {
            $data['estimatedTotalResultsCount'] = $result['estimatedTotalResultsCount'];
            $data['searchQuery']                = $result['searchQuery'];
            $data['links']                      = $result['resultElements'];
          }

        } else {
          $data['message'] = xarML('Sorry, we have reached the maximum number of google searches for today');
          $data['links'] = '';
        }// if

    } else {
        $data['links']                      = '';
    }

    return $data;
}
?>
