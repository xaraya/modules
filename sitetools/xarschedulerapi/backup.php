<?php
/*
 * File: $Id:
 *
 * Backup the database using scheduler module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteTools module
 * @author jojodee <jojodee@xaraya.com>
*/
/**
 * take a backup of the database(s) (executed by the scheduler module)
 * 
 * @author jojodee <http://xaraya.athomeandabout.com >
 * @access private
 */
function sitetools_schedulerapi_backup($args)
{
    extract ($args);

    if (!isset($dbname) || ($dbname='') || (empty($dbname))){
        list($dbconn) = xarDBGetConn();
            $dbname= xarDBGetName();
            $dbtype= xarDBGetType();
    }
    $SelectedTables=''; //Todo: setup a default array of selected tables for partial backups

    $startbackup=xarModGetVar('sitetools','defaultbktype');

    if ((!isset($startbackup)) || (empty($startbackup))) {
      $startbackup='complete';
    }

    if ((!isset($usegz)) && (bool)(function_exists('gzopen'))) {
         $usegz =true;
    } else {
        $usegz = false;
    }

    $screen=0; //TODO: Fix this when configurable in main backup util
    $data=array();
    $data= xarModAPIFunc('sitetools','admin','backupdb',
                               array ('usegz'          => $usegz,
                                      'startbackup'    => $startbackup,
                                      'screen'         => $screen,
                                      'SelectedTables' => $SelectedTables,
                                      'dbname'         => $dbname,
                                      'dbtype'         => $dbtype));


 return true;
}

?>
