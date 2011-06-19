<?php
/**
 * Twitter Module
 *
 * @package modules
 * @copyright (C) 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
sys::import('xaraya.structures.containers.blocks.basicblock');
class Twitter_TimelineBlock extends BasicBlock implements iBlock
{
    public $nocache         = 1;

    public $name        = 'TimelineBlock';
    public $module          = 'twitter';
    public $text_type       = 'Twitter Timeline';
    public $text_type_long      = 'Displays Twitter Timelines';

    public $screen_name     = '';
    public $numitems        = 3;
    public $truncate        = 0;
    public $showimages      = false;
    public $showsource      = true;
    public $showmodule      = false;
    public $showfollow      = false;
    public $dyntitle        = false;

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display(Array $data=array())
    {
        $data = parent::display($data);
        if (empty($data)) return;
        $vars = $data['content'];

        if (empty($vars['screen_name'])) {
            $items = xarMod::apiFunc('twitter', 'rest', 'timeline',
                array(
                    'method' => 'public_timeline',
                ));
        } else {
            $items = xarMod::apiFunc('twitter', 'rest', 'timeline',
                array(
                    'method' => 'user_timeline',
                    'screen_name' => $vars['screen_name'],
                ));
        }
        if (count($items) > $vars['numitems'])
            $items = array_slice($items, 0, $vars['numitems']);
        $vars['status_elements'] = !$items ? array() : $items;
        if (!empty($vars['dyntitle']))
            $data['title'] = !empty($vars['screen_name']) ? '@'.$vars['screen_name'] : xarML('Public Timeline');

        $data['content'] = $vars;
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
        if (!xarVarFetch('screen_name', 'str:1:20', $vars['screen_name'], '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('numitems', 'int', $vars['numitems'], 3, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('truncate', 'int', $vars['truncate'], 0, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showimages', 'checkbox', $vars['showimages'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showmyimage', 'checkbox', $vars['showmyimage'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showsource', 'checkbox', $vars['showsource'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showmodule', 'checkbox', $vars['showmodule'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showfollow', 'checkbox', $vars['showfollow'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('dyntitle', 'checkbox', $vars['dyntitle'], false, XARVAR_NOT_REQUIRED)) return;
        $data['content'] = $vars;
        return $data;
    }

}

?>