<?php
function netquery_user_main()
{
    $data = xarModAPIFunc('netquery', 'user', 'mainapi');
    $clrlink = $data['clrlink'];
    if ($data['querytype'] == 'none')
    {
        return $data;
    }
    else if ($data['querytype'] == 'whois')
    {
        $domain = $data['domain'];
        $whois_tld = $data['whois_tld'];
        $whois_max_limit = $data['whois_max_limit'];
        $msg = ('<table class="nqoutput">');
        $j = 1;
        while ($j <= $whois_max_limit && !empty($domain[$j])) {
            $readbuf = '';
            $nextServer = '';
            $link = xarModAPIFunc('netquery', 'user', 'getlink', array('whois_tld' => $whois_tld[$j]));
            $whois_server = $link['whois_server'];
            $whois_prefix = $link['whois_prefix'];
            $whois_suffix = $link['whois_suffix'];
            $whois_unfound = $link['whois_unfound'];
            $target = $domain[$j].'.'.$whois_tld[$j];
            if (! empty($whois_prefix)) $target = $whois_prefix.' '.$target;
            if (! empty($whois_suffix)) $target = $target.' '.$whois_suffix;
            $msg .= ('<tr><th>Whois Results '.$j.' [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</th></tr><tr><td>');
            if (! $sock = @fsockopen($whois_server, 43, $errnum, $error, 10)){
                unset($sock);
                $msg .= "Cannot connect to ".$whois_server." (".$error.")";
            } else {
                fputs($sock, $target."\r\n");
                while (!feof($sock)) {
                    $readbuf .= fgets($sock, 10240);
                }
                @fclose($sock);
            }
            if (! eregi("Whois Server:", $readbuf)) {
                if (! empty($whois_unfound) && eregi($whois_unfound, $readbuf)) $msg .= "<span class=\"nq-red\">NOT FOUND</span>: No match for $target<br />";
            } else {
                $readbuf = split("\n", $readbuf);
                for ($i=0; $i<sizeof($readbuf); $i++) {
                    if (eregi("Whois Server:", $readbuf[$i]))
                        $readbuf = $readbuf[$i];
                    }
                $nextServer = substr($readbuf, 17, (strlen($readbuf)-17));
                $nextServer = str_replace("1:Whois Server:", "", trim(rtrim($nextServer)));
                $readbuf = "";
                if (! $sock = @fsockopen($nextServer, 43, $errnum, $error, 10)) {
                    unset($sock);
                    $msg .= "Cannot connect to ".$nextServer." (".$error.")";
                } else {
                    fputs($sock, $target."\r\n");
                    while (!feof($sock)) {
                        $readbuf .= fgets($sock, 10240);
                    }
                    @fclose($sock);
                }
            }
            $msg .= nl2br($readbuf);
            $msg .= '</td></tr>';
            $j++;
        }
        $msg .= '</table><hr />';
        $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'whoisip')
    {
        $readbuf = '';
        $nextServer = '';
        $extra = '';
        $target = $data['host'];
        $whois_server = "whois.arin.net";
        $msg = ('<table class="nqoutput">');
        $msg .= ('<tr><th>IP Whois Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</th></tr><tr><td>');
        if (!$target = gethostbyname($target)) {
            $msg .= "IP Whois requires an IP address.";
        } else {
            if (! $sock = @fsockopen($whois_server, 43, $errnum, $error, 10)) {
                unset($sock);
                $msg .= "Cannot connect to ".$whois_server." (".$error.")";
            } else {
                fputs($sock, $target."\r\n");
                while (!feof($sock)) {
                    $readbuf .= fgets($sock, 10240);
                }
                @fclose($sock);
            }
            if (eregi("whois.apnic.net", $readbuf))
                $nextServer = "whois.apnic.net";
            else if (eregi("whois.ripe.net", $readbuf))
                $nextServer = "whois.ripe.net";
            else if (eregi("whois.lacnic.net", $readbuf))
                $nextServer = "whois.lacnic.net";
            else if (eregi("whois.registro.br", $readbuf))
                $nextServer = "whois.registro.br";
            else if (eregi("whois.afrinic.net", $readbuf)) {
                $nextServer = "whois.afrinic.net";
            }
            if ($nextServer) {
                $readbuf = "";
                if (! $sock = @fsockopen($nextServer, 43, $errnum, $error, 10)) {
                    unset($sock);
                    $msg .= "Cannot connect to ".$nextServer." (".$error.")";
                } else {
                    fputs($sock, $target.$extra."\r\n");
                    while (!feof($sock)) {
                        $readbuf .= fgets($sock, 10240);
                    }
                    @fclose($sock);
                }
            }
            $readbuf = str_replace(" ", "&nbsp;", $readbuf);
            $msg .= nl2br($readbuf);
        }
        $msg .= '</td></tr></table><hr />';
        $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'lookup')
    {
        $target = $data['host'];
        $msg = ('<table class="nqoutput">');
        $msg .= ('<tr><th>DNS Lookup Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</th></tr><tr><td>');
        $msg .= $target.' resolved to ';
        if (eregi("[a-zA-Z]", $target)) {
          $ipaddr = gethostbyname($target);
          $geoipc = xarModAPIFunc('netquery', 'user', 'getgeoip', (array('ip' => $ipaddr)));
          $msg .= $ipaddr." [".$geoipc['cn']."]";
        } else {
          $geoipc = xarModAPIFunc('netquery', 'user', 'getgeoip', (array('ip' => $target)));
          $ipname = gethostbyaddr($target);
          $msg .= $ipname." [".$geoipc['cn']."]";
        }
        if (!empty($geoipc['geoflag'])) $msg .= " <img class=\"geoflag\" src=\"".$geoipc['geoflag']."\" />";
        $msg .= '</td></tr></table><hr />';
        $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'dig')
    {
        $target = sanitizeSysString($data['host']);
        $digparam = $data['digparam'];
        $digexec_local = $data['digexec_local'];
        $msg = ('<table class="nqoutput">');
        $msg .= ('<tr><th>DNS Query (Dig) Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</th></tr><tr><td>');
        if (eregi("[a-zA-Z]", $target))
          $ntarget = gethostbyname($target);
        else
          $ntarget = gethostbyaddr($target);
        if (! eregi("[a-zA-Z]", $target) && !eregi("[a-zA-Z]", $ntarget)) {
          $msg .= 'DNS query (Dig) requires a hostname.';
        } else {
          if (! eregi("[a-zA-Z]", $target) ) $target = $ntarget;
          if ($data['winsys']) {
              if (@exec("$digexec_local -type=$digparam $target", $output, $ret))
                  while (list($k, $line) = each($output)) {
                    $msg .= $line.'<br />';
                  }
              else
                  $msg .= "The <i>nslookup</i> command is not working on your system.";
          } else {
              if (! $msg .= trim(nl2br(`$digexec_local $digparam '$target'`)))
                  $msg .= "The <i>dig</i> command is not working on your system.";
          }
        }
        $msg .= '</td></tr></table><hr />';
        $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'email')
    {
        $target = $data['email'];
        $msg = ('<table class="nqoutput">');
        $msg .= ('<tr><th>Email Validation Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</th></tr><tr><td>');
        if ((preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $target)) || (preg_match('/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/',$target))) {
          $addmsg = "Format Check: Correct format.";
          $msg .= $addmsg;
          list ($username,$domain) = split ("@",$target,2);
          if (!$data['winsys'] || $data['dns_dig_enabled']) {
            if (checkdnsrr($domain.'.', 'MX') ) $addmsg = "<br />DNS Record Check: MX record returned OK.";
            else if (checkdnsrr($domain.'.', 'A') ) $addmsg = "<br />DNS Record Check: A record returned OK.";
            else if (checkdnsrr($domain.'.', 'CNAME') ) $addmsg = "<br />DNS Record Check: CNAME record returned OK.";
            else $addmsg = "<br />DNS Record Check: DNS record not returned.)";
            $msg .= $addmsg;
            if ($data['query_email_server']) {
              if (getmxrr($domain, $mxhost))  {
                $address = $mxhost[0];
              } else {
                $address = $domain;
              }
              $addmsg = "<br />MX Server Address Check: Address accepted by ".$address;
              if (!$sock = @fsockopen($address, 25, $errnum, $error, 10)) {
                unset($sock);
                $addmsg = "<br />MX Server Address Check: Cannot connect to ".$address." (".$error.")";
              } else {
                if (ereg("^220", $out = fgets($sock, 1024))) {
                  fputs ($sock, "HELO ".$_SERVER['HTTP_HOST']."\r\n");
                  $out = fgets ( $sock, 1024 );
                  fputs ($sock, "MAIL FROM: <{$target}>\r\n");
                  $from = fgets ( $sock, 1024 );
                  fputs ($sock, "RCPT TO: <{$target}>\r\n");
                  $to = fgets ($sock, 1024);
                  fputs ($sock, "QUIT\r\n");
                  fclose($sock);
                  if (!ereg ("^250", $from) || !ereg ( "^250", $to )) {
                    $addmsg = "<br />MX Server Address Check: Address rejected by ".$address;
                  }
                } else {
                  $addmsg = "<br />MX Server Address Check: No response from ".$address;
                }
              }
              $msg .= $addmsg;
            }
          }
        } else {
          $addmsg = "Format check: Incorrect format.";
          $msg .= $addmsg;
        }
        $msg .= '</td></tr></table><hr />';
        $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'port')
    {
        $target = $data['server'];
        $tport = $data['portnum'];
        $submitlink = $data['submitlink'];
        $portdata = xarModAPIFunc('netquery', 'user', 'getportdata', array('port' => $tport));
        $msg = ('<table class="nqoutput">');
        $msg .= ('<tr><th colspan="3">Port '.$tport.' Services &amp; Exploits [<a href="javascript:NQpopup(\'http://isc.sans.org/port_details.php?port='.$tport.'\');">Details</a>]');
        if ($data['user_submissions']) $msg .= (' [<a href="'.$submitlink['url'].'">'.$submitlink['label'].'</a>]');
        $msg .= (' [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:<br />');
        if (!empty($target) && $target != 'None') {
            if (! $sock = @fsockopen($target, $tport, $errnum, $error, 10)) {
                $msg .= 'Port '.$tport.' does not appear to be open.';
            } else {
                $msg .= 'Port '.$tport.' is open and accepting connections.';
                @fclose($sock);
            }
        } else {
            $msg .= "No host specified for port check.";
        }
        $msg .= '</th></tr>';
        $msg .= '<tr><th>Protocol</th><th>Service/Exploit</th><th>Notes (Click to Search)</th></tr>';
        foreach($portdata as $portdatum)
        {
          if (!empty($portdatum['protocol'])) {
            $flagdata = xarModAPIFunc('netquery', 'user', 'getflagdata', array('flagnum' => $portdatum['flag']));
            $notes = '<span class="nq-'.$flagdata['fontclr'].'">['.$flagdata['keyword'].']</span> <a href="javascript:NQpopup(\''.$flagdata['lookup_1'].$portdatum['comment'].'\');">'.$portdatum['comment'].'</a>';
            $msg .= '<tr><td>'.$portdatum['protocol'].'</td><td>'.$portdatum['service'].'</td><td>'.$notes.'</td></tr>';
          }
        }
        $msg .= '</table><hr />';
        $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'http')
    {
        $readbuf = '';
        $url_Complete = parse_url($data['httpurl']);
        $url_Scheme   = (!empty ($url_Complete["scheme"])) ? $url_Complete["scheme"] : "http";
        $url_Host     = (!empty ($url_Complete["host"])) ? $url_Complete["host"] : "localhost";
        $url_Port     = (!empty ($url_Complete["port"])) ? $url_Complete["port"] : "80";
        $url_User     = (!empty ($url_Complete["user"])) ? $url_Complete["user"] : "";
        $url_Pass     = (!empty ($url_Complete["pass"])) ? $url_Complete["pass"] : "";
        $url_Path     = (!empty ($url_Complete["path"])) ? $url_Complete["path"] : "/";
        $url_Query    = (!empty ($url_Complete["query"])) ? ":".$url_Complete["query"] : "";
        $url_Fragment = (!empty ($url_Complete["fragment"])) ? $url_Complete["fragment"] : "";
        $url_HostPort = ($url_Port != 80) ? $url_Host.":".$url_Port : $url_Host;
        $url_Long     = $url_Scheme . "://" . $url_Host;
        $url_Req      = $url_Path . $url_Query;
        $fp_Send      = $data['httpreq'] . " $url_Req HTTP/1.0\n";
        $fp_Send     .= "Host: $url_Host\n";
        $fp_Send     .= "User-Agent: Netquery/1.2 PHP/" . phpversion() . "\n";
        $target = $url_Host;
        $msg = ('<table class="nqoutput">');
        $msg .= ('<tr><th>HTTP Request Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</th></tr><tr><td><pre>');
        if (! $sock = @fsockopen($url_Host, $url_Port, $errnum, $error, 10)) {
            unset($sock);
            $msg .= "Cannot connect to host: ".$url_Host." port: ".$url_Port." (".$error.")";
        } else {
            fputs($sock, "$fp_Send\n");
            while (!feof($sock)) {
                $readbuf .= fgets($sock, 10240);
            }
            @fclose($sock);
            $msg .= htmlspecialchars($readbuf);
        }
        $msg .= '</pre></td></tr></table><hr />';
        $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'ping')
    {
        $png = '';
        $target = sanitizeSysString($data['host']);
        $tpoints = $data['maxp'];
        $msg = ('<table class="nqoutput">');
        $msg .= ('<tr><th>ICMP Ping Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</th></tr><tr><td>');
        if ($data['winsys']) {$PN=$data['pingexec_local'].' -n '.$tpoints.' '.$target;}
        else {$PN=$data['pingexec_local'].' -c '.$tpoints.' '.$target;}
        exec($PN, $response, $rval);
        for ($i = 0; $i < count($response); $i++) {
            $png .= $response[$i].'<br />';
        }
        if (! $msg .= trim(nl2br($png))) {
            $msg .= 'Ping failed. You may need to configure your server permissions.';
        }
        $msg .= '</td></tr></table><hr />';
        $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'pingrem')
    {
        $target = $data['host'];
    }
    else if ($data['querytype'] == 'trace')
    {
        $rt = '';
        $target = sanitizeSysString($data['host']);
        $tpoints = $data['maxt'];
        $msg = ('<table class="nqoutput">');
        $msg .= ('<tr><th>Traceroute Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</th></tr><tr><td>');
        if ($data['winsys']) {$TR=$data['traceexec_local'].' -h '.$tpoints.' '.$target;}
        else {$TR=$data['traceexec_local'].' -m '.$tpoints.' '.$target;}
        exec($TR, $response, $rval);
        for ($i = 0; $i < count($response); $i++) {
            $rt .= $response[$i].'<br />';
        }
        if (! $msg .= trim(nl2br($rt))) {
            $msg .= 'Traceroute failed. You may need to configure your server permissions.';
        }
        $msg .= '</td></tr></table><hr />';
        $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'tracerem')
    {
        $target = $data['host'];
    }
    else if ($data['querytype'] == 'lgquery')
    {
        $target = $data['router'];
        $lgdefault  = $data['lgdefault'];
        $lgrequests = $data['lgrequests'];
        $lgreq      = $data['request'];
        $lgparam    = $data['lgparam'];
        $lgrequest  = $lgrequests[$lgreq];
        $lgrouter   = xarModAPIFunc('netquery', 'user', 'getlgrouter', array('router' => $data['router']));
        $readbuf = '';
        $lgaddress  = $lgrouter['address'];
        $lgport     = ($lgrouter[$lgrequest['handler'] . '_port'] > 0) ? $lgrouter[$lgrequest['handler'] . '_port'] : $lgdefault[$lgrequest['handler'] . '_port'];
        $lgcommand  = $lgrequest['command'] . (!empty ($lgparam) ? (" " . htmlentities(substr($lgparam,0,50))) : "");
        $lghandler  = (($lgrouter[$lgrequest['handler']]) && ($lgdefault[$lgrequest['handler']]));
        $lgargc     = ((($lgrouter['use_argc']) && ($lgdefault['use_argc'])) || (!$lgrequest['argc']));
        $lgusername = (!empty ($lgrouter['username'])) ? $lgrouter['username'] : $lgdefault['username'];
        if (!empty ($lgrouter[$lgrequest['handler'] . '_password']))
        {
            $lgpassword = $lgrouter[$lgrequest['handler'] . '_password'];
        }
        else if (!empty ($lgdefault[$lgrequest['handler'] . '_password']))
        {
            $lgpassword = $lgdefault[$lgrequest['handler'] . '_password'];
        }
        else if (!empty ($lgrouter['password']))
        {
            $lgpassword = $lgrouter['password'];
        }
        else
        {
            $lgpassword = $lgdefault['password'];
        }
        $msg = ('<table class="nqoutput">');
        $msg .= ('<tr><th>Looking Glass Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</th></tr><tr><td>');
        if (!$lghandler)
        {
            $msg .= 'This '.$lgrequest['request'].' request is not permitted for '.$lgrouter['router'].':'.$lgport.' by administrator.';
        }
        else if (!$lgargc)
        {
            $msg .= 'Full table view is not permitted on this router.';
        }
        else if (!$sock = @fsockopen($lgaddress, $lgport, $errnum, $error, 10))
        {
            unset($sock);
            $msg .= "Cannot connect to router ".$lgaddress.":".$lgport." (".$error.")";
        }
        else
        {
            socket_set_timeout ($sock, 5);
            if (!empty ($lgusername)) fputs ($sock, "{$lgusername}\n");
            if (!empty ($lgpassword))
                fputs ($sock, "{$lgpassword}\nterminal length 0\n{$lgcommand}\n");
            else
                fputs ($sock, "terminal length 0\n{$lgcommand}\n");
            if (empty ($lgparam) && $lgargc > 0) sleep (2);
            fputs ($sock, "quit\n");
            while (!feof ($sock)) {
                $readbuf .= fgets ($sock, 256);
            }
            $start = strpos ($readbuf, $lgcommand);
            $len = strpos ($readbuf, "quit") - $start;
            while ($readbuf[$start + $len] != "\n") {
                $len--;
            }
            $msg .= nl2br(substr($readbuf, $start, $len));
            @fclose ($sock);
        }
        $msg .= '</td></tr></table><hr />';
        $data['results'] .= $msg;
    }
    else if ($data['querytype'] == 'countries')
    {
        $target = "Top Countries";
        $countries = $data['countries'];
        $msg = ('<table class="nqoutput">');
        $msg .= ('<tr><th colspan="6">Top Countries Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</th</tr>');
        $msg .= "<tr><th>Code</th><th>Country</th><th>Flag</th><th>Latitude</th><th>Longitude</th><th>Users</th></tr>\n";
        foreach ($countries as $country) {
          if (!empty ($country['cn'])) {
            $msg .= "<tr><td>".$country['cc']."</td><td>\n";
            if ($data['mapping_site'] == 1)
              $msg .= "<a href=\"javascript:NQpopup('http://www.mapquest.com/maps/map.adp?latlongtype=decimal&amp;latitude=".$country['lat']."&amp;longitude=".$country['lon']."&amp;zoom=0');\">".$country['cn']."</a>\n";
            else if ($data['mapping_site'] == 2)
              $msg .= "<a href=\"javascript:NQpopup('http://www.multimap.com/map/browse.cgi?lat=".$country['lat']."&amp;lon=".$country['lon']."&amp;scale=40000000&amp;icon=x');\">".$country['cn']."</a>\n";
            else
              $msg .= $country['cn']."\n";
            $msg .= "</td><td>\n";
            $msg .= "<img class=\"geoflag\" src=\"".$country['geoflag']."\" alt=\"\" />\n";
            $msg .= "</td><td>";
            $msg .= $country['lat']."\n";
            $msg .= "</td><td>";
            $msg .= $country['lon']."\n";
            $msg .= "</td><td>";
            $msg .= $country['users']."\n";
            $msg .= "</td></tr>";
          }
        }
        $msg .= "</table><hr />\n";
        $data['results'] .= $msg;
    }
    else
    {
        $target = "";
    }
    if ($data['querytype'] != 'none' && $data['capture_log_enabled'] && $data['capture_log_filepath'])
    {
        $geoip = $data['geoip'];
        $datetime = date($data['capture_log_dtformat']);
        $fp = @fopen($data['capture_log_filepath'], 'a');
        if ($fp) {
            $string = $datetime." - User: ".$data['browserinfo']->property('ip')." [";
            if (!empty($geoip['cn'])) $string .= $geoip['cn'].", ";
            $string .= $data['browserinfo']->property('platform')." ".$data['browserinfo']->property('os').", ";
            $string .= $data['browserinfo']->property('browser')." ".$data['browserinfo']->property('version')."]";
            $string .= " - Target: ".$target." \n";
            $write = fputs($fp, $string);
            @fclose($fp);
        }
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $GeoccTable = $xartable['netquery_geocc'];
        $query = "UPDATE $GeoccTable SET users = users + 1 WHERE cc = ?";
        $bindvars = array($geoip['cc']);
        $result =& $dbconn->Execute($query,$bindvars);
    }
    $data['timer']->stop('main');
    return $data;
}
?>