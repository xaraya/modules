<?php

/**
// TODO: move this to some common place in Xaraya (base module ?)
// + replace with dynamic_propertytypes table
 * get array of field formats numbers for publication types
// TODO: extend with other pre-defined formats
 * @returns array
 * @return array('static'  => 1,
                 'textbox' => 2,
                 ...);
 */
function articles_userapi_getfieldformatnums($args)
{
    return array(
        'static'          => 1,
        'textbox'         => 2,
        'textarea_small'  => 3,
        'textarea_medium' => 4,
        'textarea_large'  => 5,
// TODO: define how to fill this in (cfr. status)
//        'dropdown'        => 6,
        'username'        => 7,
        'calendar'        => 8,
        'fileupload'      => 9,
        'status'          => 10,
        'url'             => 11,
        'image'           => 12,
        'webpage'         => 13,
        'imagelist'       => 35,
        'textupload'      => 38,
        'urltitle'        => 41,
    );
}

?>
