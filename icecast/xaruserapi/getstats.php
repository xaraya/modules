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
 * @param string server
 * @param int port
 * @return stats array
 * @raise 
 */
function icecast_userapi_getstats($args)
{ 

    //if (!xarSecurityCheck()) {
    //    return;
    //} 
    extract($args);
    
    if (!isset($host)) {
        $host = xarModGetVar('icecast', 'DefaultServer');
    } 
    
    if (!isset($port)) {
        $port = xarModGetVar('icecast', 'DefaultPort');
    }
    /**
     * Get Stats File
     */
    $rawStats = @file_get_contents("http://$host:$port/icestats.xsl");

    // @todo make this better
    if (empty($rawStats)) {
        // failed file get
        return false;
    }

    /**
     * Split up stats by colon
     * the server stats are in the first element
     * followed by stats for each mount point
     */
    $iceStats = explode(':', $rawStats);
    
    $serverStats = array_shift($iceStats);
    
    $serverStats = explode(',', $serverStats);
     
    $stats['server']['client_connections'] = trim($serverStats[0]);
    $stats['server']['connections'] = trim($serverStats[1]);
    $stats['server']['source_connections'] = trim($serverStats[2]);
    $stats['server']['sources'] = trim($serverStats[3]);
    
    foreach($iceStats as $mount) {
        $mountStats = explode(',',$mount);

        $mountName = trim($mountStats[0]);
        
        $stats[$mountName]['artist'] = trim($mountStats[1]);
        $stats[$mountName]['channels'] = trim($mountStats[2]);
        $stats[$mountName]['listeners'] = trim($mountStats[3]);
        $stats[$mountName]['public'] = trim($mountStats[4]);
        $stats[$mountName]['quality'] = trim($mountStats[5]);
        $stats[$mountName]['samplerate'] = trim($mountStats[6]);
        $stats[$mountName]['title'] = trim($mountStats[7]);
        $stats[$mountName]['type'] = trim($mountStats[8]);
           
    } 

    return $stats;
} 
?>
