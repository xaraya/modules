<?php
/**
 * Add a new item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Menu Tree Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * Create a new item of the menutree object
 */
function menutree_admin_new()
{
    //See if the current user has the privilege to add an item. We cannot pass any extra arguments here
    if (!xarSecurityCheck('AddMenuTree')) return;

    //Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    //Get the object we'll be working with
    $data['object'] = DataObjectMaster::getObject(array('name' => 'menutree'));
	$properties = $data['object']->getProperties();
	$relative_propname = 'dd_' . $properties['relative']->id; 
	$relationship_propname = 'dd_' . $properties['relationship']->id;

    //Check if we are in 'preview' mode from the input here - the rest is handled by checkInput()
    //Here we are testing for a button clicked, so we test for a string
    if(!xarVarFetch('preview', 'str', $data['preview'],  NULL, XARVAR_DONT_SET)) {return;}

    //Check if we are submitting the form
    //Here we are testing for a hidden field we define as true on the template, so we can use a boolean (true/false)
    
	if (!xarVarFetch($relative_propname, 'int', $data['relative'],  NULL, XARVAR_NOT_REQUIRED)) {return;}
	if (!xarVarFetch($relationship_propname, 'int', $data['relationship'],  1, XARVAR_NOT_REQUIRED)) {return;}
	if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

	$data['relationship'] = (int)$data['relationship'];

    if ($data['confirm']) {

        //Check for a valid confirmation key. The value is automatically gotten from the template
        if (!xarSecConfirmAuthKey()) {
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }        

        //Get the data from the form and see if it is all valid
        //Either way the values are now stored in the object
		//$data['object']->properties['seq']->setInputStatus(DataPropertyMaster::DD_INPUTSTATE_IGNORED);  
		
		//$isvalid = $data['object']->checkInput();
        $isvalid = $data['object']->properties['link']->checkInput();

        if (!$isvalid) {
            //Bad data: redisplay the form with the data we picked up and with error messages
            return xarTplModule('menutree','admin','new', $data);        
        } elseif (isset($data['preview'])) {
            //Show a preview, same thing as the above essentially
            return xarTplModule('menutree','admin','new', $data);        
        } else {
            //Good data: create the item 

			if(is_numeric($data['relative'])) {
				$obj = DataObjectMaster::getObject(array('name' => 'menutree'));
				$obj->getItem(array('itemid' => $data['relative'])); 
				$vals = $obj->getFieldValues(); 
				$seq = $vals['seq']; //the relative's seq
				if ($data['relationship'] < 3) { //sibling
					$parentid = $vals['parentid'];
					if ($data['relationship'] == 2) { //right after
						$offspring = xarMod::apiFunc('menutree','user','getitemlevels',array('parentid' => $data['relative']));
						if(!empty($offspring)) { 
							foreach ($offspring as $key=>$value) {
								$obj = DataObjectMaster::getObject(array(
									'name' => 'menutree' 
								)); 
								$obj->getItem(array('itemid' => $key));
								$vals = $obj->getFieldValues();
								$seq_arr[] = $vals['seq'];
							}
							asort($seq_arr);
							$seq = end($seq_arr); //the last seq of all the relative's offspring
						} 
						$seq = $seq + 1; //don't increment the relative's seq
					}
				} else { //first child
					$parentid = $data['relative'];
					$seq = $seq + 1;
				}

				// get all the items that will need their seq incremented
				$obj = DataObjectMaster::getObjectList(array(
						'name' => 'menutree',
						'where' => 'seq ge ' . $seq
					));
				$items = $obj->getItems();
				//increment the seq of all items that follow our new item
				foreach ($items as $item) {
					$obj = DataObjectMaster::getObject(array(
						'name' => 'menutree' 
					));
					$obj->getItem(array('itemid' => $item['itemid']));
					$new = $item['seq']+1;
					$obj->properties['seq']->setValue($new);
					$obj->updateItem();
				}

			} else { //relative is null: no menu items yet
				$seq = '';
				$parentid = '';
			} 

			$data['object']->properties['seq']->setValue($seq);
			$data['object']->properties['parentid']->setValue($parentid);
			$item = $data['object']->createItem();

            //Jump to the next page
            xarResponse::Redirect(xarModURL('menutree','admin','menus'));
            //Always add the next line even if processing never reaches it
            return true;
        }
    }

    //Return the template variables defined in this function
    return $data;
}

?>