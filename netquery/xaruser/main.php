<?php
/**
 * File: $Id:
 */
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
        $buffer = '';
        $nextServer = '';
        $target = $data['domain'].$data['whois_ext'];
        $link = xarModAPIFunc('netquery', 'user', 'getlink', array('whois_ext' => $data['whois_ext']));
        $whois_server = $link['whois_server'];
        $msg = ('<p><b>Whois Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote>');
        if (! $sock = fsockopen($whois_server, 43, $num, $error, 10)){
            unset($sock);
            $msg .= "Timed-out connecting to $whois_server (port 43)";
        } else {
            fputs($sock, "$target\n");
            while (!feof($sock))
                $buffer .= fgets($sock, 10240); 
        }
        fclose($sock);
        if (! eregi("Whois Server:", $buffer)) {
            if(eregi("no match", $buffer))
                $msg .= "NOT FOUND: No match for $target<br>";
            else
                $msg .= "Ambiguous query, multiple matches for $target:<br>";
        } else {
            $buffer = split("\n", $buffer);
            for ($i=0; $i<sizeof($buffer); $i++) {
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
        $data['results'] .= $msg . '<hr>';
    }
    else if ($data['querytype'] == 'whoisip')
    {
        $buffer = '';
        $nextServer = '';
        $target = $data['addr'];
        $whois_server = "whois.arin.net";
        $msg = ('<p><b>IP Whois Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote>');
        if (!$target = gethostbyname($target)) {
            $msg .= "IP Whois requires an IP address.";
        } else {
            if (! $sock = fsockopen($whois_server, 43, $num, $error, 20)) {
                unset($sock);
                $msg .= "Timed-out connecting to $whois_server (port 43)";
            } else {
                fputs($sock, "$target\n");
                while (!feof($sock))
                    $buffer .= fgets($sock, 10240);
                fclose($sock);
            }
            if (eregi("RIPE.NET", $buffer))
                $nextServer = "whois.ripe.net";
            else if (eregi("whois.apnic.net", $buffer))
                $nextServer = "whois.apnic.net";
            else if (eregi("nic.ad.jp", $buffer)) {
                $nextServer = "whois.nic.ad.jp";
                #/e suppresses Japanese character output from JPNIC
                $extra = "/e";
            }
            else if (eregi("whois.registro.br", $buffer))
                $nextServer = "whois.registro.br";
            if ($nextServer) {
                $buffer = "";
                if (! $sock = fsockopen($nextServer, 43, $num, $error, 10)) {
                    unset($sock);
                    $msg .= "Timed-out connecting to $nextServer (port 43)";
                } else {
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
        $msg = ('<p><b>Port '.$tport.' Check Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote>');
        if (! $sock = fsockopen($target, $tport, $num, $error, 5))
            $msg .= 'Port '.$tport.' does not appear to be open.';
        else{
            $msg .= 'Port '.$tport.' is open and accepting connections.';
            fclose($sock);
        }
        $msg .= '</blockquote></p>';
        $data['results'] .= $msg . '<hr>';
    }
    else if ($data['querytype'] == 'ping')
    {
        $png = '';
        $target = $data['host'];
        $tpoints = $data['maxp'];
        $pexec = $data['pingexec'];
        $msg = ('<p><b>ICMP Ping Results [<a href="'.$clrlink['url'].'">'.$clrlink['label'].'</a>]:</b><blockquote>');
        if ($pexec['winsys']) {$PN=$pexec['local'].' -n '.$tpoints.' '.$target;}
        else {$PN=$pexec['local'].' -c'.$tpoints.' -w'.$tpoints.' '.$target;}
        exec($PN, $response, $rval);
        for ($i = 0; $i < count($response); $i++) {
            $png .= $response[$i].'<br>';
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
        if ($texec['winsys']) {$TR=$texec['local'].' '.$target;}
        else {$TR=$texec['local'].' '.$target;}
        exec($TR, $response, $rval);
        for ($i = 0; $i < count($response); $i++) {
            $rt .= $response[$i].'<br>';
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
        else if (!$lglink = fsockopen ($lgaddress, $lgport, $errno, $errstr, 5))
        {
            $msg .= 'Error connecting to router. ' .$lgaddress . ':' . $lgport;
        }
        else
        {
            $readbuf = '';
            socket_set_timeout ($lglink, 5);
            if (!empty ($lgusername)) fputs ($lglink, "{$lgusername}\n");
            if (!empty ($lgpassword))
                fputs ($lglink, "{$lgpassword}\nterminal length 0\n{$lgcommand}\n");
            else
                fputs ($lglink, "terminal length 0\n{$lgcommand}\n");
            if (empty ($lgparam) && $lgargc > 0) sleep (2);
            fputs ($lglink, "quit\n");
            while (!feof ($lglink)) $readbuf = $readbuf . fgets ($lglink, 256);
            $start = strpos ($readbuf, $lgcommand);
            $len = strpos ($readbuf, "quit") - $start;
            while ($readbuf[$start + $len] != "\n") $len--;
            $msg .= nl2br(substr($readbuf, $start, $len));
            fclose ($lglink);
        }
        $msg .= '</blockquote></p>';
        $data['results'] .= $msg . '<hr>';
    }
    $logfp = $data['logfile'];
    if ($data['capture_log_enabled'] && $logfp['local'])
    {
        $datetime = date($logfp['remote']);
        $fp = fopen($logfp['local'], 'a');
        if ($fp) {
            $string = $datetime." - User IP: ".$_SERVER[REMOTE_ADDR]." - Target: ".$target." \n";
            $write = fputs($fp, $string);
            fclose($fp);
        }
    }
    return $data;
}
?>
