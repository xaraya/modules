<?php
/**
 * Get the list of server images
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 * get the list of server images
 *
 * @author mikespub
 * @param   string  $basedir   The directory where images are stored
 * @param   string  $baseurl   (optional) The corresponding base URL for the images
 * @param   string  $filetypes (optional) The list of file extensions to look for
 * @param   boolean $recursive (optional) Recurse into sub-directories or not (default TRUE)
 * @param   mixed   $fileId    (optional) The file id(s) of the image(s) we're looking for
 * @param   string  $fileName  (optional) The name of the image we're looking for
 * @param   string  $filematch (optional) Specific file match for images
 * @param   integer $cacheExpire (optional) Cache the result for a number of seconds
 * @param   boolean $cacheRefresh (optional) Force refresh of the cache
 * @return array containing the list of images
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

        $params = array('basedir'   => $basedir,
                        'filematch' => $filematch,
                        'filetype'  => $filetype,
                        'recursive' => $recursive);

        $cachekey = md5(serialize($params));
        if (!empty($cacheExpire) && is_numeric($cacheExpire) && empty($cacheRefresh)) {
            $cacheinfo = xarModVars::get('images','file.cachelist.'.$cachekey);
            if (!empty($cacheinfo)) {
                $cacheinfo = @unserialize($cacheinfo);
                if (!empty($cacheinfo['time']) && $cacheinfo['time'] > time() - $cacheExpire) {
                    $imagelist = $cacheinfo['list'];
                }
                unset($cacheinfo);
            }
        }

        if (!isset($imagelist)) {
            $files = xarModAPIFunc('dynamicdata','admin','browse',
                                   $params);
            if (!isset($files)) return;
        }
    }

    if (!isset($imagelist)) {
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
                                    'isWritable'   => @is_writable($basedir . '/' . $file),
                                    'width'        => $sizeinfo[0],
                                    'height'       => $sizeinfo[1]);
        }

        // we're done here
        if (!empty($fileId)) {
            return $imagelist;
        }

        if (!empty($cacheExpire) && is_numeric($cacheExpire)) {
            $cacheinfo = array('time' => time(),
                               'list' => $imagelist);
            $cacheinfo = serialize($cacheinfo);
            xarModVars::set('images','file.cachelist.'.$cachekey,$cacheinfo);
            unset($cacheinfo);
        }
    }

    // save the number of images in temporary cache for countimages()
    xarVarSetCached('Modules.Images','countimages.'.$cachekey, count($imagelist));

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

    if (!empty($getnext)) {
        $found = 0;
        $newlist = array();
        foreach (array_keys($imagelist) as $id) {
            if ($id == $getnext) {
                $found++;
                continue;
            } elseif ($found) {
                $newlist[$id] = $imagelist[$id];
                break;
            }
        }
        $imagelist = $newlist;
        unset($newlist);

    } elseif (!empty($getprev)) {
        $oldid = '';
        $newlist = array();
        foreach (array_keys($imagelist) as $id) {
            if ($id == $getprev) {
                if (!empty($oldid)) {
                    $newlist[$oldid] = $imagelist[$oldid];
                }
                break;
            }
            $oldid = $id;
        }
        $imagelist = $newlist;
        unset($newlist);

    } elseif (!empty($numitems) && is_numeric($numitems)) {
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