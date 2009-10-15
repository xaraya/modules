<?php
/**
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispbb module
 * @link http://www.xaraya.com/index.php/release/970.html
 * @author
 */
/**
 * @author xardev@invalid.tld
 * @todo make me useful :@)
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class TopitemsBlock extends BasicBlock implements iBlock
{
    public $no_cache            = 0;

    public $name                = 'TopitemsBlock';
    public $module              = 'crispbb';
    public $text_type           = 'crispBB Top Items';
    public $text_type_long      = 'Displays forum topics';
    public $show_preview        = true;

    public $numitems            = 5;
    public $fids                = array();
    public $sort                = 'ptime';
    public $order               = 'DESC';

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display(Array $data=array())
    {
        $data = parent::display($data);
        if (empty($data)) return;
        //if (!xarSecurityCheck('ReadCrispBBBlock', 0, 'Block', $data['name'])) {return;}

        $vars = isset($data['content']) ? $data['content'] : array();
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

        $data['content'] = $vars;
        return $data;
    }

/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 */
    public function modify(Array $data=array())
    {
        $data = parent::modify($data);

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
        $data = parent::update($data);
        $vars = array();

        $forums = xarMod::apiFunc('crispbb', 'user', 'getitemlinks');
        $this->fids = !empty($forums) && is_array($forums) ? array_keys($forums) : array();

        if (!xarVarFetch('numitems', 'int:1:50', $vars['numitems'], $this->numitems, XARVAR_NOT_REQUIRED)) {return;}
        if (!xarVarFetch('fids', 'list', $vars['fids'], $this->fids, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('sort', 'pre:trim:lower:enum:ptime:numhits:numratings', $vars['sort'], $this->sort, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('order', 'pre:trim:upper:enum:ASC:DESC', $vars['order'], $this->order, XARVAR_NOT_REQUIRED)) return;

        $data['content'] = $vars;
        return $data;
    }
}
?>