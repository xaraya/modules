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
    if(!xarVarFetch('objectid',           'isset', $objectid,          1, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('eav_name',           'isset', $eav_name,           NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('eav_label',          'isset', $eav_label,          NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('eav_property_id',    'isset', $eav_property_id,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('eav_default',        'isset', $eav_defaultvalue,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('eav_seq',            'isset', $eav_seq,            NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('display_eav_status', 'isset', $display_eav_status, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('input_eav_status',   'isset', $input_eav_status,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('eav_configuration',  'isset', $eav_configuration,  NULL, XARVAR_DONT_SET)) {return;}

    // Security
    if(!xarSecurityCheck('AdminEAV')) return;

    if (!xarSecConfirmAuthKey()) {
        return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
    }        

    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(array('objectid' => $objectid));

    $fields = xarMod::apiFunc('eav','user','getattributes', array('object_id' => $objectid));

    sys::import('xaraya.structures.query');
    $tables = xarDB::getTables();
    
    $i = 0;
    # --------------------------------------------------------
    # Update the current attributes
    #
    foreach ($fields as $name => $field) {
        $id = $field['id'];
        $i++;
        if (empty($eav_label[$id])) {
            $property = DataPropertyMaster::getProperty(array('type' => $field['type']));
            $res = $property->removeFromObject(array('object_id' => $objectid));
            // delete property (and corresponding data) in xaradminapi.php
            $q = new Query('DELETE', $tables['eav_attributes']);
            $q->eq('id', $id);
            $q->run();
        } else {
            // update property in xaradminapi.php
            if (!isset($eav_defaultvalue[$id])) {
                $eav_defaultvalue[$id] = null;
            } elseif (!empty($eav_defaultvalue[$id]) && preg_match('/\[LF\]/',$eav_defaultvalue[$id])) {
                // replace [LF] with line-feed again
                $lf = chr(10);
                $eav_defaultvalue[$id] = preg_replace('/\[LF\]/',$lf,$eav_defaultvalue[$id]);
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

            $valuefield = xarMod::apiFunc('eav', 'admin', 'getvaluefield', array('property_id' => (int)$eav_property_id[$id]));

            $q = new Query('UPDATE', $tables['eav_attributes']);
            $q->addfield('name', $eav_name[$id]);
            $q->addfield('label', $eav_label[$id]);
            $q->addfield('object_id', (int)$objectid);
            $q->addfield('property_id', (int)$eav_property_id[$id]);
            $q->addfield($valuefield, $eav_defaultvalue[$id]);
            $q->addfield('status', (int)$eav_status[$id]);
            $q->addfield('seq', $eav_seq[$id]);
            $q->addfield('timeupdated', time());
            $q->addfield('configuration', $eav_configuration[$id]);
            $q->eq('id', $id);
            if(!$q->run()) return;
        }
    }
    $i++;
    # --------------------------------------------------------
    # Insert a new attribute
    #
    if (!empty($eav_label[0]) && !empty($eav_property_id[0])) {
        // create new property in xaradminapi.php
        $name = strtolower($eav_label[0]);
        $name = preg_replace('/[^a-z0-9_]+/','_',$name);
        $name = preg_replace('/_$/','',$name);
        if (!isset($display_eav_status[0])) {
            $display_eav_status[0] = DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE;
        }
        if (!isset($input_eav_status[0])) {
            $input_eav_status[0] = DataPropertyMaster::DD_INPUTSTATE_ADDMODIFY;
        }
        $eav_status[0] = $display_eav_status[0] + $input_eav_status[0];

        $valuefield = xarMod::apiFunc('eav', 'admin', 'getvaluefield', array('property_id' => $eav_property_id[0]));

        $q = new Query('INSERT', $tables['eav_attributes']);
        $q->addfield('name', $name);
        $q->addfield('label', $eav_label[0]);
        $q->addfield('object_id', (int)$objectid);
        $q->addfield('property_id', (int)$eav_property_id[0]);
        $q->addfield($valuefield, $eav_defaultvalue[0]);
        $q->addfield('status', (int)$eav_status[0]);
        $q->addfield('seq', $i);
        $q->addfield('timecreated', time());
        $q->addfield('timeupdated', time());
        if(!$q->run()) return;
    }

    xarController::redirect(xarModURL('eav', 'admin', 'add_attribute',
                        array('objectid'    => $objectid)));
    return true;
}
?>