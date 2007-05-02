<?php

/**
 * create an entry for a module item - hook for ('item','create','GUI')
 *
 * @param $args['pingurl'] URL that we are pinging
 * @param $args['permalink'] URL of the site that we are sending
 * @param $args['title'] title of the trackback
 * @param $args['sitename'] Name of site or entry
 * @param $args['excerpt'] Excerpt of entry
 * @returns array
 * @return extrainfo array
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function trackback_adminapi_ping($args)
{
    extract($args);

    if (!isset($pingurl) || !is_string($pingurl)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'ping url', 'admin', 'ping', 'trackback');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (empty($title)){
        $title = xarML('Trackback from ') . xarModGetVar('themes', 'SiteName');
    }

    if (empty($sitename)){
        $sitename = xarModGetVar('themes', 'SiteName');
    }

    $target = parse_url($pingurl);

    // Credit to both Drupal and Jannis Herman for much of below:
    // http://www.drupal.org || http://www.jannis.to/programming/trackback.html
    if (isset($target['query'])){
        if ($target["query"] != "") $target["query"] = "?".$target["query"];
    } else {
        $target['query'] = '';
    }

    switch ($target["scheme"]) {
    case "http":
      $fp = @fsockopen($target["host"], ($target["port"] ? $target["port"] : 80), $errno, $errstr, 15);
      break;
    case "https":
      // Note: only works for PHP 4.3 compiled with openssl
      $fp = @fsockopen("ssl://$target[host]", ($target["port"] ? $target["port"] : 443), $errno, $errstr, 20);
      break;
    default:
        $msg = xarML('Invalid Schema for Ping Url #(1) :  Should be either http:// or ssl://', $target["scheme"]);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if (!is_resource($fp)){
        $msg = xarML('Could not connect to #(1)', $fp);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Variables for the user agent
    $generator = xarConfigGetVar('System.Core.VersionId');
    $generator .= ' :: ';
    $generator .= xarConfigGetVar('System.Core.VersionNum');
    // Put together the things we want to send
    $send = "url=" . rawurlencode($permalink) . "&title=" . rawurlencode($title) .  "&blog_name=" . rawurlencode($sitename) . "&excerpt=" . rawurlencode($excerpt);
    // Send the trackback
    fputs($fp, "POST ".$target["path"].$target["query"]." HTTP/1.1\n");
    fputs($fp, "Host: ".$target["host"]."\n");
    fputs($fp, "user-agent: " . $generator ." (+http://www.xaraya.com/)\n");
    fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
    fputs($fp, "Content-length: ". strlen($send)."\n");
    fputs($fp, "Connection: close\n\n");
    fputs($fp, $send);

    // Featch response
    while (!feof($fp)) {
        $response[] = fgets($fp);
        $status = socket_get_status($fp);
        if ($status["unread_bytes"] = 0) {
            break;
        }
    }
    // TODO -- Parse the result
    // Close
    fclose($fp);     
    return true;
}

?>