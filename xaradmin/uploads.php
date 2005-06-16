<?php

/**
 * View a list of uploaded images (managed by the uploads module)
 *
 * @todo add startnum and numitems support
 */
function images_admin_uploads()
{
    // Security check for images
    if (!xarSecurityCheck('AdminImages')) return;

    // Security check for uploads
    if (!xarModIsAvailable('uploads') || !xarSecurityCheck('AdminUploads')) return;

    $data = array();

    $data['images'] = xarModAPIFunc('images','admin','getuploads');

    // Check if we need to do anything special here
    if (!xarVarFetch('action','str:1:',$action,'',XARVAR_NOT_REQUIRED)) return;

    // Note: fileId is the uploads fileId here
    if (!xarVarFetch('fileId','int:1:',$fileId,'',XARVAR_NOT_REQUIRED)) return;

    // Find the right uploaded image
    if (!empty($action) && !empty($fileId)) {
        $found = '';
        if (!empty($data['images'][$fileId])) {
            $found = $data['images'][$fileId];
            if (!empty($found['fileHash'])) {
                $found['derivatives'] = xarModAPIFunc('images','admin','getderivatives',
                                                      array('fileName' => $found['fileHash']));
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
                    // delete the uploaded image now
                    $fileList = array($fileId => $found);
                    $result = xarModAPIFunc('uploads', 'user', 'purge_files', array('fileList' => $fileList));
                    if (!$result) return;
                    xarResponseRedirect(xarModURL('images', 'admin', 'uploads'));
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

    if (!xarVarFetch('sort','enum:name:type:width:height:size:time',$sort,'name',XARVAR_NOT_REQUIRED)) return;
    switch ($sort) {
        case 'name':
            $strsort = 'fileName';
            break;
        case 'type':
            $strsort = 'fileType';
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
