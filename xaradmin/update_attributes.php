<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Update the attributes of an object
 */
function eav_admin_update_attributes()
{
    if (!xarVar::fetch('objectid', 'isset', $objectid, 1, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('eav_name', 'isset', $eav_name, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('eav_label', 'isset', $eav_label, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('eav_type', 'isset', $eav_type, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('eav_default', 'isset', $eav_defaultvalue, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('eav_seq', 'isset', $eav_seq, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('display_eav_status', 'isset', $display_eav_status, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('input_eav_status', 'isset', $input_eav_status, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('eav_configuration', 'isset', $eav_configuration, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('attribute_definition', 'int', $attribute_definition, 0, xarVar::DONT_SET)) {
        return;
    }

    // Security
    if (!xarSecurity::check('AdminEAV')) {
        return;
    }

    if (!xarSec::confirmAuthKey()) {
        return xarTpl::module('privileges', 'user', 'errors', ['layout' => 'bad_author']);
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(['objectid' => $objectid]);

    $fields = xarMod::apiFunc('eav', 'user', 'getattributes', ['object_id' => $objectid]);

    sys::import('xaraya.structures.query');
    $tables =& xarDB::getTables();

    $i = 0;
    # --------------------------------------------------------
    # Update the current attributes
    #
    foreach ($fields as $name => $field) {
        $id = $field['id'];
        $i++;
        if (empty($eav_label[$id])) {
            $property = DataPropertyMaster::getProperty(['type' => $field['type']]);
            $res = $property->removeFromObject(['object_id' => $objectid]);
            // delete property (and corresponding data) in xaradminapi.php
            $q = new Query('DELETE', $tables['eav_attributes']);
            $q->eq('id', $id);
            $q->run();
        } else {
            // update property in xaradminapi.php
            if (!isset($eav_defaultvalue[$id])) {
                $eav_defaultvalue[$id] = null;
            } elseif (!empty($eav_defaultvalue[$id]) && preg_match('/\[LF\]/', $eav_defaultvalue[$id])) {
                // replace [LF] with line-feed again
                $lf = chr(10);
                $eav_defaultvalue[$id] = preg_replace('/\[LF\]/', $lf, $eav_defaultvalue[$id]);
            }
            if (!isset($eav_configuration[$id])) {
                $eav_configuration[$id] = null;
            }
            if (!isset($display_eav_status[$id])) {
                $display_eav_status[$id] = DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE;
            }
            if (!isset($input_eav_status[$id])) {
                $input_eav_status[$id] = DataPropertyMaster::DD_INPUTSTATE_ADDMODIFY;
            }
            $eav_status[$id] = $display_eav_status[$id] + $input_eav_status[$id];

            $valuefield = xarMod::apiFunc('eav', 'admin', 'getvaluefield', ['property_id' => (int)$eav_type[$id]]);

            $q = new Query('UPDATE', $tables['eav_attributes']);
            $q->addfield('name', $eav_name[$id]);
            $q->addfield('label', $eav_label[$id]);
            $q->addfield('object_id', (int)$objectid);
            $q->addfield('type', (int)$eav_type[$id]);
            $q->addfield($valuefield, $eav_defaultvalue[$id]);
            $q->addfield('status', (int)$eav_status[$id]);
            $q->addfield('seq', $eav_seq[$id]);
            $q->addfield('timeupdated', time());
            $q->addfield('configuration', $eav_configuration[$id]);
            $q->eq('id', $id);
            if (!$q->run()) {
                return;
            }
        }
    }
    $i++;
    # --------------------------------------------------------
    # Insert a new attribute
    #
    if (!empty($attribute_definition)) {
        // User chose a definition from the dropdown
        // Get the definition
        $q = new Query('SELECT', $tables['eav_attributes_def']);
        $q->addfield('name');
        $q->addfield('label');
        $q->addfield('property_id');
        $q->addfield('configuration');
        $q->addfield('default_tinyint');
        $q->addfield('default_integer');
        $q->addfield('default_decimal');
        $q->addfield('default_string');
        $q->addfield('default_text');
        $q->eq('id', $attribute_definition);
        if (!$q->run()) {
            return;
        }
        $definition = $q->row();

        // Insert it in the attributes table
        $q = new Query('INSERT', $tables['eav_attributes']);
        $q->addfield('name', $definition['name']);
        $q->addfield('label', $definition['label']);
        $q->addfield('object_id', (int)$objectid);
        $q->addfield('type', $definition['property_id']);
        $q->addfield('configuration', $definition['configuration']);
        $q->addfield('default_tinyint', $definition['default_tinyint']);
        $q->addfield('default_integer', $definition['default_integer']);
        $q->addfield('default_decimal', $definition['default_decimal']);
        $q->addfield('default_string', $definition['default_string']);
        $q->addfield('default_text', $definition['default_text']);
        $q->addfield('status', 3);
        $q->addfield('seq', $i);
        $q->addfield('timecreated', time());
        $q->addfield('timeupdated', time());
        if (!$q->run()) {
            return;
        }
    } else {
        // No efinition chosen. Check if we are entering a property manually
        if (!empty($eav_label[0]) && !empty($eav_property_id[0])) {
            // create new property in xaradminapi.php
            $name = strtolower($eav_label[0]);
            $name = preg_replace('/[^a-z0-9_]+/', '_', $name);
            $name = preg_replace('/_$/', '', $name);
            if (!isset($display_eav_status[0])) {
                $display_eav_status[0] = DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE;
            }
            if (!isset($input_eav_status[0])) {
                $input_eav_status[0] = DataPropertyMaster::DD_INPUTSTATE_ADDMODIFY;
            }
            $eav_status[0] = $display_eav_status[0] + $input_eav_status[0];

            $valuefield = xarMod::apiFunc('eav', 'admin', 'getvaluefield', ['property_id' => $eav_type[0]]);

            $q = new Query('INSERT', $tables['eav_attributes']);
            $q->addfield('name', $name);
            $q->addfield('label', $eav_label[0]);
            $q->addfield('object_id', (int)$objectid);
            $q->addfield('type', (int)$eav_property_id[0]);
            $q->addfield($valuefield, $eav_defaultvalue[0]);
            $q->addfield('status', (int)$eav_status[0]);
            $q->addfield('seq', $i);
            $q->addfield('timecreated', time());
            $q->addfield('timeupdated', time());
            if (!$q->run()) {
                return;
            }
        }
    }

    xarController::redirect(xarController::URL(
        'eav',
        'admin',
        'add_attribute',
        ['objectid'    => $objectid]
    ));
    return true;
}
