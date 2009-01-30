<?php
/**
 * Twitter Module 
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */

/**
 * Process requests to Twitter API
 * 
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return mixed array containing the items, or false on failure
 */
function twitter_userapi_process($args)
{ 
  extract($args);

  if (empty($url)) return;
  if (empty($postargs)) {
    $cached = !isset($cached) ? true : false;
    $refresh = empty($refresh) ? 300 : $refresh;
    $cachedir = empty($cachedir) ? 'cache' : $cachedir;
    $extension = empty($extension) ? 'xml' : $extension;
    $superrors = empty($superrors) ? true : $superrors;
  } 
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
            if (!empty($response)) {
              return $response;
            }
          }
      }
  }

  // we can only try GETfile if we're not POSTing
  if (empty($postargs)) {
    $response = xarModAPIFunc('base', 'user', 'getfile', 
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
  if ((!isset($response) || $response==false) && function_exists('curl_init')) {
    $useragent = 'Xaraya Twitter Module v0.0.2';
    $headers = array(
      'X-Twitter-Client: Xaraya Twitter Module',
      'X-Twitter-Client-Version: 0.0.2',
      'X-Twitter-Client-URL: http://www.xaraya.com/index.php/release/991.html',
      'Exist: '
    );
    $ch = curl_init($url);

    if(!empty($postargs)){
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($postargs !== true) 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postargs);
    }
    
    if(!empty($username) && !empty($password))
        curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
    
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

  
  // if we still got no response and we were posting, try json
  if ((!isset($response) || $response==false) && !empty($postargs)) {

    $url = str_replace('.xml', '.json', $url);

    $out="POST ".$url." HTTP/1.1\r\n"
      ."Host: twitter.com\r\n"
      ."Authorization: Basic ".base64_encode ($username.':'.$password)."\r\n"
      ."Content-type: application/x-www-form-urlencoded\r\n"
      ."Content-length: ".strlen ("$postargs")."\r\n"
      ."User-Agent: Xaraya Twitter Module v0.0.2\r\n"
      ."X-Twitter-Client: Xaraya Twitter Module\r\n"
      ."X-Twitter-Client-Version: 0.0.2\r\n"
      ."X-Twitter-Client-URL: http://www.xaraya.com/index.php/release/991.html\r\n"
      ."Connection: Close\r\n\r\n";
      if ($postargs !== true) {
        $out .= "$postargs";
      }
    $fp = fsockopen ('twitter.com', 80);
    if (!$fp) return false;
    fwrite ($fp, $out);
    $response =  '';
    while(!feof($fp)) { $response .= fgets($fp,8192); }
    fclose($fp);
    if (!empty($response)) {
      $chunks = explode("\r\n\r\n",trim($response));
      if (!is_array($chunks) or count($chunks) < 2) {
        return false;
      }
      $header  = $chunks[count($chunks) - 2];
      $headers = explode("\n",$header);    
      if (!is_array($headers) or count($headers) < 1) return false;
      $httpResponse = strtolower(trim($headers[0]));
      //if ($httpResponse != 'http/1.0 200 ok' && $httpResponse != 'http/1.1 200 ok') return false;
    }
  }

  // give up and just return whatever we found
  return $response;

} 
?>