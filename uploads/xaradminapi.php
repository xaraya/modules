<?php

// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Marie Altobelli (Ladyofdragons)
// Purpose of file: uploads admin API
// ----------------------------------------------------------------------

function uploads_adminapi_download( $args )
{
    //security check    
    if (!xarSecurityCheck('ReadUploads')) return;
    
    extract($args);

    // Lookup download
    $info = xarModAPIFunc('uploads',
                          'user',
                          'get',
                          array(  'ulid'=>$ulid
						        , 'ulname'=>$ulname)
						 );
						 
						 
    // Check if download is not approved for viewing
    if( $info['ulhash'] == '' )
    {
        // Check if download exists
        if( $info['ulfile'] != '' )
        {
            $fname="NotApproved.gif";
            $file="modules/uploads/xarimages/NotApproved.gif";
        } else {
            $fname="NotFound.gif";
            $file="modules/uploads/xarimages/NotFound.gif";
        }
    } else {
        $fname=$info['ulfile'];
        $uploads_directory = xarModGetVar('uploads', 'uploads_directory');
        $file=trim($uploads_directory).trim($info['ulhash']);
        
        if( !file_exists( $file ) )
        {
	        $file=trim($info['ulhash']);
		}
        
        if( !file_exists( $file ) )
        {
            $fname="NotFound.gif";
            $file="modules/uploads/xarimages/NotFound.gif";
        } else {
			if ( 
					(isset($thumbwidth) && ($thumbwidth>0)) 
				||	(isset($thumbheight) && ($thumbheight>0)) 
				||	(isset($thumb) && ($thumb>0)) 
				)
			{
				include "thumb.php";
				
				if( $thumb > 0 )
				{
					list($width, $height, $type, $attr) = GetImageSize($file);
					$total = $width+$height;
					$perc = $thumb/$total;
					$thumbwidth = floor($width * $perc);
					$thumbheight = floor($height * $perc);
        }
				
				if( !$thumbwidth ) { $thumbwidth = 0; }
				if( !$thumbheight ) { $thumbheight = 0; }

				$newfile=$file.'_'.$thumbwidth.'_'.$thumbheight;

				if( !file_exists( $newfile ) )
				{
					// echo "Need to convert<br>";
					// echo "Loaded image, converting<br>";
					$args = array( 'file' => $file
					              ,'thumbwidth' => $thumbwidth
								  ,'thumbheight' => $thumbheight
								  ,'newfile' => $newfile );
								  
				    xarModAPIFunc('uploads','user','createthumbimg',$args);
    }
				// Thumbnail already exists, or has just been created.  Set $file to thumbnail
				$file = $newfile;
				
			}
		}
    }
	
	
    $size=filesize($file);
    ob_end_clean();
//     header("Cache-Control: no-cache, must-revalidate");
//     header("Pragma: no-cache");
    header("Pragma: ");
    header("Cache-Control: ");

    if( function_exists("getimagesize") )
    {
        $imageInfo = getImageSize( $file );
        if( $imageInfo )
        {
            header( "Content-type: ".$imageInfo['mime'] );
        } else {
            header("Content-type: application/octet-stream");
        }
    } else {
        header("Content-type: application/octet-stream");
    }

    header("Content-disposition: attachment; filename=\"".basename($fname)."\"");
    header("Content-length: $size");
    $fp = fopen($file,"rb");
    if( is_resource($fp) )
    {
        while( !feof($fp) )
        {
            echo fread($fp, 1024);
        }
    }
    fclose($fp);
    exit();
}





function uploads_adminapi_getuploads($args)
{
    //security check    
    if (!xarSecurityCheck('EditUploads')) return;
    
    extract($args);
        
        if ($filter == 'waiting') {
            $whereclause = 'WHERE xar_ulapp = 0';
        } else {
            $whereclause = '';
        }
        
        // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
        
        // table and column definitions
    $uploadstable = $xartable['uploads'];
        
        // Get items
    $sql = "SELECT xar_ulid,
                                       xar_ulmod,
                                     xar_ulmodid,
                                     xar_uluid,
                                       xar_ulfile,
                                     xar_ulhash,
                   xar_ulapp
            FROM $uploadstable
            $whereclause;";
    $result = $dbconn->Execute($sql);
        
        // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
        if ($result->EOF) {
           return array();
        }
        
    for (; !$result->EOF; $result->MoveNext()) {
            list($ulid, $ulmod, $ulmodid, $uluid, $ulfile, $ulhash, $ulapp) = $result->fields;
        if (xarSecurityCheck('EditUploads')) {
            $items[] = array('ulid' => $ulid,
                             'ulmod' => $ulmod,
                             'ulmodid' => $ulmodid,
                             'uluid' => xarUserGetVar('name',$uluid),
                             'ulfile' => $ulfile,
                             'ulhash' => $ulhash,
                             'ulapp' => $ulapp);
        }
        }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
        
        return $items;
}
function uploads_adminapi_getuploadinfo($args)
{
    //security check    
    if (!xarSecurityCheck('EditUploads')) return;
    extract($args);
        
    if (!isset($ulid) || !is_numeric($ulid)) 
    {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'upload ID', 'admin', 'getuploadinfo', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
        
        // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
        
        // table and column definitions
    $uploadstable = $xartable['uploads'];
        
        // Get items
    $sql = "SELECT xar_ulid,
                                       xar_ulmod,
                                     xar_ulmodid,
                                     xar_uluid,
                                       xar_ulfile,
                                     xar_ulhash,
                   xar_ulapp
            FROM $uploadstable
            WHERE xar_ulid = $ulid;";
    $result = $dbconn->Execute($sql);
        
        // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    for (; !$result->EOF; $result->MoveNext()) 
    {
        list($ulid, $ulmod, $ulmodid, $uluid, $ulfile, $ulhash, $ulapp) = $result->fields;
        if (xarSecurityCheck('EditUploads')) {
            $items[] = array('ulid' => $ulid,
                             'ulmod' => $ulmod,
                             'ulmodid' => $ulmodid,
                             'uluid' => xarUserGetVar('name',$uluid),
                             'ulfile' => $ulfile,
                             'ulhash' => $ulhash,
                             'ulapp' => $ulapp);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
        
    return $items;
}
function uploads_adminapi_approveupload($args)
{
    //security check    
    if (!xarSecurityCheck('EditUploads')) return;
    extract($args);
        
    if (!isset($ulid) || !is_numeric($ulid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'upload ID', 'admin', 'approveupload', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    
    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
        
    // table and column definitions
    $uploadstable = $xartable['uploads'];
        
        
        
        // Get items
    $sql = "UPDATE $uploadstable

                    SET xar_ulapp = 1
            WHERE xar_ulid = $ulid;";
    $result = $dbconn->Execute($sql);
        
        // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
        
        return True;
}
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function uploads_adminapi_getmenulinks()
{
    if (xarSecurityCheck('EditUploads')) {
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                   'admin',
                                                   'main'),
                              'title' => xarML('Uploads Module Overview'),
                              'label' => xarML('Overview'));
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                   'admin',
                                                   "view"),
                              'title' => xarML('View All Uploads'),
                              'label' => xarML('View Uploads'));
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                   'user',
                                                   'uploadform'),
                              'title' => xarML('Upload a File'),
                              'label' => xarML('Upload File'));
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                   'admin',
                                                   'fileimport'),
                              'title' => xarML('Import Files'),
                              'label' => xarML('Import Files'));
    }
    if (xarSecurityCheck('AdminUploads')) {
        $menulinks[] = Array('url'   => xarModURL('uploads',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Edit the Uploads Configuration'),
                              'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
function uploads_adminapi_reject($args)
{
    //security check    
    if (!xarSecurityCheck('EditUploads')) return;
    extract($args);
        
    if (!isset($ulid) || !is_numeric($ulid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'upload ID', 'admin', 'approveupload', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $info = xarModAPIFunc('uploads',
                          'admin',
                          'getuploadinfo',
                          array('ulid'=>$ulid));
    $info = $info[0];

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
        
    // table and column definitions
    $uploadstable = $xartable['uploads'];
        
    // Remove item
    $sql = "DELETE FROM $uploadstable
            WHERE xar_ulid = $ulid;";
    $result = $dbconn->Execute($sql);
        
    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
	// Remove File
	$uploads_directory = xarModGetVar('uploads', 'uploads_directory');
	$fulloldfile = $uploads_directory.$info['ulhash'];
	@unlink( $fulloldfile );
    
    return True;
}


function uploads_adminapi_newhook( $args )
{
    extract($args);
    // TODO: update the upload's module-ID to correspond to the article's ID
    return $extrainfo;    
}

function uploads_adminapi_createhook( $args )
{
    extract($args);
    // TODO: update the upload's module-ID to correspond to the article's ID
    return $extrainfo;    
}

?>
