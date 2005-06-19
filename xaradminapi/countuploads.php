<?php
/**
 * count the number of uploaded images (managed by the uploads module)
 *
 * @returns integer
 * @return the number of uploaded images
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
