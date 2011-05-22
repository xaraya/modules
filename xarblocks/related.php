<?php
/**
 * Publications module related publications block
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * Original Author of file: Jim McDonald
 */
/**
 * initialise block
 */
    sys::import('xaraya.structures.containers.blocks.basicblock');

    class Publications_RelatedBlock extends BasicBlock implements iBlock
    {
        public $numitems          = 5;
        public $showvalue         = true;
        public $nocache           = 1; // don't cache by default
        public $usershared        = 1; // share across group members
        public $pageshared        = 0; // don't share across pages
        public $showauthor        = false;
        public $showsubmit        = false;
        public $state             = '2,3';

        public function __construct(Array $data=array())
        {
            parent::__construct($data);
            $this->text_type = 'Related publication';
            $this->text_type_long = 'Show related categories and author links';
            $this->allow_multiple = true;
            $this->form_content = false;
            $this->form_refresh = false;
            $this->show_preview = true;
        }

        public function display(Array $data=array())
        {
            $data = parent::display($data);

            if (empty($data['numitems']))          $data['numitems'] = $this->numitems;
            if (empty($data['showvalue']))         $data['showvalue'] = $this->showvalue;

            // Trick : work with cached variables here (set by the module function)        
            // Check if we've been through publications display
            if (!xarVarIsCached('Blocks.publications','id')) {return;}
        
            $pubtypes = xarModAPIFunc('publications','user','get_pubtypes');

            $links = 0;
            // Show publication type (for now)
            if (xarVarIsCached('Blocks.publications','ptid')) {
                $ptid = xarVarGetCached('Blocks.publications','ptid');
                if (!empty($ptid) && isset($pubtypes[$ptid]['description'])) {
                    $vars['pubtypelink'] = xarModURL('publications','user','view',
                                                     array('ptid' => $ptid));
                    $vars['pubtypename'] = $pubtypes[$ptid]['description'];
                    $links++;
                }
            }
            
            // Show categories (for now)
            if (xarVarIsCached('Blocks.publications','cids')) {
                $cids = xarVarGetCached('Blocks.publications','cids');
                // TODO: add related links
            }
            // Show author (for now)
            if (xarVarIsCached('Blocks.publications','owner') &&
                xarVarIsCached('Blocks.publications','author')) {
                $owner = xarVarGetCached('Blocks.publications','owner');
                $author = xarVarGetCached('Blocks.publications','author');
                if (!empty($owner) && !empty($author)) {
                    $vars['authorlink'] = xarModURL('publications','user','view',
                                                    array('ptid' => (!empty($ptid) ? $ptid : null),
                                                          'owner' => $owner));
                    $vars['authorname'] = $author;
                    $vars['owner'] = $owner;
                    if (!empty($vars['showvalue'])) {
                        $vars['authorcount'] = xarModAPIFunc('publications','user','countitems',
                                                             array('ptid' => (!empty($ptid) ? $ptid : null),
                                                                   'owner' => $owner,
                                                                   // limit to approved / frontpage publications
                                                                   'state' => array(2,3),
                                                                   'enddate' => time()));
                    }
                    $links++;
                }
            }

    $vars['blockid'] = $blockinfo['bid'];

            // Populate block info and pass to theme
            if ($links > 0) {
                // Set the data to return.
                $data['content'] = $data;
                return $data;
            }
        
            return;
        }
    }
?>