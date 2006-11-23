<?php
/**
 * Images Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
function uploads_admin_importgallery( $args )
{
    global $outputStuff;
    $outputStuff = '';

//    global $dd_26;
    //$dd_26 = 'http://epicsaga.com/what_do_you_know?';

    $actuallyImport = true;

    // Can use this or the dd_26 one
    global $uploads_var_overide;

    //Config
    $image_import_dir              = '/home/ogexch/public_html/var/uploads/gallery';
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



    $outputStuff .= "<h2>Importing Gallery</h2>";

    // Kick mod available
    $outputStuff .= "Checking mod avaliable (dynamicdata): ";
    $avail = xarModIsAvailable("dynamicdata");
    if( $avail )
    {
        $outputStuff .= "yes<br/>";
    } else {
        $outputStuff .= "no<br/>";
    }
    $outputStuff .= "Checking mod avaliable (categories): ";
    $avail = xarModIsAvailable("categories");
    if( $avail )
    {
        $outputStuff .= "yes<br/>";
    } else {
        $outputStuff .= "no<br/>";
    }



    $outputStuff .= "<h2>Importing Albums</h2>";

    $alumbs_lookup = array();

    // Main Album Database file -- lists all albums
    $albumdb = $image_import_dir."/albumdb.dat";

    if( !file_exists( $albumdb ) )
    {
        $outputStuff .= "Can't find albumdb.dat file at $albumdb<br/>";

        return array('outputStuff'=>$outputStuff);
    }

    $x = getSerializedData( $albumdb );

    // Iterate over albums
    foreach( $x as $album )
    {
        //Check for individual album data files
        $albumdir = $image_import_dir."/".$album;
        if( is_dir( $albumdir ) )
        {
            $outputStuff .= "<b>Found album: ".$album."</b><br/>";

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
//                $outputStuff .= "<i>Storing File: - </i> ".$file['filename'].' - ';
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
//                    $outputStuff .= "Stored<br/>";
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

//                $outputStuff .= "<i>Creating Article: - </i> ".$article['title']."<br/>";

                if( $actuallyImport )
                {
                    $aid = xarModAPIFunc('articles','admin','create',$article);
                } else {
                    $aid = "Mock run, Article not actually created.";
                }

                echo 'Article Created :: '.$aid.' :: '.$article['title'].' <br/>';

            }

            $outputStuff .= "<br/><br/>";
        }
    }

    return array('outputStuff'=>$outputStuff);
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
    global $outputStuff;

    $catpath = $Root_Cat_Name.$catpath;

    $outputStuff .= "<i>Looking up Category: ".$catpath.'</i><br/>';

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
//        $outputStuff .= "path: $path [$lastCID/$cid]<br/>";
        if( !$cid )
        {
            //This one is missing, create it.
            $args = array();
            $args['name'] = $cat_name;
            $args['description'] = $description;
            $args['image'] = '';
            $args['parent_id'] = $lastCID;
            $cid = xarModAPIFunc('categories','admin','create',$args);
            $outputStuff .= "Created: $cat_name ($cid)<br/>";
        }
        $lastCID = $cid;
    }
    return $cid;
}


function getGalleryInfo( $photodat )
{
//    $outputStuff .= "<i>Processing dat file :: $photodat <br/></i>";

    // get contents of a file into a string
    $filename = $photodat;
    $handle = fopen ($filename, "r");
    $contents = fread ($handle, filesize ($filename));
    fclose ($handle);

    $x = unserialize($contents) ;

/*

    $outputStuff .= "<pre>||";
    print_r( $x );
    $outputStuff .= "||</pre>";
*/
//    echo $contents."<pre>";
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
    global $outputStuff;

    $FilesInDir = array();
    if ($dir = @opendir($import_directory))
    {
        // Check for Gallery data file.
        $photodat = $import_directory.'/photos.dat';
        if( file_exists($photodat) )
        {
//            $outputStuff .= "Found photo.dat<br/>";
            $fileinfo = getGalleryInfo( $photodat );
        }
        while (($file = readdir($dir)) !== false)
        {
            if (
                ($file != '.') && ($file != '..')
                && (!strpos($file,'.dat'))             // Ignore dat files
                && (!strpos($file,'.html'))         // Ignore empty directory html file
                && (!strpos($file,'.sized.'))         // Ignore thumbs
                && (!strpos($file,'.thumb.'))         // Ignore thumbs
                && (!strpos($file,'.highlight.'))     // Ignore thumbs

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
                $outputStuff .= "Removing unnessary thumbnail: $file<br/>";
                unlink( $RealPathName );
            }
        }
    }

    return $FilesInDir;
}


function pruneFiles( $FilesInDir, $image_import_dir, $album )
{
    global $outputStuff;

    // Now check to see if any of those files are already in the system
    if( isset($FilesInDir) )
    {

        // Get database setup
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();

        // table and column definitions
        $uploadstable = $xartable['uploads'];

        // Remove dupes and sort
//        $FilesInDir = array_unique($FilesInDir);
        sort($FilesInDir);

        $prunedFiles = array();
        foreach ($FilesInDir as $file)
        {
            $filename = $file['filename'];
//            $outputStuff .= "Checking $filename - ";

            // Get items
            $sql = "SELECT  xar_ulid,
                            xar_ulfile,
                            xar_ulhash,
                            xar_ulapp
                    FROM $uploadstable
                    WHERE xar_ulfile = '$filename' OR xar_ulfile = '$album$filename' OR xar_ulhash = '$filename' OR xar_ulhash = '$image_import_dir$filename';";

//            $outputStuff .= "<hr><pre>";
//            print_r($sql);
//            $outputStuff .= "</pre></hr>";

            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // Check for no rows found, and if so, add file to pruned list
            if ($result->EOF)
            {
                $insystem = 'No';
                $prunedFiles[] = $file;

//                $outputStuff .= "Added to import list.<br/>";
            } else {
                $outputStuff .= "$filename - Already in system.<br/>";
            }
            //close the result set
            $result->Close();

        }
    }
    return $prunedFiles;
}

?>
