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
function googlesearch_admin_getcached()
{
  $tplData = array();
  $errors = array();
  $itemsperpage = 10;  # currently limited to 10 by google

  xarVarFetch('q', 'str:0:', $q, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);
  xarVarFetch('page', 'int', $page, 0, XARVAR_NOT_REQUIRED);
  xarVarFetch('url', 'str:0:', $url, 'http://', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);
  xarVarFetch('action', 'str:0:', $action, '', XARVAR_NOT_REQUIRED);
  xarVarFetch('googleURLs', 'array', $googleURLs, '', XARVAR_NOT_REQUIRED);
  xarVarFetch('savedlinks', 'array', $savedlinks, '', XARVAR_NOT_REQUIRED);

  $queryDay = xarModGetVar('googlesearch', 'queryCountDay');
  $queryCount = xarModGetVar('googlesearch', 'queryCount');
  $midnight = mktime(0,0,0, date('m'), date('d'), date('Y'));
  if ($queryDay < $midnight) {
    $queryDay = $midnight;
    $queryCount = 0;
    xarModSetVar('googlesearch', 'queryCountDay', $midnight);
    xarModSetVar('googlesearch', 'queryCount', 0);
  }// if



  # query google
  if (!empty($q)) {
    xarModSetVar('googlesearch', 'cacheQuery', $q);

    include_once('modules/googlesearch/xarclass/nusoap.php');
    $args['key']          = xarModGetUserVar('googlesearch', 'license-key');
    $args['q']            = $q;
    $args['start']        = $page*$itemsperpage;
    $args['maxResults']   = $itemsperpage;   //xarModGetUserVar('googlesearch', 'itemsperpage');
    $args['filter']       = false; //xarModGetUserVar('googlesearch', 'filter');
    $args['restrict']     = '';   //xarModGetUserVar('googlesearch', 'restrict');
    $args['safeSearch']   = false; //xarModGetUserVar('googlesearch', 'safesearch');
    $args['lr']           = '';   //xarModGetUserVar('googlesearch', 'lr');
    $args['ie']           = '';
    $args['oe']           = '';

    $soapclient = new soapclient("http://api.google.com/search/beta2");
    $result = $soapclient->call("doGoogleSearch", $args, "urn:GoogleSearch");
    $queryCount++;

    xarModSetVar('googlesearch', 'cacheGoogleSearchResponse', serialize($result));
    xarModSetVar('googlesearch', 'cacheGoogleSearchPage', $page);

  } else {
    $q = xarModGetVar('googlesearch', 'cacheQuery');
    $result = @unserialize(xarModGetVar('googlesearch', 'cacheGoogleSearchResponse'));
    $page = xarModGetVar('googlesearch', 'cacheGoogleSearchPage');

  }// if

  if (sizeof($result) > 0) {
    $tplData['prev'] = $page-1;
    $tplData['viewrange'] = $result['startIndex'].' - '.$result['endIndex'];
    $tplData['lastpage'] = $result['endIndex'] == $result['estimatedTotalResultsCount'];
    $tplData['next'] = $page+1;

    $tplData['estimatedTotalResultsCount'] = $result['estimatedTotalResultsCount'];
    $tplData['searchQuery']                = $result['searchQuery'];
    $tplData['searchComments']             = $result['searchComments'];
    $googlelinks                           = $result['resultElements'];
  } else {
    $googlelinks = array();

  }// if


  # add url to links
  $links = @unserialize(xarModGetVar('googlesearch', 'cacheLinks'));
  if (!empty($url) && $url != 'http://') {
    if (!isset($links[$url])) {
      $links[$url] = sizeof($links);
    }// if
  }// if



  # add any googlelinks to our saved links
  if (!empty($googleURLs) && is_array($googleURLs)) {
    foreach($googleURLs as $k=>$v) {
      if ($k != '') {
        $links[$k] = sizeof($links);
      }// if
    }// foreach
    if ($action == 'Fetch') {
      $savedlinks = $googleURLs;
    }// if
  }// if



  $savedPages = @unserialize(xarModGetVar('googlesearch', 'cacheRetrievedPages'));
  # do any actions
  switch($action) {
  default:
    break;

  case 'sortlinks':
    krsort($links);
    break;

  case 'Delete':
    if (!empty($url) && $url != 'http://') {
      $savedlinks[$url] = 0;
    }// if
    if (is_array($savedlinks)) {
      foreach ($savedlinks as $k=>$v) {
        if ($k != '') {
          unset($links[$k]);
        }// if
      }// foreach
      $links = array_flip(array_keys($links));
    }// if
    break;

  case 'Fetch':
    set_time_limit(0);
    if (!empty($url) && $url != 'http://') {
      $savedlinks[$url] = 0;
    }// if
    foreach($savedlinks as $k=>$v) {
      if ($k != '') {
        $pages[$k] = retrieveCachedPage($k);
        $queryCount++;
        $savedPages[$k] = md5($k);
      }// if
    }// foreach
    $errors['Fetch'] = saveCachedPages($pages);
    unset($pages);
    xarModSetVar('googlesearch', 'cacheRetrievedPages', serialize($savedPages));
    break;
  }// switch


  #save the state of our saved links
  xarModSetVar('googlesearch', 'cacheLinks', serialize($links));



  $tplData['q'] = $q;
  $tplData['googlelinks'] = $googlelinks;
  $tplData['links'] = @array_reverse(array_flip($links));
  $tplData['savedPages'] = $savedPages;
  $tplData['url'] = $url;
  $tplData['errors'] = &$errors;
  xarModSetVar('googlesearch', 'queryCount', $queryCount);
  return $tplData;
}


function &retrieveCachedPage($url)
{
    include_once('modules/googlesearch/xarclass/nusoap.php');
    $args['key']          = xarModGetUserVar('googlesearch', 'license-key');
    $args['url']           = $url;

    $soapclient = new soapclient("http://api.google.com/search/beta2");
    return $soapclient->call("doGetCachedPage", $args, "urn:GoogleSearch");
}

function saveCachedPages($pages)
{
  $errors = '';
  $indexPath = 'var/cache/google/CACHEKEYS';

  if ($index = fopen($indexPath, 'a+')) {
    foreach($pages as $url=>$content) {
      $key = md5($url);
      fputs($index, "$key = $url\n");
      fflush($index);

      $pagePath = 'var/cache/google/'.$key.'.html';
      if ($page = fopen($pagePath, 'w')) {
        fputs($page, base64_decode($content));
        fflush($page);
        fclose($page);
      } else {
        $errors[$url] = 'Error opening '.$pagePath;
      }// if
    }// foreach
    fclose($index);
  } else {
    $errors[''] = 'Error opening '.$indexPath;
  }// if

  return $errors;
}
?>