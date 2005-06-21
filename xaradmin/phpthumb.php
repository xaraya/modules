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
    if (empty($fileId)) {
        return array();
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

    if (is_numeric($fileId)) {
        $data['images'] = xarModAPIFunc('images','admin','getuploads',
                                        array('fileId'   => $fileId));
        if (!empty($data['images'][$fileId])) {
            $data['selimage'] = $data['images'][$fileId];
        }
    }

    if (empty($data['selimage']) || empty($data['selimage']['fileLocation'])) {
        return array();
    }

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

    // Process filters via input form
    if (!xarVarFetch('filter', 'isset',    $filter,    NULL, XARVAR_DONT_SET)) return;
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

    if (!xarVarFetch('save', 'str:1:',     $save,      NULL, XARVAR_DONT_SET)) return;
    if (empty($save)) {
        $save = $data['selimage']['fileLocation'];
        $save = preg_replace('/\.(\w+)$/','_new.$1',$save);
        $save = realpath($save);
    }
    $data['save'] = $save;

    if (!xarVarFetch('preview','str:1:',$preview,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;
    if (!empty($preview) || !empty($confirm)) {
        if (!empty($confirm)) {
            if (!xarSecConfirmAuthKey()) return;
        }

        include_once('modules/images/xarclass/phpthumb.class.php');
        $phpThumb = new phpThumb();

// CHECKME: document root may be incorrect in some cases

        if (file_exists($data['selimage']['fileLocation'])) {
            $phpThumb->setSourceFilename($data['selimage']['fileLocation']);
        }
// or $phpThumb->setSourceData($binary_image_data);
// or $phpThumb->setSourceImageResource($gd_image_resource);

        foreach ($paramlist as $param) {
            if (isset($$param) && $$param !== false) {
                $phpThumb->$param = $$param;
            }
        }

        if ($phpThumb->GenerateThumbnail()) {
            if (!empty($confirm) && !empty($save)) {
            // TODO: update files stored in the database too
                if (!$phpThumb->RenderToFile($save)) {
                    // do something with debug/error messages
                    $msg = implode("\n\n", $phpThumb->debugmessages);
                    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                                new SystemException($msg));
                    // Throw back the error
                    return;
                } else {
                    // update the uploads file entry if we overwrite a file !
                    if (is_numeric($fileId) && realpath($save) == realpath($data['selimage']['fileLocation'])) {
                        if (empty($f)) {
                            $fileType = 'image/jpeg';
                        } else {
                            $fileType = 'image/' . $f;
                        }
                    // TODO: update extrainfo too for file stored in the database
                        if (!xarModAPIFunc('uploads','user','db_modify_file',
                                           array('fileId'   => $fileId,
                                                 'fileType' => $fileType,
                                                 'fileSize' => filesize($save)))) {
                            return;
                        }
                        // Redirect to viewing the updated image here (for now)
                        xarResponseRedirect(xarModURL('images', 'admin', 'uploads',
                                                      array('action' => 'view',
                                                            'fileId' => $fileId)));
                        return true;
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
    if (count($previewargs) > 1) {
        $previewargs['preview'] = 1;
        $data['selimage']['filePreview'] = xarModURL('images','admin','phpthumb',
                                                     $previewargs);
        // restore | characters in fltr
        $data['selimage']['filePreview'] = strtr($data['selimage']['filePreview'], array('%7C' => '|'));
    }

// CHECKME: check combination of $fltr and $filter

    // preset the different filter attributes for the input form
    if (!empty($fltr) && empty($filter)) {
         $filter = array();
         foreach ($fltr as $info) {
             if (empty($info)) continue;
             $values = split('|',$info);
             $name = array_shift($values);
             // skip invalid filter entries
             if (!isset($filterlist[$name]) || count($values) != $filterlist[$name]) continue;
             $filter[$name] = $values;
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
