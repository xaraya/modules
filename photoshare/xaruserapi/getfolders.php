<?php
/**
 * Photoshare by Chris van de Steeg
 * based on Jorn Lind-Nielsen 's photoshare
 * module for PostNuke
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Chris van de Steeg
 */

function photoshare_userapi_getfolders($args)
{
	extract($args);

	if (isset($objectid))
		$folderID = $objectid;
	if (isset($fid) && !isset($folderID))
		$folderID = $fid;

	if (!isset($getForList)) {
		$getForList = false;
	}

	if (!isset($countSubFolders))
		$countSubFolders = false;

	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	
	$whereSQL = ($getForList ? "NOT f.ps_blockfromlist " : "");
	//TODO : topics
	//$topicRestriction = (isset($topic) ? "ps_topic = " . xarVarPrepForStore($topic) : '');
	
	$topicsJoin = "";
	
	// Get order by clause
	$orderBySQL = "ORDER BY f.ps_title";
	if (isset($order)) {
		if ($order == 'date')
			$orderBySQL = " ORDER BY f.ps_createddate DESC";
		else if ($order == 'title')
    			$orderBySQL = " ORDER BY f.ps_title";
		else if ($order == 'owner')
			$orderBySQL = " ORDER BY u.xar_uname";
		///TODO: Topics
		//else if ($order == 'topic') {
		//	$orderBySQL = " ORDER BY ps_topictext";
		//	$topicsJoin = "LEFT JOIN $xartables[topicsTable] ON s_tid = topic]";
		//}
	}
	// Open up for use of view key
	if (isset($viewKey)) {
		if (strlen($whereSQL) > 0)
			$whereSQL .= " AND ";
		$whereSQL .= "f.ps_viewkey = '" . xarVarPrepForStore($viewKey) ."'";
	}

	if (isset($folderID)){
		if (strlen($whereSQL) > 0)
			$whereSQL .= " AND ";
		$whereSQL .= "f.ps_id = " . xarVarPrepForStore($folderID);
	}
	
	if (isset($ownerID)){
		if (strlen($whereSQL) > 0)
			$whereSQL .= " AND ";
		$whereSQL .= "f.ps_owner = " . xarVarPrepForStore($ownerID);
	}
	
	if (isset($parentFolderID)) {
		if (strlen($whereSQL) > 0)
			$whereSQL .= " AND ";
		$whereSQL .= "f.ps_parentfolder = " . xarVarPrepForStore($parentFolderID);
	}

	//TODO: Topics
	//if ($topicRestriction)
	//  $whereSQL .= " AND $topicRestriction";
	//}
	
	$sql = 'SELECT 	f.ps_id,
					f.ps_title,
					f.ps_owner,
					f.ps_parentfolder,
					UNIX_TIMESTAMP(f.ps_createddate),
					IF(LENGTH(u.xar_name) <> 0, u.xar_name, u.xar_uname),
				   	UNIX_TIMESTAMP(f.ps_modifieddate),
					f.ps_description,
					f.ps_template,
					f.ps_hideframe,
					f.ps_blockfromlist,
					f.ps_viewKey,
					f.ps_mainImage,
					COUNT(i.ps_id)';

	if ($countSubFolders) {
		$sql .= ", count(subFolders.ps_id)";
	}
	
	$sql.=  ' 	FROM '.$xartables['photoshare_folders'].' f
				LEFT JOIN '.$xartables['photoshare_images'].' i ON (f.ps_id=i.ps_parentfolder)
				INNER JOIN '.$xartables['roles'].' u ON (f.ps_owner = u.xar_uid)';

	if ($countSubFolders) {
		$sql .= 'LEFT JOIN '.$xartables['photoshare_folders'].' as subFolders ON (subFolders.ps_parentfolder = f.ps_id)';
	}

	if (strlen($whereSQL) > 0)
		$whereSQL = 'WHERE '.$whereSQL;
	$sql .= ' '.$whereSQL.' GROUP BY f.ps_id '.$orderBySQL;
	$result =& $dbconn->execute($sql);

    if (!$result) return;

    if (isset($folderID) && $result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exists');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }

    $folders = array();
	for (; !$result->EOF; $result->MoveNext()) {
		if ($countSubFolders)
			list(	$id ,$title, $owner, $parentfolder, $creadedDate, $ownername, $modifiedDate,
				$description, $template, $hideframe, $blockfromlist, $viewkey, $mainImage, $imageCount, $subFolderCount) = $result->fields;
		else
			list(	$id ,$title, $owner, $parentfolder, $creadedDate, $ownername, $modifiedDate,
				$description, $template, $hideframe, $blockfromlist, $viewkey, $mainImage, $imageCount) = $result->fields;

		if (!$getForList || xarSecurityCheck('ReadFolder', 0, 'Folder', "$id:$owner:$parentfolder")) {
			$newfolder = array(	'id'			=> $id,
								'title'			=> $title,
								'owner'			=> $owner,
								'parentFolder'	=> $parentfolder,
								'parentfolder'	=> $parentfolder,
								'createdDate'	=> $creadedDate,
								'ownername'		=> $ownername,
								'modifiedDate'	=> $modifiedDate,
								'description'	=> $description,
								'template' 		=> $template,
								'hideframe'		=> $hideframe,
								'blockfromlist'	=> $blockfromlist,
								'viewkey' 		=> $viewkey,
								'mainImage'		=> $mainImage,
								'imageCount'	=> $imageCount
							);
			if ($countSubFolders)
				$newfolder['subFolderCount'] = $subFolderCount;

			$folders[] = $newfolder;
		}
	}

	$result->Close();

	if (isset($prepareForDisplay) && $prepareForDisplay) {
		for ($i=0;$i<count($folders);$i++) {
			// Clean up the item text before display
			$folders[$i]['title'] = xarVarPrepForDisplay($folders[$i]['title']);
			$folders[$i]['description'] = xarVarPrepForDisplay($folders[$i]['description']);
			$folders[$i]['ownername'] = xarVarPrepForDisplay($folders[$i]['ownername']);
			$folders[$i]['createdDate'] = xarLocaleFormatDate(null, $folders[$i]['createdDate']);
		}
	}

	if (isset($folderID))
		return $folders[0];
	else
		return $folders;

}

?>
