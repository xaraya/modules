<?php
function uploads_admin_importgallery( $args )
{
//	global $dd_26;
	//$dd_26 = 'http://epicsaga.com/what_do_you_know?';

	$actuallyImport = true;

	// Can use this or the dd_26 one
	global $uploads_var_overide;

	//Config
	$image_import_dir 			 = '/home/epicsaga/public_html/var/uploads/images/Drew Vogal/Gallery';
	$Picture_Publication_Type_ID = 5;
	$Root_Cat_Name               = 'Gallery/Drew Vogal/Gallery';


	// Setup Article Defaults
    $article = array('title' => '',
                     'summary' => '',
                     'body' => '',
                     'notes' => '',
                     'pubdate' => time(),
                     'status' => 2,
                     'ptid' => $Picture_Publication_Type_ID,
                     'cids' => array(),
                  // for preview
                     'pubtypeid' => $Picture_Publication_Type_ID,
                     'authorid' => xarSessionGetVar('uid'),
                     'aid' => 0
					 );


	// Gallery Classes
	include "modules/uploads/import/gallery/Album.php";
	include "modules/uploads/import/gallery/AlbumDB.php";
	include "modules/uploads/import/gallery/AlbumItem.php";
	include "modules/uploads/import/gallery/Image.php";



	echo "<h2>Importing Gallery</h2>";
	
	// Kick mod available
	echo "Checking mod avaliable (dynamicdata): ";
	$avail = xarModIsAvailable("dynamicdata");
	if( $avail )
	{
		echo "yes<br/>";
	} else {
		echo "no<br/>";
	}
	echo "Checking mod avaliable (categories): ";
	$avail = xarModIsAvailable("categories");
	if( $avail )
	{
		echo "yes<br/>";
	} else {
		echo "no<br/>";
	}



	echo "<h2>Importing Albums</h2>";
	
	$alumbs_lookup = array();
		
	// Main Album Database file -- lists all albums
	$albumdb = $image_import_dir."/albumdb.dat";

	$x = getSerializedData( $albumdb );

	// Iterate over albums
	foreach( $x as $album )
	{	
		//Check for individual album data files
		$albumdir = $image_import_dir."/".$album;
		if( is_dir( $albumdir ) )
		{
			echo "<b>Found album: ".$album."</b><br/>";

			$albumdat = $albumdir."/album.dat";
			$x = getSerializedData( $albumdat );

			// Get Current Album's information
			$album_title = $x->fields['title'];
			$album_desc  = $x->fields['description'];
			
			// Determine ancestery
			$album_parent = $x->fields['parentAlbumName'];
			if( !$album_parent )
			{
				$album_parent = '';
			} else {
				if( $alumbs_lookup[$album_parent]['parent'] )
				{
					$album_parent = $alumbs_lookup[$album_parent]['parent'].'/'.$alumbs_lookup[$album_parent]['title'];
				} else {
					$album_parent = '/'.$alumbs_lookup[$album_parent]['title'];
				}
			}

			// Build Album's Category, if nessesary -- then retrieve Category ID.
			$album_cid = createCat( $Root_Cat_Name, $album_parent.'/'.$album_title, $album_desc);


			// Store Current Album's information in general album lookup, for child albums to lookup.
			$alumbs_lookup[$album] = array(
											'title'=>$album_title
										   ,'description'=>$album_desc
										   ,'parent'=>$album_parent
										   ,'cid'=>$album_cid
										  );

			// Get files to import 
			$FilesInDir = galleryTraverse( $image_import_dir.'/'.$album );
	
			// Prune out dupes, and ones already in the system
			$prunedFiles = pruneFiles( $FilesInDir, $image_import_dir.'/'.$album,$album );
			

			// Loop through files and import
			foreach ($prunedFiles as $file) 
			{
//				echo "<i>Storing File: - </i> ".$file['filename'].' - ';
				$filetitle = ucwords(str_replace( "_", " ", substr ($file['filename'], 1, strpos( $file['filename'], '.')-1 ) ));
				
				// import file into Uploads
				$filepath = $image_import_dir.'/'.$album.$file['filename'];

				if (is_file($filepath))
				{
					$data = array('ulfile'   => $album.$file['filename']
								 ,'filepath' => $filepath
								 ,'utype'    => 'file'
								 ,'mod'      => 'uploads'
								 ,'modid'    => 0
								 ,'filesize' => filesize($filepath)
								 ,'type'     => '');
		
					if( $actuallyImport )
					{
						$info = xarModAPIFunc('uploads','user','store',$data);
					} else {
						$info = array('link'=>''); //dummy for when not importing
					}
//					echo "Stored<br/>";	
				}		
				
			//Setup Article with file specific values
			//***************************************
				// Setup file specific title, Retreive caption from Gallery Photos Dat if available
				if( isset($file['fileinfo']['caption']) )
				{
					$article['title'] = $file['fileinfo']['caption'];
				} else {
					$article['title'] = $filetitle;
				}
				if( isset($file['fileinfo']['date']) )
				{
					$article['pubdate'] = $file['fileinfo']['date'];
				}
				if( isset($file['fileinfo']['comments']) )
				{
					$article['summary'] = $file['fileinfo']['comments'];
				}
				
				// Set Category ID
				$article['cids'] = array($album_cid);

						
				// Setup var to overide the uploads dd property when dd hook is called to place correct link
				$uploads_var_overide = $info['link'];
				
//				echo "<i>Creating Article: - </i> ".$article['title']."<br/>";
				
				if( $actuallyImport )
				{
					$aid = xarModAPIFunc('articles','admin','create',$article);
				} else {
					$aid = "Mock run, Article not actually created.";
				}	
			
				echo 'Article Created :: '.$aid.' :: '.$article['title'].' <br/>';

			}
			
			echo "<br/><br/>";
		}
	}


//	echo $contents."<pre>";
exit();

			echo '<hr><pre>';
			print_r( $alumbs_lookup[$album] );
			echo '</pre></hr>';										  



	



	// Loop through files and import
	foreach ($prunedFiles as $file) 
	{
		$filename = $file['filename'];
		
		echo "<hr> <b>File: ".$filename."</b><br/>";
		$lastSlash = strlen($filename) - strpos( strrev( $filename ), '/' );
		$title = ucwords(str_replace( "_", " ", substr ($filename, $lastSlash, strpos( $filename, '.')-1 ) ));

		$catpath  = substr($filename, 0, $lastSlash-1);

		// import file into Uploads
		$filepath = $image_import_dir.$filename;
		
		if (is_file($filepath))
		{
			$data = array('ulfile'   => $filename
						 ,'filepath' => $filepath
						 ,'utype'    => 'file'
						 ,'mod'      => 'uploads'
						 ,'modid'    => 0
						 ,'filesize' => filesize($filepath)
						 ,'type'     => '');

			echo "<i>About to store</i>:<br/>";	
			if( $actuallyImport )
			{
				$info = xarModAPIFunc('uploads','user','store',$data);
			}
			echo "Stored<br/>";	

		}		
		
		// Setup file specific title
		$article['title'] = $title;

		// Retreive caption from Gallery Photos Dat if available
		if( isset($file['fileinfo']['caption']) )
		{
			$article['title'] = $file['fileinfo']['caption'];
		}
		if( isset($file['fileinfo']['date']) )
		{
			$article['pubdate'] = $file['fileinfo']['date'];
		}
		if( isset($file['fileinfo']['comments']) )
		{
			$article['summary'] = $file['fileinfo']['comments'];
		}
				
		// Setup var to overide the uploads dd property when dd hook is called to place correct link
		$uploads_var_overide = $info['link'];
//		$dd_26 				 = $info['link'];

		// Deal with categories
		if( $catpath != '' )
		{
			$cat = $Root_Cat_Name.$catpath;
			echo "<br/><i>Looking up CID</i>:<br/>";
			echo $cat."<br/>";
			$args = array('path'=>$cat);
			$cid  = xarModAPIFunc('categories','user','categoryexists',$args);
	
			if( !$cid )
			{
				echo "<br/><i>CID not found, creating or resolving</i><br/>";
				$cid = createCat( $Root_Cat_Name,$catpath,$image_import_dir );
			}
			
			
			echo "CID: $cid<br/>";
			
			$article['cids'] = array($cid);
		}		
		
	
		

		// Create Picture Article
		echo "<br/><br/><i>Creating Article:</i><br/>";
		echo '<pre>';
		print_r( $article );
		echo '</pre>';
		echo 'Link :: '.$info['link'].'<br/>';
		echo 'File :: '.$filename.'<br/>';
		echo 'Path :: '.$filepath.'<br/>';

		
		if( $actuallyImport )
		{
			$aid = xarModAPIFunc('articles','admin','create',$article);
		}	
	
		echo "Article Created :: ID :: $aid<br/>";
	}
	exit();
}

function getSerializedData( $filename )
{
	$handle = fopen ($filename, "r");
	$contents = fread ($handle, filesize ($filename));
	fclose ($handle);

	return unserialize($contents) ;
}

function createCat( $Root_Cat_Name,$catpath,$description )
{
	$catpath = $Root_Cat_Name.$catpath;

	echo "<i>Looking up Category: ".$catpath.'</i><br/>';
	
	$path = '';
	$lastCID = 0;
    $path_array = explode("/", $catpath);
    foreach ($path_array as $cat_name) 
	{
		if( $path != '' )
		{
			$path = $path.'/'.$cat_name;
		} else {
			$path = $cat_name;
		}

		$cid  = xarModAPIFunc('categories','user','categoryexists',array('path'=>$path));
//		echo "path: $path [$lastCID/$cid]<br/>";
		if( !$cid )
		{
			//This one is missing, create it.
			$args = array();
			$args['name'] = $cat_name;
			$args['description'] = $description;
			$args['image'] = '';
			$args['parent_id'] = $lastCID;
			$cid = xarModAPIFunc('categories','admin','create',$args);
			echo "Created: $cat_name ($cid)<br/>";
		}
		$lastCID = $cid;
	}
	return $cid;
}

function getGalleryInfo( $photodat )
{
//	echo "<i>Processing dat file :: $photodat <br/></i>";
	
	// get contents of a file into a string
	$filename = $photodat;
	$handle = fopen ($filename, "r");
	$contents = fread ($handle, filesize ($filename));
	fclose ($handle);

	$x = unserialize($contents) ;

/*	
	
	echo "<pre>||";
	print_r( $x );
	echo "||</pre>";
*/	
//	echo $contents."<pre>";
	$fileinfo = array();
	foreach( $x as $item )
	{	
		$filename = $item->image->name.'.'.$item->image->type;
		$fileinfo[$filename] = array( 'date'    => $item->uploadDate
		                             ,'caption' => $item->caption
		                             ,'comments' => $item->comments
									 );
	}
	return $fileinfo;
}

function galleryTraverse( $import_directory )
{
	$FilesInDir = array();
	if ($dir = @opendir($import_directory)) 
	{
		// Check for Gallery data file.
		$photodat = $import_directory.'/photos.dat';
		if( file_exists($photodat) )
		{
//			echo "Found photo.dat<br/>";
			$fileinfo = getGalleryInfo( $photodat );
		}
		while (($file = readdir($dir)) !== false) 
		{
			if (
				($file != '.') && ($file != '..') 
				&& (!strpos($file,'.dat')) 			// Ignore dat files
				&& (!strpos($file,'.html')) 		// Ignore empty directory html file
				&& (!strpos($file,'.sized.')) 		// Ignore thumbs
				&& (!strpos($file,'.thumb.')) 		// Ignore thumbs
				&& (!strpos($file,'.highlight.')) 	// Ignore thumbs
				
			   ) 
			{
				$RealPathName = realpath($import_directory.'/'.$file);
				if (is_file($RealPathName)) 
				{
					$FilesInDir[] = array( 'filename' => substr($RealPathName,strlen($import_directory))
										  ,'filepath' => $RealPathName
										  ,'fileinfo' => $fileinfo[$file]
										 );
				}
			} else if (strpos($file,'.sized.') || strpos($file,'.thumb.') || strpos($file,'.highlight.')) {
				$RealPathName = realpath($import_directory.'/'.$file);
				echo "Removing unnessary thumbnail: $file<br/>";
				unlink( $RealPathName );
			}
		}
	}
	return $FilesInDir;
}


/*
function getFileList( $import_directory )
{
	// Recurse through import directories, getting files
	$DirectoriesToScan = array($import_directory);
	$DirectoriesScanned = array();
	while (count($DirectoriesToScan) > 0) 
	{
	 foreach ($DirectoriesToScan as $DirectoryKey => $startingdir) {
	   if ($dir = @opendir($startingdir)) {
		 while (($file = readdir($dir)) !== false) {
		   if (($file != '.') && ($file != '..')) {
		   $RealPathName = realpath($startingdir.'/'.$file);
			 if (is_dir($RealPathName)) {
			   if (!in_array($RealPathName, $DirectoriesScanned) && !in_array($RealPathName, $DirectoriesToScan)) {
				 $DirectoriesToScan[] = $RealPathName;
			   }
			 } elseif (is_file($RealPathName)) {
			   $FilesInDir[] = substr($RealPathName,strlen($import_directory));
			 }
		   }
		 }
		closedir($dir);
	   }
	   $DirectoriesScanned[] = $startingdir;
	  unset($DirectoriesToScan[$DirectoryKey]);
	 }
	}
	
	return $FilesInDir;
}
*/
function pruneFiles( $FilesInDir, $image_import_dir, $album )
{
	// Now check to see if any of those files are already in the system
	if( isset($FilesInDir) )
	{	
	
		// Get database setup
		list($dbconn) = xarDBGetConn();
		$xartable = xarDBGetTables();
			
		// table and column definitions
		$uploadstable = $xartable['uploads'];
	
		// Remove dupes and sort
//		$FilesInDir = array_unique($FilesInDir);
		sort($FilesInDir);

		$prunedFiles = array();
		foreach ($FilesInDir as $file) 
		{
			$filename = $file['filename'];
//			echo "Checking $filename - ";
			
			// Get items
			$sql = "SELECT  xar_ulid,
							xar_ulfile,
							xar_ulhash,
							xar_ulapp
					FROM $uploadstable
					WHERE xar_ulfile = '$filename' OR xar_ulfile = '$album$filename' OR xar_ulhash = '$filename' OR xar_ulhash = '$image_import_dir$filename';";
					
//			echo "<hr><pre>";
//			print_r($sql);
//			echo "</pre></hr>";
			$result = $dbconn->Execute($sql);
			
			// Check for an error with the database code, and if so set an appropriate
			// error message and return
			if ($dbconn->ErrorNo() != 0) {
				$msg = xarMLByKey('DATABASE_ERROR', $sql);
				xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
							   new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
							   
				return;
			}
			
			// Check for no rows found, and if so, add file to pruned list
			if ($result->EOF) 
			{
				$insystem = 'No';
				$prunedFiles[] = $file;
				
//				echo "Added to import list.<br/>";
			} else {
				echo "$filename - Already in system.<br/>";
			}
			//close the result set
			$result->Close();
		
		}
	}
	return $prunedFiles;
}

?>
