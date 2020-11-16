<?php
/**
 * Ratings Module
 *
 * @package modules
 * @subpackage ratings module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/41.html
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * handle ratings property
 *
 * @package dynamicdata
 *
 */
sys::import("modules.base.xarproperties.floatbox");

class RatingProperty extends FloatBoxProperty
{
    public $id         = 30118;
    public $name       = 'rating';
    public $desc       = 'Rating';
    public $reqmodules = array('ratings');
    
    public $initialization_ratingstyle = 'outoffivestars';

    public $display_tooltip = 'Number of times this item was displayed';

    private $hitcache  = null;

    public function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->filepath   = 'modules/ratings/xarproperties';
        // we want a reference to the object here
        $this->include_reference = 1;

        // Force settings for datastore, input and display status
        $this->source = '';
        $this->setInputStatus(DataPropertyMaster::DD_INPUTSTATE_NOINPUT);
        $this->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISPLAYONLY);
    }

    public function getValue()
    {
        return $this->getHitcount();
    }

    public function getItemValue($itemid)
    {
        return $this->getHitcount(array('value' => $itemid));
    }

    public function showInput(array $data = array())
    {
//        $data['value'] = $this->getHitcount($data);
        return parent::showInput($data);
    }

    public function showOutput(array $data = array())
    {
        // the dummy datastore will use the itemid as value for this property !
//        $data['value'] = $this->getHitcount($data, 1);
        return parent::showOutput($data);
    }

    private function getHitcount(array $data = array(), $update = 0)
    {
        // if we don't have an objectref, return the value as is
        if (empty($this->objectref) || empty($this->objectref->objectid)) {
            if (isset($data['value'])) {
                return $data['value'];
            } else {
                return $this->value;
            }
        }

        // we're dealing with a single item here
        if (!empty($this->_itemid)) {
            if (!isset($this->hitcache)) {
                if (!empty($update) && $this->checkForUpdate()) {
                    // update the hitcount for this item
                    $this->hitcache = xarMod::apiFunc(
                        'hitcount',
                        'admin',
                        'update',
                        array('modname'  => xarMod::getName($this->objectref->moduleid),
                                                            'itemtype' => $this->objectref->itemtype,
                                                            'objectid' => $this->objectref->itemid)
                    );
                } else {
                    // get the hitcount for this item
                    $this->hitcache = xarMod::apiFunc(
                        'hitcount',
                        'user',
                        'get',
                        array('modname'  => xarMod::getName($this->objectref->moduleid),
                                                            'itemtype' => $this->objectref->itemtype,
                                                            'objectid' => $this->objectref->itemid)
                    );
                }
                if (empty($this->hitcache)) {
                    $this->hitcache = 0;
                }
            }
            return $this->hitcache;

        // the dummy datastore will use the itemid as value for this property
        } elseif (!empty($this->_items) && isset($data['value']) && !empty($this->_items[$data['value']])) {
            if (!isset($this->hitcache)) {
                // get the hitcount for all the items in the objectref
                $this->hitcache = xarMod::apiFunc(
                    'hitcount',
                    'user',
                    'getitems',
                    array('modid'    => $this->objectref->moduleid,
                                                        'itemtype' => $this->objectref->itemtype,
                                                        'itemids'  => $this->objectref->itemids)
                );
                if (empty($this->hitcache)) {
                    $this->hitcache = array();
                }
                /*
                                // set the hitcount for all the items in the objectref ? No, we'll work via local hitcache
                                foreach ($this->objectref->itemids as $itemid) {
                                    if (isset($this->hitcache[$itemid])) {
                                        $this->objectref->items[$itemid][$this->name] = $this->hitcache[$itemid];
                                    } else {
                                        $this->objectref->items[$itemid][$this->name] = 0;
                                    }
                                }
                */
            }
            if (!empty($this->hitcache) && isset($this->hitcache[$data['value']])) {
                return $this->hitcache[$data['value']];
            }
        }
        // hitcount is 0
        return 0;
    }

    private function checkForUpdate()
    {
        // check for 'preview' argument
        xarVarFetch('preview', 'isset', $preview, null, XARVAR_DONT_SET);

        // if we're previewing an item, don't update
        if (!empty($preview)) {
            return false;

        // if we don't count admin hits and the user is an admin, don't update
        } elseif (!xarModVars::get('hitcount', 'countadmin') && xarSecurityCheck('AdminHitcount', 0)) {
            return false;
        } else {
            return true;
        }
    }

    public function createValue($itemid=0)
    {
        if (empty($itemid) || empty($this->objectref) || empty($this->objectref->objectid)) {
            return;
        }
        // CHECKME: do we create now, or wait until the first display hit ?
        return true;
    }

    public function updateValue($itemid=0)
    {
        if (empty($itemid) || empty($this->objectref) || empty($this->objectref->objectid)) {
            return;
        }
        // nothing to do here
        return true;
    }

    public function deleteValue($itemid=0)
    {
        if (empty($itemid) || empty($this->objectref) || empty($this->objectref->objectid)) {
            return;
        }
        // delete hitcount entry
        xarMod::apiFunc(
            'hitcount',
            'admin',
            'delete',
            array('modname'  => xarMod::getName($this->objectref->moduleid),
                              'itemtype' => $this->objectref->itemtype,
                              'objectid' => $itemid)
        );
        return true;
    }
}
