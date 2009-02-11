<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * get array of field formats for publication types
 * @TODO : move this to some common place in Xaraya (base module ?)
 * + replace with dynamic_propertytypes table
 *
 * + extend with other pre-defined formats
 * @return array('static'  => xarML('Static Text'),
                 'textbox' => xarML('Text Box'),
                 ...);
 */
function publications_userapi_getpubfieldformats($args)
{
    $fieldlist=array(
        'static'          => xarML('Static Text'),
        'textbox'         => xarML('Text Box'),
        'textarea'  => xarML('Small Text Area'),
        'textarea_medium' => xarML('Medium Text Area'),
        'textarea_large'  => xarML('Large Text Area'),
        'dropdown'        => xarML('Dropdown List'),
        'textupload'      => xarML('Text Upload'),
        'fileupload'      => xarML('File Upload'),
        'url'             => xarML('URL'),
        'urltitle'        => xarML('URL + Title'),
        'image'           => xarML('Image'),
        'imagelist'       => xarML('Image List'),
        'calendar'        => xarML('Calendar'),
        'webpage'         => xarML('HTML Page'),
        'username'        => xarML('Username'),
        'userlist'        => xarML('User List'),
        'state'          => xarML('Status'),
        'locale'        => xarML('Language List'),
    // TODO: add more property types after testing
   //other 'text' DD property types won't give significant performance hits
    );

    // Add  'text' dd properites that are dependent on module availability
    $extrafields=array();
    if (xarModIsAvailable('tinymce')) {
        $extrafields=array('tinymce'=> xarML('TinyMCE GUI'));
        $fieldlist=array_merge($fieldlist,$extrafields);
    }

    return $fieldlist;
}

?>
