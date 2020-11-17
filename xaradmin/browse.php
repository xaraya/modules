<?php
sys::import('modules.base.class.pager');

/**
 * View a list of server images
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
/**
 * View a list of server images
 *
 * @todo add startnum and numitems support
 */
function images_admin_browse()
{
    // Security check
    if (!xarSecurity::check('AdminImages')) {
        return;
    }

    // Note: fileId is a base 64 encode of the image location here, or an array of fileId's
    if (!xarVar::fetch('fid', 'isset', $fileId, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!empty($fileId) && is_array($fileId)) {
        $fileId = array_keys($fileId);
    }
    if (empty($fileId)) {
        $fileId = null;
    }

    // Get the base directories configured for server images
    $basedirs = xarMod::apiFunc('images', 'user', 'getbasedirs');

    if (!xarVar::fetch('bid', 'isset', $baseId, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (empty($baseId) || empty($basedirs[$baseId])) {
        $data = $basedirs[0]; // themes directory
        $baseId = null;
    } else {
        $data = $basedirs[$baseId];
    }
    $data['baseId'] = $baseId;
    $data['fileId'] = $fileId;

    if (!xarVar::fetch('startnum', 'int:0:', $startnum, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('numitems', 'int:0:', $numitems, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('sort', 'enum:name:type:width:height:size:time', $sort, 'name', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('action', 'str:1:', $action, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('getnext', 'str:1:', $getnext, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('getprev', 'str:1:', $getprev, null, xarVar::DONT_SET)) {
        return;
    }

    $data['startnum'] = $startnum;
    $data['numitems'] = $numitems;
    $data['sort'] = ($sort != 'name') ? $sort : null;
    $data['getnext'] = $getnext;
    $data['getprev'] = $getprev;

    // Check if we can cache the image list
    $data['cacheExpire'] = xarModVars::get('images', 'file.cache-expire');

    $data['pager'] = '';
    if (!empty($fileId)) {
        $data['images'] = xarMod::apiFunc(
            'images',
            'admin',
            'getimages',
            $data
        );
    } else {
        $params = $data;
        if (!isset($numitems)) {
            $params['numitems'] = xarModVars::get('images', 'view.itemsperpage');
        }
        // Check if we need to refresh the cache anyway
        if (!xarVar::fetch('refresh', 'int:0:', $refresh, null, xarVar::DONT_SET)) {
            return;
        }
        $params['cacheRefresh'] = $refresh;

        $data['images'] = xarMod::apiFunc(
            'images',
            'admin',
            'getimages',
            $params
        );

        if ((!empty($getnext) || !empty($getprev)) &&
            !empty($data['images']) && count($data['images']) == 1) {
            $image = array_pop($data['images']);
            xarController::redirect(xarController::URL(
                'images',
                'admin',
                'browse',
                array('action' => empty($action) ? 'view' : $action,
                                                'bid' => $baseId,
                                                'fid' => $image['fileId'])
            ));
            return true;
        }

        // Note: this must be called *after* getimages() to benefit from caching
        $countitems = xarMod::apiFunc(
            'images',
            'admin',
            'countimages',
            $params
        );

        // Add pager
        if (!empty($params['numitems']) && $countitems > $params['numitems']) {
            $data['pager'] = xarTplPager::getPager(
                $startnum,
                $countitems,
                xarController::URL(
                                                'images',
                                                'admin',
                                                'browse',
                                                array('bid'      => $baseId,
                                                            'startnum' => '%%',
                                                            'numitems' => $data['numitems'],
                                                            'sort'     => $data['sort'])
                                            ),
                $params['numitems']
            );
        }
    }

    $data['basedirs'] = $basedirs;

    // Get the pre-defined settings for phpThumb
    $data['settings'] = xarMod::apiFunc('images', 'user', 'getsettings');

    // Check if we need to do anything special here
    if (!xarVar::fetch('processlist', 'str:1:', $processlist, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('resizelist', 'str:1:', $resizelist, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!empty($processlist)) {
        $action = 'processlist';
    } elseif (!empty($resizelist)) {
        $action = 'resizelist';
    }

    // Find the right server image
    if (!empty($action) && !empty($fileId)) {
        $found = '';

        // if we're dealing with a list of fileId's, make sure they exist
        if (is_array($fileId) && ($action == 'resizelist' || $action == 'processlist')) {
            $found = array();
            foreach ($fileId as $id) {
                if (!empty($data['images'][$id])) {
                    $found[$id] = $data['images'][$id];
                }
            }
            if (count($found) < 1) {
                $action = '';
            }

            // if we're dealing with an individual fileId, get some additional information
        } elseif (is_string($fileId) && !empty($data['images'][$fileId])) {
            $found = $data['images'][$fileId];
            // Get derivative images for this image
            if (file_exists($found['fileLocation'])) {
                $found['derivatives'] = xarMod::apiFunc(
                    'images',
                    'admin',
                    'getderivatives',
                    array('fileLocation' => $found['fileLocation'])
                );
            }
        }
    }

    if (!empty($action) && !empty($found)) {
        switch ($action) {
            case 'view':
                $data['selimage'] = $found;
                $data['action'] = 'view';
                return $data;

            case 'resize':
                if (!xarVar::fetch('width', 'int:1:', $width, null, xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (!xarVar::fetch('height', 'int:1:', $height, null, xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (!xarVar::fetch('replace', 'int:0:1', $replace, 0, xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (!xarVar::fetch('confirm', 'str:1:', $confirm, '', xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (!empty($confirm) && (!empty($width) || !empty($height))) {
                    if (!xarSec::confirmAuthKey()) {
                        return;
                    }
                    if (!empty($replace) && !empty($found['fileLocation'])) {
                        $location = xarMod::apiFunc(
                            'images',
                            'admin',
                            'replace_image',
                            array('fileLocation' => $found['fileLocation'],
                                                        'width'  => (!empty($width) ? $width . 'px' : null),
                                                        'height' => (!empty($height) ? $height . 'px' : null))
                        );
                        if (!$location) {
                            return;
                        }
                        // Redirect to viewing the original image here (for now)
                        xarController::redirect(xarController::URL(
                            'images',
                            'admin',
                            'browse',
                            array('action' => 'view',
                                                            'bid' => $baseId,
                                                            'fid' => $found['fileId'])
                        ));
                    } else {
                        $location = xarMod::apiFunc(
                            'images',
                            'admin',
                            'resize_image',
                            array('fileLocation' => $found['fileLocation'],
                                                        'width'  => (!empty($width) ? $width . 'px' : null),
                                                        'height' => (!empty($height) ? $height . 'px' : null))
                        );
                        if (!$location) {
                            return;
                        }
                        // Redirect to viewing the derivative image here (for now)
                        xarController::redirect(xarController::URL(
                            'images',
                            'admin',
                            'derivatives',
                            array('action' => 'view',
                                                            'fileId' => md5($location))
                        ));
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
                    $data['replace'] = 0;
                } else {
                    $data['replace'] = 1;
                }
                $data['action'] = 'resize';
                $data['authid'] = xarSec::genAuthKey();
                return $data;

            case 'delete':
                if (!xarVar::fetch('confirm', 'str:1:', $confirm, '', xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (!empty($confirm)) {
                    if (!xarSec::confirmAuthKey()) {
                        return;
                    }
                    // delete the server image now
                    @unlink($found['fileLocation']);
                    xarController::redirect(xarController::URL('images', 'admin', 'browse'));
                    return true;
                }
                $data['selimage'] = $found;
                $data['action'] = 'delete';
                $data['authid'] = xarSec::genAuthKey();
                return $data;

            case 'resizelist':
                if (!xarVar::fetch('width', 'int:1:', $width, null, xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (!xarVar::fetch('height', 'int:1:', $height, null, xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (!xarVar::fetch('replace', 'int:0:1', $replace, 0, xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (!xarVar::fetch('confirm', 'str:1:', $confirm, '', xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (empty($confirm) || (empty($width) && empty($height))) {
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
                    $data['authid'] = xarSec::genAuthKey();
                    return $data;
                }

                if (!xarSec::confirmAuthKey()) {
                    return;
                }
                if (!empty($replace)) {
                    foreach ($found as $id => $info) {
                        if (empty($info['fileLocation'])) {
                            continue;
                        }
                        $location = xarMod::apiFunc(
                            'images',
                            'admin',
                            'replace_image',
                            array('fileLocation' => $info['fileLocation'],
                                                        'width'  => (!empty($width) ? $width . 'px' : null),
                                                        'height' => (!empty($height) ? $height . 'px' : null))
                        );
                        if (!$location) {
                            return;
                        }
                    }
                    // Redirect to viewing the server images here (for now)
                    xarController::redirect(xarController::URL(
                        'images',
                        'admin',
                        'browse',
                        array('bid'     => $baseId,
                                                        'sort'    => 'time',
                                                        // we need to refresh the cache here
                                                        'refresh' => 1)
                    ));
                } else {
                    foreach ($found as $id => $info) {
                        if (empty($info['fileLocation'])) {
                            continue;
                        }
                        $location = xarMod::apiFunc(
                            'images',
                            'admin',
                            'resize_image',
                            array('fileLocation' => $info['fileLocation'],
                                                        'width'  => (!empty($width) ? $width . 'px' : null),
                                                        'height' => (!empty($height) ? $height . 'px' : null))
                        );
                        if (!$location) {
                            return;
                        }
                    }
                    // Redirect to viewing the derivative images here (for now)
                    xarController::redirect(xarController::URL(
                        'images',
                        'admin',
                        'derivatives',
                        array('sort'    => 'time',
                                                        // we need to refresh the cache here
                                                        'refresh' => 1)
                    ));
                }
                return true;

            case 'processlist':
                if (!xarVar::fetch('saveas', 'int:0:2', $saveas, 0, xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (!xarVar::fetch('setting', 'str:1:', $setting, null, xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (!xarVar::fetch('confirm', 'str:1:', $confirm, '', xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (empty($confirm) || empty($setting) || empty($data['settings'][$setting])) {
                    $data['selected'] = array_keys($found);
                    if (empty($setting) || empty($data['settings'][$setting])) {
                        $data['setting'] = '';
                        $data['action'] = '';
                    } else {
                        $data['setting'] = $setting;
                        $data['action'] = 'processlist';
                    }
                    if (empty($saveas)) {
                        $data['saveas'] = 0;
                    } else {
                        $data['saveas'] = $saveas;
                    }
                    $data['authid'] = xarSec::genAuthKey();
                    return $data;
                }

                if (!xarSec::confirmAuthKey()) {
                    return;
                }

                // Process images
                foreach ($found as $id => $info) {
                    if (empty($info['fileLocation'])) {
                        continue;
                    }
                    $location = xarMod::apiFunc(
                        'images',
                        'admin',
                        'process_image',
                        array('image'   => $info,
                                                    'saveas'  => $saveas,
                                                    'setting' => $setting)
                    );
                    if (!$location) {
                        return;
                    }
                }

                switch ($saveas) {
                    case 1: // [image]_new.[ext]
                        // Redirect to viewing the server images here (for now)
                        xarController::redirect(xarController::URL(
                            'images',
                            'admin',
                            'browse',
                            array('bid'     => $baseId,
                                                            'sort'    => 'time',
                                                            // we need to refresh the cache here
                                                            'refresh' => 1)
                        ));
                        break;

                    case 2: // replace
                        // Redirect to viewing the server images here (for now)
                        xarController::redirect(xarController::URL(
                            'images',
                            'admin',
                            'browse',
                            array('bid'     => $baseId,
                                                            'sort'    => 'time',
                                                            // we need to refresh the cache here
                                                            'refresh' => 1)
                        ));
                        break;

                    case 0: // derivative
                    default:
                        // Redirect to viewing the derivative images here (for now)
                        xarController::redirect(xarController::URL(
                            'images',
                            'admin',
                            'derivatives',
                            array('sort'    => 'time',
                                                            // we need to refresh the cache here
                                                            'refresh' => 1)
                        ));
                        break;
                }
                return true;

            default:
                break;
        }
    }

    // Return the template variables defined in this function
    return $data;
}
