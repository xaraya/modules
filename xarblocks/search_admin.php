<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author Marc Lutolf <mfl@netspan.ch>
 */
sys::import('modules.keywords.xarblocks.search');

class Keywords_SearchBlockAdmin extends Keywords_SearchBlock implements iBlock
{
    /*function modify()
    {
        $data = $this->getContent();

        $data['status'] = '';
        switch ($data['cloudtype']) {
            default:
            case 1:
                if (!xarMod::isAvailable('categories')) $data['status'] = 'not_available';
                break;
            case 3:
                if (!xarMod::isAvailable('keywords')) $data['status'] = 'not_available';
                break;
        }
        return $data;
    }*/

    public function update()
    {
        if (!xarVarFetch('module_id',  'str:1:',   $vars['module_id'],  $this->module_id,XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('itemtype',   'str:1:',   $vars['itemtype'],   $this->itemtype,XARVAR_NOT_REQUIRED)) return;
        $this->setContent($vars);
        return true;
    }
}
?>
