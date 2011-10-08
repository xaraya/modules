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

    sys::import('xaraya.structures.containers.blocks.basicblock');

    class Publications_FillerBlock extends BasicBlock implements iBlock
    {
        public $pubtype_id          = 0;
        public $fillerid            = 0;
        public $displaytype         = 'summary';
        public $alttitle            = '';
        public $alttext          = '';
        public $state               = '2,3';

        public function __construct(Array $data=array())
        {
            parent::__construct($data);
            $this->text_type = 'Featured Items';
            $this->text_type_long = 'Show featured publications';
            $this->allow_multiple = true;
            $this->show_preview = true;

            $this->toptype = 'ratings';
        }

        public function display(Array $data=array())
        {
            $data = parent::display($data);
        
            // Defaults
            if (empty($data['state'])) {$data['state'] = $this->state;}

            // Setup featured item
            if ($data['fillerid'] > 0) {
        
                $fillerid = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $data['fillerid']));
                $ptid = xarMod::apiFunc('publications','user','getitempubtype',array('itemid' => $data['fillerid']));
                $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
                $pubtypeobject->getItem(array('itemid' => $ptid));
                $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
                $data['object']->getItem(array('itemid' => $data['fillerid']));
        
                $data['content'] = $data;
                return $data;

            } 
            return;
        }
    }

?>