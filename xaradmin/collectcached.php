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
function googlesearch_admin_collectcached()
{
  $tplData = array();
  $tplData['message'] = '';
  $urlfragment = '/index.php?module=googlesearch&type=admin&func=viewcached';

  xarVarFetch('url', 'str:0:', $url, 'http://', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);
  xarVarFetch('cache', 'str', $cachePage, '', XARVAR_NOT_REQUIRED);
  xarVarFetch('action', 'str:0:', $action, '', XARVAR_NOT_REQUIRED);
  xarVarFetch('import', 'array', $import, array(), XARVAR_NOT_REQUIRED);



  if ($url != 'http://') {
    xarModSetVar('googlesearch', 'cacheRemoteURL', $url);

    $fullurl = $url.$urlfragment.'&list=1';
    if ($index = @fopen($fullurl, 'r')) {

      $failed = false;
      $files = array();
      while($line = fgets($index, 4096)) {
        $line = trim($line);
        if ($line == '') continue;
        $tmp = explode(' = ', $line);
        if (sizeof($tmp) == 2) {
          $files[$tmp[0]] = trim($tmp[1]);
        } else {
          $failed = true;
        }// if
      }// while

      fclose($index);

      if ($failed) {
        $tplData['files'] = $files = @unserialize(xarModGetVar('googlesearch', 'cacheRemoteFiles'));
        $tplData['message'] ="Response was not correct from <a target='_blank' href='$fullurl'>$fullurl</a>";

      } else if (sizeof($files) <= 0) {
        $tplData['files'] = $files = array();
        $tplData['message'] ="Response was empty from <a target='_blank' href='$fullurl'>$fullurl</a>";

      } else {
        $tplData['files'] = $files;
        $tplData['message'] = "Successfully retrieved list of available cached pages from <a target='_blank' href='$fullurl'>$url</a>";
        xarModSetVar('googlesearch', 'cacheRemoteFiles', serialize($files));

      }// if

    } else {
      $tplData['files'] = $files = @unserialize(xarModGetVar('googlesearch', 'cacheRemoteFiles'));
      $tplData['message'] = "Could not open <a target='_blank' href='$fullurl'>$fullurl</a>";
      $url = xarModGetVar('googlesearch', 'cacheRemoteURL');

    }// if
  } else {
    $tplData['files'] = $files = @unserialize(xarModGetVar('googlesearch', 'cacheRemoteFiles'));
    $url = xarModGetVar('googlesearch', 'cacheRemoteURL');

  }// if


  $savedPages = @unserialize(xarModGetVar('googlesearch', 'cacheRetrievedPages'));
  # do any actions
  switch($action) {
  default:
    break;

  case 'Import':
    set_time_limit(0);
    if (!empty($cachePage)) {
      $import[$cachePage] = 0;
    }// if
    $fullurl = $url.$urlfragment.'&filter=base64&cache=';
    foreach($import as $k=>$v) {
      if ($k != '') {
        $content = join('', file($fullurl.$k));
        if (trim($content) != '') {
          $pages[$files[$k]] = $content;
          $savedPages[$files[$k]] = $k;

        }// if
      }// if
    }// foreach

    $errors['Import'] = saveCachedPages($pages);
    unset($pages);
    xarModSetVar('googlesearch', 'cacheRetrievedPages', serialize($savedPages));
    break;
  }// switch


  $tplData['savedPages'] = $savedPages;
  $tplData['url'] = $url;
  return $tplData;
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