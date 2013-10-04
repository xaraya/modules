<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
 
function publications_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication ID', 'admin', 'delete',
                    'Publications');
        throw new BadParameterException(null,$msg);
    }
    $ids = !is_array($itemid) ? explode(',',$itemid) : $itemid;
    if (!isset($deletetype)) $deletetype = 0;
    
    sys::import('xaraya.structures.query');
    $table =& xarDB::getTables();
    
    switch ($deletetype) {
        case 0:
        default:
            $q = new Query('UPDATE', $table['publications']);
            $q->addfield('state', 0);
        break;
        
        case 10:
            $q = new Query('DELETE', $table['publications']);
        break;
    }

    $q->in('id',$ids);
    if (!$q->run()) return false;
    return true;
}

?>
