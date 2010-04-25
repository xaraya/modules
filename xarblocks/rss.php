<?php
/**
 * Displays an RSS Display.
 *
 * @package modules
 * @copyright (C) 2005-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * @author RevJim (revjim.net), John Cox
 * @todo Make the admin selectable number of headlines work.
 * @todo show search and image of rss site
 */
/**
 * Block init - holds security.
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class RssBlock extends BasicBlock implements iBlock
{
    public $nocache             = 1;

    public $name                = 'RssBlock';
    public $module              = 'headlines';
    public $text_type           = 'RSS';
    public $text_type_long      = 'RSS Newsfeed';
    public $pageshared          = 1;
    public $usershared          = 1;
    public $refresh             = 3600;        // (deprec)
    public $cacheexpire         = 3601; // right after the refresh rate :-)
    public $allow_multiple      = true;
    public $func_update         = 'headlines_rssblock_insert';
    public $form_content        = false;
    public $form_refresh        = false;
    public $show_preview        = true;
    
    public $rssurl              = '';
    public $maxitems            = 5;
    public $showdescriptions    = false;
    public $show_chantitle      = 1;
    public $show_chandesc       = 1;
    public $truncate            = 0;
    public $alt_chantitle       = ''; // FR - alt channel title
    public $alt_chandesc        = '';  // FR - alt channel desc
    public $alt_chanlink        = '';  // FR - alt channel link
    public $linkhid             = false;  // FR - link to headline feed
    public $show_warning        = 1;  // TODO: FR - option to hide block/display warning on failed feed
    public $show_chanimage      = 0;  // added for SimplePie
    public $show_itemimage      = 0;  // added for SimpePie
    public $show_itemcats       = 0;  // added for SimpePie


/**
 * Display func.
 * @param $data array containing title,content
 */
    function display(Array $data=array())
    {
        $data = parent::display($data);
        if (empty($data)) return;

        $vars = $data;

        $data['content'] = '';

        // Check and see if a feed has been supplied to us.
        if (empty($vars['rssurl'])) {
            $data['title'] = xarML('Headlines');
            $data['content'] = xarML('No Feed URL Specified');
            return $data;
        }

        // bug[ 5322 ]
        if (is_numeric($vars['rssurl'])) {
            $headline = xarMod::apiFunc('headlines', 'user', 'get', array('hid' => $vars['rssurl']));
            if (!empty($headline)) {
                $feedfile = $headline['url'];
                $thishid = $headline['hid'];
            } else {
                $data['title'] = xarML('Headlines');
                $data['content'] = xarML('No Feed URL Specified');
                return $data;
            }
        } else {
            $feedfile = $vars['rssurl'];
        }


        return $data;
    }
}
?>