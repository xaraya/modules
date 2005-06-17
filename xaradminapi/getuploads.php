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
            $imageInfo = @getimagesize($image['fileLocation']);
            if (!empty($imageInfo)) {
                $imagelist[$id]['width']  = $imageInfo[0];
                $imagelist[$id]['height'] = $imageInfo[1];

            // FIXME: don't do this for every image !?
            } elseif (defined('_UPLOADS_STORE_DB_DATA') && ($image['storeType'] & _UPLOADS_STORE_DB_DATA) && extension_loaded('gd')) {
                // get the image data from the database
                $data = xarModAPIFunc('uploads', 'user', 'db_get_file_data', array('fileId' => $image['fileId']));
                if (!empty($data)) {
                    $string = implode('', $data);
                    $src = @imagecreatefromstring($string);
                    $imagelist[$id]['width']  = @imagesx($src);
                    $imagelist[$id]['height'] = @imagesy($src);
                } else {
                    $imagelist[$id]['width']  = '';
                    $imagelist[$id]['height'] = '';
                }

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
