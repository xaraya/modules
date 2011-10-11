<?php
/**
 * Ephemerids
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ephemerids Module
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * Ephemerids block
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Ephemerids_EphemBlock extends BasicBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'ephem';
    protected $module           = 'ephemerids'; // module block type belongs to, if any
    protected $text_type        = 'Ephemerids';  // Block type display name
    protected $text_type_long   = 'Show Ephemerids'; // Block type description
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

        function display(Array $data=array())
        {
            $data = $this->getContent();

            // Database information
            xarModDBInfoLoad('ephemerids');
            $dbconn =& xarDB::getConn();
        
            $xartable =& xarDB::getTables();
            $ephemtable = $xartable['ephem'];
        
            $data['items'] = array();
            $data['emptycontent'] = false;
        
            // The admin API function is called.
            $ephemlist = xarModAPIFunc('ephemerids',
                                       'user',
                                       'getalltoday');
            $data['items'] = $ephemlist;
            if (empty($data['items'])) {
                $data['emptycontent'] = true;
            }
        
            if (empty($this->title)){
                $this->setTitle(xarML('Historical Reference'));
            }
        
            return $data;
        }
}
?>