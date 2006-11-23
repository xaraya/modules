<?php
/**
 * Get the list of derivative images
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
/**
 * get the list of derivative images (thumbnails and resized)
 *
 * @author mikespub
 * @param   mixed   $fileId    (optional) The file id(s) of the image(s) we're looking for
 * @param   string  $fileName  (optional) The name of the image we're getting derivatives for
 * @param   string  $fileLocation (optional) The location of the image we're getting derivatives for
 * @param   string  $thumbsdir (optional) The directory where derivative images are stored
 * @param   string  $filematch (optional) Specific file match for derivative images
 * @param   integer $cacheExpire (optional) Cache the result for a number of seconds
 * @param   boolean $cacheRefresh (optional) Force refresh of the cache
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
    if (empty($filematch)) {
        $filematch = '';
        if (!empty($fileName)) {
            // Note: resized images are named [filename]-[width]x[height].jpg - see resize() method
            // Note: processed images are named [filename]-[setting].[ext] - see process_image() function
            $filematch = '^' . $fileName . '-.+';
        } elseif (!empty($fileLocation)) {
            // Note: resized images are named md5(filelocation)-[width]x[height].jpg - see resize() method
            // Note: processed images are named md5(filelocation)-[setting].[ext] - see process_image() function
            if (!is_numeric($fileLocation)) {
                $fileLocation = md5($fileLocation);
            }
            $filematch = '^' . $fileLocation . '-.+';
        }
    }
    if (empty($filetype)) {
        // Note: resized images are JPEG files - see resize() method
        //$filetype = 'jpg';
        // Note: processed images can be JPEG, GIF or PNG files - see process_image() function
        $filetype = '(jpg|png|gif)';
    }

    $params = array('basedir'   => $thumbsdir,
                    'filematch' => $filematch,
                    'filetype'  => $filetype);

    if (!empty($fileId)) {
        if (!is_array($fileId)) {
            $fileId = array($fileId);
        }
    } else {
        $cachekey = md5(serialize($params));
        if (!empty($cacheExpire) && is_numeric($cacheExpire) && empty($cacheRefresh)) {
            $cacheinfo = xarModGetVar('images','file.cachederiv.'.$cachekey);
            if (!empty($cacheinfo)) {
                $cacheinfo = @unserialize($cacheinfo);
                if (!empty($cacheinfo['time']) && $cacheinfo['time'] > time() - $cacheExpire) {
                    $imagelist = $cacheinfo['list'];
                }
                unset($cacheinfo);
            }
        }
    }

    if (!isset($imagelist)) {
        $files = xarModAPIFunc('dynamicdata','admin','browse',
                               $params);
        if (!isset($files)) return;

        $imagelist = array();
        $filenames = array();
        foreach ($files as $file) {
            // Note: resized images are named [filename]-[width]x[height].jpg - see resize() method
            if (preg_match('/^(.+?)-(\d+)x(\d+)\.jpg$/',$file,$matches)) {
                $id = md5($thumbsdir . '/' . $file);
                if (!empty($fileId)) {
                    if (!in_array($id,$fileId)) continue;
                }
                $info = stat($thumbsdir . '/' . $file);
                $imagelist[] = array('fileLocation' => $thumbsdir . '/' . $file,
                                     'fileDownload' => xarModURL('images','user','display',
                                                                 array('fileId' => base64_encode($thumbsdir . '/' . $file))),
                                     'fileName'     => $matches[1],
                                     'fileType'     => 'image/jpeg',
                                     'fileSize'     => $info['size'],
                                     'fileId'       => $id,
                                     'fileModified' => $info['mtime'],
                                     'width'        => $matches[2],
                                     'height'       => $matches[3]);
                $filenames[$matches[1]] = 1;

            // Note: processed images are named [filename]-[setting].[ext] - see process_image() function
            } elseif (preg_match('/^(.+?)-(.+?)\.\w+$/',$file,$matches)) {
                $id = md5($thumbsdir . '/' . $file);
                if (!empty($fileId)) {
                    if (!in_array($id,$fileId)) continue;
                }
                $statinfo = stat($thumbsdir . '/' . $file);
                $sizeinfo = getimagesize($thumbsdir . '/' . $file);
                $imagelist[] = array('fileLocation' => $thumbsdir . '/' . $file,
                                     'fileDownload' => xarModURL('images','user','display',
                                                                 array('fileId' => base64_encode($thumbsdir . '/' . $file))),
                                     'fileName'     => $matches[1],
                                     'fileType'     => $sizeinfo['mime'],
                                     'fileSize'     => $statinfo['size'],
                                     'fileId'       => $id,
                                     'fileModified' => $statinfo['mtime'],
                                     'fileSetting'  => $matches[2],
                                     'width'        => $sizeinfo[0],
                                     'height'       => $sizeinfo[1]);
                $filenames[$matches[1]] = 1;
            }
        }

    // CHECKME: keep track of originals for server images too ?

        if (empty($fileName) && xarModIsAvailable('uploads')) {

            $fileinfo = array();
            foreach (array_keys($filenames) as $file) {
            // CHECKME: verify this once derivatives can be created in sub-directories of thumbsdir
                // this is probably the file id for some uploaded/imported file stored in the database
                if (preg_match('/^(.*\/)?(\d+)$/',$file,$matches)) {

                    $fileinfo[$file] = xarModAPIFunc('uploads','user','db_get_file',
                                                     array('fileId' => $matches[2]));

                // this may be the md5 hash of the file location for some uploaded/imported file
                } elseif (preg_match('/^(.*\/)?([0-9a-f]{32})$/i',$file,$matches)) {

                // CHECKME: watch out for duplicates here too
                    $fileinfo[$file] = xarModAPIFunc('uploads','user','db_get_file',
                                                     array('fileLocationMD5' => $matches[2]));
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

        // we're done here
        if (!empty($fileId)) {
            return $imagelist;
        }

        if (!empty($cacheExpire) && is_numeric($cacheExpire)) {
            $cacheinfo = array('time' => time(),
                               'list' => $imagelist);
            $cacheinfo = serialize($cacheinfo);
            xarModSetVar('images','file.cachederiv.'.$cachekey,$cacheinfo);
            unset($cacheinfo);
        }
    }

    // save the number of images in temporary cache for countderivatives()
    xarVarSetCached('Modules.Images','countderivatives.'.$cachekey, count($imagelist));

    if (empty($sort)) {
        $sort = '';
    }
    switch ($sort) {
        case 'name':
            // handled by browse above
            //$strsort = 'fileName';
            break;
        case 'type':
            $strsort = 'fileType';
            break;
        case 'width':
        case 'height':
            $numsort = $sort;
            break;
        case 'size':
            $numsort = 'fileSize';
            break;
        case 'time':
            $numsort = 'fileModified';
            break;
        default:
            break;
    }
    if (!empty($numsort)) {
        $sortfunc = create_function('$a,$b','if ($a["'.$numsort.'"] == $b["'.$numsort.'"]) return 0; return ($a["'.$numsort.'"] > $b["'.$numsort.'"]) ? -1 : 1;');
        usort($imagelist, $sortfunc);
    } elseif (!empty($strsort)) {
        $sortfunc = create_function('$a,$b','return strcmp($a["'.$strsort.'"], $b["'.$strsort.'"]);');
        usort($imagelist, $sortfunc);
    }

    if (!empty($numitems) && is_numeric($numitems)) {
        if (empty($startnum) || !is_numeric($startnum)) {
            $startnum = 1;
        }
        if (count($imagelist) > $numitems) {
            // use array slice on the keys here (at least until PHP 5.0.2)
            $idlist = array_slice(array_keys($imagelist),$startnum-1,$numitems);
            $newlist = array();
            foreach ($idlist as $id) {
                $newlist[$id] = $imagelist[$id];
            }
            $imagelist = $newlist;
            unset($newlist);
        }
    }

    return $imagelist;
}

?>
