<?php
function netquery_userapi_validemail($args)
{
    extract($args);
    if (!isset($target))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $msg = '';
    $query_email_server = xarModGetVar('netquery', 'query_email_server');
    list($username,$domain) = explode("@",$target,2);
    if (checkdnsrr($domain.'.', 'MX') ) $addmsg = "<br />DNS Record Check: MX record returned OK.";
    else if (checkdnsrr($domain.'.', 'A') ) $addmsg = "<br />DNS Record Check: A record returned OK.";
    else if (checkdnsrr($domain.'.', 'CNAME') ) $addmsg = "<br />DNS Record Check: CNAME record returned OK.";
    else $addmsg = "<br />DNS Record Check: DNS record not returned.)";
    $msg .= $addmsg;
    if ($query_email_server)
    {
      if (getmxrr($domain, $mxhost))
      {
        $address = $mxhost[0];
      }
      else
      {
        $address = $domain;
      }
      $addmsg = "<br />MX Server Address Check: Address accepted by ".$address;
      if (!$sock = @fsockopen($address, 25, $errnum, $error, 10))
      {
        unset($sock);
        $addmsg = "<br />MX Server Address Check: Cannot connect to ".$address." (".$error.")";
      }
      else
      {
        if (preg_match("/^220/", $out = fgets($sock, 1024)))
        {
          fputs ($sock, "HELO ".$_SERVER['HTTP_HOST']."\r\n");
          $out = fgets ( $sock, 1024 );
          fputs ($sock, "MAIL FROM: <{$target}>\r\n");
          $from = fgets ( $sock, 1024 );
          fputs ($sock, "RCPT TO: <{$target}>\r\n");
          $to = fgets ($sock, 1024);
          fputs ($sock, "QUIT\r\n");
          fclose($sock);
          if (!preg_match("/^250/", $from) || !preg_match("/^250/", $to ))
          {
            $addmsg = "<br />MX Server Address Check: Address rejected by ".$address;
          }
        }
        else
        {
          $addmsg = "<br />MX Server Address Check: No response from ".$address;
        }
      }
      $msg .= $addmsg;
    }
    return $msg;
}
if (!function_exists('checkdnsrr'))
{
  function checkdnsrr($host, $type = '')
  {
    $digexec_local = xarModGetVar('netquery', 'digexec_local');
    if(!empty($host))
    {
      if($type == '') $type = "MX";
      $output = '';
      $k = '';
      $line = '';
      @exec("$digexec_local -type=$type $host", $output);
      $pattern = "/^$host/i";
      while(list($k, $line) = each($output))
      {
        if(preg_match($pattern, $line)) return true;
      }
      return false;
    }
  }
}
if (!function_exists('getmxrr'))
{
  function getmxrr($hostname, &$mxhosts)
  {
    $digexec_local = xarModGetVar('netquery', 'digexec_local');
    if (!is_array($mxhosts)) $mxhosts = array();
    if (!empty($hostname ))
    {
      $output = '';
      $ret = '';
      $k = '';
      $line = '';
      @exec("$digexec_local -type=MX $hostname", $output, $ret);
      while (list($k, $line) = each($output))
      {
        if (preg_match("/^$hostname\tMX preference = ([0-9]+), mail exchanger = (.*)$/", $line, $parts))
        {
          $mxhosts[$parts[1]]=$parts[2];
        }
      }
      if (count($mxhosts))
      {
        reset($mxhosts);
        ksort($mxhosts);
        $i = 0;
        while (list($pref,$host) = each($mxhosts))
        {
          $mxhosts2[$i] = $host;
          $i++;
        }
        $mxhosts = $mxhosts2;
        return true;
      }
      else
      {
        return false;
      }
    }
  }
}
?>