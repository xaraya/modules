<?php
/**
 * Manage uploaded files
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
xarModAPILoad('uploads','user');
/**
 * The view function for users with edit privileges
 *
 * @param int mimetype
 * @param int subtype
 * @param int status
 * @param bool inverse
 * @param int fileId
 * @param string fileDo
 * @param int action
 * @param int startnum
 * @param int numitems
 * @param string sort
 * @param string catid
 * @return array
 */
function uploads_admin_view( )
{
    //security check
    if (!xarSecurityCheck('EditUploads')) return;

    /**
     *  Validate variables passed back
     */

    if (!xarVarFetch('mimetype',    'int:0:',     $mimetype,         NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('subtype',     'int:0:',     $subtype,          NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('status',      'int:0:',     $status,           NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('inverse',     'checkbox',   $inverse,          NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('fileId',      'list:int:1', $fileId,           NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('fileDo',      'str:5:',     $fileDo,           NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('action',      'int:0:',     $action,           NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('startnum',    'int:0:',     $startnum,         NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('numitems',    'int:0:',     $numitems,         NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('sort', 'enum:id:name:size:user:status', $sort,      NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('catid',       'str:1:',     $catid,            NULL, XARVAR_DONT_SET)) return;

    /**
     *  Determine the filter settings to use for this view
     */
    if (!(isset($mimetype) || isset($status) )) {
        // if mimetype and status settings are both empty, then
        // grab the users last view filter
        $options  = unserialize(xarModGetUserVar('uploads','view.filter'));
        $data     = $options['data'];
        $filter   = $options['filter'];
        unset($options);
    } else {
        // otherwise, grab whatever filter options where passed in
        // and process them to create a filter
        $filters['mimetype'] = $mimetype;
        $filters['subtype']  = $subtype;
        $filters['status']   = $status;
        $filters['inverse']  = $inverse;
        $filters['catid']    = $catid;

        $options  =  xarModAPIFunc('uploads','user','process_filters', $filters);
        $data     = $options['data'];
        $filter   = $options['filter'];
        unset($options);
    }
    // Override catid when we're not simply changing the sort order
    if (!isset($sort)) {
        $filter['catid'] = $catid;
        $data['catid']   = $catid;
    }

    /**
     * Perform all actions
     */

    if (isset($action)) {

        if ($action > 0) {
            if (isset($fileDo)) {
                // If we got a signal to change status but no list of files to change,
                // then do nothing
                if (isset($fileId) && !empty($fileId)) {
                    $args['fileId']     = $fileId;
                } else {
                    $action = 0;
                }
            } else {
                $args['fileType']   = $filter['fileType'];
                $args['inverse']    = (isset($inverse) ? $inverse : FALSE);
                $args['curStatus']  = $filter['fileStatus'];
            }
        }

        switch ($action) {
            case _UPLOADS_STATUS_APPROVED:
                    xarModAPIFunc('uploads','user','db_change_status', $args + array('newStatus'    => _UPLOADS_STATUS_APPROVED));
                    break;
            case _UPLOADS_STATUS_SUBMITTED:
                    xarModAPIFunc('uploads','user','db_change_status', $args + array('newStatus'    => _UPLOADS_STATUS_SUBMITTED));
                    break;
            case _UPLOADS_STATUS_REJECTED:
                xarModAPIFunc('uploads','user','db_change_status', $args + array('newStatus'   => _UPLOADS_STATUS_REJECTED));
                if (xarModGetVar('uploads', 'file.auto-purge')) {
                    if (xarModGetVar('uploads', 'file.delete-confirmation')) {
                        return xarModFunc('uploads', 'admin', 'purge_rejected', array('confirmation' => FALSE, 'authid' => xarSecGenAuthKey('uploads')));
                    } else {
                        return xarModFunc('uploads', 'admin', 'purge_rejected', array('confirmation' => TRUE, 'authid' => xarSecGenAuthKey('uploads')));
                    }
                }
                break;
            case 0: /* Change View or anything not defined */
            default:
                break;
        }
    }

    /**
     * Grab a list of files based on the defined filter
     */

    if (!isset($numitems)) {
        $numitems = xarModGetVar('uploads','view.itemsperpage');
        $skipnum = 1;
    }

    if (!isset($filter) || count($filter) <= 0) {
        $filter = array();
        $filter['startnum'] = $startnum;
        $filter['numitems'] = $numitems;
        $filter['sort']     = $sort;
        $items = xarModAPIFunc('uploads', 'user', 'db_getall_files', $filter);
    } else {
        $filter['startnum'] = $startnum;
        $filter['numitems'] = $numitems;
        $filter['sort']     = $sort;
        $items = xarModAPIFunc('uploads', 'user', 'db_get_file', $filter);
    }
    $countitems = xarModAPIfunc('uploads', 'user', 'db_count', $filter);

    if (!empty($items)) {
        $data['numassoc'] = xarModAPIFunc('uploads','user','db_count_associations',
                                          array('fileId' => array_keys($items)));
    }

    if (xarSecurityCheck('EditUploads', 0)) {

        $data['diskUsage']['stored_size_filtered'] = xarModAPIFunc('uploads', 'user', 'db_diskusage', $filter);
        $data['diskUsage']['stored_size_total']    = xarModAPIFunc('uploads', 'user', 'db_diskusage');

        $uploadsdir = realpath(xarModGetVar('uploads', 'path.uploads-directory'));
        $data['diskUsage']['device_free']  = @disk_free_space($uploadsdir);
        $data['diskUsage']['device_total'] = @disk_total_space($uploadsdir);
        $data['diskUsage']['device_used']  = $data['diskUsage']['device_total'] - $data['diskUsage']['device_free'];

        foreach ($data['diskUsage'] as $key => $value) {
            $data['diskUsage'][$key] = xarModAPIFunc('uploads', 'user', 'normalize_filesize', $value);
        }

        $data['diskUsage']['numfiles_filtered']   = $countitems;
        $data['diskUsage']['numfiles_total']      = xarModAPIFunc('uploads', 'user', 'db_count');
    }
    // now we check to see if the user has enough access to view
    // each particular file - if not, we just silently remove it
    // from the view
    foreach ($items as $key => $fileInfo) {
        unset($instance);
        $instance[0] = $fileInfo['fileTypeInfo']['typeId'];
        $instance[1] = $fileInfo['fileTypeInfo']['subtypeId'];
        $instance[2] = xarSessionGetVar('uid');
        $instance[3] = $fileInfo['fileId'];

        if (is_array($instance)) {
            $instance = implode(':', $instance);
        }
        if (!xarSecurityCheck('EditUploads', 0, 'File', $instance)) {
            unset($items[$key]);
        }
    }


    /**
     *  Check for exceptions
     */
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    $data['items'] = $items;
    $data['authid'] = xarSecGenAuthKey();

    // Add pager
    if (!empty($numitems) && $countitems > $numitems) {
        $data['pager'] = xarTplGetPager($startnum,
                                        $countitems,
                                        xarModURL('uploads', 'admin', 'view',
                                                  array('startnum' => '%%',
                                                        'numitems' => (empty($skipnum) ? $numitems : null),
                                                        'sort'     => $sort)),
                                        $numitems);
    } else {
        $data['pager'] = '';
    }

    return $data;
}

?>
