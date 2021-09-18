<?php
/**
 * Hitcount Module
 *
 * @package modules
 * @subpackage hitcount module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/177.html
 */
/**
 * ItemCreate Hook Subject Observer
 *
**/
sys::import('xaraya.structures.hooks.observer');
class HitcountItemCreateObserver extends HookObserver implements ixarEventObserver
{
    public $module = 'hitcount';
    public function notify(ixarEventSubject $subject)
    {
        // get extrainfo from subject (array containing module, module_id, itemtype, itemid)
        $extrainfo = $subject->getExtrainfo();
        extract($extrainfo);

        // validate parameters...
        // NOTE: this isn't strictly necessary, the hook subject will have already
        // taken care of validations and these values can be relied on to be pre-populated
        // however, just for completeness...
        if (!isset($module) || !is_string($module) || !xarMod::isAvailable($module)) {
            $invalid['module'] = 1;
        }
        if (isset($itemtype) && !is_numeric($itemtype)) {
            $invalid['itemtype'] = 1;
        }
        if (!isset($itemid) || !is_numeric($itemid)) {
            $invalid['itemid'] = 1;
        }

        // NOTE: as of Jamaica 2.2.0 it's ok to throw exceptions in hooks, the subject handles them
        if (!empty($invalid)) {
            $args = [join(',', $invalid), 'hitcount', 'hooks', 'ItemCreate'];
            $msg = 'Invalid #(1) for #(2) module #(2) #(3) observer notify method';
            throw new BadParameterException($args, $msg);
        }

        // call the hitcount create api function
        $hit = xarMod::apiFunc(
            'hitcount',
            'admin',
            'create',
            [
                'modname' => $module,
                'itemtype' => !empty($itemtype) ? $itemtype : 0,
                'objectid' => $itemid,
            ]
        );

        // @checkme: the api func returns an array of info, only really need the id ?
        if (empty($hit)) {
            // @todo: exception here?
            return $extrainfo;
        }

        // the subject expects an array of extrainfo
        // return the merged array of extrainfo and the created hit
        return $extrainfo += $hit;
    }
}
