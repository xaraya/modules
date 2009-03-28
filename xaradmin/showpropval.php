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
 * Show validation of some property
 */
function publications_admin_showpropval($args)
{
    extract($args);

    // get the property id
    if (!xarVarFetch('ptid',    'id',    $ptid)) {return;}
    if (!xarVarFetch('field',   'str:1', $field)) {return;}
    if (!xarVarFetch('preview', 'isset', $preview, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm', 'isset', $confirm, NULL, XARVAR_DONT_SET)) {return;}

    // Get current publication types
    $pubtypes = xarModAPIFunc('publications','user','get_pubtypes');

    if (empty($pubtypes[$ptid]['config'][$field])) {
        $msg = xarML('Invalid item id');
        throw new BadParameterException(null,$msg);
    }

    // Get current configuration
    $info = $pubtypes[$ptid]['config'][$field];

    $fieldformatnums = xarModAPIFunc('publications','user','getfieldformatnums');
    $proptype = $fieldformatnums[$info['format']];
    $validation = !empty($info['validation']) ? $info['validation'] : '';
    $id = 0;

    // check if the module+itemtype this property belongs to is hooked to the uploads module
    if (xarModIsHooked('uploads', 'publications', $ptid)) {
        xarVarSetCached('Hooks.uploads','ishooked',1);
    }

    $data = array();
    // get a new property of the right type
    $data['type'] = $proptype;

    $data['name']       = $field;
    $data['label']      = $info['label'];
    // pass the actual id for the property here
    $data['id']         = $field;
    // pass the original invalid value here
    $data['invalid']    = !empty($invalid) ? $invalid :'';
    $property = xarModAPIFunc('dynamicdata','user','getproperty',$data);
    if (empty($property)) return;

    if (!empty($preview) || !empty($confirm)) {
        if (!xarVarFetch($data['name'],'isset',$value,NULL,XARVAR_NOT_REQUIRED)) return;

        // pass the current value as validation rule
        $data['configuration'] = isset($value) ? $value : '';

        $isvalid = $property->updateConfiguration($data);

        if ($isvalid) {
            // store the updated configuration rule back in the value
            $configuration = $property->configuration;
            if (!empty($confirm)) {
                if (!xarSecConfirmAuthKey()) return;

                $descr = $pubtypes[$ptid]['description'];
                $config = $pubtypes[$ptid]['config'];
                $config[$field]['validation'] = $configuration;

                if (!xarModAPIFunc('publications', 'admin', 'updatepubtype',
                                   array('ptid' => $ptid,
                                         'descr' => $descr,
                                         'config' => $config))) {
                    return; // throw back
                }

                if (!xarVarFetch('return_url', 'isset', $return_url,  NULL, XARVAR_DONT_SET)) {return;}
                if (empty($return_url)) {
                    // return to modifyprop
                    $return_url = xarModURL('publications', 'admin', 'viewpubtypes',
                                            array('ptid' => $ptid,
                                                  'action' => 'modify'));
                }
                xarResponseRedirect($return_url);
                return true;
            }
        } else {
            $data['invalid'] = $property->invalid;
        }
    }

    // pass the id for the input field here
    $data['id']         = $field;
    $data['tabindex']   = !empty($tabindex) ? $tabindex : 0;
    $data['maxlength']  = !empty($maxlength) ? $maxlength : 254;
    $data['size']       = !empty($size) ? $size : 50;
    // pass the current value as validation rule
    if (!empty($validation)) {
        $value = $validation;
    } else {
        $value = null;
    }
    $data['configuration'] = $value;

    // call its showConfiguration() method and return
    $data['showval'] = $property->showConfiguration($data);

    $data['ptid'] = $ptid;
    $data['field'] = $field;
    $data['item'] = $pubtypes[$ptid];

    // Return the template variables defined in this function
    return $data;
}

?>
