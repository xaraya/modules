<?php
/**
 * View a list of derivative images
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
 * View a list of derivative images (thumbnails, resized etc.)
 * @author mikespub
 * @todo add startnum and numitems support
 */
function images_admin_derivatives()
{
    // Security check
    if (!xarSecurityCheck('AdminImages')) return;

    $data = array();

    // Note: fileId is an MD5 hash of the derivative image location here
    if (!xarVarFetch('fileId','str:1:',$fileId,'',XARVAR_NOT_REQUIRED)) return;
    $data['fileId'] = $fileId;

    if (!xarVarFetch('startnum',    'int:0:',     $startnum,         NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('numitems',    'int:0:',     $numitems,         NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('sort','enum:name:width:height:size:time',$sort,'name',XARVAR_NOT_REQUIRED)) return;

    $data['startnum'] = $startnum;
    $data['numitems'] = $numitems;
    $data['sort'] = ($sort != 'name') ? $sort : null;

    // Check if we can cache the image list
    $data['cacheExpire'] = xarModVars::get('images', 'file.cache-expire');

    $data['thumbsdir'] = xarModVars::get('images', 'path.derivative-store');

    $data['pager'] = '';
    if (!empty($fileId)) {
        $params = $data;
        $data['images'] = xarModAPIFunc('images','admin','getderivatives',
                                        $params);
    } else {
        $params = $data;
        if (!isset($numitems)) {
            $params['numitems'] = xarModVars::get('images','view.itemsperpage');
        }
        // Check if we need to refresh the cache anyway
        if (!xarVarFetch('refresh',     'int:0:',     $refresh,          NULL, XARVAR_DONT_SET)) return;
        $params['cacheRefresh'] = $refresh;

        $data['images'] = xarModAPIFunc('images','admin','getderivatives',
                                        $params);

        // Note: this must be called *after* getderivatives() to benefit from caching
        $countitems = xarModAPIFunc('images','admin','countderivatives',
                                    $params);

        // Add pager
        if (!empty($params['numitems']) && $countitems > $params['numitems']) {
            $data['pager'] = xarTplGetPager($startnum,
                                            $countitems,
                                            xarModURL('images', 'admin', 'derivatives',
                                                      array('startnum' => '%%',
                                                            'numitems' => $data['numitems'],
                                                            'sort'     => $data['sort'])),
                                            $params['numitems']);
        }
    }

    // Check if we need to do anything special here
    if (!xarVarFetch('action','str:1:',$action,'',XARVAR_NOT_REQUIRED)) return;

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
                if (!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;
                if (!empty($confirm)) {
                    if (!xarSecConfirmAuthKey()) return;
                    // delete the derivative image now
                    @unlink($found['fileLocation']);
                    xarResponse::Redirect(xarModURL('images', 'admin', 'derivatives'));
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

    // Return the template variables defined in this function
    return $data;
}
?>
