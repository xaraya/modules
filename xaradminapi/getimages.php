<?php
/**
 * get the list of server images
 *
 * @param   string  $basedir   The directory where images are stored
 * @param   string  $baseurl   (optional) The corresponding base URL for the images
 * @param   string  $filetypes (optional) The list of file extensions to look for
 * @param   boolean $recursive (optional) Recurse into sub-directories or not (default TRUE)
 * @param   mixed   $fileId    (optional) The file id(s) of the image(s) we're looking for
 * @param   string  $fileName  (optional) The name of the image we're looking for
 * @param   string  $filematch (optional) Specific file match for images
 * @returns array
 * @return array containing the list of images
 * @todo add startnum and numitems support + cache for large # of images
 */
function images_adminapi_getimages($args)
{
    extract($args);
    if (empty($basedir)) {
        return array();
    }
    if (!isset($baseurl)) {
        $baseurl = $basedir;
    }

    // Note: fileId is a base 64 encode of the image location here, or an array of fileId's
    if (!empty($fileId)) {
        $files = array();
        if (!is_array($fileId)) {
            $fileId = array($fileId);
        }
        foreach ($fileId as $id) {
            $file = base64_decode($id);
            if (file_exists($basedir . '/' . $file)) {
                $files[] = $file;
            }
        }

    } else {
        if (empty($filematch)) {
            $filematch = '';
            if (!empty($fileName)) {
                $filematch = '^' . $fileName;
            }
        }
        if (empty($filetypes)) {
            $filetype = '(gif|jpg|png)';
        } elseif (is_array($filetypes)) {
            $filetype = '(' . join('|',$filetypes) . ')';
        } else {
            $filetype = '(' . $filetypes . ')';
        }
        if (!isset($recursive)) {
            $recursive = true;
        }

        $files = xarModAPIFunc('dynamicdata','admin','browse',
                               array('basedir'   => $basedir,
                                     'filematch' => $filematch,
                                     'filetype'  => $filetype,
                                     'recursive' => $recursive));
        if (!isset($files)) return;
    }

    $imagelist = array();
    foreach ($files as $file) {
        $statinfo = @stat($basedir . '/' . $file);
        $sizeinfo = @getimagesize($basedir . '/' . $file);
        if (empty($statinfo) || empty($sizeinfo)) continue;
        // Note: we're using base 64 encoded fileId's here
        $id = base64_encode($file);
        $imagelist[$id] = array('fileLocation' => $basedir . '/' . $file,
                                'fileDownload' => $baseurl . '/' . $file,
                                'fileName'     => $file,
                                'fileType'     => $sizeinfo['mime'],
                                'fileSize'     => $statinfo['size'],
                                'fileId'       => $id,
                                'fileModified' => $statinfo['mtime'],
                                'width'        => $sizeinfo[0],
                                'height'       => $sizeinfo[1]);
    }

    return $imagelist;
}

?>
