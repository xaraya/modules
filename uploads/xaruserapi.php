<?php 

// File: $Id
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Marie Altobelli (Ladyofdragons)
// Purpose of file:  uploads user API
// ----------------------------------------------------------------------
function uploads_userapi_upload($args) 
{
  // Get arguments from argument array
  //$uploadfile = form control name
  //$mod = module name
  //$modid = item ID in module
  //$utype = upload type: 'file','text','db'
  //$extensions = overriding extensions given by the module.
  //$uploadOnly = Upload Only = true, otherwise stores and uploads
  extract($args);
  
  if(!isset($uploadOnly) || $uploadOnly!=true)
  {
      $uploadOnly = false;
  }
  
// Security check
    if (!xarSecurityCheck('ReadUploads')) return;
  
      if (!is_array($_FILES[$uploadfile]) || !$_FILES[$uploadfile]['name']) {
        $msg = xarML('no file was uploaded');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, xarML('file error'), new SystemException($msg));
        return;
      }
    
    // Argument check
    if (!isset($mod)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module', 'user', 'upload', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    
    if (!isset($modid) || !is_numeric($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module item ID', 'user', 'upload', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
        
    if (!isset($utype)) {
        //default to file type upload.
        $utype = 'file';
    }
    
    if (!isset($extensions)) {
        //default to file type upload.
        $extensions = xarModGetVar('uploads','allowed_types');
    }
        
     $file = $_FILES[$uploadfile];
    $ulfile = $file['name'];
    
    // Clean up file name (only lowercase letters, numbers and underscores)
    $ulfile = ereg_replace("[^a-z0-9._]", "", str_replace(" ", "_", str_replace("%20", "_", strtolower($ulfile))));
    $filesize = $file['size'];
    $tmp_name = $file['tmp_name'];
    $type = $file['type'];

    // check and make sure the file extension is of the proper type
    $allowed_types = explode(';',$extensions);
    $allowfile = False;

    // Get the file extension - could by multi-part (eg .tar.gz)
    $file_extension = explode(".", $ulfile);
    $file_ext_cnt = count($file_extension);

    // Check for filename of "name.ext"
    if ($file_ext_cnt == 2) {
        foreach($allowed_types AS $typevalue) {
            if ($file_extension[1] == $typevalue) {
                $allowfile = True;
                break;
            }
        } 
    } elseif ($file_ext_cnt > 2) {
        // Check for multi-part extensions
        foreach($allowed_types AS $typevalue) {

            // Check for multi-part allowed extensions
            $allowed_ext = explode(".",$typevalue);
            $allowed_ext_cnt = count($allowed_ext);

            // Ignore allowed types with only one extension
            if ($allowed_ext_cnt < 2) {
                continue;
            } else {
                // Try to match arrays
                $start = $file_ext_cnt - $allowed_ext_cnt;
                for ($idx = 0, $sdx = $start; $idx < $allowed_ext_cnt; $idx++, $sdx++) {
                    if($allowed_ext[$idx] != $file_extension[$sdx]) {
                        $allowfile = False;
                        break;
                    }
                    $allowfile = True;
                }
            }
        } 
    }

    if ($allowfile == False) {
        $msg = xarML('file extension not allowed.  allowed file types are: #(1)',$extensions);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, xarML('Invalid Extension'), new SystemException($msg));
        return;
    }
    
//check to make sure the file size isn't too big.
    $maxsize = xarModGetVar('uploads','maximum_upload_size');
    if ($filesize > $maxsize) {
        $msg = xarML('file is to large.  only files under #(1) bytes allowed, your filesize is #(2).',$maxsize, $filesize);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    
//now check to make sure the image isn't too big.
    if(ereg("image", $file["type"])) {
        $max_width = xarModGetVar('uploads','max_image_width');
            $max_height = xarModGetVar('uploads','max_image_height');
        $image = getimagesize($file["tmp_name"]);
             $imagewidth  = $image[0];
             $imageheight = $image[1];
            
            // test max image size
            if(($max_width || $max_height) && (($imagewidth > $max_width) || ($imageheight > $max_height))) {
                $msg = xarML('Maximum image size exceeded. Image may be no more than #(1) x #(2) pixels',
                                              $max_width, $max_height);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, xarML('Image Size Error'), new SystemException($msg));
        return;
        }
    }
    $savepath = xarModGetVar('uploads','uploads_directory');
      $savefile = $savepath  . $ulfile;
      if (is_uploaded_file($tmp_name)) {
          $aok = move_uploaded_file($tmp_name, $savefile);
          if (!$aok) {
              $msg = xarML('The file did not copy properly.  Please report the problem to the site administrator. ' . "$tmp_name, $savefile");
          xarExceptionSet(XAR_SYSTEM_EXCEPTION, xarML('Error Copying Uploaded File'), new SystemException($msg));
          return;
          }
          
    } else {
      $msg = xarML('Possible file upload attack.  Filename: #(1)',$ulfile);
      xarExceptionSet(XAR_SYSTEM_EXCEPTION, xarML('Possible File Upload Attack'), new SystemException($msg));
      return;
    }

    $data = array('ulfile' => $ulfile
                 ,'utype' => $utype
                 ,'mod' => $mod
                 ,'modid' => $modid
                 ,'filesize' => $filesize
                 ,'type' => $type);
    if( $uploadOnly == false )
    {
        return uploads_userapi_store( $data );
    } else {
        return $data;
    }
}
function uploads_userapi_store( $args ) 
{
	// Place file into uploads DB so it can be managed by Uploads Module
    // args
    // ulfile - Filename of the file
	// filepath (optional) - Full path to file
    // utype
    // mod
    // modid
    // filesize
    // type
	// movefile (optional) - Yes: Moves file to uploads_dir, No: Copies file to uploads_dir
    extract( $args );
    if (!xarSecurityCheck('ReadUploads')) return;

	// Path to uploads directory
    $savepath = xarModGetVar('uploads','uploads_directory');
	
	// Get full path to file
	if( !isset($filepath) || ($filepath=='') )
	{
		// Assume file uploaded with uploads_userapi_upload -- file should now be in uploads_dir w/ original filename
		// Build path to temp file
    $tmp_name = $savepath . $ulfile;
	} else {
		$tmp_name = $filepath;
	}

// Build fairly unique file name
    $jumble = md5(time() . getmypid());
    $salt = substr($jumble,0,12);
    $ulhash = crypt($ulfile, $salt);
    $ulhash = ereg_replace("[^a-z0-9._]", "", str_replace(" ", "_", str_replace("%20", "_", strtolower($ulhash))));
// Note user ID 
    $uid = xarUserGetVar('uid');
// Store file
    if ($utype == 'file') 
	{ 
    //move it to the given directory/rename it
    //and add an entry in the uploads table.
  
      $savefile = $savepath . $ulhash;
		
		if( !isset($movefile) || ($movefile=='') || ($movefile=='No') )
		{
    $aok = rename($tmp_name, $savefile);
			if (!$aok) 
			{
				$msg = xarML('Could not move file into Uploads Directory.  Please report the problem to the site administrator. ' . "$tmp_name, $savefile");
				xarExceptionSet(XAR_SYSTEM_EXCEPTION, xarML('Error Copying Uploaded File'), new SystemException($msg));
				return;
			}
		} else if ( $movefile=='Yes' ) {
			$aok = copy($tmp_name, $savefile);
			if (!$aok) 
			{
				$msg = xarML('Could not copy file into Uploads Directory.  Please report the problem to the site administrator. ' . "$tmp_name, $savefile");
      xarExceptionSet(XAR_SYSTEM_EXCEPTION, xarML('Error Copying Uploaded File'), new SystemException($msg));
      return;
    }
		} else {
			$msg = xarML('Invalid Argument to [$movefile].');
			xarExceptionSet(XAR_SYSTEM_EXCEPTION, xarML('Invalid Argument'), new SystemException($msg));
			return;
		}
      
      //add to uploads table
      // Get database setup
      list($dbconn) = xarDBGetConn();
      $xartable = xarDBGetTables();
          
          
      // table and column definitions
      $uploadstable = $xartable['uploads'];
            $nextUL = $dbconn->GenID($uploadstable);

    // Check to see if user has the right to approve uploads, if so... then just approve this one too
      if( xarSecurityCheck('EditUploads',0))
      {
          $ulapp = 1;
      } else {
          $ulapp = 0;
      }
    
  
      // insert value into table
      $sql = "INSERT INTO $uploadstable
             (xar_ulid, xar_ulmod, xar_ulmodid, xar_uluid, xar_ulfile, xar_ulhash, xar_ulapp, xar_ulbid, xar_ultype) 
        VALUES ($nextUL, '"
              . xarVarPrepForStore($mod) . "', "
              . xarVarPrepForStore($modid) . ", "
              . xarVarPrepForStore($uid) . ",'"
              . xarVarPrepForStore($ulfile) . "', '"
              . xarVarPrepForStore($ulhash). "', "
            . $ulapp.", 0, '" . substr($utype,0,1) . "')" ;
      $result = $dbconn->Execute($sql);
  
      // Check for an error with the database code, and if so set an appropriate
      // error message and return
      if ($dbconn->ErrorNo() != 0) {
          //$msg = xarMLByKey('DATABASE_ERROR', $sql);
                  $msg = $dbconn->ErrorMsg() . " - " . $sql;
          xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
          new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
          return;
      }
      
      $ulid = $dbconn->PO_Insert_ID();
      $uploadlink = xarModURL('uploads',
                       'user',
                               'main',
                               array('ulid' => $ulid));
      return array('ulid'=>$ulid, 'link'=>$uploadlink, 'filesize'=>$filesize, 'filetype'=>$type);
  }
    elseif ($utype == 'text') {
      //open the uploaded file and return the text.
      $handle = fopen($tmp_name,"rb");
        set_magic_quotes_runtime(0); 
        $contents = fread($handle, filesize ($tmp_name));
        set_magic_quotes_runtime(get_magic_quotes_gpc()); 
      fclose($handle);
      return $contents;
    }
    elseif ($utype == 'db') {
      //move the uploaded file into the db
        $handle = fopen($tmp_name,"rb");
        set_magic_quotes_runtime(0); 
        $contents = fread($handle, filesize ($tmp_name));
        set_magic_quotes_runtime(get_magic_quotes_gpc()); 
        fclose($handle);
        
        //add to the db
        //add to uploads table
      // Get database setup
      list($dbconn) = xarDBGetConn();
      $xartable = xarDBGetTables();
          
      // table and column definitions
      $uploadstable = $xartable['uploads'];
            $blobstable = $xartable['blobs'];
            $nextUL = $dbconn->GenID($uploadstable);
            $nextBlob = $dbconn->GenID($blobstable);
   
     //insert file into database
      $sql = "INSERT INTO $blobstable (xar_ulbid, xar_ulid, xar_ulblob)VALUES (" 
                . xarVarPrepForStore($nextBlob)
                . ","
                . xarVarPrepForStore($ulid) 
                . ",'"
              . addslashes(xarVarPrepForStore($contents)) 
                . "')" ;
        
        $result = $dbconn->Execute($sql);
  
      // Check for an error with the database code, and if so set an appropriate
      // error message and return
      if ($dbconn->ErrorNo() != 0) {
          //$msg = xarMLByKey('DATABASE_ERROR', $sql);
                  $msg = $dbconn->ErrorMsg() . " - " . $sql;
          xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
          new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
          return;
      }
    
    
      if( xarSecurityCheck('EditUploads',0))
      {
          $ulapp = 1;
      } else {
          $ulapp = 0;
      }
    
      // insert value into table
      $sql = "INSERT INTO $uploadstable
             (xar_ulid, xar_ulmod, xar_ulmodid, xar_uluid, xar_ulfile, xar_ulhash, xar_ulapp, xar_ulbid, xar_ultype) 
        VALUES ($nextUL, '"
              . xarVarPrepForStore($mod) . "', "
              . xarVarPrepForStore($modid) . ", "
              . xarVarPrepForStore($uid) . ",' "
              . xarVarPrepForStore($ulfile) . "',' "
              . xarVarPrepForStore($ulhash). "', "
            . $ulapp.", " 
            . xarVarPrepForStore($nextBlob) . ", '" 
            . substr($utype,0,1) . "')" ;
      $result = $dbconn->Execute($sql);
  
      // Check for an error with the database code, and if so set an appropriate
      // error message and return
      if ($dbconn->ErrorNo() != 0) {
          //$msg = xarMLByKey('DATABASE_ERROR', $sql);
                  $msg = $dbconn->ErrorMsg() . " - " . $sql;
          xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
          new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
          return;
      }
      
      $ulid = $dbconn->PO_Insert_ID();
        
        
      $uploadlink = xarModURL('uploads',
                               'user',
                               'main',
                               array('ulid' => $ulid));
      return array('ulid'=>$ulid, 'link'=>$uploadlink, 'filesize'=>$filesize, 'filetype'=>$type);
    }
}

function uploads_userapi_uploadmagic($args) 
{
    $fileUpload = uploads_userapi_upload($args);
    
    if( is_array($fileUpload) )
    {
        return '#ulid:' . $fileUpload['ulid'] . '#';

    } else {
        return $fileUpload;
    }
}

function uploads_userapi_hashfilename( $args )
{
    //$ulfile = filename to hash
    extract($args);
    
    // Clean up file name (only lowercase letters, numbers and underscores)
    $ulfile = ereg_replace("[^a-z0-9._]", "", str_replace(" ", "_", str_replace("%20", "_", strtolower($ulfile))));
    
    $jumble = md5(time() . getmypid());
     $salt = substr($jumble,0,12);
    $ulhash = crypt($ulfile, $salt);
    $ulhash = ereg_replace("[^a-z0-9._]", "", str_replace(" ", "_", str_replace("%20", "_", strtolower($ulhash))));
    
    return $ulhash;
}
function uploads_userapi_get($args) 
{
    // Get arguments from argument array
    extract($args);
    

    
    // Argument check
	if( !isset($ulname) || ($ulname == "") )
	{
		
    if (!isset($ulid) || !is_numeric($ulid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'upload ID', 'user', 'get', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
	}

    // Security check before we do all the hard work.
    if (!xarSecurityCheck('ReadUploads')) return;

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
        
        // table and column definitions
    $uploadstable = $xartable['uploads'];
    // Get item
	if( isset($ulname) && ($ulname != "") )
	{
		$sql = "SELECT xar_ulid,
					   xar_ulmod,
					   xar_ulhash,
					   xar_ulapp,
					   xar_ulfile
				FROM $uploadstable
				WHERE xar_ulfile = '" . xarVarPrepForStore($ulname) ."'";
	} else {
    $sql = "SELECT xar_ulid,
                   xar_ulmod,
                   xar_ulhash,
                   xar_ulapp,
                   xar_ulfile
            FROM $uploadstable
            WHERE xar_ulid = " . xarVarPrepForStore($ulid);
	}
	
	
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
        // Create an empty item array
        $item = array('ulid' => 0,
                      'ulhash' => '',
                      'ulapp' => 0,
                      'ulext' => '',
                      'ulfile' => ''
                      ); 
    
        // Return the item array
        return $item;
    }
    // Obtain the item information from the result set
    list($ulid, $ulmod, $ulhash, $ulapp, $ulfile) = $result->fields;
    
    $ulfile = trim($ulfile);
    $ulhash = trim($ulhash);
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    if( !$ulapp && !xarSecurityCheck('EditUploads',0)) 
    {
        //Not approved file, and user doesn't have rights to see unapproved files
        $ulhash = '';   //without ulhash file can't be found/downloaded
    }
        
    $ulext = substr($ulfile, strrpos($ulfile,".") + 1);
        
    // Create the item array
    $item = array('ulid' => $ulid,
                  'ulhash' => $ulhash,
                  'ulapp' => $ulapp,
                  'ulext' => $ulext,
                  'ulfile' => $ulfile);
    // Return the item array
    return $item;
}

function uploads_userapi_transformhook ( $args )
{
    extract($args);

    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = uploads_userapi_transform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        foreach ($extrainfo as $text) {
            $result[] = uploads_userapi_transform($text);
        }
    } else {
        $result = uploads_userapi_transform($extrainfo);
    }
    return $result;
}

function uploads_userapi_transform ( $body )
{
    // Loop over each Upload ID Tag, Auto detect display tag based on extension
    while( ereg('#ulid:([0-9]+)#',$body,$reg) )
    {
        $ulid = $reg[1];

        // Lookup Upload
        $info = xarModAPIFunc('uploads',
                              'user',
                              'get',
                              array('ulid'=>$ulid));
        // Retrieve user specified filename
        $ulfile = $info['ulfile'];

        // Check if file approved
        if( $info['ulhash'] != '' )
        {
            $tpl = xarTplModule('uploads','user','viewdownload'
                               ,array('ulid' => $ulid, 'ulfile' => $ulfile)
                               ,$info['ulext']);
        } else {
            $tpl = xarTplModule('uploads','user','viewdownload_na'
                               ,array('ulid' => $ulid, 'ulfile' => $ulfile)
                               ,$info['ulext']);
        }
        $body=ereg_replace("#ulid:$reg[1]#",$tpl,$body);
    }

    // Loop over each Upload Tag set to use Default Template
    while( ereg('#ulidd:([0-9]+)#',$body,$reg) )
    {
        $ulid = $reg[1];

        // Lookup Upload
        $info = xarModAPIFunc('uploads',
                              'user',
                              'get',
                              array('ulid'=>$ulid));
        // Retrieve user specified filename
        $ulfile = $info['ulfile'];

        // Check if file approved
        $tpl = xarTplModule('uploads','user','viewdownload'
                           ,array('ulid' => $ulid, 'ulfile' => $ulfile));
        $body=ereg_replace("#ulidd:$reg[1]#",$tpl,$body); 
    }

    // Loop over each Upload Tag set to use Default Template
    while( ereg('#ulfn:(.+)#',$body,$reg) )
    {
        $ulname = $reg[1];

        // Lookup Upload
        $info = xarModAPIFunc('uploads',
                              'user',
                              'get',
                              array('ulname'=>$ulname));
        // Retrieve user specified filename
        $ulfile = $info['ulfile'];

        // Check if file approved
        $tpl = xarTplModule('uploads','user','viewdownload'
                           ,array('ulid' => $ulid, 'ulfile' => $ulfile));
        $body=ereg_replace("#ulidd:$reg[1]#",$tpl,$body); 
    }

    return $body;
}
?>
