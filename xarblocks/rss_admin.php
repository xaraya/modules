<?php
/**
 * Authentication Block
 * @package math
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.headlines.xarblocks.rss');

class RssBlockAdmin extends RssBlock implements iBlock
{
/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 */
    public function modify(Array $data=array())
    {
        $data = parent::modify($data);
        $vars = $data;

        // Migrate $row['rssurl'] to content if present
        if (!empty($vars['url'])) {
            $vars['rssurl'] = $vars['url'];
            unset($vars['url']);
        }

        // Get parameters from whatever input we need
        $vars['items'] = array();

        // The user API function is called
        $links = xarMod::apiFunc('headlines', 'user', 'getall');
        $vars['items'] = $links;

        // Defaults
        if (!isset($vars['rssurl'])) $vars['rssurl'] = $this->rssurl;

        // bug[ 5322 ] - check for hid
        if (is_numeric($vars['rssurl'])) {
            $headline = xarMod::apiFunc('headlines', 'user', 'get', array('hid' => $vars['rssurl']));
            if (!empty($headline)) { // found headline, use that url
                $vars['rssurl'] = $headline['url'];
            } else {
                $vars['rssurl'] = $this->rssurl;
            }
        }

        // If the current URL is not in the headlines list, then pass it in as 'custom'
        $vars['otherrssurl'] = $vars['rssurl'];
        if (is_array($links) && $vars['rssurl'] != $this->rssurl) {
            foreach($links as $link) {
                if ($link['url'] == $vars['rssurl']) {
                    // The URL was found in the list, so it is not custom
                    $vars['otherrssurl'] = '';
                }
            }
        }



        $vars['blockid'] = $data['bid'];

        // Just return the template variables.
        return $vars;
    }

}
?>
