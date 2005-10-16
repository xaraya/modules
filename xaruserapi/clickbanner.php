<?php
/**
 * Clickbanner
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarlinkme
 * @link http://xaraya.com/index.php/release/889.html
 * @author jojodee <jojodee@xaraya.com>
 */

function xarlinkme_userapi_clickbanner()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $bid = pnVarCleanFromInput('bid');

    $xarlinkmebannertable = $xartable['banners'];

	$sql = "SELECT $bannerColumn[clickurl]
			FROM $bannerTable
			WHERE $bannerColumn[bid]='".(int)pnVarPrepForStore($bid)."'";

    $bresult =& $dbconn->Execute($sql);
	
    list($clickurl) = $bresult->fields;
    $bresult->Close();

	$sql = "UPDATE $bannerTable
			SET $bannerColumn[clicks]=$bannerColumn[clicks]+1
			WHERE $bannerColumn[bid]='".(int)pnVarPrepForStore($bid)."'";

    $dbconn->Execute($sql);
    Header('HTTP/1.1 301 Moved Permanently'); 
    Header("Location: $clickurl");
}
?>