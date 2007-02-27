<?php
/**
 * AddressBook admin functions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */
/**
 * Display form used to update the custom field settings
 * Handle the data submission
 *
 * @param GET / POST passed from modifycustomfields form
 * @return xarTemplate data
 */
function addressbook_admin_modifycustomfields()
{

    $output = array(); // template contents go here
    /**
     * Security check first
     */
    if (xarSecurityCheck('AdminAddressBook',0)) {
        /*
         * Check if we've come in via a submit, commit everything and cary on
         */
        xarVarFetch('formSubmit', 'str::', $formSubmit,FALSE);
        if ($formSubmit) {
            if (!xarSecConfirmAuthKey())
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

            if (!xarVarFetch ('id', 'array::',$formData['id'], FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('custDisplay', 'array::',$formData['custDisplay'], FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('custShortLabel', 'array::',$formData['custShortLabel'], FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('del', 'array::',$formData['del'], FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('custLabel', 'array::',$formData['custLabel'], FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('custType','array::',$formData['custType'],FALSE)) {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('incID','int::',$formData['incID'],FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('decID','int::',$formData['decID'],FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('newDisplay', 'checkbox',$formData['newDisplay'], FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('newShortLabel', 'str::30',$formData['newShortLabel'], FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('newname','str::30',$formData['newname'],FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('newtype','str::30',$formData['newtype'],FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }

            /**
             * Perform the update
             */
            if (!xarModAPIFunc('addressbook','admin','updatecustomfields',$formData)) {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
        }
//FIXME:<garrett> would rather use a userapi here
        $output['custfields'] = xarModAPIFunc('addressbook','admin','getcustomfields');
        if(!is_array($output['custfields'])) {
            return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
        }

        //TODO - this should be in a table & configurable. geh
        $output['datatypes'][] = array('id'=>_AB_CUSTOM_TEXT_SHORT,     'name'=>'Text, 60 chars, 1 line');
        $output['datatypes'][] = array('id'=>_AB_CUSTOM_TEXT_MEDIUM,    'name'=>'Text, 120 chars, 2 lines');
        $output['datatypes'][] = array('id'=>_AB_CUSTOM_TEXT_LONG,      'name'=>'Text, 240 chars, 4 lines');
        $output['datatypes'][] = array('id'=>_AB_CUSTOM_INTEGER,        'name'=>'Integer numbers');
        $output['datatypes'][] = array('id'=>_AB_CUSTOM_DECIMAL,        'name'=>'Decimal numbers');
        $output['datatypes'][] = array('id'=>_AB_CUSTOM_CHECKBOX,       'name'=>'Checkbox');
        $output['datatypes'][] = array('id'=>_AB_CUSTOM_DATE,           'name'=>'Date');
        $output['datatypes'][] = array('id'=>_AB_CUSTOM_BLANKLINE,      'name'=>'Blank line');
        $output['datatypes'][] = array('id'=>_AB_CUSTOM_HORIZ_RULE,     'name'=>'Horizontal rule');

        // Generate a one-time authorisation code for this operation
        $output['authid'] = xarSecGenAuthKey();

        // Submit button
        $output['btnCommitText'] = xarML('Commit Changes');

    } else {
        return xarTplModule('addressbook','user','noauth');
    }

    return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

} // END modifycustomfields

?>
