<?php
/**
 * Add a new ctype
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Create a new ctype
 */
function content_admin_newcontenttype()
{
    // See if the current user has the privilege to add an item. We cannot pass any extra arguments here
    if (!xarSecurityCheck('AddContentTypes')) return;

    sys::import('modules.dynamicdata.class.objects.master');

    $object = DataObjectMaster::getObject(array('name' => 'content_types'));
    $data['object'] = $object;

    // Get form input if any
    $isvalid = $object->properties['content_type']->checkInput();
    $ctype = $object->properties['content_type']->getValue();
    $object->properties['label']->checkInput();
    $label =  $object->properties['label']->getValue();
    $ctype = xarMod::APIFunc('content','admin','slug',array('string' => $ctype));

    $isvalid = $object->properties['model']->checkInput();
    $model = $object->properties['model']->getValue();

    if (isset($ctype) && !empty($ctype)) { 
        $list = DataObjectMaster::getObjectList(array(
                            'name' => 'content_types',
                            'where' => 'content_type eq \'' . $ctype . '\''
                            ));
    
        $num = $list->countItems();  
        if ($num > 0) {
            return 'There is already a content type named \'' . $ctype . '\'. <a href="' . xarModURL('content','admin','newcontenttype') . '">Please try again</a>.';
        }

        $info = DataObjectMaster::getObjectInfo(array('name' => $ctype));
        $dupexists = !empty($info);
        if ($dupexists)  {
            return 'There is already a DataObject named \'' . $ctype . '\'. <a href="' . xarModURL('content','admin','newcontenttype') . '">Please try again</a>.';
        }
    }

    // Check if we are in 'preview' mode from the input here - the rest is handled by checkInput()
    // Here we are testing for a button clicked, so we test for a string
    if(!xarVarFetch('preview', 'str', $data['preview'],  NULL, XARVAR_DONT_SET)) {return;}

    // Check if we are submitting the form
    // Here we are testing for a hidden field we define as true on the template, so we can use a boolean (true/false)
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    if ($data['confirm']) {

        if (!xarVarFetch('add_pdate',    'checkbox',   $data['add_pdate'], false,     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('add_expdate',    'checkbox',   $data['add_expdate'], false,     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('add_pstatus',    'checkbox',   $data['add_pstatus'], false,     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('add_pauthor',    'checkbox',   $data['add_pauthor'], false,     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('add_dtemplate',    'checkbox',   $data['add_dtemplate'], false,     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('add_datecreated',    'checkbox',   $data['add_datecreated'], false,     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('add_path',    'checkbox',   $data['add_path'], false,     XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('add_datemodified',    'checkbox',   $data['add_datemodified'], false,     XARVAR_NOT_REQUIRED)) return;

        // Fill in any values in the fields if we have to return because validation failed
        if(!xarVarFetch('ctype', 'str', $data['ctype'],  NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('model', 'str', $data['model'],  NULL, XARVAR_DONT_SET)) {return;}

        // Check for a valid confirmation key. The value is automatically gotten from the template
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        // Get the data from the form and see if it is all valid
        // Either way the values are now stored in the object
        $isvalid = $object->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with the data we picked up and with error messages
            return xarTplModule('content','admin','newcontenttype', $data);        
        } elseif (isset($data['preview'])) {
            // Show a preview, same thing as the above essentially
            return xarTplModule('content','admin','newcontenttype', $data);        
        } else {

            //import
            $file = sys::code() . 'modules/content/xardata/'. $model . '.xml';
            $objectid = xarMod::apiFunc('dynamicdata','util','import',array('file' => $file));
            $data['objectid'] = $objectid;

            if (isset($objectid)) { // Good data: create the item

                $ctobject = DataObjectMaster::getObject(array('name' => 'objects'));
                $ctobject->getItem(array('itemid' => $objectid));
                $ctobject->properties['name']->setValue($ctype);
                $ctobject->properties['label']->setValue($label);
                $ctobject->updateItem();
                
                if ($data['add_dtemplate']) {
                    xarMod::apiFunc('content','admin','adddisplaytemplate',array('objectid' => $objectid, 'ctype' => $ctype));
                }

                if ($data['add_pdate']) {
                    xarMod::apiFunc('content','admin','addpubdate',array('objectid' => $objectid));
                }

                if ($data['add_expdate']) {
                    xarMod::apiFunc('content','admin','addexpdate',array('objectid' => $objectid));
                }

                if ($data['add_pstatus']) {
                    xarMod::apiFunc('content','admin','addpubstatus',array('objectid' => $objectid));
                }

                if ($data['add_pauthor']) {
                    xarMod::apiFunc('content','admin','addpubauthor',array('objectid' => $objectid));
                }

                if ($data['add_datecreated']) {
                    xarMod::apiFunc('content','admin','adddatecreated',array('objectid' => $objectid));
                }

                if ($data['add_datemodified']) {
                    xarMod::apiFunc('content','admin','adddatemodified',array('objectid' => $objectid));
                }

                if ($data['add_path']) {
                    xarMod::apiFunc('content','admin','addpath',array('objectid' => $objectid));
                }

                //We want these content types to have the same itemid as the associated DataObject
                $object->properties['content_type']->setValue($ctype);
                $object->properties['label']->setValue($label);
                $item = $object->createItem(array('itemid' => $objectid));

                $aliases = xarConfigVars::get(null, 'System.ModuleAliases');
                if (!isset($aliases[$ctype])) { 
                    // $ctype is not an alias, so register one...
                    xarModAlias::set($ctype, 'content');
                } 

            } else {
                print 'some error';
            }

            // Jump to the next page
            xarResponse::redirect(xarModURL('content','admin','viewcontenttypes'));
            // Always add the next line even if processing never reaches it
            return true;
        }
    }

    // Return the template variables defined in this function
    return $data;
}

?>