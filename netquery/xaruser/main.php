<?php
function netquery_user_main()
{
    $data = xarModAPIFunc('netquery', 'user', 'mainapi');
    $clrlink = $data['clrlink'];
    $data['authid'] = xarSecGenAuthKey();        
    if ($data['querytype'] == 'none')
    {
        return $data;
    }
    else if ($data['querytype'] == 'whois')
    {
        $domain = $data['domain'];
        $whois_ext = $data['whois_ext'];
        $whois_max_limit = $data['whois_max_limit'];
        $j = 1;
        while ($j <= $whois_max_limit && !empty($domain[$j])) {
            $readbuf = '';
            $nextServer = '';
            $target = $domain[$j].$whois_ext[$j];
            $link = xarModAPIFunc('netquery', 'user', 'getlink', array('whois_ext' => $whois_ext[$j]));
            $whois_server = $link['whois_server'];
            $msg = ('<p><b>Whois Results '.$j.' [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote>');
            if (! $sock = @fsockopen($whois_server, 43, $errnum, $error, 10)){
                unset($sock);
                $msg .= "Timed-out connecting to $whois_server (port 43)";
            } else {
                fputs($sock, "$target\n");
                while (!feof($sock)) {
                    $readbuf .= fgets($sock, 10240);
                }
            }
            @fclose($sock);
            if (! eregi("Whois Server:", $readbuf)) {
                if (eregi("No match", $readbuf) || eregi("No entries", $readbuf) || eregi("Not found", $readbuf) || eregi("AVAIL", $readbuf))
                    $msg .= "NOT FOUND: No match for $target<br />";
                else if (! eregi("Timed-out", $msg))
                    $msg .= "Ambiguous query, multiple matches for $target:<br />";
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
                    $msg .= "Timed-out connecting to $nextServer (port 43)";
                } else {
                    fputs($sock, "$target\n");
                    while (!feof($sock)) {
                        $readbuf .= fgets($sock, 10240);
                    }
                    @fclose($sock);
                }
            }
            $msg .= nl2br($readbuf);
            $msg .= "</blockquote></p>";
            $data['results'] .= $msg . '<hr>';
            $j++;
        }
    }
    else if ($data['querytype'] == 'whoisip')
    {
        $readbuf = '';
        $nextServer = '';
        $target = $data['host'];
        $whois_server = "whois.arin.net";
        $msg = ('<p><b>IP Whois Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote>');
        if (!$target = gethostbyname($target)) {
            $msg .= "IP Whois requires an IP address.";
        } else {
            if (! $sock = @fsockopen($whois_server, 43, $errnum, $error, 10)) {
                unset($sock);
                $msg .= "Timed-out connecting to $whois_server (port 43)";
            } else {
                fputs($sock, "$target\n");
                while (!feof($sock)) {
                    $readbuf .= fgets($sock, 10240);
                }
                @fclose($sock);
            }
            if (eregi("RIPE.NET", $readbuf))
                $nextServer = "whois.ripe.net";
            else if (eregi("whois.apnic.net", $readbuf))
                $nextServer = "whois.apnic.net";
            else if (eregi("nic.ad.jp", $readbuf)) {
                $nextServer = "whois.nic.ad.jp";
                #/e suppresses Japanese character output from JPNIC
                $extra = "/e";
            }
            else if (eregi("whois.registro.br", $readbuf))
                $nextServer = "whois.registro.br";
            if ($nextServer) {
                $readbuf = "";
                if (! $sock = @fsockopen($nextServer, 43, $errnum, $error, 10)) {
                    unset($sock);
                    $msg .= "Timed-out connecting to $nextServer (port 43)";
                } else {
                    fputs($sock, "$target$extra\n");
                    while (!feof($sock)) {
                        $readbuf .= fgets($sock, 10240);
                    }
                    @fclose($sock);
                }
            }
            $readbuf = str_replace(" ", "&nbsp;", $readbuf);
            $msg .= nl2br($readbuf);
        }
        $msg .= "</blockquote></p>";
        $data['results'] .= $msg . '<hr>';
    }
    else if ($data['querytype'] == 'lookup')
    {
        $target = $data['host'];
        $msg = ('<p><b>DNS Lookup Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote>');
        $msg .= $target.' resolved to ';
        if (eregi("[a-zA-Z]", $target))
            $msg .= gethostbyname($target);
        else
            $msg .= gethostbyaddr($target);
        $msg .= '</blockquote></p>';
        $data['results'] .= $msg . '<hr>';
    }
    else if ($data['querytype'] == 'dig')
    {
        $target = $data['host'];
        $msg = ('<p><b>DNS Query (Dig) Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote>');
        if (eregi("[a-zA-Z]", $target))
            $ntarget = gethostbyname($target);
        else
            $ntarget = gethostbyaddr($target);
        if (! eregi("[a-zA-Z]", $target) && !eregi("[a-zA-Z]", $ntarget)) {
            $msg .= 'DNS query (Dig) requires a hostname.';
        } else {
            if (! eregi("[a-zA-Z]", $target) ) $target = $ntarget;
            if (! $msg .= trim(nl2br(`dig any '$target'`)))
                $msg .= "The <i>dig</i> command is not working on your system.";
        }
        $msg .= '</blockquote></p>';
        $data['results'] .= $msg . '<hr>';
    }
    else if ($data['querytype'] == 'port')
    {
        $target = $data['server'];
        $tport = $data['portnum'];
        $portdata = xarModAPIFunc('netquery', 'user', 'getportdata', array('port' => $tport));
        $msg = ('<p><b>Port '.$tport.' Services &amp; Exploits [<a href="http://isc.sans.org/port_details.php?port='.$tport.'" target="_blank">Details</a>] [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote>');
        if (!empty($target) && $target != 'None') {
            if (! $sock = @fsockopen($target, $tport, $errnum, $error, 10)) {
                $msg .= 'Port '.$tport.' does not appear to be open.<br />';
            } else {
                $msg .= 'Port '.$tport.' is open and accepting connections.<br />';
                @fclose($sock);
            }
        } else {
            $msg .= "No host specified for port check.";
        }
        $msg .= '<table border=0 cellspacing=0 cellpadding=4>';
        $msg .= '<tr><th align="left">Protocol</th><th align="left">Service/Exploit</th><th align="left">Notes (Click to Search)</th></tr>';
        foreach($portdata as $portdatum)
        {
          if (!empty($portdatum['protocol'])) {
            $flagdata = xarModAPIFunc('netquery', 'user', 'getflagdata', array('flagnum' => $portdatum['flag']));
            $notes = '<font color="'.$flagdata['fontclr'].'">['.$flagdata['keyword'].']</font> <a href="'.$flagdata['lookup_1'].$portdatum['comment'].'" target="_blank">'.$portdatum['comment'].'</a>';
            $msg .= '<tr><td>'.$portdatum['protocol'].'</td><td>'.$portdatum['service'].'</td><td>'.$notes.'</td></tr>';
          }
        }
        $msg .= '</table></blockquote></p>';
        $data['results'] .= $msg . '<hr>';
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
        $msg = ('<p><b>HTTP Request Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote><pre>');
        if (! $sock = @fsockopen($url_Host, $url_Port, $errnum, $error, 10)) {
            unset($sock);
            $msg .= 'Unable to connect to host: '.$url_Host.' port: '.$url_Port.'.';
        } else {
            fputs($sock, "$fp_Send\n");
            while (!feof($sock)) {
                $readbuf .= fgets($sock, 10240);
            }
            @fclose($sock);
            $msg .= htmlspecialchars($readbuf);
        }
        $msg .= '</pre></blockquote></p>';
        $data['results'] .= $msg . '<hr>';
    }
    else if ($data['querytype'] == 'ping')
    {
        $png = '';
        $target = $data['host'];
        $tpoints = $data['maxp'];
        $pexec = $data['pingexec'];
        $msg = ('<p><b>ICMP Ping Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote>');
        if ($pexec['exec_winsys']) {$PN=$pexec['exec_local'].' -n '.$tpoints.' '.$target;}
        else {$PN=$pexec['exec_local'].' -c'.$tpoints.' -w'.$tpoints.' '.$target;}
        exec($PN, $response, $rval);
        for ($i = 0; $i < count($response); $i++) {
            $png .= $response[$i].'<br />';
        }
        if (! $msg .= trim(nl2br($png))) {
            $msg .= 'Ping failed. You may need to configure your server permissions.';
        }
        $msg .= '</blockquote></p>';
        $data['results'] .= $msg . '<hr>';
    }
    else if ($data['querytype'] == 'pingrem')
    {
    }
    else if ($data['querytype'] == 'trace')
    {
        $rt = '';
        $target = $data['host'];
        $texec = $data['traceexec'];
        $msg = ('<p><b>Traceroute Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote>');
        if ($texec['exec_winsys']) {$TR=$texec['exec_local'].' '.$target;}
        else {$TR=$texec['exec_local'].' '.$target;}
        exec($TR, $response, $rval);
        for ($i = 0; $i < count($response); $i++) {
            $rt .= $response[$i].'<br />';
        }
        if (! $msg .= trim(nl2br($rt))) {
            $msg .= 'Traceroute failed. You may need to configure your server permissions.';
        }
        $msg .= '</blockquote></p>';
        $data['results'] .= $msg . '<hr>';
    }
    else if ($data['querytype'] == 'tracerem')
    {
    }
    else if ($data['querytype'] == 'lgquery')
    {
        $lgrequest  = xarModAPIFunc('netquery', 'user', 'getlgrequest', array('request' => $data['request']));
        $lgrouter   = xarModAPIFunc('netquery', 'user', 'getlgrouter', array('router' => $data['router']));
        $lgdefault  = xarModAPIFunc('netquery', 'user', 'getlgrouter', array('router' => 'default'));
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
        $msg = ('<p><b>Looking Glass Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote>');
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
            $msg .= 'Error connecting to router. ' .$lgaddress . ':' . $lgport;
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
        $msg .= '</blockquote></p>';
        $data['results'] .= $msg . '<hr>';
    }
    $logfp = $data['logfile'];
    if ($data['capture_log_enabled'] && $logfp['exec_local'])
    {
        $datetime = date($logfp['exec_remote']);
        $fp = @fopen($logfp['exec_local'], 'a');
        if ($fp) {
            $string = $datetime." - User IP: ".$_SERVER[REMOTE_ADDR]." - Target: ".$target." \n";
            $write = fputs($fp, $string);
            @fclose($fp);
        }
    }
    return $data;
}
?>
