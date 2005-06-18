<?php
/**
 * get the list of uploaded images (managed by the uploads module)
 *
 * @returns array
 * @return array containing the list of uploads
 * @todo add startnum and numitems support + cache for large # of images
 */
function images_adminapi_getuploads($args)
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

    $imagelist = xarModAPIFunc('uploads', 'user', 'db_get_file', $filter);

    foreach ($imagelist as $id => $image) {
        if (!empty($image['fileLocation'])) {
            $imageInfo = xarModAPIFunc('images','user','getimagesize', $image);
            if (!empty($imageInfo)) {
                $imagelist[$id]['width']  = $imageInfo[0];
                $imagelist[$id]['height'] = $imageInfo[1];
            } else {
                $imagelist[$id]['width']  = '';
                $imagelist[$id]['height'] = '';
            }
            $imagelist[$id]['fileModified'] = @filemtime($image['fileLocation']);
        } else {
            $imagelist[$id]['width']  = '';
            $imagelist[$id]['height'] = '';
            $imagelist[$id]['fileModified'] = '';
        }
    }

    return $imagelist;
}

?>
