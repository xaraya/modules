<?php

/**
 * View a list of server images
 *
 * @todo add startnum and numitems support
 */
function images_admin_browse()
{
    // Security check
    if (!xarSecurityCheck('AdminImages')) return;

    // Note: fileId is a base 64 encode of the image location here, or an array of fileId's
    if (!xarVarFetch('fid','isset',$fileId,'',XARVAR_NOT_REQUIRED)) return;
    if (!empty($fileId) && is_array($fileId)) {
        $fileId = array_keys($fileId);
    }
    if (empty($fileId)) {
        $fileId = null;
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

    $data['images'] = xarModAPIFunc('images','admin','getimages',
                                    $data);

    $data['basedirs'] = $basedirs;

    // Check if we need to do anything special here
    if (!xarVarFetch('action','str:1:',$action,'',XARVAR_NOT_REQUIRED)) return;

    // Find the right uploaded image
    if (!empty($action) && !empty($fileId)) {
        $found = '';

        // if we're dealing with a list of fileId's, make sure they exist
        if (is_array($fileId) && $action == 'resize') {
            $found = array();
            foreach ($fileId as $id) {
                if (!empty($data['images'][$id])) {
                    $found[] = $id;
                }
            }
            if (count($found) > 0) {
                $action = 'resizelist';
            } else {
                $action = '';
            }

        // if we're dealing with an individual fileId, get some additional information
        } elseif (is_string($fileId) && !empty($data['images'][$fileId])) {
            $found = $data['images'][$fileId];
        }
    }

    if (!empty($action) && !empty($found)) {
        switch ($action) {
            case 'view':
                $data['selimage'] = $found;
                $data['action'] = 'view';
                return $data;

            case 'resize':
                if (!xarVarFetch('width', 'int:1:',$width, NULL, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('height','int:1:',$height,NULL,XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('replace','checkbox',$replace,'',XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;
                if (!empty($confirm) && (!empty($width) || !empty($height))) {
                    if (!xarSecConfirmAuthKey()) return;
                    if (!empty($replace) && !empty($found['fileLocation'])) {
                        $location = xarModAPIFunc('images','admin','replace_image',
                                                  array('fileLocation' => $found['fileLocation'],
                                                        'width'  => (!empty($width) ? $width . 'px' : NULL),
                                                        'height' => (!empty($height) ? $height . 'px' : NULL)));
                        if (!$location) return;
                        // Redirect to viewing the original image here (for now)
                        xarResponseRedirect(xarModURL('images', 'admin', 'browse',
                                                      array('action' => 'view',
                                                            'bid' => $baseId,
                                                            'fid' => $found['fileId'])));
                    } else {
                        $location = xarModAPIFunc('images','admin','resize_image',
                                                  array('fileLocation' => $found['fileLocation'],
                                                        'width'  => (!empty($width) ? $width . 'px' : NULL),
                                                        'height' => (!empty($height) ? $height . 'px' : NULL)));
                        if (!$location) return;
                        // Redirect to viewing the derivative image here (for now)
                        xarResponseRedirect(xarModURL('images', 'admin', 'derivatives',
                                                      array('action' => 'view',
                                                            'fileId' => md5($location))));
                    }
                    return true;
                }
                $data['selimage'] = $found;
                if (empty($width) && empty($height)) {
                    $data['width'] = $found['width'];
                    $data['height'] = $found['height'];
                } else {
                    $data['width'] = $width;
                    $data['height'] = $height;
                }
                if (empty($replace)) {
                    $data['replace'] = '';
                } else {
                    $data['replace'] = 'checked="checked"';
                }
                $data['action'] = 'resize';
                $data['authid'] = xarSecGenAuthKey();
                return $data;

            case 'delete':
                if (!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;
                if (!empty($confirm)) {
                    if (!xarSecConfirmAuthKey()) return;
                    // delete the server image now
                    @unlink($found['fileLocation']);
                    xarResponseRedirect(xarModURL('images', 'admin', 'browse'));
                    return true;
                }
                $data['selimage'] = $found;
                $data['action'] = 'delete';
                $data['authid'] = xarSecGenAuthKey();
                return $data;

            default:
                break;
        }
    }

    if (!xarVarFetch('sort','enum:name:width:height:size:time',$sort,'',XARVAR_NOT_REQUIRED)) return;
    switch ($sort) {
        case 'name':
            //$strsort = 'fileName';
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
        usort($data['images'], $sortfunc);
    } elseif (!empty($strsort)) {
        $sortfunc = create_function('$a,$b','return strcmp($a["'.$strsort.'"], $b["'.$strsort.'"]);');
        usort($data['images'], $sortfunc);
    }

    // Return the template variables defined in this function
    return $data;
}
?>
