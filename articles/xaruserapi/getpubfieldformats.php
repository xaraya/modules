<?php

/**
// TODO: move this to some common place in Xaraya (base module ?)
// + replace with dynamic_propertytypes table
 * get array of field formats for publication types
// TODO: extend with other pre-defined formats
 * @returns array
 * @return array('static'  => xarML('Static Text'),
                 'textbox' => xarML('Text Box'),
                 ...);
 */
function articles_userapi_getpubfieldformats($args)
{
    return array(
        'static'          => xarML('Static Text'),
        'textbox'         => xarML('Text Box'),
        'textarea_small'  => xarML('Small Text Area'),
        'textarea_medium' => xarML('Medium Text Area'),
        'textarea_large'  => xarML('Large Text Area'),
        'dropdown'        => xarML('Dropdown List'),
        'textupload'      => xarML('Text Upload'),
        'url'             => xarML('URL'),
        'urltitle'        => xarML('URL + Title'),
        'image'           => xarML('Image'),
        'imagelist'       => xarML('Image List'),
        'username'        => xarML('Username'),
        'calendar'        => xarML('Calendar'),
        'status'          => xarML('Status'),
        'fileupload'      => xarML('File Upload'),
        'webpage'         => xarML('HTML Page'),
// TODO: add more property types after testing
    );
}

?>
