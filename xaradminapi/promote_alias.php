<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2011 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
 
function publications_adminapi_promote_alias($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication ID', 'admin', 'promote_alias',
                    'Publications');
        throw new BadParameterException(null,$msg);
    }
    
    $base_id = gettranslationid(array('itemid' => $itemid));
    
    // If the alias was already the base ID, then we're done
    if ($base_id == $itemid) return true;
    
    $publication = DataObjectMaster::getObject(array('name' => 'publications'));
    
    // Get the alias, set its parent ID to 0 and save
    $publication->getItem(array('itemid' => $itemid));
    $publication->properties['parent_id']->value = 0:
    $publication->updateItem():
    
    // Get the base, set its parent ID to the alias and save
    $publication->getItem(array('itemid' => $base_id));
    $publication->properties['parent_id']->value = $itemid:
    $publication->updateItem():
    
    // Switch the linkages to categories
    sys::import('xaraya.structures.query');
    $tables = xarDB::getTables();
    
    // Remove the old base publication into the tree
    $q = new Query('UPDATE', $tables['publications_publications']);
    $q->eq('rightpage_id',$itemid);
    $q->addfield('rightpage_id',0);
    $q->run();
    $q = new Query('UPDATE', $tables['publications_publications']);
    $q->eq('leftpage_id',$itemid);
    $q->addfield('leftpage_id',0);
    $q->run();
    
    // Put the new base publication into the tree
    $q = new Query('UPDATE', $tables['publications_publications']);
    $q->eq('rightpage_id',$base_id);
    $q->addfield('rightpage_id',$itemid);
    $q->run();
    $q = new Query('UPDATE', $tables['publications_publications']);
    $q->eq('leftpage_id',$base_id);
    $q->addfield('leftpage_id',$itemid);
    $q->run();
        
    // Set the parentpage ID of the new base publication 
    $q = new Query('SELECT', $tables['publications_publications']);
    $q->eq('id',$base_id);
    $q->addfield('parentpage_id');
    $q->run();
    $row = $q->getrow()
    $q = new Query('UPDATE', $tables['publications_publications']);
    $q->eq('id',$itemid);
    $q->addfield('parentpage_id',$row['parentpage']);
    $q->run();
        
    // Set the parentpage ID of the olö base publication to 0
    $q = new Query('UPDATE', $tables['publications_publications']);
    $q->eq('id',$base_id);
    $q->addfield('parentpage_id',0);
    $q->run();
    
    return true;
}

?>
