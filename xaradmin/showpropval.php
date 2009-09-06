<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * Show validation of some property
 */
function articles_admin_showpropval($args)
{
    extract($args);

    // get the property id
    if (!xarVarFetch('ptid',    'id',    $ptid)) {return;}
    if (!xarVarFetch('field',   'str:1', $field)) {return;}
    if (!xarVarFetch('preview', 'isset', $preview, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('confirm', 'isset', $confirm, NULL, XARVAR_DONT_SET)) {return;}

    // Get current publication types
    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    if (empty($pubtypes[$ptid]['config'][$field])) {
        $msg = xarML('Invalid item id');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
        return;
    }

    // Get current configuration
    $info = $pubtypes[$ptid]['config'][$field];

    $fieldformatnums = xarModAPIFunc('articles','user','getfieldformatnums');
    $proptype = $fieldformatnums[$info['format']];
    $validation = !empty($info['validation']) ? $info['validation'] : '';
    $id = 0;

    // check if the module+itemtype this property belongs to is hooked to the uploads module
    if (xarModIsHooked('uploads', 'articles', $ptid)) {
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
        $data['validation'] = isset($value) ? $value : '';

        $isvalid = $property->updateValidation($data);

        if ($isvalid) {
            // store the updated validation rule back in the value
            $validation = $property->validation;
            if (!empty($confirm)) {
                if (!xarSecConfirmAuthKey()) return;

                $name = $pubtypes[$ptid]['name'];
                $descr = $pubtypes[$ptid]['descr'];
                $config = $pubtypes[$ptid]['config'];
                $config[$field]['validation'] = $validation;

                if (!xarModAPIFunc('articles', 'admin', 'updatepubtype',
                                   array('ptid' => $ptid,
                                         'name' => $name,
                                         'descr' => $descr,
                                         'config' => $config))) {
                    return; // throw back
                }

                if (!xarVarFetch('return_url', 'isset', $return_url,  NULL, XARVAR_DONT_SET)) {return;}
                if (empty($return_url)) {
                    // return to modifyprop
                    $return_url = xarModURL('articles', 'admin', 'pubtypes',
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
    $data['validation'] = $value;

    // call its showValidation() method and return
    $data['showval'] = $property->showValidation($data);

    $data['ptid'] = $ptid;
    $data['field'] = $field;
    $data['item'] = $pubtypes[$ptid];

    // Return the template variables defined in this function
    return $data;
}

?>
