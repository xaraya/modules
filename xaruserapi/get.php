<?php
/**
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

// This should be the only place where we include the class file
include_once("modules/bkview/xarincludes/scmrepo.class.php");

/**
 * get a specific item
 *
 * @param int $args[repoid] id of bkview item to get
 * @return array item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bkview_userapi_get($args)
{
    if (!xarSecurityCheck('ViewAllRepositories')) return;

    extract($args);
    if (!isset($repoid) || !is_numeric($repoid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item ID', 'user', 'get', 'Bkview');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return;
    }
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $bkviewtable = $xartable['bkview'];
    $sql = "SELECT xar_repoid,
                   xar_name,
                   xar_path,
                   xar_repotype,
                   xar_lod
            FROM $bkviewtable
            WHERE xar_repoid = ?";
    $result = $dbconn->Execute($sql,array($repoid));

    if(!$result) return;

    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist:').$sql;
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // Obtain the item information from the result set
    list($repoid, $reponame, $repopath, $repotype, $repobranch) = $result->fields;
    $result->Close();

    // Create the item array
    $args = array('repopath' => $repopath, 'repobranch' => $repobranch);
    $repo = scmRepo::construct(scmRepo::map($repotype),$args);
    if(!$repo) $repopath = xarML("[INVALID]") . $repopath;
    $item = array('repoid'     => $repoid,
                  'reponame'   => $reponame,
                  'repopath'   => $repopath,
                  'repotype'   => $repotype,
                  'repobranch' => $repobranch,
                  'repo' => $repo);
    // Return the item 
    return $item;
}
?>