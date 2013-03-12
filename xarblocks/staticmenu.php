<?php
/**
 * Displays a menu block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004-2009 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 * @author Jason Judge
*/

/**
 * init func
 */

    sys::import('xaraya.structures.containers.blocks.basicblock');

    class Publications_StaticmenuBlock extends BasicBlock implements iBlock
    {
        public $name                = 'StaticmenuBlock';
        public $module              = 'publications';
        public $text_type           = 'Content';
        public $text_type_long      = 'Publications Static Menu Block';
        public $allow_multiple      = true;

        public $form_content        = false;
        public $form_refresh        = false;

        public $show_preview        = true;
        public $multi_homed         = true;
        public $current_source      = 'AUTO'; // Other values: 'DEFAULT'
        public $default_id          = 0; // 0 == 'None'
        public $root_ids            = array();
        public $prune_ids           = array();
        public $max_level           = 0;
        public $start_level         = 0;

        public $func_update         = 'menublock_update';
        public $notes               = "no notes";


/**
 * Display func.
 * @param $blockinfo array
 * @returns $blockinfo array
 */

/**
 * Display func.
 * @param $blockinfo array
 * @returns $blockinfo array
 */
    function display(Array $data=array())
    {
        $data = $this->getContent();

        // Pointer to simplify referencing.
        $vars =& $data;

#------------------------------------------------------------
# If we don't have any page data, then fetch it now
#
        if (empty($pagedata)) {
            $pagedata = xarMod::apiFunc('publications', 'user', 'get_menu_pages');
        }
        
#------------------------------------------------------------
# Add the chosen pages as nodes to a graph
#
        sys::import('xaraya.structures.graph');
        $g = new Graph();
        foreach ($pagedata as $page) {
            $n = new GraphNode();
            $n->setData($page);
            $n->setIndex($page['id']);
            $g->addNode($n);
        }
        
#------------------------------------------------------------
# Connect the nodes according to which page has which ancestor
#
        $allnodes =& $g->getNodes();
        foreach ($allnodes as $k => $n) {
            $ndata = $n->getData();
            $thisparent = $ndata['parentpage_id'];
            
            // If we are at the top level, then no parent to connect to
            if (empty($thisparent)) continue;
            
            // Otherwise carry on
            $thisindex = $ndata['id'];
            $thisleft = $ndata['leftpage_id'];
            $parentcandidate = null;
            
            foreach ($allnodes as $k1 => $n1) {
                $n1data = $n1->getData();
                $thatindex = $n1data['id'];
                
                // Don't bother testing the node against itself
                if ($thisindex == $thatindex) continue;
                
                $thatleft = $n1data['leftpage_id'];
                $thatright = $n1data['rightpage_id'];
                if ($thisparent == $thatindex) {
                    // Found a direct parent, connect the nodes
                    $allnodes[$k]->connectTo($allnodes[$k1]);
                    $parentcandidate = null;
                    break;
                } elseif (($thisleft > $thatleft) && ($thisleft < $thatright)) {
                    if (empty($parentcandidate) || ($parentcandidate[1] < $thatleft)) {
                        // Replace the current parent candidate if this one is a closer ancestor
                        $parentcandidate = array($thatindex,$thatleft,$allnodes[$k1]);
                    }
                }
            }
            // Check if there is a parent candidate pending
            if (!empty($parentcandidate)) {
                // There is, connect the nodes
                $allnodes[$k]->connectTo($parentcandidate[2]);
                // Update the parent of this node so we don't have to do this searching again
                $ndata['parentpage_id'] = $parentcandidate[0];
                $allnodes[$k]->setData($ndata);
            }
        }
        
#------------------------------------------------------------
# Sort the nodes
#
        // $sorter = new TopologicalSorter();
        // $result = $sorter->sort($g);

#------------------------------------------------------------
# Rearrange them as needed for menus
#
        // Define some things we will need
        $data['menuarray'] = array();
        
        $access = DataPropertyMaster::getProperty(array('name' => 'access'
        ));
        
        // Get the information on publication types
        $pubtypes = DataObjectMaster::getObjectList(array('name' => 'publications_types'));
        $pubtypes->dataquery->gt('state',2);
        $typeinfo = $pubtypes->getItems();

        foreach ($g->getNodes() as $node) {
            $ndata = $node->getData();
            
            // Ignore this page if we don't have display access
            $access->value = $ndata['access'];
            $filter = $access->getValue();
            if (!$access->check($filter['display'])) continue;
            
            // Figure out where the data for the label will be taken from
            $menusource = $ndata['menu_source_flag'];
            if ($menusource == 1) $menusource = $typeinfo[$ndata['pubtype_id']]['menu_source_flag'];
            switch ($menusource) {
                case 2: default: $label = 'title'; break;
                case 3: $label = 'description'; break;
                case 4: $label = 'menu_alias'; break;
            }
            
            // Assemble the menu item
            $data['menuarray'][$ndata['parentpage_id']][] = array(
                                                        'id'             => $ndata['id'],
                                                        'name'           => $ndata['name'],
                                                        'label'          => !empty($ndata[$label]) ? $ndata[$label] : $ndata['name'],
                                                        'state'          => $ndata['state'],
                                                        'redirect_flag'  => $ndata['redirect_flag'],
                                                    );
        }
        return $data;
    }
}

?>