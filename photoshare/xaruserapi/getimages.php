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
 
function photoshare_userapi_getimages($args)
{
	extract($args);
    
	if (!isset($folderID) && !isset($imageID)) {
        $msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
            'folderID or imageID', 'userapi', 'getimages', 'Photoshare');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	$imagesTable  = $xartables['photoshare_images'];

	if (isset($imageID))
		$where = " ps_id = ".xarVarPrepForStore($imageID);
	else
		$where = " ps_parentfolder = ".xarVarPrepForStore($folderID);

	$sql = "SELECT ps_id,
	               ps_title,
				   ps_owner,
	               ps_description,
	               ps_position,
				   ps_uploadid,
				   ps_parentfolder,
				   ps_bytesize,
				   IF(LENGTH(u.xar_name) <> 0, u.xar_name, u.xar_uname)
	      FROM     $imagesTable i
		  INNER JOIN $xartables[roles] u ON (i.ps_owner = u.xar_uid)
	      WHERE 	$where
	      ORDER BY ps_position";

	$result =& $dbconn->execute($sql);
    if (!$result) return;

    $images = array();
	for (; !$result->EOF; $result->MoveNext()) {
		list($id ,$title, $owner, $description, $position, $uploadid, $parentfolder, $bytesize, $ownername) = $result->fields;
		$images[] = array(	'id'    => $id,
							'title' => $title,
							'owner' => $owner,
							'description' => $description,
							'position'   => $position,
							'uploadid' => $uploadid,
							'parentfolder' => $parentfolder,
							'bytesize' => $bytesize,
							'ownername' => $ownername
						);
	}
	if (isset($prepareForDisplay) && $prepareForDisplay) {
		$thumbsize = xarModGetVar('photoshare', 'thumbnailsize');
		for ($i=0;$i<count($images);$i++) {
			$images[$i]['title'] = xarVarPrepForDisplay($images[$i]['title']);
			$images[$i]['description'] = xarVarPrepForDisplay($images[$i]['description']);
			$images[$i]['thumburl'] = xarModUrl(	'uploads',
													'admin',
													'download',
													array(	'ulid' => $images[$i]['uploadid'],
															'thumbwidth' => $thumbsize,
															'thumbheight' => $thumbsize));
		}
	}

	//var_dump($folders); exit(0);

	$result->Close();
	if (isset($imageID))
		return $images[0];
	else
		return $images;

}
 
?>
