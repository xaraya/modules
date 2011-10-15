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
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'filler';
    protected $module           = 'publications'; // module block type belongs to, if any
    protected $text_type        = 'Featured Items';  // Block type display name
    protected $text_type_long   = 'Show featured publications'; // Block type description
    // Additional info, supplied by developer, optional 
    protected $type_category    = 'block'; // options [(block)|group] 
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';
    
    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    // @todo: drop the show_help flag, and go back to checking if help method is declared 
    protected $show_help    = false; // let the subsystem know if this block type has a help() method


    public $pubtype_id          = 0;
    public $fillerid            = 0;
    public $displaytype         = 'summary';
    public $alttitle            = '';
    public $alttext             = '';
    // chris: state is a reserved property name used by blocks
    //public $state               = '2,3';
    public $pubstate            = '2,3';
    public $toptype             = 'ratings'; 

    public function display()
    {
        $data = $this->getContent();

        // Setup featured item
        if ($data['fillerid'] > 0) {
        
            $fillerid = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $data['fillerid']));
            $ptid = xarMod::apiFunc('publications','user','getitempubtype',array('itemid' => $data['fillerid']));
            $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
            $pubtypeobject->getItem(array('itemid' => $ptid));
            $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
            $data['object']->getItem(array('itemid' => $data['fillerid']));

            return $data;

        } 
        return;
    }
}
?>