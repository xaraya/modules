<?php
/**
 * File: $Id:
 * 
 * Get "Now Playing"
 * 
 * Get stats per mount point 
 *
 * @copyright (C) 2004 Johnny Robeson
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage icecast
 * @author Johnny Robeson 
 * 
 * @return stats array
 * @raise 
 */
function icecast_userapi_getnowplaying($args)
{ 

    extract($args);
    
    if (!isset($host)) {
        $host = xarModGetVar('icecast', 'DefaultServer');
    }
    
    if (!isset($port)) {
        $port = xarModGetVar('icecast', 'DefaultPort');
    }
    //if (!xarSecurityCheck()) {
    //    return;
    //} 
    
    $iceStats = xarModAPIFunc('icecast', 
                              'user', 
                              'getstats',
                              array('host' => $host, 'port' => $port));
    
    // get rid of server stats
    array_shift($iceStats);
    

    foreach ($iceStats as $mount => $stat) {
        $iceStats[$mount]['listen_url'] = "http://$host:$port$mount.m3u";
    }
    
    return $iceStats;
} 

?>
