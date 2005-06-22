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

    if (!xarVarFetch('startnum',    'int:0:',     $startnum,         NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('numitems',    'int:0:',     $numitems,         NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('sort','enum:name:type:width:height:size:time',$sort,'name',XARVAR_NOT_REQUIRED)) return;

    $data['startnum'] = $startnum;
    $data['numitems'] = $numitems;
    $data['sort'] = ($sort != 'name') ? $sort : null;

    // Check if we can cache the image list
    $data['cacheExpire'] = xarModGetVar('images', 'file.cache-expire');

    $data['pager'] = '';
    if (!empty($fileId)) {
        $data['images'] = xarModAPIFunc('images','admin','getimages',
                                        $data);

    } else {
        $params = $data;
        if (!isset($numitems)) {
            $params['numitems'] = xarModGetVar('images','view.itemsperpage');
        }
        // Check if we need to refresh the cache anyway
        if (!xarVarFetch('refresh',     'int:0:',     $refresh,          NULL, XARVAR_DONT_SET)) return;
        $params['cacheRefresh'] = $refresh;

        $data['images'] = xarModAPIFunc('images','admin','getimages',
                                        $params);

        // Note: this must be called *after* getimages() to benefit from caching
        $countitems = xarModAPIFunc('images','admin','countimages',
                                    $params);

        // Add pager
        if (!empty($params['numitems']) && $countitems > $params['numitems']) {
            $data['pager'] = xarTplGetPager($startnum,
                                            $countitems,
                                            xarModURL('images', 'admin', 'browse',
                                                      array('bid'      => $baseId,
                                                            'startnum' => '%%',
                                                            'numitems' => $data['numitems'],
                                                            'sort'     => $data['sort'])),
                                            $params['numitems']);
        }
    }

    $data['basedirs'] = $basedirs;

    // Check if we need to do anything special here
    if (!xarVarFetch('action','str:1:',$action,'',XARVAR_NOT_REQUIRED)) return;

    // Find the right server image
    if (!empty($action) && !empty($fileId)) {
        $found = '';

        // if we're dealing with a list of fileId's, make sure they exist
        if (is_array($fileId) && $action == 'resize') {
            $found = array();
            foreach ($fileId as $id) {
                if (!empty($data['images'][$id])) {
                    $found[$id] = $data['images'][$id];
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

            case 'resizelist':
                if (!xarVarFetch('width', 'int:1:',$width, NULL, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('height','int:1:',$height,NULL,XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('replace','checkbox',$replace,'',XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;
                if (!empty($confirm) && (!empty($width) || !empty($height))) {
                    if (!xarSecConfirmAuthKey()) return;
                    if (!empty($replace)) {
                        foreach ($found as $id => $info) {
                            if (empty($info['fileLocation'])) continue;
                            $location = xarModAPIFunc('images','admin','replace_image',
                                                      array('fileLocation' => $info['fileLocation'],
                                                            'width'  => (!empty($width) ? $width . 'px' : NULL),
                                                            'height' => (!empty($height) ? $height . 'px' : NULL)));
                            if (!$location) return;
                        }
                        // Redirect to viewing the server images here (for now)
                        xarResponseRedirect(xarModURL('images', 'admin', 'browse',
                                                      array('bid'     => $baseId,
                                                            'sort'    => 'time',
                                                            // we need to refresh the cache here
                                                            'refresh' => 1)));
                    } else {
                        foreach ($found as $id => $info) {
                            if (empty($info['fileLocation'])) continue;
                            $location = xarModAPIFunc('images','admin','resize_image',
                                                      array('fileLocation' => $info['fileLocation'],
                                                            'width'  => (!empty($width) ? $width . 'px' : NULL),
                                                            'height' => (!empty($height) ? $height . 'px' : NULL)));
                            if (!$location) return;
                        }
                        // Redirect to viewing the derivative images here (for now)
                        xarResponseRedirect(xarModURL('images', 'admin', 'derivatives',
                                                      array('sort' => 'time')));
                    }
                    return true;
                }
                $data['selected'] = array_keys($found);
                if (empty($width) && empty($height)) {
                    $data['width'] = '';
                    $data['height'] = '';
                    $data['action'] = '';
                } else {
                    $data['width'] = $width;
                    $data['height'] = $height;
                    $data['action'] = 'resizelist';
                }
                if (empty($replace)) {
                    $data['replace'] = '';
                } else {
                    $data['replace'] = '1';
                }
                $data['authid'] = xarSecGenAuthKey();
                return $data;

            default:
                break;
        }
    }

    // Return the template variables defined in this function
    return $data;
}
?>
