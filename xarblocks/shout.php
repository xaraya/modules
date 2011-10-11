<?php
/**
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

    sys::import('xaraya.structures.containers.blocks.basicblock');

    class Shouter_ShoutBlock extends BasicBlock implements iBlock
    {
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'shout';
    protected $module           = 'shouter'; // module block type belongs to, if any
    protected $text_type        = 'Shout';  // Block type display name
    protected $text_type_long   = 'Show shouts'; // Block type description
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

        public $numitems            = 5;
        public $blockwidth          = 180;
        public $blockwrap           = 19;
        public $allowsmilies        = true;
        public $lightrow            = 'FFFFFF';
        public $darkrow             = 'E0E0E0';
        public $shoutblockrefresh   = 0;
        public $anonymouspost       = false;


    /**
     * Display shoutblock
     *
     * @param array $blockinfo
     * @return array
     */
        function display()
        {
            $data = $this->getContent();

            $items = xarModAPIFunc('shouter', 'user', 'getall',
                             array('numitems' => $data['numitems'])
                     );
        
            $totitems = count($items);
            for ($i = 0; $i < $totitems; $i++) {
                $item = $items[$i];
                $items[$i]['shout'] = wordwrap(xarVarPrepForDisplay($item['shout']), $data['blockwrap'], "\n", 1);
            }
        
            $data['shouturl'] = xarModURL('shouter', 'admin', 'create',array(),false);        
        
            $blockwidth = xarModVars::get('shouter','blockwidth');
            $data['blockwidth'] = "width:".$data['blockwidth']."px;";
        
            $data['refresh'] = true;
        
            if ($data['shoutblockrefresh'] == 0) {
                $data['refresh'] = false;
            }
            $data['shoutblockrefresh'] = $data['shoutblockrefresh'] . '000';
        
            // Transform Hook for smilies
            $data['items'] = array();
        
            foreach ($items as $item) {
                $item['module'] = 'shouter';
                $item['itemtype'] = 0;
                $item['itemid'] = $item['shoutid'];
                $item['transform'] = array('shout');
        
                $item = xarModCallHooks('item', 'transform', $item['shoutid'], $item);
                // Display the content
                $data['items'][] = $item;
            }
        
            $data['blockurl'] = xarModURL('blocks', 'user', 'display',array('name' => $this->name),false);
        
            $requestinfo = xarController::$request->getInfo();
        
        
            /**
             * Don't refresh inside of blocks admin
             * @todo: need a better way to handle whether to load the onLoad event for the timer
             */
            if ($requestinfo[0] == 'blocks' && $requestinfo[1] == 'admin') {
                $data['refresh'] = false;
            }
            
            return $data;
        }
}
?>