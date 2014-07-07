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
 * Configure an attribute of an eav object
 */
function eav_admin_configure_attribute(Array $args=array())
{
    // Security
    if(!xarSecurityCheck('ManageEAV')) return;

    extract($args);

    // get the property id
    if (!xarVarFetch('itemid',  'id',    $itemid, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('exit', 'isset', $exit, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm', 'isset', $confirm, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('preview', 'isset', $preview, NULL, XARVAR_DONT_SET)) {return;}

    if (empty($itemid)) {
        // Get the property type for sample configuration
        if (!xarVarFetch('proptype', 'isset', $proptype, NULL, XARVAR_NOT_REQUIRED)) {return;}

        // Show sample configuration for some property type
        return eav_config_propval($proptype);
    }

    // Get the object corresponding to this attribute
    $attribute = DataObjectMaster::getObject(array('name'   => 'eav_attributes',
                                                  'itemid' => $itemid));
    if (empty($attribute)) return;

    $newid = $attribute->getItem();

    if (empty($newid) || empty($attribute->properties['id']->value)) {
        throw new BadParameterException(null,'Invalid item id');
    }
    if (empty($attribute->properties['object_id']->value)) {
        throw new BadParameterException(null,'Invalid object id');
    }

    // Check security of the parent object
    $parentobjectid = $attribute->properties['object_id']->value;
    $parentobject = DataObjectMaster::getObject(array('objectid' => $parentobjectid));
    if (empty($parentobject)) return;
    if (!$parentobject->checkAccess('config'))
        return xarResponse::Forbidden(xarML('Configure #(1) is forbidden', $parentobject->label));
    unset($parentobject);

    $data = array();
    // Get a new property of the right type
    $data['type'] = $attribute->properties['type']->value;
    $id = $attribute->properties['configuration']->id;

    $data['name']       = 'dd_'.$id;
    // Pass the actual id for the property here
    $data['id']         = $id;
    // Pass the original invalid value here
    $data['invalid']    = !empty($invalid) ? $invalid :'';
    $property = DataPropertyMaster::getProperty($data);
    $data['propertytype'] = DataPropertyMaster::getProperty(array('type' => $data['type']));
    if (empty($property)) return;

    if (!empty($preview) || !empty($confirm) || !empty($exit)) {
        if (!xarVarFetch($data['name'],'isset',$configuration,NULL,XARVAR_NOT_REQUIRED)) return;

        // Pass the current value as configuration rule
        $data['configuration'] = isset($configuration) ? $configuration : '';

        $isvalid = $property->updateConfiguration($data);

        if ($isvalid) {
            if (!empty($confirm) || !empty($exit)) {
                // store the updated configuration rule back in the value
                $attribute->properties['configuration']->value = $property->configuration;
                if (!xarSecConfirmAuthKey()) {
                    return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));
                }

                $newid = $attribute->updateItem();
                if (empty($newid)) return;

                if (empty($exit)) {
                    $return_url = xarModURL('eav', 'admin', 'configure_attribute', array('itemid' => $itemid));
                    xarController::redirect($return_url);
                    return true;
                }
            }
            if (!empty($exit)) {
                if (!xarVarFetch('return_url', 'isset', $return_url,  NULL, XARVAR_DONT_SET)) {return;}
                if (empty($return_url)) {
                    // return to modifyprop
                    $return_url = xarModURL('eav', 'admin', 'add_attribute',
                                            array('objectid' => $parentobjectid));
                }
                xarController::redirect($return_url);
                return true;
            }
            // show preview/updated values

        } else {
            $attribute->properties['configuration']->invalid = $property->invalid;
        }        

    // pass the current value as configuration rule
    } elseif (!empty($attribute->properties['configuration'])) {
        $data['configuration'] = $attribute->properties['configuration']->value;

    } else {
        $data['configuration'] = null;
    }

    // pass the id for the input field here
    $data['id']         = 'dd_'.$id;
    $data['tabindex']   = !empty($tabindex) ? $tabindex : 0;
    $data['maxlength']  = !empty($maxlength) ? $maxlength : 254;
    $data['size']       = !empty($size) ? $size : 50;

    // call its showConfiguration() method and return
    $data['showval'] = $property->showConfiguration($data);
    $data['itemid'] = $itemid;
    $data['object'] =& $attribute;

    xarTpl::setPageTitle(xarML('Configuration for DataProperty #(1)', $itemid));

    // Return the template variables defined in this function
    return $data;
}

/**
 * Show sample configuration for some property type
 * @return array
 */
function eav_config_propval($proptype)
{
    $data = array();
    if (empty($proptype)) {
        xarTpl::setPageTitle(xarML('Sample Configuration for DataProperty Types'));
        return $data;
    }

    // get a new property of the right type
    $data['type'] = $proptype;
    $data['name'] = 'dd_' . $proptype;
    $property =& DataPropertyMaster::getProperty($data);
    if (empty($property)) {
        xarTpl::setPageTitle(xarML('Sample Configuration for DataProperty Types'));
        return $data;
    }

    if (!xarVarFetch('preview', 'isset', $preview, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm', 'isset', $confirm, NULL, XARVAR_DONT_SET)) {return;}
    if (!empty($preview) || !empty($confirm)) {
        if (!xarVarFetch($data['name'],'isset',$configuration,NULL,XARVAR_NOT_REQUIRED)) return;

        // pass the current value as configuration rule
        $data['configuration'] = isset($configuration) ? $configuration : '';

        $isvalid = $property->updateConfiguration($data);

        if ($isvalid) {
            $data['configuration'] = $property->configuration;
        } else {
            $data['invalid'] = $property->invalid;
        }

    // pass the current value as configuration rule
    } elseif (!empty($property->configuration)) {
        $data['configuration'] = $property->configuration;

    } else {
        $data['configuration'] = null;
    }

    // pass the id for the input field here
    $data['id']         = 'dd_'.$proptype;
    $data['tabindex']   = !empty($tabindex) ? $tabindex : 0;
    $data['maxlength']  = !empty($maxlength) ? $maxlength : 254;
    $data['size']       = !empty($size) ? $size : 50;

    // call its showConfiguration() method and return
    $data['showval'] = $property->showConfiguration($data);
    $data['proptype'] = $proptype;
    $data['propertytype'] = $property;
    $data['propinfo'] =& $property;
    $object = & DataPropertyMaster::getProperty(array('type' => $proptype));
    $data['propertytype'] = $object;

    xarTpl::setPageTitle(xarML('Sample Configuration for DataProperty Type #(1)', $proptype));

    return $data;
}

?>