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
// TODO: define how to fill this in (cfr. status)
//        'dropdown'        => xarML('Dropdown List'),
        'username'        => xarML('Username'),
        'calendar'        => xarML('Calendar'),
        'fileupload'      => xarML('File Upload'),
        'status'          => xarML('Status'),
        'url'             => xarML('URL'),
        'image'           => xarML('Image'),
        'webpage'         => xarML('HTML Page'),
    );
}

?>
