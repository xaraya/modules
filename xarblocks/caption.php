<?php
/*
 * File: $Id: $
 *
 * CHSF Content Navigation Block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Schwab Foundation
 * @link http://wwwk.schwabfoundation.org
 *
 * @subpackage navigator module
 * @author Richard Cave <caveman : rcave@xaraya.com>
*/

/**
 * initialise block
 */
function navigator_captionblock_init()
{
    return true;
}

/**
 * get information on block
 */
function navigator_captionblock_info()
{
    // Values
    return array('text_type' => 'caption',
                 'module' => 'navigator',
                 'text_type_long' => 'Navigator Image w/ Caption',
                 'allow_multiple' => false,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true);
}

/**
 * display block
 */
function navigator_captionblock_display($blockinfo)
{
    // Security Check
    if(!xarSecurityCheck('ViewNavigatorBlock', 0, 'Block', "$blockinfo[title]")) { return; } 

    $args['module'] = 'navigator';
    $args['moduleid'] = xarModGetIDFromName('navigator');
    $args['itemtype'] = $blockinfo['bid'];
    $args['objectid'] = $blockinfo['bid'];
    $args['itemid']   = $blockinfo['bid'];

    $navigator_styleSheets = @unserialize(xarModGetVar('navigator', 'style.list.files'));

    if (!is_array($navigator_styleSheets)) {
        $navigator_styleSheets = array();
    }
    
    $navigator_styleName = "navigator-caption";
    if (is_array($navigator_styleSheets) && !in_array($navigator_styleName, $navigator_styleSheets)) {
        $navigator_styleSheets[] = $navigator_styleName;
        xarModSetVar('navigator', 'style.list.files', serialize($navigator_styleSheets));
    }

    // Make sure the fileupload property is using the uploads functions
    xarVarSetCached('Hooks.uploads', 'ishooked', TRUE);

    // Grab the properties for this block and prep them for display
    list($properties) = xarModAPIFunc('dynamicdata','user','getitemfordisplay',
                                    array('module'   => 'navigator',
                                          'itemtype' => $blockinfo['bid'],
                                          'itemid'   => $args['itemid'],
                                          'preview'  => 1));

    if (!empty($properties) && count($properties) > 0) {
        foreach (array_keys($properties) as $field) {
            $data[$field] = $properties[$field]->getValue();
        // TODO: clean up this temporary fix
            $data[$field.'_output'] = $properties[$field]->showOutput();
        }
    } else {
        $data['image'] = $data['image_output'] = '';
        $data['caption'] = $data['caption_output'] = '';
    }

    // Return data, not rendered content.
    $blockinfo['content'] = $data;

    if (!empty($blockinfo['content'])) {
        return $blockinfo;
    }
}

/**
 * modify block settings
 */
function navigator_captionblock_modify($blockinfo)
{
    if(!xarSecurityCheck('AdminNavigatorBlock', 1,'Block',"$blockinfo[title]")) {
        return;
    }

    $image_args['name']           = 'image';
    $image_args['label']          = 'Image';
    $image_args['type']           = 9;
    $image_args['default']        = '';
    $image_args['source']         = 'dynamic_data';
    $image_args['status']         = 1;
    $image_args['order']          = 1;
    $image_args['validation']     = 'single:style=raw:methods(+upload,+stored,+trusted,-external)';

    $caption_args['name']          = 'caption';
    $caption_args['label']         = 'Caption';
    $caption_args['type']          = 202;   // Small gui == 201, med == 202, large == 203
    $caption_args['default']       = '';
    $caption_args['source']        = 'dynamic_data';
    $caption_args['status']        = 1;
    $caption_args['order']         = 2;
    $caption_args['validation']    = '';

    $args['module']       = 'navigator';
    $args['moduleid']     = xarModGetIDFromName('navigator');
    $args['modid']        = &$args['moduleid'];
    $args['itemtype']     = $blockinfo['bid'];
    $args['itemid']       = 0;

    // Make sure the fileupload prop uses the hooked version
    xarVarSetCached('Hooks.uploads', 'ishooked', TRUE);

    // begin check to see if the caption object exists
    $object = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                            array('objectid' => $args['itemid'],
                                  'moduleid' => $args['modid'],
                                  'itemtype' => $args['itemtype']));

    if (isset($object)) {
        $args['objectid'] = $object['objectid'];
        $args['modid']    = $object['moduleid'];
        $args['itemtype'] = $object['itemtype'];
    } elseif (!empty($args['modid'])) {
        // if the caption object doesn't exist, lets create it
        $modinfo = xarModGetInfo($args['modid']);

        if (!empty($modinfo['name'])) {
            $name = $modinfo['name'] . '_caption_block';

            if (!empty($itemtype)) {
                $name .= '_' . $itemtype;
            }

            if (!xarModAPILoad('dynamicdata','admin')) {
                return;
            }

            $args['objectid'] = xarModAPIFunc('dynamicdata','admin','createobject',
                                      array('moduleid' => $args['modid'],
                                            'itemtype' => $args['itemtype'],
                                            'name' => $name,
                                            'label' => ucfirst($name)));

            if (!isset($args['objectid'])) {
                return;
            }
        }
    }


    // Make sure we have both the image and the caption field
    // if not create them
    $fields = xarModAPIFunc('dynamicdata','user','getprop',
                             array('modid' => $args['modid'],
                                   'itemtype' => $args['itemtype'],
                                   'allprops' => true));

    if (!isset($fields) || $fields == false || count($fields) != 2) {
        // Create the properties if they don't exist.
        xarModAPIFunc('dynamicdata', 'admin', 'createproperty', array_merge($args, $image_args));
        xarModAPIFunc('dynamicdata', 'admin', 'createproperty', array_merge($args, $caption_args));
    }

    // Now prepare the properties for input display
    list($properties) = xarModAPIFunc('dynamicdata','user','getitemfordisplay',
                                        array('module'   => 'navigator',
                                              'itemtype' => $blockinfo['bid'],
                                              'itemid'   => $blockinfo['bid'],
                                              'preview'  => 1));
    $data = array();

    if (!empty($properties) && count($properties) > 0) {
        foreach (array_keys($properties) as $field) {
            $data[$field . '_input'] = array('label' => $properties[$field]->label,
                            'id' => $field,
                            'definition' => $properties[$field]->showInput());
        }
    }

    // Return output
    return $data;
}

/**
 * update block settings
 */
function navigator_captionblock_update($blockinfo)
{
    if(!xarSecurityCheck('AdminNavigatorBlock', 1,'Block',"$blockinfo[title]")) {
        return;
    }

    $args['extrainfo']['module']   = 'navigator';
    $args['extrainfo']['itemid']   = 0;
    $args['extrainfo']['itemtype'] = $blockinfo['bid'];
    $args['objectid'] = $blockinfo['bid'];

    // Make sure the fileupload prop uses the hooked version
    xarVarSetCached('Hooks.uploads', 'ishooked', TRUE);

    xarModAPIFunc('dynamicdata', 'admin', 'updatehook', $args);;

    // Return blockinfo
    return $blockinfo;
}

/**
 * built-in block help/information system.
 */
function navigator_captionblock_help()
{
    return '';
}

?>