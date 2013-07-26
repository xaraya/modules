<?php
/**
 * Redirect Block
 *
 * @package modules
 * @subpackage wurfl module
 * @category Third Party Xaraya Block
 * @version 1.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Manage block config
 *
*/
sys::import('modules.wurfl.xarblocks.redirect');
class Wurfl_RedirectBlockConfig extends Wurfl_RedirectBlock
{
/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 */
    public function configmodify(Array $data=array())
    {
        $data = $this->getContent();
        return $data;
    }

/**
 * Updates the Block config from the Blocks Admin
 * @param $data array containing title,content
 */
    public function update(Array $data=array())
    {
        $vars = array();
        
        // fetch the array of redirects from input
        if (!xarVarFetch('redirects', 'array', $redirects, array(), XARVAR_NOT_REQUIRED)) return;
        $newredirects = array();
        foreach ($redirects as $redirect) {
            // delete if flag is set not empty
            if (isset($redirect['delete']) && !empty($redirect['delete'])) continue;
            $newredirects[] = $redirect;
        }

        // fetch the value of the new redirect
        if (!xarVarFetch('redirectua', 'pre:trim:str:1:', $redirectua, '', XARVAR_NOT_REQUIRED)) return;
        // only fetch other params if user agent isn't empty
        if (!empty($redirectua)) {
            if (!xarVarFetch('redirecttheme', 'pre:trim:str:1:', $redirecttheme, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('redirecttemplate', 'pre:trim:str:1:', $redirecttemplate, '', XARVAR_NOT_REQUIRED)) return;
            $newredirects[] = array(
                'ua' => $redirectua,
                'theme' => $redirecttheme,
                'template' => $redirecttemplate,
            );
        }
        $vars['redirects'] = $newredirects;
        $this->setContent($vars);
        return true;
    }

}
?>