<?php
/**
 * Get the configured base directories for server images
 *
 * @returns array
 * @return array containing the base directories for server images
 */
function images_userapi_getbasedirs()
{
    $basedirs = xarModGetVar('images','basedirs');
    if (!empty($basedirs)) {
        $basedirs = unserialize($basedirs);
    }
    if (empty($basedirs)) {
        $basedirs = array();
        $basedirs[0] = array('basedir'   => 'themes',
                             'baseurl'   => 'themes',
                             'filetypes' => 'gif|jpg|png',
                             'recursive' => true);
        xarModSetVar('images','basedirs',serialize($basedirs));
    }

    return $basedirs;
}
?>
