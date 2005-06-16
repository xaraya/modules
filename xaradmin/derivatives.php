<?php

/**
 * View a list of derivative images (thumbnails, resized etc.)
 *
 * @todo add startnum and numitems support
 */
function images_admin_derivatives()
{
    // Security check
    if (!xarSecurityCheck('AdminImages')) return;

    $data = array();
    $data['thumbsdir'] = xarModGetVar('images', 'path.derivative-store');
    $data['images'] = xarModAPIFunc('images','admin','getderivatives',
                                    array('thumbsdir' => $data['thumbsdir']));

    // Check if we need to do anything special here
    if (!xarVarFetch('action','str:1:',$action,'',XARVAR_NOT_REQUIRED)) return;

    // Note: fileId is an MD5 hash of the derivative image location here
    if (!xarVarFetch('fileId','str:1:',$fileId,'',XARVAR_NOT_REQUIRED)) return;

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
                    xarResponseRedirect(xarModURL('images', 'admin', 'derivatives'));
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
