<?php
/**
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 *  
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

include_once("modules/bkview/xarincludes/scmrepo.php");

/**
 * Detect repositories installed on this server.
 *
 * To be able to make registering repositories a bit easier
 * we try to detect installed repositories on this server and
 * allow adding them. 
 * 
 * @access  public
 */
function bkview_admin_detectrepos() 
{
    if (!xarSecurityCheck('AdminAllRepositories')) return;
    
    $data = array();

    // Detect the repositories installed on this server.
    // the key is BitKeeper/etc/SCCS/s.config
    // wherever that exists, there must be a repository
    // On unix servers where locate is installed this is a breeze, but
    // i guess this is more difficult for windows servers or where
    // locate is not installed.
    $out = shell_exec('locate BitKeeper/etc/SCCS/s.config');
    $out = str_replace("\r\n","\n",$out);
    $out = explode("\n", $out);
    // strip the last entry, always empty
    array_pop($out);
    $reporoots = array();
    foreach($out as $fullpath) {
        $reporoots[] = str_replace('BitKeeper/etc/SCCS/s.config','',$fullpath);
    }
    
    // Now we have an array with repository roots, construct this in list of some sort.
    $repositories = array();
    foreach($reporoots as $reporoot) {
        $repo = scmRepo::construct('bk',$reporoot);
        $repositories[$reporoot]['config'] = $repo->_config;
    }
    
    $data['repositories'] = $repositories;
    $data['pageinfo'] = xarML("Detected repositories");
    return $data;
}


?>
