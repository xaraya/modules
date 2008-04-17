<?php
/**
 * Images Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
function uploads_admin_importpictures( $args )
{
    //global $dd_26;
    //$dd_26 = 'http://epicsaga.com/what_do_you_know?';

    // Can use this or the dd_26 one
    global $uploads_var_overide;

    //Config
    $image_import_dir = '/home/epicsaga/public_html/var/uploads/images';
    $Picture_Publication_Type_ID = 5;

    xarModVars::set('uploads', 'obfuscate_imports', 0);


    echo "Import Pictures here<br/>";

    // Kick mod available
    echo "Checking mod avaliable (dynamicdata): ";
    $avail = xarModIsAvailable("dynamicdata");
    if ( $avail ) {
        echo "yes<br/>";
    } else {
        echo "no<br/>";
    }

    // Get files to import
    $FilesInDir = getFileList( $image_import_dir );

    // Prune out dupes, and ones already in the system
    $prunedFiles = pruneFiles( $FilesInDir, $image_import_dir );


    // Setup Article Defaults
    $title   = '';
    $summary = '';
    $notes   = '';
    $pubdate = time();
    $status  = 2;        //Default to approved
    $ptid    = $Picture_Publication_Type_ID;
    $cids = array();

    $pubtypeid = $Picture_Publication_Type_ID;
    $authorid  = xarSession::getVar('role_id');
    $aid       = 0;

    $article = array('title' => $title,
                     'summary' => $summary,
                     'body' => $body,
                     'notes' => $notes,
                     'pubdate' => $pubdate,
                     'status' => $status,
                     'ptid' => $ptid,
                     'cids' => $cids,
                  // for preview
                     'pubtypeid' => $ptid,
                     'authorid' => $authorid,
                     'aid' => 0
                     );

    // Loop through files and import
    foreach ($prunedFiles as $filename)
    {

        $lastSlash = strlen($filename) - strpos( strrev( $filename ), '/' );
        $title = ucwords(str_replace( "_", " ", substr ($filename, $lastSlash, strpos( $filename, '.')-1 ) ));

        $shortname = substr ($filename, $lastSlash, strlen( $filename));
        echo "File: ".$filename."<br/>";


        // import file into Uploads
        $filepath = $image_import_dir.$filename;

        if (is_file($filepath))
        {
            $data = array('ulfile'   => $shortname
                         ,'filepath' => $filepath
                         ,'utype'    => 'file'
                         ,'mod'      => 'uploads'
                         ,'modid'    => 0
                         ,'filesize' => filesize($filepath)
                         ,'type'     => '');

            echo "About to store<br/>";
            $info = xarModAPIFunc('uploads','user','store',$data);
            echo '<pre>';
            print_r( $info );
            echo '</pre>';
            echo "Stored<br/>";

        }

        // Setup file specific title
        $article['title'] = $title;

        // Setup var to overide the uploads dd property when dd hook is called to place correct link
        $uploads_var_overide = $info['link'];
//        $dd_26                  = $info['link'];

        // Create Picture Article
        echo "Creating Article<br/>";
        $aid = xarModAPIFunc('articles','admin','create',$article);


        echo "Article Created :: ID :: $aid<br/>";
    }
    exit();
}



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

function pruneFiles( $FilesInDir, $image_import_dir )
{
    // Now check to see if any of those files are already in the system
    if( isset($FilesInDir) )
    {

        // Get database setup
        $dbconn = xarDB::getConn();
        $xartable = xarDB::getTables();

        // table and column definitions
        $uploadstable = $xartable['uploads'];

        // Remove dupes and sort
        $FilesInDir = array_unique($FilesInDir);
        sort($FilesInDir);

        $prunedFiles = array();
        foreach ($FilesInDir as $filename)
        {
            // Get items
            $sql = "SELECT  xar_ulid,
                            xar_ulfile,
                            xar_ulhash,
                            xar_ulapp
                    FROM $uploadstable
                    WHERE xar_ulfile = '$filename' OR xar_ulhash = '$filename' OR xar_ulhash = '$image_import_dir$filename';";
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // Check for no rows found, and if so, add file to pruned list
            if ($result->EOF)
            {
                $insystem = 'No';
                $prunedFiles[] = $filename;

            }
            //close the result set
            $result->Close();

        }
    }
    return $prunedFiles;
}

?>
