<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Standard function to do something
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_userapi_checkupdate($args)
{
   return false;
   // @TODO:
    extract($args);

  $modname = 'crispbb';
  $modid = xarMod::getRegID($modname);
  $modinfo = xarMod::getInfo($modid);
  if (empty($version)) {
    $version = $modinfo['version'];
  }
  $client     = 'Xaraya crispBB Module';
  $clientver  = $version;
  $clienturl  = 'http://www.xaraya.com/index.php/release/970.html';
  $useragent  = 'Xaraya crispBB Module v.'.$version;

  $url = 'http://www.crispbb.com/index.php?module=crispbb&type=release&func=latestrelease&pageName=xml';

  if (empty($url)) return;
  if (empty($postargs)) {
    $cached = !isset($cached) ? false : $cached;
    $refresh = empty($refresh) ? 300 : $refresh;
    $cachedir = empty($cachedir) ? 'cache' : $cachedir;
    $extension = empty($extension) ? '.xml' : $extension;
    if (!empty($username)) $extension = '.'.$username.$extension;
  }
  $superrors = !isset($superrors) ? true : $superrors;
  // check if this file is already cached
  if ($cached) {
      $vardir = xarCoreGetVarDirPath();
      $file = $vardir . '/' . $cachedir . '/' . md5($url) . $extension;
      $expire = time() - $refresh;
      if (file_exists($file) && filemtime($file) > $expire) {
          $fp = @fopen($file, 'rb');
          if ($fp != false) {
            $response = '';
            while (!feof($fp)) {
                $response .= fread($fp, filesize($file));
            }
            fclose($fp);
            /*
            if (!empty($response)) {
              return $response;
            }
            */
          }
      }
  }
  // we can only try GETfile if we're not POSTing
  if (empty($postargs) && empty($response)) {
    $response = xarMod::apiFunc('base', 'user', 'getfile',
      array(
        'url' => $url,
        'cached' => $cached,
        'refresh' => $refresh,
        'extension' => $extension,
        'cachedir' => $cachedir,
        'superrors' => $superrors
      ));
  }

  // if we got no response we either got postargs or getfile failed, try curl
  if ((!isset($response) || empty($response) || $response==false) && function_exists('curl_init')) {
    $headers = array(
      'X-crispBB-Client: '.$client,
      'X-crispBB-Client-Version: '.$clientver,
      'X-crispBB-Client-URL: '.$clienturl,
      'Exist: ');
    $ch = curl_init($url);

    /*
    if(!empty($postargs)){
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($postargs !== true)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postargs);
    }

    if(!empty($username) && !empty($password))
        curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
    */

    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_NOBODY, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);

    $responseInfo=curl_getinfo($ch);
    curl_close($ch);

    if(intval($responseInfo['http_code'])!=200){
      if (!$superrors){
        $msg = xarML('URL #(1) returned response #(2)', $url, $responseInfo['http_code']);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return;
      }
      $response = false;
    }
    if (empty($postargs) && !empty($response) && $cached && is_dir($vardir . '/' . $cachedir)) {
      $fp = @fopen($file,'wb');
      if (!$fp) {
          if (!$superrors){
          $msg = xarML('Error saving URL #(1) to cache file #(2)', $url, $file);
          xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                          new SystemException($msg));
          }
          return;
      }
      $size = fwrite($fp, $response);
      if (!$size || $size < strlen($response)) {
          if (!$superrors){
              $msg = xarML('URL #(1) truncated to #(2) bytes when saving to cache file #(3)', $url, $size, $file);
              xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                              new SystemException($msg));
          }
          return;
      }
      fclose($fp);
    }
  }

  if (!$response) return false;


    // try parsing the response with simplexml
    if (class_exists('SimpleXMLElement')){
        $xml = new SimpleXMLElement($response);
        if (!$xml) return false;
        $newversion = (string)$xml->version;
    }

    $isupdated = false;

    if (!empty($newversion)) {
        list($maj, $min, $mic) = explode('.', $newversion);
        list($omaj, $omin, $omic) = explode('.', $version);
        if ($maj > $omaj) { // new major version
            $isupdated = $newversion;
        } elseif ($maj == $omaj) { // same major version
            if ($min > $omin) { // new minor version
                $isupdated = $newversion;
            } elseif ($min == $omin) { // same minor version
                if ($mic > $omic) { // new micro version
                    $isupdated = $newversion;
                }
            }
        }
    }


    return $isupdated;
}
?>