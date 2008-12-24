<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
function uploads_userapi_process_filters( $args )
{

    extract ( $args );
    /**
     *  Set up the filter data for the template to use
     */

    if (!isset($storeOptions)) {
        $storeOptions = TRUE;
    }

    $options   =  unserialize(xarModVars::get('uploads', 'view.filter'));

    $data      =  $options['data'];
    $filter    =  $options['filter'];
    $mimetypes =& $data['filters']['mimetypes'];
    $subtypes  =& $data['filters']['subtypes'];
    $statuses  =& $data['filters']['status'];

    $data['filters']['inverse'] = isset($inverse) ? $inverse : FALSE;
    $filter['inverse']  = isset($inverse) ? $inverse : FALSE;

    unset($options);
    /**
     *  Grab the mimetypes and setup the selected one
     */
    if (isset($mimetype) && $mimetype > 0) {
        $selected_mimetype = xarModAPIFunc('mime','user','get_type', array('typeId' => $mimetype));
    }

    // if selected mimetype isn't set, empty or has an array count of
    // zero, then we set
    if (isset($selected_mimetype) && count($selected_mimetype)) {
        $mimetypes[$mimetype]['selected'] = TRUE;
    } else {
        $mimetypes[0]['selected'] = TRUE;
    }

    /**
     *  Grab the subtypes (if necessary) and setup the selected subtype
     */
    if (isset($selected_mimetype)) {
        if (isset($subtype) && $subtype > 0) {
            $selected_subtype = xarModAPIFunc('mime','user','get_subtype', array('subtypeId' => $subtype));
        }

        // add the rest of the types to the array
        // array returns is in form of: array[typeId]{[subtypeId], [subtypeName]}
        $subtypes = $subtypes + xarModAPIFunc('mime','user','getall_subtypes', array('typeId' => $selected_mimetype['typeId']));

        // if selected subtype isn't set, empty or has an array count of
        // zero, then we set
        if (isset($selected_subtype['typeId']) && $selected_subtype['typeId'] == $selected_mimetype['typeId']) {
                $subtypes[$subtype]['selected'] = TRUE;
        } else {
            $subtypes[0]['selected'] = TRUE;
        }
    } else {
        $subtypes[0]['selected'] = TRUE;
    }
    unset($subtypes);
    unset($mimetypes);

    /**
     *  Set up the actual filter that will be passed to the api get function
     */

    if (isset($selected_mimetype)) {
        if (isset($selected_subtype)) {
            $filter['fileType'] = strtolower($selected_mimetype['typeName']) . '/' . strtolower($selected_subtype['subtypeName']);
        } else {
            $filter['fileType'] = strtolower($selected_mimetype['typeName']) . '/%';
        }

    } else {
        $filter['fileType'] = '%';
    }

    if (!isset($status)) {
        $status = '';
    }
    /**
     *  Set up the MIME subtype filter
     */

    switch($status) {
        case _UPLOADS_STATUS_REJECTED:
            $filter['fileStatus'] = _UPLOADS_STATUS_REJECTED;
            $statuses[_UPLOADS_STATUS_REJECTED]['selected'] = TRUE;
            break;
        case _UPLOADS_STATUS_SUBMITTED:
            $filter['fileStatus'] = _UPLOADS_STATUS_SUBMITTED;
            $statuses[_UPLOADS_STATUS_SUBMITTED]['selected'] = TRUE;
            break;
        case _UPLOADS_STATUS_APPROVED:
            $filter['fileStatus'] = _UPLOADS_STATUS_APPROVED;
            $statuses[_UPLOADS_STATUS_APPROVED]['selected'] = TRUE;
            break;
        case 0:
            $filter['fileStatus'] = '';
            $statuses[0]['selected'] = TRUE;
            break;
        default:
            $filter['fileStatus'] = _UPLOADS_STATUS_SUBMITTED;
            $statuses[_UPLOADS_STATUS_SUBMITTED]['selected'] = TRUE;
            break;
    }
    unset($statuses);
    $data['catid'] = isset($catid) ? $catid : null;
    $filter['catid'] = isset($catid) ? $catid : null;
    $filterInfo = array('data' => $data, 'filter' => $filter);


    if ($storeOptions) {
        // Save the filter settings for later use
        xarModUserVars::set('uploads','view.filter', serialize($filterInfo));
    }

    return $filterInfo;
}

?>
