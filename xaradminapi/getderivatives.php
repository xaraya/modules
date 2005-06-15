<?php
/**
 * get the list of derivative images (thumbnails and resized)
 *
 * @returns array
 * @return array containing the list of derivatives
 */
function images_adminapi_getderivatives($args)
{
    extract($args);
    if (empty($thumbsdir)) {
        $thumbsdir = xarModGetVar('images', 'path.derivative-store');
    }
    if (empty($thumbsdir)) {
        return array();
    }

    // Note: resized images are JPEG files - see resize() method
    $files = xarModAPIFunc('dynamicdata','admin','browse',
                           array('basedir'  => $thumbsdir,
                                 'filetype' => 'jpg'));
    if (!isset($files)) return;

    $imagelist = array();
    $filenames = array();
    foreach ($files as $file) {
        // Note: resized images are named [filename]-[width]x[height].jpg - see resize() method
        if (preg_match('/^(.+?)-(\d+)x(\d+)\.jpg$/',$file,$matches)) {
            $imagelist[] = array('fileLocation' => $thumbsdir . '/' . $file,
                                 'fileName'     => $matches[1],
                                 'fileType'     => 'image/jpeg',
                                 'width'        => $matches[2],
                                 'height'       => $matches[3]);
            $filenames[$matches[1]] = 1;
        }
    }

// TODO: find original file info in uploads module if obfuscated

    if (xarModIsAvailable('uploads') && 
        (xarModGetVar('uploads', 'file.obfuscate-on-import') ||
         xarModGetVar('uploads', 'file.obfuscate-on-upload'))) {

        $fileinfo = array();
        foreach (array_keys($filenames) as $file) {
            // this is probably an obfuscated hash for some uploaded/imported file
        // CHECKME: verify this once derivatives can be created in sub-directories of thumbsdir
            if (preg_match('/^(.*\/)?[0-9a-f]{8}\d+$/i',$file)) {

                $fileinfo[$file] = xarModAPIFunc('uploads','user','db_get_file',
                                                 array('fileHash' => $file));

            }
        }
        if (count($fileinfo) > 0) {
            foreach (array_keys($imagelist) as $id) {
                $fileHash = $imagelist[$id]['fileName'];
                if (!empty($fileinfo[$fileHash])) {
                    $info = $fileinfo[$fileHash];
                // CHECKME: assume only one match here ?
                    $imagelist[$id]['original'] = array_pop($info);
                }
            }
        }
    }

    return $imagelist;
}

?>
