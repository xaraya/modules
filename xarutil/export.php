<?php
/**
 *
 * Xaraya Autolinks
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @todo This is very much work-in-progress
 *
 * @subpackage Autolinks Module
 * @author Jason Judge
*/

/*
 * Although this is going to get a major rewrite, I have included it with
 * this release for two reasons:
 * 1. The export function is useful in diagnosing Autolink faults.
 * 2. It gets some of the export ideas into the community.
 *
 * The main idea with the export is to create a set of generic functios
 * that can be bolted together easily to create an export function for
 * any module. It works by preparing data (extracted through the APIs)
 * into an array, and formatting that array as XML. At any point in that
 * structure, callback functions can be invoked to extract a sub-tree of
 * XML data. Sub-trees such as hooked data can be made generic, since those
 * sub-trees would look the same for all items in all modules. For example,
 * a set of DD property values could still be formatted in the same way
 * regardless of whether those values came from an Autolink or an Article.
 * This should help prevent a lot of duplication.
 *
 * Some restructuring is needed before this can be considered anywhere near
 * complete. The main task is to separate the generic from the specific.
 *
 * An over-riding requirement for this functionality is that a module
 * export function does not need to touch XML in any way - it just
 * handles arrays, and the generic export stuff does the XML conversion.
 *
 * Importing will be done in a similar way: using a streaming XML parser,
 * the import function provides a set of functions to handle the arrays
 * that the imporer gives it. Certain arrays, such as hook values, can be
 * dealt with generically, saving a lot of duplication of coding effort.
 */

function autolinks_util_export_callbackddobject($value, $level, $extrainfo)
{
    // Within a DD object.
    // Get the object property details.

    $ddproperties = xarModAPIfunc(
        'dynamicdata', 'user', 'getprop',
        array(
            'modid' => $value['moduleid'],
            'itemtype' => $value['itemtype']
        )
    );

    foreach ($ddproperties as $key => $property) {
        $ddproperties['dd-property:id:' . $property['id'] . ':name:' . $property['name']] = $property;
        unset($ddproperties[$key]);
    }

    return autolinks_util_export_arraytoxml(array('dd-properties' => $ddproperties), $level, '', $extrainfo);
}


function autolinks_util_export_callbackgenericitemtype($value, $level, $extrainfo)
{
    return autolinks_util_export_arraytoxml(
        array('itemtype-hooks' => ''), $level,
        array('itemtype-hooks' => 'autolinks_util_export_callbackitemtypehooks'), $extrainfo
    );
}


function autolinks_util_export_callbackgenericitem($value, $level, $extrainfo)
{
    return autolinks_util_export_arraytoxml(
        array('item-hooks' => ''), $level,
        array('item-hooks' => 'autolinks_util_export_callbackitemhooks'), $extrainfo
    );
}


function autolinks_util_export_callbackitemhooks($value, $level, $extrainfo)
{
    // Within a generic item.
    // Here we will fetch any DD property values that the link may have.
    // To get the properties, we need the module, the itemtype and the item ID.

    $xml = '';

    // Gather information.
    $module = '';
    $moduleid = 0;
    $itemtype = 0;
    $itemid = 0;

    // Get the module and module ID.
    // Look in extrainfo: module or moduleid
    if (isset($extrainfo['moduleid']) && is_numeric($extrainfo['moduleid'])) {
        $moduleid = $extrainfo['moduleid'];
    }

    if (empty($moduleid) && isset($extrainfo['module'])) {
        $module = $extrainfo['module'];
        $moduleid = xarModGetIDFromName($extrainfo['module']);
    }

    if (empty($module) && !empty($moduleid)) {
        // Get module name from ID.
        $info = xarModGetInfo($moduleid);
        $module = $info['name'];
    }

    // Item type.
    if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    }

    // Item ID.
    if (isset($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    }

    // Hooked details that need module, itemtype and itemid.
    if (!empty($moduleid) && !empty($itemtype) && !empty($itemid)) {

        // TODO: call out to external functions for each hook to export?

        if (xarModIsHooked('dynamicdata', $module, $itemtype)) {
            // If there is an item id available, then fetch any property values for this item.
            $propvalues = xarModAPIfunc(
                'dynamicdata', 'user', 'getitem',
                array(
                    'moduleid' => $moduleid,
                    'itemtype' => $itemtype,
                    'itemid' => $itemid
                )
            );

            if (!empty($propvalues)) {
                // There are some values available - format them.
                foreach ($propvalues as $key => $prop) {
                    $propvalues['dd-property-value:name:' . $key] = $prop;
                    unset($propvalues[$key]);
                }

                // Format the property values.
                $xml .= autolinks_util_export_arraytoxml(
                    array('dd-property-values' => $propvalues),
                    $level, '', $extrainfo
                );
            }
        }
    }

    return $xml;
}


function autolinks_util_export_callbackitemtypehooks($value, $level, $extrainfo)
{
    $xml = '';

    // Gather information.
    $module = '';
    $moduleid = 0;
    $itemtype = 0;

    // Get the module and module ID.
    // Look in extrainfo: module or moduleid
    if (isset($extrainfo['moduleid']) && is_numeric($extrainfo['moduleid'])) {
        $moduleid = $extrainfo['moduleid'];
    }

    if (empty($moduleid) && isset($extrainfo['module'])) {
        $module = $extrainfo['module'];
        $moduleid = xarModGetIDFromName($extrainfo['module']);
    }

    if (empty($module) && !empty($moduleid)) {
        // Get module name from ID.
        $info = xarModGetInfo($moduleid);
        $module = $info['name'];
    }

    // Item type.
    if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    }

    // Hooked details that need module, itemtype and itemid.
    if (!empty($moduleid) && !empty($itemtype)) {
        // DD object for type.
        if (xarModIsHooked('dynamicdata', $module, $itemtype)) {
            // DD object to fetch.
            $ddobject = xarModAPIfunc(
                'dynamicdata', 'user', 'getobjectinfo',
                array(
                    'moduleid' => $moduleid,
                    'itemtype' => $itemtype
                )
            );

            if (!empty($ddobject)) {
                $xml .= autolinks_util_export_arraytoxml(
                    array('dd-object:objectid:' . $ddobject['objectid'] => $ddobject),
                    $level,
                    array('dd-object' => 'autolinks_util_export_callbackddobject'),
                    $extrainfo
                );
            }
        }
    }

    return $xml;
}


function autolinks_util_export_callbackconfig($value, $level, $extrainfo)
{
    return autolinks_util_export_arraytoxml(
        array(
            'config-items:module:autolinks' => array(
                // TODO: we could fetch these in one go from the modules API.
                // It does not matter if any of these are array structures, as the arrays
                // will be expanded in the XML file.
                'config-item:name:itemsperpage'     => xarModGetVar('autolinks', 'itemsperpage'),
                'config-item:name:maxlinkcount'     => xarModGetVar('autolinks', 'maxlinkcount'),
                'config-item:name:decoration'       => xarModGetVar('autolinks', 'decoration'),
                'config-item:name:punctuation'      => xarModGetVar('autolinks', 'punctuation'),
                'config-item:name:nbspiswhite'      => xarModGetVar('autolinks', 'nbspiswhite'),
                'config-item:name:templatebase'     => xarModGetVar('autolinks', 'templatebase'),
                'config-item:name:showerrors'       => xarModGetVar('autolinks', 'showerrors'),
                'config-item:name:showsamples'      => xarModGetVar('autolinks', 'showsamples'),
                'config-item:name:typeitemtype'     => xarModGetVar('autolinks', 'typeitemtype'),
                'config-item:name:excludeelements'  => xarModGetVar('autolinks', 'excludeelements')
            ),
            'itemtype-hooks:itemtype:' . xarModGetVar('autolinks', 'typeitemtype') => ''
        ),
        $level,
        array('itemtype-hooks' => 'autolinks_util_export_callbackitemtypehooks'),
        $extrainfo
    );
}

function autolinks_util_export_callbacklinktype($value, $level, $extrainfo)
{
    // Within a link type.
    // Loop for all links.

    // TODO: call 'itemtype' hooks, then 'item' hooks.
    // TODO: as an item type, there could be objects attached to it, and as
    //       an item there could be object instance values attached to it.
    // Need to fetch the type object details and sample links.

    // Get all links for this type.
    $links = xarModAPIfunc('autolinks', 'user', 'getall', array('tid' => $value['tid']));

    if (!empty($links)) {
        // Set the element keys to generate attributes.
        foreach ($links as $key => $link) {
            $link['itemid'] = $link['lid'];
            // Some elements are not for exporting.
            unset($link['cache_replace']);
            if (is_numeric($key)) {
                $links['autolink:lid:' . $link['lid'] . ':module:autolinks:itemtype:' . $link['itemtype'] . ':itemid:' . $link['lid'] ] = $link;
                unset($links[$key]);
            }
        }

        // Get the links and hooks for this type.
        return autolinks_util_export_arraytoxml(
            array(
                'item-hooks:itemtype:1:itemid:' . $link['tid'] => '',
                'itemtype-hooks:itemtype:' . $link['itemtype'] => '',
                'autolinks' => $links
            ),
            $level,
            array(
                'autolink' => 'autolinks_util_export_callbackgenericitem',
                'item-hooks' => 'autolinks_util_export_callbackitemhooks',
                'itemtype-hooks' => 'autolinks_util_export_callbackitemtypehooks'
            ),
            $extrainfo
        );
    }

    return '';
}

// This is a useful function to be made available in a separate module.

function autolinks_util_export_arraytoxml($array, $level=0, $callbacks='', $extrainfo = array())
{
    $xml = '';
    $indent = '   ';

    foreach ($array as $key => $value) {
        $key = strtolower($key);

        // Determine whether there are attributes for this element,
        // and extract them if there are.
        $attr = '';
        $split = split(':', $key);
        if (count($split) >= 3 && count($split)%2 == 1) {
            $key = $split[0];
            for($i=1; $i < count($split); $i+=2) {
                if (!empty($split[$i+1])) {
                    $attr .= ' ' . $split[$i] . '="' . htmlspecialchars($split[$i+1]) . '"';
                    $extrainfo[$split[$i]] = $split[$i+1];
                }
            }
        }

        if (is_array($value)) {
            if (count($value) > 0) {
                $xml .= str_repeat($indent, $level) . "<$key$attr>\n";
                $xml .= autolinks_util_export_arraytoxml($value, $level+1, $callbacks, $extrainfo);

                // Invoke callback functions if there are any for this key.
                if (is_array($callbacks) && isset($callbacks[$key])) {
                    foreach(explode(',', $callbacks[$key]) as $callback) {
                        if (function_exists($callback)) {
                            $xml .= $callback($value, $level+1, $extrainfo);
                        }
                    }
                }

                $xml .= str_repeat($indent, $level) . "</$key>\n";
            }
        } else {
            if (htmlspecialchars($value) != $value) {
                // CDATA characters to be encoded.
                $xml .= str_repeat($indent, $level) . "<$key$attr>"
                    . "<![CDATA[$value]]></$key>\n";
            } elseif (trim($value) == '' && is_array($callbacks) && isset($callbacks[$key]) && function_exists($callbacks[$key])) {
                // No data, but there is a callback.
                $xml .= str_repeat($indent, $level)."<$key$attr>\n";
                foreach(explode(',', $callbacks[$key]) as $callback) {
                    if (function_exists($callback)) {
                        $xml .= $callback($value, $level+1, $extrainfo);
                    }
                }
                $xml .= str_repeat($indent, $level) . "</$key>\n";
            } else {
                // Normal text that requires no encoding.
                $xml .= str_repeat($indent, $level)
                    . "<$key$attr>$value</$key>\n";
            }
        }
    }

    return $xml;
}


/**
 * Export an object definition or an object item to XML
 */
function autolinks_util_export($args)
{
    // Security Check
    if(!xarSecurityCheck('AdminDynamicData')) {return;}

    extract($args);

    // Get the autolink types.
    $types = xarModAPIfunc('autolinks', 'user', 'getalltypes');

    // Set the type tags and attributes by altering the keys.
    // TODO: find a function that will alter the keys insitu, without
    // copying each element.

    $autolink_type_itemtype = xarModGetVar('autolinks', 'typeitemtype');

    // An explanation:
    // - The autolink types are fetched into an array.
    // - The array keys are set to define what the tag for each of those
    //   types will look like.
    // - The key is of a form: name:attr1:value1[:attr2:value2] etc.
    // - The key here is: autolink-type:tid:<tid-value>:itemtype:<itemtype-value>:itemid:<tid-value>
    //   e.g. autolink type ID 2, with itemtype value 12, would have the following key value:
    //   'autolink-type:tid:2:itemtype:12:itemid:2'
    //   and this would generate the tag:
    //   <autolink-type tid="2" itemtype="1" itemid="2">...</autolink-type>
    foreach ($types as $key => $type) {
        if (is_numeric($key) ) {
            $types['autolink-type:tid:' . $type['tid'] . ':itemtype:' . $autolink_type_itemtype . ':itemid:' . $type['tid']] = $type;
            unset($types[$key]);
        }
    }

    // TODO: a generic solution will provide the encoding as an option - including 'auto'.
    $xml = '<?xml version="1.0" encoding="ISO-8859-1"?>' . "\n";

    // This gets the ball rolling. Explanation:
    // - The top-level tag is 'autolinks-export', and it has a single
    //   attribute 'module' with the value 'autolinks'.
    // - Within that tag are two further tags: autolinks-global and
    //   autolink-types.
    // - The autolinks-global tags is handled by callback function
    //   autolinks_util_export_callbackconfig().
    // - The autolink-types tag has content generated from the array
    //   $types (an extract of the autolink types, done above).
    // - There is a further call-back function to handle tag autolink-type.
    // - Tag autolink-type is embedded in the array $types.
    // Simple really ;-)

    $xml .= autolinks_util_export_arraytoxml(
        array(
            'autolinks-export:module:autolinks' => array(
                'autolinks-global' => '',
                'autolink-types' => $types
            )
        ),
        0,
        array(
            'autolinks-global' => 'autolinks_util_export_callbackconfig',
            'autolink-type' => 'autolinks_util_export_callbacklinktype'
        )
    );

    // Return the XML.
    // TODO: we need a variety of methods for returning this data. It
    // could go to a form item for cut-n-paste, to the browser as a
    // standalone MIME data stream, to a local file, etc.
    $data['xml'] = xarVarPrepForDisplay($xml);

    return $data;
}

?>