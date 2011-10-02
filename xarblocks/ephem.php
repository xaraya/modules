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
        public $name                = 'Ephemerids';
        public $module              = 'ephemerids';
        public $text_type           = 'Ephemerids';
        public $text_type_long      = 'Ephemerids Block';
        public $allow_multiple      = true;
        public $show_preview        = true;

        public $form_content        = false;
        public $form_refresh        = false;


        function display(Array $data=array())
        {
            $data = parent::display($data);
            if (empty($data)) return;

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
        
            if (empty($blockinfo['title'])){
                $blockinfo['title'] = xarML('Historical Reference');
            }
        
            if (empty($blockinfo['template'])) {
                $template = 'ephem';
            } else {
                $template = $blockinfo['template'];
            }
            $vars['content'] = $data;
            $data = array_merge($data,$vars);
            return $data;
        }
}
?>