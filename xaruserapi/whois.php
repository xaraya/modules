<?php
function netquery_userapi_whois($args)
{
    extract($args);
    if ((!isset($target)) || (!isset($whois_server)) || (!isset($whois_unfound)))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $msg = '';
    $readbuf = '';
    $nextServer = '';
    if (! $sock = @fsockopen($whois_server, 43, $errnum, $error, 10))
    {
        unset($sock);
        $msg .= "Cannot connect to ".$whois_server." (".$error.")";
    }
    else
    {
        fputs($sock, $target."\r\n");
        while (!feof($sock))
        {
            $readbuf .= fgets($sock, 10240);
        }
        @fclose($sock);
    }
    if (! preg_match("/Whois Server:/i", $readbuf))
    {
        $pattern = "/$whois_unfound/i";
        if (! empty($whois_unfound) && preg_match($pattern, $readbuf))
        {
            $msg .= "<span class=\"nq-red\">NOT FOUND</span>: No match for $target<br />";
        }
    }
    else
    {
        $readbuf = explode("\n", $readbuf);
        for ($i=0; $i<sizeof($readbuf); $i++)
        {
            if (preg_match("/Whois Server:/i", $readbuf[$i])) $readbuf = $readbuf[$i];
        }
        $nextServer = substr($readbuf, 17, (strlen($readbuf)-17));
        $nextServer = str_replace("1:Whois Server:", "", trim(rtrim($nextServer)));
        $readbuf = "";
        if (! $sock = @fsockopen($nextServer, 43, $errnum, $error, 10))
        {
            unset($sock);
            $msg .= "Cannot connect to ".$nextServer." (".$error.")";
        }
        else
        {
            fputs($sock, $target."\r\n");
            while (!feof($sock))
            {
                $readbuf .= fgets($sock, 10240);
            }
            @fclose($sock);
        }
    }
    $msg .= nl2br($readbuf);
    return $msg;
}
?>
