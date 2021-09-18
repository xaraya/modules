<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Handle the realm property
 */

sys::import('modules.dynamicdata.xarproperties.objectref');

class RealmProperty extends ObjectRefProperty
{
    public $id         = 30096;
    public $name       = 'realm';
    public $desc       = 'Realm Dropdown';
    public $reqmodules = ['realms'];

    public $initialization_firstline    = '0,All Realms';
    public $initialization_refobject    = 'realms_realms';    // Name of the object we want to reference
    public $initialization_store_prop   = 'id';               // Name of the property we want to use for storage

    public function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->tplmodule = 'realms';
        $this->filepath   = 'modules/realms/xarproperties';
        $this->template = 'realm';
    }

    public function showInput(array $data = [])
    {
        if (!isset($data['value'])) {
            $data['value'] = $this->value;
        }
        $cacheKey ='Roles.User.' . xarSession::getVar('role_id');
        $infoid = 'realm';
        if (!empty($data['firstline'])) {
            $this->initialization_firstline = $data['firstline'];
        }
        if (!empty($data['refobject'])) {
            $this->initialization_refobject = $data['refobject'];
        }
        if (!empty($data['store_prop'])) {
            $this->initialization_store_prop = $data['store_prop'];
        }
        $data['isrealmed'] = xarCoreCache::getCached($cacheKey, $infoid);
        if ($data['isrealmed']) {
            $data['value'] = $data['isrealmed']['id'];
        }
        return parent::showInput($data);
    }

    public function showOutput(array $args = [])
    {
        if (!isset($data['value'])) {
            $data['value'] = $this->value;
        }
        $cacheKey ='Roles.User.' . xarSession::getVar('role_id');
        $infoid = 'realm';
        if (!empty($data['firstline'])) {
            $this->initialization_firstline = $data['firstline'];
        }
        if (!empty($data['refobject'])) {
            $this->initialization_refobject = $data['refobject'];
        }
        if (!empty($data['store_prop'])) {
            $this->initialization_store_prop = $data['store_prop'];
        }
        $data['isrealmed'] = xarCoreCache::getCached($cacheKey, $infoid);
        if ($data['isrealmed']) {
            $data['value'] = $data['isrealmed']['id'];
        }
        return parent::showOutput($data);
    }
}
