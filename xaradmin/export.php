<?php
/**
 * @package modules
 * @subpackage dynamicdata module
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @link http://xaraya.com/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
 * @todo move the xml generate code to a template based system.
 */
/**
 * Export an object definition or an object item to XML
 */
sys::import('modules.dynamicdata.class.objects.master');
//sys::import('modules.eav.class.master');
function eav_admin_export(Array $args=array())
{
    // Security
    if (!xarSecurity::check('ManageEAV')) return;

    extract($args);

    if(!xarVar::fetch('objectid', 'isset', $objectid, NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('name',     'isset', $name    , NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('module_id','isset', $moduleid, NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('itemtype', 'isset', $itemtype, NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('itemid',   'isset', $itemid,   NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('tofile',   'isset', $tofile,   NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('convert',  'isset', $convert,  NULL, xarVar::DONT_SET)) {return;}

    $modulename = 'eav';
    $data = array();
    $data['menutitle'] = xarML('EAV Data Utilities');
	//Fill object dropdown
	$data['object'] = DataObjectMaster::getObjectList(array('name' => 'eav_entities'));
    $items = $data['object']->getItems();
    if(empty($items)) {
    	$data['errormessage'] = xarML('No entitites exist.');
    	return $data;
    }
    $options = array();
    foreach ($items as $item) {
    	$object = DataObjectMaster::getObjectInfo(
                                array('moduleid' => $item['module'],
                                      'objectid' => $item['object']));
        $objectname = $object['name'];
        array_push($options, array('id' => $item['object'], 'name' => $objectname));
    }
    if (!empty($options) && count($options) >= 0 ) {
    	$data['options'] = $options;
    } 

    //get the first item id in array if object id is null 
    if (empty($objectid)) {
    	$objectid = $options[0]['id'];
    }
    $myobject = DataObjectMaster::getObject(array('objectid' => $objectid,
                                         		  'name'     => $name,
                                         		  'itemid'   => $itemid,
                                         		   'allprops' => true));
    
    if (!isset($myobject) || empty($myobject->label)) {
        $data['label'] = xarML('Unknown Object');
        $data['xml'] = '';
        return $data;
    }
    // check security of the object
    if (!$myobject->checkAccess('config'))
        return xarResponse::Forbidden(xarML('Configure #(1) is forbidden', $myobject->label));


    $proptypes = DataPropertyMaster::getPropertyTypes();
    $prefix = xarDB::getPrefix();
    $prefix .= '_';

    $xml = '';

    // export object definition
    if (empty($itemid)) {
        $data['label'] = xarML('Export Object Definition for #(1)', $myobject->label);

    	 $xml = xarMod::apiFunc('eav','util','export', 
    	 						array('objectref' => &$myobject, "objectid" => $objectid));
	    

        $data['formlink'] = xarController::URL('eav','admin','export',
                                      array('objectid' => $myobject->objectid,
                                            'itemid'   => 'all'));
        $data['filelink'] = xarController::URL('eav','admin','export',
                                      array('objectid' => $myobject->objectid,
                                            'itemid'   => 'all',
                                            'tofile'   => 1));

        if (!empty($myobject->datastores) && count($myobject->datastores) == 1 && !empty($myobject->datastores['_dynamic_data_'])) {
            $data['convertlink'] = xarController::URL('eav','admin','export',
                                             array('objectid' => $myobject->objectid,
                                                   'convert'  => 1));
            if (!empty($convert)) {
                if (!xarMod::apiFunc('eav','util','maketable',
                                   array('objectref' => &$myobject))) return;

            }
        }

    // export specific item
    } elseif (is_numeric($itemid)) {
        $data['label'] = xarML('Export Data for #(1) # #(2)', $myobject->label, $itemid);

        $myobject->getItem();

        $xml .= '<'.$myobject->name.' itemid="'.$itemid.'">'."\n";
        foreach (array_keys($myobject->properties) as $name) {
            $xml .= "  <$name>" . xarVar::prepForDisplay($myobject->properties[$name]->value) . "</$name>\n";
        }
        $xml .= '</'.$myobject->name.">\n";

    // export all items (better save this to file, e.g. in var/cache/...)
    } elseif ($itemid == 'all') {
        $data['label'] = xarML('Export Data for all #(1) Items', $myobject->label);
		
        $mylist = DataObjectMaster::getObjectList(array('objectid' => $objectid,
                                                'moduleid' => $moduleid,
                                                'itemtype' => $itemtype));

        $mylist->getItems(array('getvirtuals' => 1));
      
        if (empty($tofile)) {
            $xml .= "<items>\n";
            foreach ($mylist->items as $itemid => $item) {
                $xml .= '  <'.$mylist->name.' itemid="'.$itemid.'">'."\n";
                foreach ($mylist->properties as $name => $property) {
                    if (isset($item[$name])) {
                        if ($name == 'configuration') {
                        // don't replace anything in the serialized value
                            $xml .= "    <$name>" . $item[$name];
                        } else {
                            $xml .= "    <$name>";
                            $xml .= $property->exportValue($itemid, $item);
                        }
                    } else {
                        $xml .= "    <$name>";
                    }
                    $xml .= "</$name>\n";
                }
                $xml .= '  </'.$mylist->name.">\n";
            }
            $xml .= "</items>\n";

        } else {
            $varDir = sys::varpath();
            $outfile = $varDir . '/uploads/' . xarVar::prepForOS($mylist->name) . '.data.' . xarLocale::formatDate('%Y%m%d%H%M%S',time()) . '.xml';
            $fp = @fopen($outfile,'w');
            if (!$fp) {
                $data['xml'] = xarML('Unable to open file #(1)',$outfile);
                return $data;
            }
            fputs($fp, "<items>\n");
            foreach ($mylist->items as $itemid => $item) {
                fputs($fp, "  <".$mylist->name." itemid=\"$itemid\">\n");
                foreach (array_keys($mylist->properties) as $name) {
                    if (isset($item[$name])) {
                        fputs($fp, "    <$name>" . xarVar::prepForDisplay($item[$name]) . "</$name>\n");
                    } else {
                        fputs($fp, "    <$name></$name>\n");
                    }
                }
                fputs($fp, "  </".$mylist->name.">\n");
            }
            fputs($fp, "</items>\n");
            fclose($fp);
            $xml .= xarML('Data saved to #(1)',$outfile);
        }

    } else {
        $data['label'] = xarML('Unknown Request for #(1)', $label);
        $xml = '';
    }

    $data['objectid'] = $objectid;
    $data['xml'] = xarVar::prepForDisplay($xml);

    xarTpl::setPageTemplateName('admin');

    return $data;
}

?>