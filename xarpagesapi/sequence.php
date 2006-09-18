<?php
/**
 * xProject Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
function xproject_pagesapi_sequence($args)
{
    extract($args);

    if(!isset($parentid)) $parentid = 0;

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'projectid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'pages', 'sequence', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $projectinfo = xarModAPIFunc('xproject',
                            'user',
                            'get',
                            array('projectid' => $projectid));

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$projectinfo[project_name]:All:$projectinfo[projectid]")) {
        return;
    }

    $itemlist = xarModAPIFunc('xproject',
                            'pages',
                            'getall',
                            array('projectid' => $projectid,
                                'parentid' => $parentid));

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $pages_table = $xartable['xProject_pages'];

    if(count($itemlist) > 0) {
        $sequence = 1;
        foreach($itemlist as $iteminfo) {

            $query = "UPDATE $pages_table
                      SET sequence = ?
                      WHERE pageid = ?";

            $bindvars = array(
                      $sequence,
                      $iteminfo['pageid']);

            $result = &$dbconn->Execute($query,$bindvars);

            if (!$result) { // return;
                $msg = xarML('SQL: #(1)', $dbconn->ErrorMsg());
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
                return;
            }

            $sequence++;
        }
    }

    return true;
}
?>