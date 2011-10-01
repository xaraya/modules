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

    class Shouter_ShoutBlock extends BasicBlock implements iBlock
    {
        public $name                = 'Shoutblock';
        public $module              = 'shouter';
        public $text_type           = 'Shoutblock';
        public $text_type_long      = 'Shoutblock';

        public $form_content        = true;
        public $form_refresh        = true;

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
        function display(Array $data=array())
        {
            $data = parent::display($data);
            if (empty($data)) return;

            $items = xarModAPIFunc('shouter', 'user', 'getall',
                             array('numitems' => $vars['numitems'])
                     );
        
            $totitems = count($items);
            for ($i = 0; $i < $totitems; $i++) {
                $item = $items[$i];
                $items[$i]['shout'] = wordwrap(xarVarPrepForDisplay($item['shout']), $vars['blockwrap'], "\n", 1);
            }
        
            $data['shouturl'] = xarModURL('shouter', 'admin', 'create',array(),false);
            $data['anonymouspost'] = $vars['anonymouspost'];
        
            $lightrow = xarModVars::get('shouter','lightrow');
            $data['lightrow'] = "background:#".$vars['lightrow'].";";
        
            $darkrow = xarModVars::get('shouter','darkrow');
            $data['darkrow'] = "background:#".$vars['darkrow'].";";
        
        
            $blockwidth = xarModVars::get('shouter','blockwidth');
            $data['blockwidth'] = "width:".$vars['blockwidth']."px;";
        
            $data['refresh'] = true;
        
            if ($vars['shoutblockrefresh'] == 0) {
                $data['refresh'] = false;
            }
            $data['shoutblockrefresh'] = $vars['shoutblockrefresh'] . '000';
        
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
        
            $data['blockurl'] = xarModURL('blocks', 'user', 'display',array('name' => $blockinfo['name']),false);
        
            $requestinfo = xarRequest::getInfo();
        
        
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