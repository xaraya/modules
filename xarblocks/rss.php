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

        if (!isset($vars['maxitems'])) $vars['maxitems'] = $this->maxitems;
        if (!isset($vars['show_chantitle'])) $vars['show_chantitle'] = $this->show_chantitle;
        if (!isset($vars['show_chandesc'])) $vars['show_chandesc'] = $this->show_chandesc;
        if (empty($vars['refresh'])) $vars['refresh'] = $this->refresh;
        if (!isset($vars['linkhid'])) $vars['linkhid'] = $this->linkhid;
        if (!isset($vars['show_warning'])) $vars['show_warning'] = $this->show_warning;

        // CHECKME: this forces early refresh regardless of caching options, why do we do that? old code below
        // define('MAGPIE_CACHE_AGE', round($refresh/2)); // set lower than block cache so we always get something
        // simplepie'cache_max_minutes' => round($refresh/2*60) - bug, simplepie caching is in seconds
        // this only happens in the rss block, need to work out if it's necessary
        $refresh = $vars['refresh']/2;
        if (!$vars['showdescriptions']) {
            $vars['truncate'] = 0; // no point doing extra work for nothing :)
        }
        // call api function to get the parsed feed (or warning)
        $data = array_merge($data, xarMod::apiFunc('headlines', 'user', 'getparsed',
                    array('feedfile' => $feedfile, 'refresh' => $refresh,
                          'numitems' => $vars['maxitems'], 'truncate' => $vars['truncate'])));
        // TODO: option to hide block here instead
        if (!empty($data['warning'])) {
            if (empty($vars['show_warning'])) return;
            $data['title'] = xarML('Headlines');
            $data['content'] = xarVarPrepForDisplay($data['warning']);
            return $data;
        } else {
            // here we see if this feed has been updated by comparing the stored hash against the
            // hash provided by the getparsed function, if they're different, we update the feed
            // with the new hash, and the time of the last item in the feed, or the current time
            // this means the feeds can now be sorted reliably by date ala. the cloud block
            if (isset($headline['string']) && ($headline['string'] != $data['compare'])) {
                // call api function to update our feed item
                if (!xarMod::apiFunc('headlines', 'user', 'update', array('hid' => $headline['hid'], 'date' => $data['lastitem'], 'string' => $data['compare']))) return;
            }
        }

        // FR: add alt channel title/desc/link
        if (!isset($vars['alt_chantitle'])) $vars['alt_chantitle'] = $this->alt_chantitle;
        if (!isset($vars['alt_chandesc'])) $vars['alt_chandesc'] = $this->alt_chandesc;
        if (!isset($vars['alt_chanlink'])) $vars['alt_chanlink'] = $this->alt_chanlink;
        if (!empty($vars['alt_chantitle'])) $data['chantitle'] = $vars['alt_chantitle'];
        if (!empty($vars['alt_chandesc'])) $data['chandesc'] = $vars['alt_chandesc'];
        if (!empty($vars['alt_chanlink'])) $data['chanlink'] = $vars['alt_chanlink'];
        // optionally show images and cats if available (SimplePie required)
        if (!isset($vars['show_chanimage'])) $vars['show_chanimage'] = $this->show_chanimage;
        if (!isset($vars['show_itemimage'])) $vars['show_itemimage'] = $this->show_itemimage;
        if (!isset($vars['show_itemcats'])) $vars['show_itemcats'] = $this->show_itemcats;
        // make sure SimplePie's available
        if (!xarMod::isAvailable('simplepie')) {
            $vars['show_chanimage'] = $this->show_chanimage;
            $vars['show_itemimage'] = $this->show_itemimage;
            $vars['show_itemcats'] = $this->show_itemcats;
        }
        if ($vars['linkhid'] && (isset($thishid)&& !empty($thishid))) {
            $vars['linkhid'] = $thishid;
        }
        if (!isset($vars['show_warning'])) $vars['show_warning'] = $this->show_warning;

        $data['content'] = array(
            'feedcontent'  => $data['feedcontent'],
            'blockid'      => $data['bid'],
            'chantitle'    => $data['chantitle'],
            'chanlink'     => $data['chanlink'],
            'chandesc'     => $data['chandesc'],
            'chanimage'    => $data['image'],
            'show_desc'     => $vars['showdescriptions'],
            'show_chantitle' => $vars['show_chantitle'],
            'show_chandesc'  => $vars['show_chandesc'],
            'show_chanimage' => $vars['show_chanimage'],
            'show_itemimage' => $vars['show_itemimage'],
            'show_itemcats' => $vars['show_itemcats'],
            'show_warning' => $vars['show_warning'],
            'linkhid' => $vars['linkhid']
        );

        return $data;
    }
}
?>