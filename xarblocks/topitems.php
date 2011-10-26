<?php
/**
 * @package modules
 * @copyright (C) 2008-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispbb module
 * @link http://www.xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * @author crisp <crisp@crispcreations.co.uk>
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Crispbb_TopitemsBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'topitems';
    protected $module           = 'crispbb'; // module block type belongs to, if any
    protected $text_type        = 'Forums top items';  // Block type display name
    protected $text_type_long   = 'Show forum topics'; // Block type description
    // Additional info, supplied by developer, optional 
    protected $type_category    = 'block'; // options [(block)|group] 
    protected $author           = 'Chris Powis';
    protected $contact          = 'crisp@crispcreations.co.uk';
    protected $credits          = '';
    protected $license          = '';

    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    protected $show_help    = false; // let the subsystem know if this block type has a help() method
    
    public $numitems            = 5;
    public $fids                = array();
    public $sort                = 'ptime';
    public $order               = 'DESC';

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display()
    {
        $vars = $this->getContent();
        // Defaults
        $forums = xarMod::apiFunc('crispbb', 'user', 'getitemlinks');
        $this->fids = !empty($forums) && is_array($forums) ? array_keys($forums) : array();

        $sorts = array();
        $sorts['ptime'] = array('id' => 'ptime', 'name' => xarML('Last post time'));
        $sorts['numhits'] = array('id' => 'numhits', 'name' => xarML('Number of hits'));
        if (xarModIsAvailable('ratings')) {
            //$sorts['numratings'] = array('id' => 'numratings', 'name' => xarML('Rating'));
        }

        if (empty($vars['fids']) || !is_array($vars['fids'])) $vars['fids'] = $this->fids;
        if (empty($vars['sort']) || !isset($sorts[$vars['sort']])) $vars['sort'] = $this->sort;
        if (empty($vars['order'])) $vars['order'] = $this->order;
        if (empty($vars['numitems'])) $vars['numitems'] = $this->numitems;

        $vars['topics'] = xarMod::apiFunc('crispbb', 'user', 'gettopics',
            array(
                'fid' => $vars['fids'],
                'sort' => $vars['sort'],
                'order' => $vars['order'],
                'tstatus' => array(0,1,2,4),
                'numitems' => $vars['numitems']
            ));

        return $vars;
    }

/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 */
    public function modify(Array $data=array())
    {
        $data = $this->getContent();

        // Defaults
        $forums = xarMod::apiFunc('crispbb', 'user', 'getitemlinks');
        $this->fids = !empty($forums) && is_array($forums) ? array_keys($forums) : array();

        $sorts = array();
        $sorts['ptime'] = array('id' => 'ptime', 'name' => xarML('Last post time'));
        $sorts['numhits'] = array('id' => 'numhits', 'name' => xarML('Number of hits'));
        if (xarModIsAvailable('ratings')) {
            //sorts['numratings'] = array('id' => 'numratings', 'name' => xarML('Rating'));
        }

        if (empty($data['fids']) || !is_array($data['fids'])) $data['fids'] = $this->fids;
        if (empty($data['sort']) || !isset($sorts[$data['sort']])) $data['sort'] = $this->sort;
        if (empty($data['order'])) $data['order'] = $this->order;
        if (empty($data['numitems'])) $data['numitems'] = $this->numitems;

        $presets = xarMod::apiFunc('crispbb', 'user', 'getpresets',
            array('preset' => 'sortorderoptions'));
        $data['sortoptions'] = $sorts;
        $data['orderoptions'] = $presets['sortorderoptions'];
        $data['forumoptions'] = $forums;

        return $data;
    }

/**
 * Updates the Block config from the Blocks Admin
 * @param $data array containing title,content
 */
    public function update(Array $data=array())
    {
        $vars = array();

        $forums = xarMod::apiFunc('crispbb', 'user', 'getitemlinks');
        $this->fids = !empty($forums) && is_array($forums) ? array_keys($forums) : array();

        if (!xarVarFetch('numitems', 'int:1:50', $vars['numitems'], $this->numitems, XARVAR_NOT_REQUIRED)) {return;}
        if (!xarVarFetch('fids', 'list', $vars['fids'], $this->fids, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('sort', 'pre:trim:lower:enum:ptime:numhits:numratings', $vars['sort'], $this->sort, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('order', 'pre:trim:upper:enum:ASC:DESC', $vars['order'], $this->order, XARVAR_NOT_REQUIRED)) return;
        $this->setContent($vars);
        return true;

    }
}
?>