<?php
sys::import('modules.base.class.pager');

/**
 * View a list of derivative images
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
 * View a list of derivative images (thumbnails, resized etc.)
 * @author mikespub
 * @todo add startnum and numitems support
 */
function images_admin_derivatives()
{
    // Security check
    if (!xarSecurity::check('AdminImages')) {
        return;
    }

    $data = array();

    // Note: fileId is an MD5 hash of the derivative image location here
    if (!xarVar::fetch('fileId', 'str:1:', $fileId, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    $data['fileId'] = $fileId;

    if (!xarVar::fetch('startnum', 'int:0:', $startnum, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('numitems', 'int:0:', $numitems, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('sort', 'enum:name:width:height:size:time', $sort, 'name', xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['startnum'] = $startnum;
    $data['numitems'] = $numitems;
    $data['sort'] = ($sort != 'name') ? $sort : null;

    // Check if we can cache the image list
    $data['cacheExpire'] = xarModVars::get('images', 'file.cache-expire');

    $data['thumbsdir'] = xarModVars::get('images', 'path.derivative-store');

    $data['pager'] = '';
    if (!empty($fileId)) {
        $params = $data;
        $data['images'] = xarMod::apiFunc(
            'images',
            'admin',
            'getderivatives',
            $params
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
            'getderivatives',
            $params
        );

        // Note: this must be called *after* getderivatives() to benefit from caching
        $countitems = xarMod::apiFunc(
            'images',
            'admin',
            'countderivatives',
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
                                                'derivatives',
                                                array('startnum' => '%%',
                                                            'numitems' => $data['numitems'],
                                                            'sort'     => $data['sort'])
                                            ),
                $params['numitems']
            );
        }
    }

    // Check if we need to do anything special here
    if (!xarVar::fetch('action', 'str:1:', $action, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    // Find the right derivative image
    if (!empty($action) && !empty($fileId)) {
        $found = '';
        foreach ($data['images'] as $image) {
            if ($image['fileId'] == $fileId) {
                $found = $image;
                break;
            }
        }
    }

    if (!empty($action) && !empty($found)) {
        switch ($action) {
            case 'view':
                $data['selimage'] = $found;
                $data['action'] = 'view';
                return $data;

            case 'delete':
                if (!xarVar::fetch('confirm', 'str:1:', $confirm, '', xarVar::NOT_REQUIRED)) {
                    return;
                }
                if (!empty($confirm)) {
                    if (!xarSec::confirmAuthKey()) {
                        return;
                    }
                    // delete the derivative image now
                    @unlink($found['fileLocation']);
                    xarController::redirect(xarController::URL('images', 'admin', 'derivatives'));
                    return true;
                }
                $data['selimage'] = $found;
                $data['action'] = 'delete';
                $data['authid'] = xarSec::genAuthKey();
                return $data;

            default:
                break;
        }
    }

    // Return the template variables defined in this function
    return $data;
}
