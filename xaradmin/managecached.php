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
function googlesearch_admin_managecached()
{
  $defaultPage = xarModGetVar('googlesearch', 'cachePageIndex');
  $defaultHash = xarModGetVar('googlesearch', 'cachePageHash');
  $defaultFilter = xarModGetVar('googlesearch', 'cachePageFilter');
  $defaultDataFilter = xarModGetVar('googlesearch', 'cachePageDataFilter');
  xarVarFetch('action', 'str', $action, '', XARVAR_NOT_REQUIRED);
  xarVarFetch('page', 'int', $page, $defaultPage, XARVAR_NOT_REQUIRED);
  xarVarFetch('cache', 'str', $cachePage, $defaultHash, XARVAR_NOT_REQUIRED);
  xarVarFetch('filter', 'str', $filter, $defaultFilter, XARVAR_NOT_REQUIRED);
  xarVarFetch('datafilter', 'str', $datafilter, $defaultDataFilter, XARVAR_NOT_REQUIRED);

  if (!file_exists('var/cache/google/'.$cachePage.'.html')) {
    $cachePage = '';
  }// if

  $tplData = array();
  $itemsperpage = 6;

  $startIndex = $page*$itemsperpage;
  $endIndex = ($page+1)*$itemsperpage;

  $savedPages = @unserialize(xarModGetVar('googlesearch', 'cacheRetrievedPages'));
  $cachedPages = @array_flip($savedPages);

  switch($action) {
  case 'Delete':
    @unlink('var/cache/google/'.$cachePage.'.html');
    if (isset($cachedPages[$cachePage])) {
      if (isset($savedPages[$cachedPages[$cachePage]])) {
        unset($savedPages[$cachedPages[$cachePage]]);
      }// if
      unset($cachedPages[$cachePage]);
    }// if

    xarModSetVar('googlesearch', 'cacheRetrievedPages', serialize($savedPages));
    xarResponseRedirect(xarModURL('googlesearch', 'admin', 'managecached'));
    break;
  }// switch

  $tplData['pages'] = checkSavedPagesList($savedPages, $startIndex, $endIndex);

  xarModSetVar('googlesearch', 'cacheRetrievedPages', serialize($savedPages));
  xarModSetVar('googlesearch', 'cachePageIndex', $page);
  xarModSetVar('googlesearch', 'cachePageHash', $cachePage);
  xarModSetVar('googlesearch', 'cachePageFilter', $filter);
  xarModSetVar('googlesearch', 'cachePageDataFilter', $datafilter);

  $tplData['pagecount'] = $pagecount = sizeof($savedPages);
  if ($startIndex < 0 || $startIndex > $pagecount) {
    $startIndex = 0;
    $page = 0;
  }// if
  if ($endIndex > $pagecount) {
    $endIndex = $pagecount;
  }// if

  $tplData['prev'] = $page-1;
  $tplData['viewrange'] = ($startIndex+1).' - '.($endIndex);
  $tplData['lastpage'] = $endIndex >= $pagecount;
  $tplData['next'] = $page+1;

  $tplData['cachePage'] = $cachePage;
  $tplData['filter'] = $filter;
  $tplData['datafilter'] = $datafilter;
  $tplData['cacheURL'] = isset($cachedPages[$cachePage]) ? $cachedPages[$cachePage] : '';
  return $tplData;
}



function &checkSavedPagesList(&$savedPages, $start, $end)
{
  $files = array();
  $newfile = '';
  $indexPath = 'var/cache/google/CACHEKEYS';
  touch($indexPath);
  $pages = file($indexPath);
  $index = fopen($indexPath, 'w');
  foreach($pages as $line) {
    $line = trim($line);
    if ($line == '') continue;
    $tmp = explode(' = ', $line);
    if (file_exists('var/cache/google/'.$tmp[0].'.html')) {
      $files[$tmp[0]] = trim($tmp[1]);
      $newfile .= $line."\n";
    }// if
  }// if
  fputs($index, $newfile);
  fflush($index);
  fclose($index);

  $test = array_flip($files);
  $pages = array();
  $count = 0;

  foreach ($test as $k=>$v) {
    if (!isset($savedPages[$k])) {
      $savedPages[$k] = $v;
    }// if
  }// foreach

  foreach ($savedPages as $k=>$v) {
    if (!isset($test[$k])) {
      unset($savedPages[$k]);
    } else if ($count >= $start && $count < $end) {
      $pages[$test[$k]] = $k;
    }// if
    $count++;
  }// foreach

  return $pages;
}
?>