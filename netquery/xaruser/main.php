<?php
/**
 * File: $Id:
 */

function netquery_user_main()
{ 
    $data = xarModAPIFunc('netquery', 'user', 'mainapi'); 

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    $netqueryWhoisTable = $xartable['netquery_whois'];
    $query = "SELECT whois_id, whois_ext FROM $netqueryWhoisTable ORDER BY whois_ext";
    $response =& $dbconn->Execute($query);

    $i=0;
    while (!$response->EOF) {
        $row = $response->GetRowAssoc(false);
        $tlds[$i] = $row['whois_ext'];
        $i++;
        $response->MoveNext();
    }

    $data['all_tlds']            = $tlds;
    $data['windows_system']      = xarModGetVar('netquery', 'windows_system');
    $data['localexec_enabled']      = xarModGetVar('netquery', 'localexec_enabled');
    $data['whois_enabled']       = xarModGetVar('netquery', 'whois_enabled');
    $data['whoisip_enabled']     = xarModGetVar('netquery', 'whoisip_enabled');
    $data['dns_lookup_enabled']  = xarModGetVar('netquery', 'dns_lookup_enabled');
    $data['dns_dig_enabled']     = xarModGetVar('netquery', 'dns_dig_enabled');
    $data['ping_enabled']        = xarModGetVar('netquery', 'ping_enabled');
    $data['trace_enabled']       = xarModGetVar('netquery', 'trace_enabled');
    $data['port_check_enabled']  = xarModGetVar('netquery', 'port_check_enabled');
    $data['capture_log_enabled'] = xarModGetVar('netquery', 'capture_log_enabled');
    $data['results']             = '';
    xarVarFetch('querytype', 'str:1:', $data['querytype'], 'none', XARVAR_NOT_REQUIRED);
    xarVarFetch('domain', 'str:1:', $data['domain'], 'example', XARVAR_NOT_REQUIRED);
    xarVarFetch('ext', 'str:1:', $data['ext'], '.com', XARVAR_NOT_REQUIRED);
    xarVarFetch('addr', 'str:1:', $data['addr'], $_SERVER['REMOTE_ADDR'], XARVAR_NOT_REQUIRED);
    xarVarFetch('host', 'str:1:', $data['host'], $_SERVER['REMOTE_HOST'], XARVAR_NOT_REQUIRED);
    xarVarFetch('server', 'str:1:', $data['server'], $_SERVER['SERVER_NAME'], XARVAR_NOT_REQUIRED);
    xarVarFetch('maxp', 'int:1:', $data['maxp'], '4', XARVAR_NOT_REQUIRED);
    xarVarFetch('portnum', 'int:1:', $data['portnum'], '80', XARVAR_NOT_REQUIRED);
    if ($data['querytype'] == 'none')
    {
        return $data;
    }
    else if ($data['querytype'] == 'whois')
    {
          $buffer = '';
          $nextServer = '';
          $ext = $data['ext'];
          $target = $data['domain'].$data['ext'];
          $msg = "<p><b>WWWhois Results:</b><blockquote>";
          $sql = "SELECT whois_server FROM $netqueryWhoisTable WHERE whois_ext = '$ext'";
          $response = $dbconn->Execute($sql);
          $row = $response->GetRowAssoc(false);
          $server = $row['whois_server'];
          if (! $sock = fsockopen($server, 43, $num, $error, 10)){
            unset($sock);
            $msg .= "Timed-out connecting to $server (port 43)";
          }
          else{
            fputs($sock, "$target\n");
            while (!feof($sock))
              $buffer .= fgets($sock, 10240); 
          }
          fclose($sock);
          if(! eregi("Whois Server:", $buffer)){
            if(eregi("no match", $buffer))
              $msg .= "NOT FOUND: No match for $target<br>";
            else
              $msg .= "Ambiguous query, multiple matches for $target:<br>";
          } else {
            $buffer = split("\n", $buffer);
            for ($i=0; $i<sizeof($buffer); $i++){
              if (eregi("Whois Server:", $buffer[$i]))
                $buffer = $buffer[$i];
            }
            $nextServer = substr($buffer, 17, (strlen($buffer)-17));
            $nextServer = str_replace("1:Whois Server:", "", trim(rtrim($nextServer)));
            $buffer = "";
            if(! $sock = fsockopen($nextServer, 43, $num, $error, 10)){
              unset($sock);
              $msg .= "Timed-out connecting to $nextServer (port 43)";
            } else {
              fputs($sock, "$target\n");
              while (!feof($sock))
                $buffer .= fgets($sock, 10240);
              fclose($sock);
            }
          }
          $msg .= nl2br($buffer);
          $msg .= "</blockquote></p>";
          $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'whoisip')
    {
          $buffer = '';
          $nextServer = '';
          $target = $data['addr'];
          $server = "whois.arin.net";
          $msg = "<p><b>IP Whois Results:</b><blockquote>";
          if (!$target = gethostbyname($target))
            $msg .= "IP Whois requires an IP address.";
          else{
            if (! $sock = fsockopen($server, 43, $num, $error, 20)){
              unset($sock);
              $msg .= "Timed-out connecting to $server (port 43)";
              }
            else{
              fputs($sock, "$target\n");
              while (!feof($sock))
                $buffer .= fgets($sock, 10240); 
              fclose($sock);
              }
             if (eregi("RIPE.NET", $buffer))
               $nextServer = "whois.ripe.net";
             else if (eregi("whois.apnic.net", $buffer))
               $nextServer = "whois.apnic.net";
             else if (eregi("nic.ad.jp", $buffer)){
               $nextServer = "whois.nic.ad.jp";
               #/e suppresses Japanese character output from JPNIC
               $extra = "/e";
               }
             else if (eregi("whois.registro.br", $buffer))
               $nextServer = "whois.registro.br";
             if($nextServer){
               $buffer = "";
               if(! $sock = fsockopen($nextServer, 43, $num, $error, 10)){
                 unset($sock);
                 $msg .= "Timed-out connecting to $nextServer (port 43)";
                 }
               else{
                 fputs($sock, "$target$extra\n");
                 while (!feof($sock))
                   $buffer .= fgets($sock, 10240);
                 fclose($sock);
                 }
               }
            $buffer = str_replace(" ", "&nbsp;", $buffer);
            $msg .= nl2br($buffer);
            }
          $msg .= "</blockquote></p>";
          $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'lookup')
    {
          $target = $data['host'];
          $msg = ('<p><b>DNS Lookup Results:</b><blockquote>');
          $msg .= $target.' resolved to ';
          if( eregi("[a-zA-Z]", $target) )
            $msg .= gethostbyname($target);
          else
            $msg .= gethostbyaddr($target);
          $msg .= '</blockquote></p>';
          $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'dig')
    {
          $target = $data['host'];
          $msg = ('<p><b>DNS Query (Dig) Results:</b><blockquote>');
          if( eregi("[a-zA-Z]", $target) )
            $ntarget = gethostbyname($target);
          else
            $ntarget = gethostbyaddr($target);
          if ( !eregi("[a-zA-Z]", $target) && !eregi("[a-zA-Z]", $ntarget) )
            $msg .= 'DNS query (Dig) requires a hostname.';
          else {
            if ( !eregi("[a-zA-Z]", $target) ) $target = $ntarget;
            if (! $msg .= trim(nl2br(`dig any '$target'`)))
              $msg .= "The <i>dig</i> command is not working on your system.";
            }
          $msg .= '</blockquote></p>';
          $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'ping')
    {
          $png = '';
          $winsys = $data['windows_system'];
          $localsys = $data['localexec_enabled'];
          $target = $data['host'];
          $tpoints = $data['maxp'];
          if ($localsys) {
                  $msg = ('<p><b>Ping Results:</b><blockquote>');
                  if ($winsys) {$PN='ping -n '.$tpoints.' '.$target;}
                  else {$PN='ping -c'.$tpoints.' -w'.$tpoints.' '.$target;}
                  exec($PN, $response, $rval);
                  for ($i = 0; $i < count($response); $i++) {
                        $png.=$response[$i]."<BR>";
                  }
                  if (! $msg .= trim(nl2br($png))) 
                    $msg .= 'Ping failed. You may need to configure your server permissions.';
                  $msg .= '</blockquote></p>';
                  $data['results'] .= $msg;
          }
    }
    else if ($data['querytype'] == 'trace')
    {
          $rt = '';
          $winsys = $data['windows_system'];
          $localsys = $data['localexec_enabled'];
          $target = $data['host'];
          if ($localsys) {
                  $msg = ('<p><b>Traceroute Results:</b><blockquote>');
                  if ($winsys) {$TR='tracert '.$target;}
                  else {$TR='traceroute '.$target;}
                  exec($TR, $response, $rval);
                  for ($i = 0; $i < count($response); $i++) {
                        $rt.=$response[$i].'<br>';
                  }
                  if (! $msg .= trim(nl2br($rt))) 
                    $msg .= 'Traceroute failed. You may need to configure your server permissions.';
                  $msg .= '</blockquote></p>';
                  $data['results'] .= $msg;
          }
    }
    else if ($data['querytype'] == 'port')
    {
          $target = $data['server'];
          $tport = $data['portnum'];
          $msg = ('<p><b>Checking Port '.$tport.'</b>...<blockquote>');
          if (! $sock = fsockopen($target, $tport, $num, $error, 5))
            $msg .= 'Port '.$tport.' does not appear to be open.';
          else{
            $msg .= 'Port '.$tport.' is open and accepting connections.';
            fclose($sock);
          }
          $msg .= '</blockquote></p>';
          $data['results'] .= $msg;
    }
    if ($data['capture_log_enabled'] && $data['querytype'] != 'none')
    {
          $filename = 'capturedata.txt';
          $fp = fopen($filename, 'a');
          $string = 'Network Query: '.$target.'   User IP: '.$_SERVER[REMOTE_ADDR].'\n';
          $write = fputs($fp, $string);
          fclose($fp);
    }
    return $data;
}
?>
