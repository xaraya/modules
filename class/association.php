<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('xaraya.structures.query');

class Association extends Object
{
    public $tables;
    
    function __construct()
    {
        xarMod::apiLoad('keywords');
        $this->tables = xarDB::getTables();
    }
    
/**
 * Utility function to synchronise keyword associations on validation
 * Given a list of resokeywordurce IDs, this makes sure that there is an entry in the associations table 
 * for each with the appropriate $itemid, $itemtype, $moduleid
 * 
 */
    function sync_associations($moduleid = 0, $itemtype = 0, $itemid = 0, $keyword_ids = array())
    {var_dump($keyword_ids);
        // see if we have anything to work with
        if (empty($moduleid) || empty($itemid)) return;

        // (try to) check if we're previewing or not
        xarVarFetch('preview', 'isset', $preview, false, XARVAR_NOT_REQUIRED);
        if (!empty($preview)) return;

        // get the current keyword associations for this module item
        $assoc = $this->get_associations(array('module_id'    => $moduleid,
                                               'itemtype'     => $itemtype,
                                               'itemid'       => $itemid));

        // see what we need to add or delete
        if (!empty($assoc) && count($assoc) > 0) {
            $add = array_diff($keyword_ids, array_keys($assoc));
            $del = array_diff(array_keys($assoc), $keyword_ids);
        } else {
            $add = $keyword_ids;
            $del = array();
        }

        foreach ($add as $id) {
            if (empty($id)) continue;
            $this->add_association(array('keyword_id'      => $id,
                                         'module_id'    => $moduleid,
                                         'itemtype'     => $itemtype,
                                         'itemid'       => $itemid));
        }
        foreach ($del as $id) {
            if (empty($id)) continue;
            $this->delete_association(array('keyword_id'      => $id,
                                            'module_id'    => $moduleid,
                                            'itemtype'     => $itemtype,
                                            'itemid'       => $itemid));
        }
    }

/**
 *  Retrieve a list of keyword assocations for a particular module/itemtype/item combination
 *
 * @author Carl P. Corliss
 * @access public
 * @param   integer module_id     The id of module this keyword is associated with
 * @param   integer itemtype      The item type within the defined module
 * @param   integer itemid        The id of the item types item
 * @param   integer keyword_id    The id of the keyword we are going to associate with an item
 *
 * @return array   A list of associations, including the keyword_id -> (module_id + itemtype + property_id + itemid)
 */

    function get_associations($args=array())
    {
        $q = new Query('SELECT', $this->tables['keywords_index']);
        if (isset($args['keyword_id'])) {
            $q->in('keyword_id', (int)$args['keyword_id']);    
        }
        
        // We need all the $args to have values
        if (empty($args['module_id']) || empty($args['itemtype']) || empty($args['itemid'])) return array();
        
        $q->eq('module_id', (int)$args['module_id']);
        $q->eq('itemtype', (int)$args['itemtype']);
        $q->eq('itemid', (int)$args['itemid']);
    
        if (!$q->run()) return false;
        $result = array();
        foreach ($q->output() as $row) $result[$row['keyword_id']] = $row;
        return $result;
    }

/**
 *  Remove an assocation between a particular keyword and module/itemtype/item.
 *  <br />
 *  If just the keyword_id is passed in, all assocations for that keyword will be deleted.
 *  If the keyword_id and module_id are supplied, any assocations for the given keyword and module_id
 *  will be removed. The same holds true for itemtype and itemid.
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer keyword_id    The id of the keyword we are going to remove association with
 *  @param   integer module_id     The id of module this keyword is associated with
 *  @param   integer itemtype      The item type within the defined module
 *  @param   integer itemid        The id of the item types item
 *
 *  @return bool TRUE on success, FALSE with exception on error
 */

    function delete_association($args=array())
    {
        $q = new Query('DELETE', $this->tables['keywords_index']);
        if (!isset($args['keyword_id'])) {
            // Nothing to delete
        } elseif (is_array($args['keyword_id'])) {
            $q->in('keyword_id', $args['keyword_id']);
        } else {
            $q->eq('keyword_id', (int)$args['keyword_id']);
        }
    
        if (isset($args['module_id'])) {
            $q->eq('module_id', (int)$args['module_id']);
            if (isset($args['itemtype'])) {
                $q->eq('itemtype', (int)$args['itemtype']);
                if (isset($args['itemid'])) {
                    $q->eq('itemid', (int)$args['itemid']);
                }
            }
        }

        if (!$q->run()) return false;
        return true;
    }

/**
 *  Create an assocation between a (stored) keyword and a module/itemtype/item
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer keyword_id    The id of the keyword we are going to associate with an item
 *  @param   integer module_id     The id of module this keyword is associated with
 *  @param   integer itemtype      The item type within the defined module
 *  @param   integer itemid        The id of the item types item
 *
 *  @return integer The id of the keyword that was associated, FALSE with exception on error
 */

    function add_association($args=array())
    {
        $q = new Query('INSERT', $this->tables['keywords_index']);
        if (!isset($args['keyword_id'])) throw new Exception(xarML('Missing parameter [#(1)]', 'keyword_id'));
        if (!isset($args['module_id']))  throw new Exception(xarML('Missing parameter [#(1)]', 'module_id'));
        if (!isset($args['itemtype']))   $args['itemtype'] = 0;    
        
        // If we don't have an itemid, just bail
        if (empty($args['itemid'])) return $args['keyword_id'];
    
        $q->addfield('keyword_id',  (int)$args['keyword_id']);
        $q->addfield('module_id',   (int)$args['module_id']);
        $q->addfield('itemtype',    (int)$args['itemtype']);
        $q->addfield('itemid',      (int)$args['itemid']);
        if (!$q->run()) return false;
        return $args['keyword_id'];
    }

/**
 *  Retrieve the total count associations for a particular module/itemtype/item combination
 *
 * @author  Carl P. Corliss
 * @access  public
 * @param   integer keyword_id  The id of the keyword, or an array of keyword_id's
 * @param   integer module_id   The id of module this keyword is associated with
 * @param   integer itemtype    The item type within the defined module
 * @param   integer itemid      The id of the item types item

 * @return mixed             The total number of associations for particular module/itemtype/item combination
 *                           or an array of keyword_id's and their number of associations
 */

    function count_associations($args=array())
    {
        $q = new Query('SELECT', $this->tables['keywords_index']);
        if (isset($keyword_id)) {
            if (is_array($args['keyword_id'])) {
                $q->in('keyword_id', $args['keyword_id']);    
                $isgrouped = 1;
            } else {
                $q->eq('keyword_id', $args['keyword_id']);    
            }
        }
    
        if (isset($args['module_id'])) {
            $q->addfield('module_id', $args['module_id']);    
            if (isset($args['itemtype'])) {
                $q->addfield('itemtype', $args['itemtype']);
                if (isset($itemid)) {
                    $q->addfield('itemid', $args['itemid']);
                }
            }
        }
    
        if (empty($isgrouped)) {
            $q->addfield('COUNT(id)');
        } else {
            $q->addfield('id');
            $q->addfield('COUNT(*)');
            $q->setgroup('id');
        }
        if (!$q->run()) return false;
        $result = count($q->output());
        return $result;
    }
}
?>