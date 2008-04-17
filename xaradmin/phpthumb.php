<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
///                                                         //
// See: phpthumb.changelog.txt for recent changes           //
// See: phpthumb.readme.txt for usage instructions          //
//                                                         ///
//////////////////////////////////////////////////////////////

function images_admin_phpthumb($args)
{
    // Security check
    if (!xarSecurityCheck('AdminImages')) return;

    extract($args);

    if (!xarVarFetch('fid','isset',$fileId,'',XARVAR_NOT_REQUIRED)) return;
    if (!empty($fileId) && is_array($fileId)) {
        $fileId = array_keys($fileId);
    }

    // Get the base directories configured for server images
    $basedirs = xarModAPIFunc('images','user','getbasedirs');

    if (!xarVarFetch('bid','isset',$baseId,'',XARVAR_NOT_REQUIRED)) return;
    if (empty($baseId) || empty($basedirs[$baseId])) {
        $data = $basedirs[0]; // themes directory
        $baseId = null;
    } else {
        $data = $basedirs[$baseId];
    }
    $data['baseId'] = $baseId;
    $data['fileId'] = $fileId;

    // we're defining a processing filter without image here
    if (empty($fileId)) {
        $data['selimage'] = array();

    // we're dealing with an uploads file here
    } elseif (is_numeric($fileId)) {
        $data['images'] = xarModAPIFunc('images','admin','getuploads',
                                        array('fileId'   => $fileId));
        if (!empty($data['images'][$fileId])) {
            $data['selimage'] = $data['images'][$fileId];
        }

    // we're dealing with a derivative image here
    } elseif (preg_match('/^[0-9a-f]{32}$/i',$fileId)) {
        $data['thumbsdir'] = xarModVars::get('images', 'path.derivative-store');
        $data['images'] = xarModAPIFunc('images','admin','getderivatives',
                                        array('thumbsdir' => $data['thumbsdir'],
                                              'fileId'    => $fileId));
        foreach ($data['images'] as $image) {
            if ($image['fileId'] == $fileId) {
                $data['selimage'] = $image;
                break;
            }
        }

    // we're dealing with a server image here
    } else {
        $data['images'] = xarModAPIFunc('images','admin','getimages',
                                        $data);
        if (!empty($data['images'][$fileId])) {
            $data['selimage'] = $data['images'][$fileId];
        }
    }

    // Get the pre-defined settings for phpThumb
    $data['settings'] = xarModAPIFunc('images','user','getsettings');

    if (!xarVarFetch('setting', 'str:1:', $setting, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('load',    'str:1:', $load,    NULL, XARVAR_DONT_SET)) return;
    //$data['setting'] = $setting;
    $data['setting'] = '';
    if (!empty($load) && !empty($setting)) {
        if (!empty($data['settings'][$setting])) {
            // use pre-defined settings and ignore input values here
            extract($data['settings'][$setting]);
            $skipinput = 1;
        }
    }

    if (empty($skipinput)) {
        // URL parameters for phpThumb() - cfr. xardocs/phpthumb.readme.txt
        if (!xarVarFetch('w',    'int:1:',     $w,         NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('h',    'int:1:',     $h,         NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('f',    'enum:jpeg:png:gif', $f,  NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('q',    'int:1:',     $q,         NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('sx',   'float:0:',   $sx,        NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('sy',   'float:0:',   $sy,        NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('sw',   'float:0:',   $sw,        NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('sh',   'float:0:',   $sh,        NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('zc',   'checkbox',   $zc,        NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('bg',   'str:6:6',    $bg,        NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('bc',   'str:6:6',    $bc,        NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('fltr', 'isset',      $fltr,      NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('xto',  'checkbox',   $xto,       NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('ra',   'int',        $ra,        NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('ar',   'enum:p:P:L:l:x', $ar,    NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('aoe',  'checkbox',   $aoe,       NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('iar',  'checkbox',   $iar,       NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('far',  'checkbox',   $far,       NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('maxb', 'int:1:',     $maxb,      NULL, XARVAR_DONT_SET)) return;
        // Process filters via input form
        if (!xarVarFetch('filter', 'isset',    $filter,    NULL, XARVAR_DONT_SET)) return;
    }

    // The following URL parameters are (or will be) supported here
    $paramlist = array('w', 'h', 'f', 'q', 'sx', 'sy', 'sw', 'sh', 'zc', 'bc', 'bg', 'fltr', 'xto', 'ra', 'ar', 'aoe', 'far', 'iar', 'maxb');
    // The following URL parameters are not supported here
    //$unsupported = array('src', 'new', 'bgt', 'file', 'goto', 'err', 'down', 'phpThumbDebug', 'hash', 'md5s');

    // Remove empty filters
    if (!empty($fltr)) {
        $newfltr = array();
        foreach ($fltr as $info) {
            if (empty($info)) continue;
            $newfltr[] = $info;
        }
        if (count($newfltr) > 0) {
            $fltr = $newfltr;
        } else {
            $fltr = null;
        }
    }

    // Available filter names and their number of attributes
    $filterlist = array('gam' => 1, 'ds' => 1, 'gray' => 0, 'clr' => 2, 'sep' => 2, 'usm' => 3, 'blur' => 1, 'lvl' => 3, 'wb' => 1, 'hist' => 7, 'over' => 4, 'wmi' => 4, 'wmt' => 8, 'flip' => 1, 'elip' => 0, 'mask' => 1, 'bvl' => 3, 'bord' => 4, 'fram' => 5, 'drop' => 4);

// FIXME: make this configurable in TColorPicker !?
    // Get rid of # in front of hex colors
    if (!empty($filter['wmt']) && !empty($filter['wmt'][3]) &&  substr($filter['wmt'][3],0,1) == '#') {
       $filter['wmt'][3] = substr($filter['wmt'][3],1);
    }

    if (!xarVarFetch('save', 'str:1:',     $save,      NULL, XARVAR_DONT_SET)) return;
    if (empty($save) && !empty($data['selimage']['fileLocation'])) {
        $save = $data['selimage']['fileLocation'];
        $save = realpath($save);
        if ($save) {
            $save = preg_replace('/\.(\w+)$/','_new.$1',$save);
        }
    }
    $data['save'] = $save;

    if (!xarVarFetch('preview','str:1:',$preview,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;
    if (!empty($preview) || !empty($confirm)) {
        if (!empty($confirm)) {
            if (!xarSecConfirmAuthKey()) return;
        }

        // Process filters via input form
        if (empty($fltr) && !empty($filter)) {
            $fltr = array();
            foreach ($filter as $name => $values) {
                // skip invalid filter entries
                if (!isset($filterlist[$name]) || !is_array($values) || count($values) < $filterlist[$name]) continue;
                ksort($values,SORT_NUMERIC);
                // skip empty filter entries
                if ($filterlist[$name] > 0 && $values[0] === '') continue;
                if ($filterlist[$name] > 0) {
                    $fltr[] = $name . '|' . join('|', $values);
                } else {
                    $fltr[] = $name;
                }
            }
        }

        include_once('modules/images/xarclass/phpthumb.class.php');
        $phpThumb = new phpThumb();

        $imagemagick = xarModVars::get('images', 'file.imagemagick');
        if (!empty($imagemagick) && file_exists($imagemagick)) {
            $phpThumb->config_imagemagick_path = realpath($imagemagick);
        }

// CHECKME: document root may be incorrect in some cases

        if (file_exists($data['selimage']['fileLocation'])) {
            $file = realpath($data['selimage']['fileLocation']);
            $phpThumb->setSourceFilename($file);

        } elseif (is_numeric($fileId) && defined('_UPLOADS_STORE_DB_DATA') && ($data['selimage']['storeType'] & _UPLOADS_STORE_DB_DATA)) {
            // get the image data from the database
            $data = xarModAPIFunc('uploads', 'user', 'db_get_file_data', array('fileId' => $fileId));
            if (!empty($data)) {
                $src = implode('', $data);
                unset($data);
                $phpThumb->setSourceData($src);

                if (empty($save)) {
                    $tmpdir = xarModVars::get('uploads', 'path.uploads-directory');
                    if (is_dir($tmpdir) && is_writable($tmpdir)) {
                        $save = tempnam($tmpdir, 'xarimage-');
                    } else {
                        $save = tempnam(NULL, 'xarimage-');
                    }
                    $dbfile = 1;
                }
            }

        } else {

        }

// or $phpThumb->setSourceImageResource($gd_image_resource);

        foreach ($paramlist as $param) {
            if (isset($$param) && $$param !== false) {
                $phpThumb->$param = $$param;
            }
        }

        if ($phpThumb->GenerateThumbnail()) {
            if (!empty($confirm) && !empty($save)) {
                if (!$phpThumb->RenderToFile($save)) {
                    // do something with debug/error messages
                    $msg = implode("\n\n", $phpThumb->debugmessages);
                    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                                new SystemException($msg));
                    // Throw back the error
                    return;
                } else {
                    if (!empty($dbfile) || realpath($save) == realpath($data['selimage']['fileLocation'])) {
                        // update the uploads file entry if we overwrite a file !
                        if (is_numeric($fileId)) {
                            if (empty($f)) {
                                $fileType = 'image/jpeg';
                            } else {
                                $fileType = 'image/' . $f;
                            }
                            if (!xarModAPIFunc('uploads','user','db_modify_file',
                                               array('fileId'    => $fileId,
                                                     'fileType'  => $fileType,
                                                     'fileSize'  => filesize($save),
                                                     // reset the extrainfo
                                                     'extrainfo' => ''))) {
                                return;
                            }
                            if (!empty($dbfile)) {
                                if (!xarModAPIFunc('uploads','user','file_dump',
                                                   array('fileSrc' => $save,
                                                         'fileId' => $fileId))) {
                                    return;
                                }
                            }
                            // Redirect to viewing the updated image here (for now)
                            xarResponseRedirect(xarModURL('images', 'admin', 'uploads',
                                                          array('action' => 'view',
                                                                'fileId' => $fileId)));
                            return true;

                        } elseif (preg_match('/^[0-9a-f]{32}$/i',$fileId)) {
                            // Redirect to viewing the updated image here (for now)
                            xarResponseRedirect(xarModURL('images', 'admin', 'derivatives',
                                                          array('action' => 'view',
                                                                'fileId' => $fileId)));
                            return true;

                        } else {
                            // Redirect to viewing the updated image here (for now)
                            xarResponseRedirect(xarModURL('images', 'admin', 'browse',
                                                          array('action' => 'view',
                                                                'bid'    => $baseId,
                                                                'fid'    => $fileId)));
                            return true;
                        }
                    }
                    $data['message'] = xarML('The image has been saved as "#(1)"',$save);
                }
            } else {
                $phpThumb->OutputThumbnail();
                // Stop processing here
                exit;
            }
        } else {
            $msg = implode("\n\n", $phpThumb->debugmessages);
            if (!empty($preview)) {
                $phpThumb->ErrorImage($msg);
                // Stop processing here
                exit;
            } else {
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                            new SystemException($msg));
                // Throw back the error
                return;
            }
        }
    }

    $previewargs = array();
    $previewargs['fid'] = $fileId;
    foreach ($paramlist as $param) {
        if (isset($$param) && $$param !== false) {
            $data[$param] = $$param;
            $previewargs[$param] = $$param;
        } else {
            $data[$param] = '';
        }
    }
    // Process filters via input form
    if (empty($previewargs['fltr']) && !empty($filter)) {
        $previewargs['fltr'] = array();
        foreach ($filter as $name => $values) {
            // skip invalid filter entries
            if (!isset($filterlist[$name]) || !is_array($values) || count($values) < $filterlist[$name]) continue;
            ksort($values,SORT_NUMERIC);
            // skip empty filter entries
            if ($filterlist[$name] > 0 && $values[0] === '') continue;
            if ($filterlist[$name] > 0) {
                $previewargs['fltr'][] = $name . '|' . join('|', $values);
            } else {
                $previewargs['fltr'][] = $name;
            }
        }
    }

    if (!xarVarFetch('newset',  'str:1:', $newset,  NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('store',   'str:1:', $store,   NULL, XARVAR_DONT_SET)) return;
    if (!empty($store)) {
        if (!empty($newset)) {
            // if we have both setting and newset, "rename" the old setting to the new one
            if (!empty($setting) && isset($data['settings'][$setting])) {
                unset($data['settings'][$setting]);
            }
            $setting = $newset;
            //$data['setting'] = $newset;
        }
        if (!empty($setting)) {
            $data['settings'][$setting] = $previewargs;
            if (isset($data['settings'][$setting]['fid'])) {
                unset($data['settings'][$setting]['fid']);
            }

            xarModAPIFunc('images','admin','setsettings',$data['settings']);

            // Note: processed images are named md5(filelocation)-[setting].[ext] - see process_image() function
            $add = xarVarPrepForOs($setting);
            $add = strtr($add, array(' ' => ''));
            $affected = xarModAPIFunc('images','admin','getderivatives',
                                      array('filematch' => '^\w+-' . $add));
            // Delete any derivative image using this setting earlier
            if (!empty($affected)) {
                foreach ($affected as $info) {
                    @unlink($info['fileLocation']);
                }
            }
        }
    }

    if (count($previewargs) > 1) {
        $previewargs['preview'] = 1;
        if (!empty($baseId)) {
            $previewargs['bid'] = $baseId;
        }
        $previewurl = xarModURL('images','admin','phpthumb',
                                $previewargs);
        // restore | characters in fltr
        $previewurl = strtr($previewurl, array('%7C' => '|'));
        // show parameters
        $data['params'] = preg_replace('/^.*fid=[^&]*&amp;/','',$previewurl);
        $data['params'] = preg_replace('/&amp;preview=1.*$/','',$data['params']);
        if (!empty($data['selimage'])) {
            $data['selimage']['filePreview'] = $previewurl;
        }
    }

    // preset the format based on the current file type
    if (empty($data['f'])) {
        if (empty($data['selimage'])) {
            $data['f'] = 'jpeg';
        } else {
            switch ($data['selimage']['fileType']) {
                case 'image/png':
                    $data['f'] = 'png';
                    break;
                case 'image/gif':
                    $data['f'] = 'gif';
                    break;
                case 'image/jpeg':
                default:
                    $data['f'] = 'jpeg';
                    break;
            }
        }
    }

// CHECKME: check combination of $fltr and $filter

    // preset the different filter attributes for the input form
    if (!empty($fltr) && empty($filter)) {
         $filter = array();
         foreach ($fltr as $id => $info) {
             if (empty($info)) continue;
             $values = split('\|',$info);
             $name = array_shift($values);
             // skip invalid filter entries
             if (!isset($filterlist[$name]) || count($values) < $filterlist[$name]) continue;
             $filter[$name] = $values;
             // remove from the fltr fields
             $data['fltr'][$id] = '';
         }
    }
    if (empty($filter)) {
        $data['filter'] = array();
    } else {
        $data['filter'] = $filter;
    }
    foreach ($filterlist as $name => $attr) {
        if (empty($data['filter'][$name])) {
            $data['filter'][$name] = array();
        }
        for ($i = count($data['filter'][$name]); $i <= $attr; $i++) {
            $data['filter'][$name][] = '';
        }
    }
    // preset the fltr fields
    if (empty($fltr)) {
        $data['fltr'] = array();
    }
    for ($i = count($data['fltr']); $i <= 4; $i++) {
        $data['fltr'][] = '';
    }

    $data['authid'] = xarSecGenAuthKey('images');
    return $data;
}

?>
