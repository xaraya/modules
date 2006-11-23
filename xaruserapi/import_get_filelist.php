<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 *  Get a list of files with metadata from some import directory or link
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string   fileLocation   The starting directory
 *  @param   boolean  descend        Go through all sub-directories too
 *  @param   boolean  onlyNew        Only return files that aren't imported yet
 *  @param   string   search         Search for a particular filename pattern
 *  @param   string   exclude        Exclude a particular filename pattern
 *  @param   integer  cacheExpire    Cache the result for a number of seconds (e.g. for DD Upload)
 *  @param   boolean  analyze        Analyze each file for mime type (default TRUE)
 *
 *  @returns array
 *  @return array of file information
 */

function uploads_userapi_import_get_filelist( $args )
{

    extract($args);

    if (!empty($cacheExpire) && is_numeric($cacheExpire)) {
        $cachekey = md5(serialize($args));
        $cacheinfo = xarModGetVar('uploads','file.cachelist.'.$cachekey);
        if (!empty($cacheinfo)) {
            $cacheinfo = @unserialize($cacheinfo);
            if (!empty($cacheinfo['time']) && $cacheinfo['time'] > time() - $cacheExpire) {
                return $cacheinfo['list'];
            }
        }
    }

    // Whether or not to descend into any directory
    // that is found while creating the list of files
    if (!isset($descend)) {
        $descend = FALSE;
    }

    // Whether or not to only add files that are -not- already
    // stored with entries in the database
    if (!isset($onlyNew)) {
        $onlyNew = FALSE;
    }

    if ((isset($search) && isset($exclude)) && $search == $exclude) {
        return array();
    }

    if (!isset($search)) {
        $search = '.*';
    }

    if (!isset($exclude)) {
        $exclude = NULL;
    }

    // Whether or not to analyze each file for mime type
    if (!isset($analyze)) {
        $analyze = TRUE;
    }

    // if search and exclude are the same, we would get no results
    // so return no results.
    $fileList = array();

    if (!isset($fileLocation)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].',
                     'fileLocation', 'import_get_filelist', 'uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!file_exists($fileLocation)) {
        $msg = xarML("Unable to acquire list of files to import - Location '#(1)' does not exist!",$fileLocation);
        xarErrorSet(XAR_USER_EXCEPTION, 'FILE_NOT_EXIST', new DefaultUserException($msg));
        return;
    }

    if (is_file($fileLocation)) {
        $type = _INODE_TYPE_FILE;
    } elseif (is_dir($fileLocation)) {
        $type = _INODE_TYPE_DIRECTORY;
    } elseif (is_link($fileLocation)) {
        $linkLocation = readlink($fileLocation);

        while (is_link($linkLocation)) {
            $linkLocation = readlink($linkLocation);
        }

        $fileLocation = $linkLocation;

        if (is_dir($linkLocation)) {
            $type = _INODE_TYPE_FILE;
        } elseif (is_file($linkLocation)) {
            $type = _INODE_TYPE_DIRECTORY;
        } else {
            $type = -1;
        }

    } else {
        $type = -1;
    }

    switch ($type) {
        case _INODE_TYPE_FILE:
            if ($onlyNew) {

                $file = xarModAPIfunc('uploads', 'user', 'db_get_file',
                                       array('fileLocation' => $fileLocation));
                if (count($file)) {
                    break;
                }
            }
            $fileName = $fileLocation;
            // if we are searching for specific files, then check and break if search doesn't match
            if ((isset($search) && preg_match("/$search/", $fileName)) &&
                (!isset($exclude) || !preg_match("/$exclude/", $fileName))) {
                    $fileList["$type:$fileName"] =
                        xarModAPIFunc('uploads', 'user', 'file_get_metadata',
                                       array('fileLocation' => $fileLocation,
                                             'analyze'      => $analyze));
            }
            break;
        case _INODE_TYPE_DIRECTORY:
            if ($fp = opendir($fileLocation)) {

                while (FALSE !== ($inode = readdir($fp))) {
                    if (is_dir($fileLocation . '/'. $inode) && !eregi('^([.]{1,2})$', $inode)) {
                        $type = _INODE_TYPE_DIRECTORY;
                    } elseif (is_file($fileLocation. '/' . $inode)) {
                        $type = _INODE_TYPE_FILE;
                    } elseif (is_link($fileLocation. '/' . $inode)) {
                        $linkLocation = readlink($fileLocation . '/' . $inode);

                        while (is_link($linkLocation)) {
                            $linkLocation = readlink($linkLocation);
                        }

                        if (is_dir($linkLocation) && !eregi('([.]{1,2}$', $linkLocation)) {
                            $type = _INODE_TYPE_DIRECTORY;
                        } elseif (is_file($linkLocation)) {
                            $type = _INODE_TYPE_FILE;
                        } else {
                            $type = -1;
                        }
                    } else {
                        $type = -1;
                    }


                    switch ($type) {
                        case _INODE_TYPE_FILE:
                            $fileName = $fileLocation . '/' . $inode;

                            if ($onlyNew) {
                                $file = xarModAPIfunc('uploads', 'user', 'db_get_file',
                                                    array('fileLocation' => $fileName));
                                if (count($file)) {
                                    continue;
                                }
                            }

                            if ((!isset($search) || preg_match("/$search/", $fileName)) &&
                                (!isset($exclude) || !preg_match("/$exclude/", $fileName))) {
                                    $file = xarModAPIFunc('uploads', 'user', 'file_get_metadata',
                                                        array('fileLocation' => $fileName,
                                                              'analyze'      => $analyze));
                                    $fileList["$file[inodeType]:$fileName"] = $file;
                            }
                            break;
                        case _INODE_TYPE_DIRECTORY:
                            $dirName = "$fileLocation/$inode";
                            if ($descend) {
                                $files = xarModAPIFunc('uploads', 'user', 'import_get_filelist',
                                                        array('fileLocation' => $dirName,
                                                          'descend' => TRUE,
                                                          'analyze' => $analyze,
                                                          'exclude' => $exclude,
                                                          'search' => $search));
                                $fileList += $files;
                            } else {

                                if ((!isset($search) || preg_match("/$search/", $dirName)) &&
                                    (!isset($exclude) || !preg_match("/$exclude/", $dirName))) {
                                        $files = xarModAPIFunc('uploads', 'user', 'file_get_metadata',
                                                            array('fileLocation' => $dirName,
                                                                  'analyze'      => $analyze));
                                        $fileList["$files[inodeType]:$inode"] = $files;
                                }
                            }
                            break;
                        default:
                            break;
                    }

                    if (is_dir($fileLocation. '/' . $inode) && !eregi('^([.]{1,2})$', $inode)) {
                    }
                    if (is_file($fileLocation. '/' . $inode)) {
                    }
                }
            }
            closedir($fp);
            break;
        default:
            break;
    }

    if (is_array($fileList)) {
        ksort($fileList);
    }

    if (!empty($cacheExpire) && is_numeric($cacheExpire)) {
        // get the cache list again, in case someone else filled it by now
        $cacheinfo = array('time' => time(),
                           'list' => $fileList);
        $cacheinfo = serialize($cacheinfo);
        xarModSetVar('uploads','file.cachelist.'.$cachekey,$cacheinfo);
    }

    return $fileList;
}

?>
