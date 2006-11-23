<?php
/**
 * Count the number of uploaded images
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 * count the number of uploaded images (managed by the uploads module)
 *
 * @author mikespub
 * @return integer the number of uploaded images
 */
function images_adminapi_countuploads($args)
{
    extract($args);
    if (empty($typeName)) {
        $typeName = 'image';
    }

    // Get all uploaded files of mimetype 'image' (cfr. uploads admin view)
    $typeinfo = xarModAPIFunc('mime','user','get_type', array('typeName' => $typeName));
    if (empty($typeinfo)) return;

    $filters = array();
    $filters['mimetype'] = $typeinfo['typeId'];
    $filters['subtype']  = NULL;
    $filters['status']   = NULL;
    $filters['inverse']  = NULL;

    $options  = xarModAPIFunc('uploads','user','process_filters', $filters);
    $filter   = $options['filter'];

    $numimages = xarModAPIFunc('uploads', 'user', 'db_count', $filter);

    return $numimages;
}

?>
