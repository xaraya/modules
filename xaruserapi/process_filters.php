<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

function uploads_userapi_process_filters($args)
{
    extract($args);
    /**
     *  Set up the filter data for the template to use
     */

    if (!isset($storeOptions)) {
        $storeOptions = true;
    }

    $options   =  unserialize(xarModVars::get('uploads', 'view.filter'));

    $data      =  $options['data'];
    $filter    =  $options['filter'];
    $mimetypes =& $data['filters']['mimetypes'];
    $subtypes  =& $data['filters']['subtypes'];
    $statuses  =& $data['filters']['status'];

    $data['filters']['inverse'] = isset($inverse) ? $inverse : false;
    $filter['inverse']  = isset($inverse) ? $inverse : false;

    unset($options);
    /**
     *  Grab the mimetypes and setup the selected one
     */
    if (isset($mimetype) && $mimetype > 0) {
        $selected_mimetype = xarMod::apiFunc('mime', 'user', 'get_type', array('typeId' => $mimetype));
    }

    // if selected mimetype isn't set, empty or has an array count of
    // zero, then we set
    if (isset($selected_mimetype) && count($selected_mimetype)) {
        $mimetypes[$mimetype]['selected'] = true;
    } else {
        $mimetypes[0]['selected'] = true;
    }

    /**
     *  Grab the subtypes (if necessary) and setup the selected subtype
     */
    if (isset($selected_mimetype)) {
        if (isset($subtype) && $subtype > 0) {
            $selected_subtype = xarMod::apiFunc('mime', 'user', 'get_subtype', array('subtypeId' => $subtype));
        }

        // add the rest of the types to the array
        // array returns is in form of: array[typeId]{[subtypeId], [subtypeName]}
        $subtypes = $subtypes + xarMod::apiFunc('mime', 'user', 'getall_subtypes', array('typeId' => $selected_mimetype['typeId']));

        // if selected subtype isn't set, empty or has an array count of
        // zero, then we set
        if (isset($selected_subtype['typeId']) && $selected_subtype['typeId'] == $selected_mimetype['typeId']) {
            $subtypes[$subtype]['selected'] = true;
        } else {
            $subtypes[0]['selected'] = true;
        }
    } else {
        $subtypes[0]['selected'] = true;
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

    switch ($status) {
        case _UPLOADS_STATUS_REJECTED:
            $filter['fileStatus'] = _UPLOADS_STATUS_REJECTED;
            $statuses[_UPLOADS_STATUS_REJECTED]['selected'] = true;
            break;
        case _UPLOADS_STATUS_SUBMITTED:
            $filter['fileStatus'] = _UPLOADS_STATUS_SUBMITTED;
            $statuses[_UPLOADS_STATUS_SUBMITTED]['selected'] = true;
            break;
        case _UPLOADS_STATUS_APPROVED:
            $filter['fileStatus'] = _UPLOADS_STATUS_APPROVED;
            $statuses[_UPLOADS_STATUS_APPROVED]['selected'] = true;
            break;
        case 0:
            $filter['fileStatus'] = '';
            $statuses[0]['selected'] = true;
            break;
        default:
            $filter['fileStatus'] = _UPLOADS_STATUS_SUBMITTED;
            $statuses[_UPLOADS_STATUS_SUBMITTED]['selected'] = true;
            break;
    }
    unset($statuses);
    $data['catid'] = isset($catid) ? $catid : null;
    $filter['catid'] = isset($catid) ? $catid : null;
    $filterInfo = array('data' => $data, 'filter' => $filter);


    if ($storeOptions) {
        // Save the filter settings for later use
        xarModUserVars::set('uploads', 'view.filter', serialize($filterInfo));
    }

    return $filterInfo;
}
