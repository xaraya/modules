<?php

sys::import('modules.base.class.pager');

/**
 * Images Module
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
 * View a list of uploaded images (managed by the uploads module)
 *
 * @todo add startnum and numitems support
 */
function images_admin_uploads($args)
{
    extract($args);

    // Security check for images
    if (!xarSecurity::check('AdminImages')) {
        return;
    }

    // Security check for uploads
    if (!xarMod::isAvailable('uploads') || !xarSecurity::check('AdminUploads')) {
        return;
    }

    // Note: fileId is the uploads fileId here, or an array of uploads fileId's
    if (!xarVar::fetch('fileId', 'isset', $fileId, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!empty($fileId) && is_array($fileId)) {
        $fileId = array_keys($fileId);
    }

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

    $data = [];
    $data['startnum'] = $startnum;
    $data['numitems'] = $numitems;
    $data['sort'] = ($sort != 'name') ? $sort : null;

    if (!isset($numitems)) {
        $numitems = xarModVars::get('images', 'view.itemsperpage');
    }

    $data['pager'] = '';
    if (!empty($fileId)) {
        $data['images'] = xarMod::apiFunc(
            'images',
            'admin',
            'getuploads',
            ['fileId'   => $fileId]
        );
    } elseif (!empty($getnext)) {
        $data['images'] = xarMod::apiFunc(
            'images',
            'admin',
            'getuploads',
            ['getnext'  => $getnext]
        );
        if (!empty($data['images']) && count($data['images']) == 1) {
            $image = array_pop($data['images']);
            xarController::redirect(xarController::URL(
                'images',
                'admin',
                'uploads',
                ['action' => empty($action) ? 'view' : $action,
                                                'fileId' => $image['fileId'], ]
            ));
            return true;
        }
    } elseif (!empty($getprev)) {
        $data['images'] = xarMod::apiFunc(
            'images',
            'admin',
            'getuploads',
            ['getprev'  => $getprev]
        );
        if (!empty($data['images']) && count($data['images']) == 1) {
            $image = array_pop($data['images']);
            xarController::redirect(xarController::URL(
                'images',
                'admin',
                'uploads',
                ['action' => empty($action) ? 'view' : $action,
                                                'fileId' => $image['fileId'], ]
            ));
            return true;
        }
    } else {
        $data['images'] = xarMod::apiFunc(
            'images',
            'admin',
            'getuploads',
            ['startnum' => $startnum,
                                              'numitems' => $numitems,
                                              'sort'     => $sort, ]
        );
        $countitems = xarMod::apiFunc('images', 'admin', 'countuploads');

        // Add pager
        if (!empty($numitems) && $countitems > $numitems) {
            $data['pager'] = xarTplPager::getPager(
                $startnum,
                $countitems,
                xarController::URL(
                    'images',
                    'admin',
                    'uploads',
                    ['startnum' => '%%',
                                                            'numitems' => $data['numitems'],
                                                            'sort'     => $data['sort'], ]
                ),
                $numitems
            );
        }
    }

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

    // Find the right uploaded image
    if (!empty($action) && !empty($fileId)) {
        $found = '';

        // if we're dealing with a list of fileId's, make sure they exist
        if (is_array($fileId) && ($action == 'resizelist' || $action == 'processlist')) {
            $found = [];
            foreach ($fileId as $id) {
                if (!empty($data['images'][$id])) {
                    $found[] = $id;
                }
            }
            if (count($found) < 1) {
                $action = '';
            }

        // if we're dealing with an individual fileId, get some additional information
        } elseif (is_numeric($fileId) && !empty($data['images'][$fileId])) {
            $found = $data['images'][$fileId];
            // Get derivative images for this image
            if (!empty($found['fileHash'])) {
                if (file_exists($found['fileLocation'])) {
                    $found['derivatives'] = xarMod::apiFunc(
                        'images',
                        'admin',
                        'getderivatives',
                        ['fileLocation' => $found['fileLocation']]
                    );
                } else {
                    // the file is probably stored in the database, so we pass the fileId here
                    $found['derivatives'] = xarMod::apiFunc(
                        'images',
                        'admin',
                        'getderivatives',
                        ['fileLocation' => $found['fileId']]
                    );
                }
            }
            // Get known associations for this image (currently unused)
            $found['associations'] = xarMod::apiFunc(
                'uploads',
                'user',
                'db_get_associations',
                ['fileId' => $found['fileId']]
            );
            $found['moditems'] = [];
            if (!empty($found['associations'])) {
                $modlist = [];
                foreach ($found['associations'] as $assoc) {
                    // uploads 0.9.8 format
                    if (isset($assoc['objectId'])) {
                        if (!isset($modlist[$assoc['modId']])) {
                            $modlist[$assoc['modId']] = [];
                        }
                        if (!isset($modlist[$assoc['modId']][$assoc['itemType']])) {
                            $modlist[$assoc['modId']][$assoc['itemType']] = [];
                        }
                        $modlist[$assoc['modId']][$assoc['itemType']][$assoc['objectId']] = 1;

                    // uploads_guimods 0.9.9+ format
                    } elseif (isset($assoc['itemid'])) {
                        if (!isset($modlist[$assoc['modid']])) {
                            $modlist[$assoc['modid']] = [];
                        }
                        if (!isset($modlist[$assoc['modid']][$assoc['itemtype']])) {
                            $modlist[$assoc['modid']][$assoc['itemtype']] = [];
                        }
                        $modlist[$assoc['modid']][$assoc['itemtype']][$assoc['itemid']] = 1;
                    }
                }
                foreach ($modlist as $modid => $itemtypes) {
                    $modinfo = xarMod::getInfo($modid);
                    // Get the list of all item types for this module (if any)
                    $mytypes = xarMod::apiFunc(
                        $modinfo['name'],
                        'user',
                        'getitemtypes',
                        // don't throw an exception if this function doesn't exist
                        [],
                        0
                    );
                    foreach ($itemtypes as $itemtype => $items) {
                        $moditem = [];
                        $moditem['module'] = $modinfo['name'];
                        $moditem['modid'] = $modid;
                        $moditem['itemtype'] = $itemtype;
                        if ($itemtype == 0) {
                            $moditem['modname'] = ucwords($modinfo['displayname']);
                        //    $moditem['modlink'] = xarController::URL($modinfo['name'],'user','main');
                        } else {
                            if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                                $moditem['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
                            //    $moditem['modlink'] = $mytypes[$itemtype]['url'];
                            } else {
                                $moditem['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                                //    $moditem['modlink'] = xarController::URL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
                            }
                        }
                        $itemids = array_keys($items);
                        $itemlinks = xarMod::apiFunc(
                            $modinfo['name'],
                            'user',
                            'getitemlinks',
                            ['itemtype' => $itemtype,
                                                         'itemids' => $itemids, ],
                            0
                        ); // don't throw an exception here
                        $moditem['items'] = [];
                        foreach ($itemids as $itemid) {
                            if (isset($itemlinks[$itemid])) {
                                $moditem['items'][$itemid]['link'] = $itemlinks[$itemid]['url'];
                                $moditem['items'][$itemid]['title'] = $itemlinks[$itemid]['label'];
                            } else {
                                $moditem['items'][$itemid]['link'] = '';
                                $moditem['items'][$itemid]['title'] = $itemid;
                            }
                        }
                        $found['moditems'][] = $moditem;
                    }
                }
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
                            ['fileId' => $found['fileId'],
                                                        'width'  => (!empty($width) ? $width . 'px' : null),
                                                        'height' => (!empty($height) ? $height . 'px' : null), ]
                        );
                        if (!$location) {
                            return;
                        }
                        // Redirect to viewing the original image here (for now)
                        xarController::redirect(xarController::URL(
                            'images',
                            'admin',
                            'uploads',
                            ['action' => 'view',
                                                            'fileId' => $found['fileId'], ]
                        ));
                    } else {
                        $location = xarMod::apiFunc(
                            'images',
                            'admin',
                            'resize_image',
                            ['fileId' => $found['fileId'],
                                                        'width'  => (!empty($width) ? $width . 'px' : null),
                                                        'height' => (!empty($height) ? $height . 'px' : null), ]
                        );
                        if (!$location) {
                            return;
                        }
                        // Redirect to viewing the derivative image here (for now)
                        xarController::redirect(xarController::URL(
                            'images',
                            'admin',
                            'derivatives',
                            ['action' => 'view',
                                                            'fileId' => md5($location), ]
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
                    // delete the uploaded image now
                    $fileList = [$fileId => $found];
                    $result = xarMod::apiFunc('uploads', 'user', 'purge_files', ['fileList' => $fileList]);
                    if (!$result) {
                        return;
                    }
                    xarController::redirect(xarController::URL('images', 'admin', 'uploads'));
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
                    $data['selected'] = $found;
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
                    foreach ($found as $id) {
                        $location = xarMod::apiFunc(
                            'images',
                            'admin',
                            'replace_image',
                            ['fileId' => $id,
                                                        'width'  => (!empty($width) ? $width . 'px' : null),
                                                        'height' => (!empty($height) ? $height . 'px' : null), ]
                        );
                        if (!$location) {
                            return;
                        }
                    }
                    // Redirect to viewing the uploaded images here (for now)
                    xarController::redirect(xarController::URL(
                        'images',
                        'admin',
                        'uploads',
                        ['sort' => 'time']
                    ));
                } else {
                    foreach ($found as $id) {
                        $location = xarMod::apiFunc(
                            'images',
                            'admin',
                            'resize_image',
                            ['fileId' => $id,
                                                        'width'  => (!empty($width) ? $width . 'px' : null),
                                                        'height' => (!empty($height) ? $height . 'px' : null), ]
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
                        ['sort'    => 'time',
                                                        // we need to refresh the cache here
                                                        'refresh' => 1, ]
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
                    $data['selected'] = $found;
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
                foreach ($found as $id) {
                    if (empty($data['images'][$id])) {
                        continue;
                    }
                    $location = xarMod::apiFunc(
                        'images',
                        'admin',
                        'process_image',
                        ['image'   => $data['images'][$id],
                                                    'saveas'  => $saveas,
                                                    'setting' => $setting, ]
                    );
                    if (!$location) {
                        return;
                    }
                }

                switch ($saveas) {
                    case 1: // [image]_new.[ext]
                        // Redirect to viewing the uploaded images here (for now)
                        xarController::redirect(xarController::URL(
                            'images',
                            'admin',
                            'uploads',
                            ['sort' => 'time']
                        ));
                        break;

                    case 2: // replace
                        // Redirect to viewing the uploaded images here (for now)
                        xarController::redirect(xarController::URL(
                            'images',
                            'admin',
                            'uploads',
                            ['sort' => 'time']
                        ));
                        break;

                    case 0: // derivative
                    default:
                        // Redirect to viewing the derivative images here (for now)
                        xarController::redirect(xarController::URL(
                            'images',
                            'admin',
                            'derivatives',
                            ['sort'    => 'time',
                                                            // we need to refresh the cache here
                                                            'refresh' => 1, ]
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
