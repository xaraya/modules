<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

class Daemon extends Object
{
    private static $instance;
    
    public $items_per_page       = array();
    
    public $name                 = '';          // The user's name
    public $email                = '';          // The user's email
    public $mandanten            = array();     // The mandanten this user can access
    public $current_mandant      = 0;           // The current mandant ID this user is working in
    public $mandant_settings     = array();     // The field values of the user's current mandant
    public $current_period       = array();     // The current period this user is working in
    public $previous_period      = array();     // The previous period this user is working in
    public $current_standard_period = 1;        // The current standard period this user is working in
    public $previous_standard_period = 2;       // The previous standard period this user is working in

    private function __construct()
    {
        // If this is a registered user, then set the name and email immediately
        // Otherwise this is done later
        if (xarUser::getVar('id') != _XAR_ID_UNREGISTERED) {
            $user = xarMod::apiFunc('roles', 'user', 'get', array('id' => xarUser::getVar('id')));
            $this->setName($user['name']);
            $this->setEmail($user['email']);
        }

        $this->current_period = array(
                                    'ac' => array(0,0,-400),
                                    'al' => array(0,0,-400),
                                    'ap' => array(0,0,-400),
                                    'ar' => array(0,0,-400),
                                    'ba' => array(0,0,-400),
                                    'ca' => array(0,0,-400),
                                    'gl' => array(0,0,-400),
                                    'py' => array(0,0,-400),
                                    'se' => array(0,0,-400),
                                    'so' => array(0,0,-400),
                                );
        $this->previous_period = array(
                                    'ac' => array(0,0,-450),
                                    'al' => array(0,0,-450),
                                    'ap' => array(0,0,-450),
                                    'ar' => array(0,0,-450),
                                    'ba' => array(0,0,-450),
                                    'ca' => array(0,0,-450),
                                    'gl' => array(0,0,-450),
                                    'py' => array(0,0,-450),
                                    'se' => array(0,0,-450),
                                    'so' => array(0,0,-450),
                                );
        
        // First time for an admin, then set the company to the default company
        if (xarIsParent('Administrators', xarUser::getVar('uname'))) {
            $this->setMandant((int)xarModVars::get('ledgerba', 'default_mandant'));
        } else {
        // For other users get the allowed companies
            $role_definition = xarMod::apiFunc('ledgerba', 'user', 'get_role_definition');
        
            // Set the user to the first one encountered
            try {
                $mandanten = unserialize($role_definition['mandanten']);
                $this->setMandant(reset($mandanten));
            } catch(Exception $e) {
                //Nothing there: give the user no access
            }
        }

    }
    
    public function __wakeup()
    {
        // Do whatever
    }

    public function __sleep()
    {
        // set the last run time before we exit
        $this->last_run = time();
        // return the array of public property names to store
        return array_keys($this->getPublicProperties());
    }

    public function __destruct()
    {
        // basically, we serialize and set this object as a modvar
        // xarModVars::set can be a little flaky,
        // this workaround seems to do the trick
        // NOTE: when we call serialize here, the __sleep() magic method is called
        try {
            if (xarUser::getVar('id') == _XAR_ID_UNREGISTERED) {
                xarSession::setVar('paymentsdaemon', serialize($this));
            } else {
                xarModUserVars::set('payments', 'daemon', serialize($this));
            }
        } catch (Exception $e) {
            if (xarUser::getVar('id') == _XAR_ID_UNREGISTERED) {
                xarSession::delVar('paymentsdaemon');
                xarSession::setVar('paymentsdaemon', serialize($this));
            } else {
                xarModUserVars::delete('payments', 'daemon');
                xarModUserVars::set('payments', 'daemon', serialize($this));
            }
        }
    }

    public function close()
    {
        return $this->__destruct();
    }
    
    public function delete()
    {
            if (xarUser::getVar('id') == _XAR_ID_UNREGISTERED) {
                xarSession::delVar('paymentsdaemon');
            } else {
                xarModUserVars::delete('payments', 'daemon');
            }
        return true;
    }
    
    public function __clone()
    {
        throw new ForbiddenOperationException('__clone', 'Not allowed to #(1) this singleton');
    }

    public static function getInstance($id=0)
    {
        if (!isset(self::$instance)) {
            // try unserializing the stored object
            if (xarUser::getVar('id') == _XAR_ID_UNREGISTERED) {
                self::$instance = @unserialize(xarSession::getVar('paymentsdaemon'));
            } else {
                if (!empty($id)) {
                    self::$instance = @unserialize(xarModUserVars::get('payments', 'daemon', $id));
                } else {
                    self::$instance = @unserialize(xarModUserVars::get('payments', 'daemon'));
                }
            }
            // fall back to new instance (first run)
            if (empty(self::$instance)) {
                $c = __CLASS__;
                // this is the one and only time the __construct() method will be run
                self::$instance = new $c;
            }
        }
        return self::$instance;
    }

/**
 *  Gets and sets
 */
    public function getName()           { return $this->name; }
    public function getEmail()          { return $this->email; }
    public function getCurrentMandant() { return $this->current_mandant; }
    public function getCurrentPeriod($ledger) 
    {
        // Make sure this period exists for now and the future
        if (!isset($this->current_period[$ledger])) $this->current_period[$ledger] = array(0,0,-400);

        $period = $this->current_period[$ledger];
        return $period;
    }
    public function getPreviousPeriod($ledger) 
    {
        // Make sure this period exists for now and the future
        if (!isset($this->previous_period[$ledger])) $this->previous_period[$ledger] = array(0,0,-450);
        
        $period = $this->previous_period[$ledger];
        return $period;
    }
    public function getCurrentStandardPeriod() 
    {
        sys::import('modules.dynamicdata.class.objects.master');
        $period = DataObjectMaster::getObject(array('name' => 'ledgerba_periods'));
        $period->getItem(array('itemid' => $this->current_standard_period));
        return $period;
    }
    public function getPreviousStandardPeriod() 
    {
        sys::import('modules.dynamicdata.class.objects.master');
        $period = DataObjectMaster::getObject(array('name' => 'ledgerba_periods'));
        $period->getItem(array('itemid' => $this->previous_standard_period));
        return $period;
    }

    public function setName($name='')
    {
        $this->name = $name;
        return true;
    }
    public function setEmail($email='')
    {
        $this->email = $email;
        return true;
    }
    public function setCurrentPeriod($ledger, $period) 
    {
        $this->current_period[$ledger] = $period; 
    }
    public function setPreviousPeriod($ledger, $period) 
    {
        $this->previous_period[$ledger] = $period; 
    }
    public function setMandant($id=0, $force=0)
    {
        $id = (int)$id;
        if (empty($id)) return true;
        if (!$force && ($id == $this->current_mandant) && !empty($this->mandant_settings)) return true;
        // Force a refresh
        $mandant = xarMod::apiFunc('ledgerba', 'user', 'get_mandant', array('mandant_id' => $id));
        $this->mandant_settings = $mandant->getFieldValues(array(), 1);
        $this->current_mandant = $id;
        return true;
    }

/**
 *  Get the settings of the current mandant
 */
    public function getMandantSettings($type='', $force_refresh=1)
    {
        $this->setMandant($this->current_mandant, $force_refresh);
        switch ($type) {
            case 'ap':
                $settings = @unserialize($this->mandant_settings['ap_settings']);
            break;
            case 'ar':
                $settings = @unserialize($this->mandant_settings['ar_settings']);
            break;
            case 'gl':
                $settings = @unserialize($this->mandant_settings['gl_settings']);
            break;
            case 'py':
                $settings = @unserialize($this->mandant_settings['py_settings']);
            break;
            case 'se':
                $settings = @unserialize($this->mandant_settings['se_settings']);
            break;
            case 'so':
                $settings = @unserialize($this->mandant_settings['so_settings']);
            break;
            default:
                if (!isset($this->mandant_settings[$type])) throw new Exception(xarML('The setting #(1) does not exist', $type));
                $settings = $this->mandant_settings[$type];
            break;
        }
        return $settings;
    }

/**
 *  Save the settings of the current mandant to the database
 */
    public function saveSettings($type='', $settings)
    {
        $mandant = xarMod::apiFunc('ledgerba', 'user', 'get_mandant', array('mandant_id' => $id));
        $field = $type . "_settings";
        $settingvalue = serialize($settings);
        $mandant->updateItem(array($field => $settingvalue));
        return true;
    }
    
/**
 *  Get a specific setting of the current mandant
 */
    public function getMandantSetting($type='', $setting='')
    {
        $settings = $this->getMandantSettings($type);
        if (is_array($settings)) {
            if (!isset($settings[$setting])) throw new Exception(xarML('The setting #(1) does not exist', $setting));
            return $settings[$setting];
        }
        return $settings;
    }
    
/**
 *  Set a specific setting of the current mandant
 */
    public function setSetting($type='', $setting='', $value)
    {
        if (empty($setting)) throw new Exception(xarML('No setting to be saved was passed'));
        if (!isset($value)) throw new Exception(xarML('No value to be saved was passed'));
        $settings = $this->getMandantSettings($type);
//        if (!isset($settings[$setting])) throw new Exception(xarML('The setting #(1) does not exist', $setting));
        $settings[$setting] = $value;
        $mandant = xarMod::apiFunc('ledgerba', 'user', 'get_mandant', array('mandant_id' => $this->current_mandant));
        $field = $type . "_settings";
        $fieldValues = serialize($settings);
        $mandant->updateItem(array($field => $fieldValues));
        $this->mandant_settings = $mandant->getFieldValues(array(), 1);
        return true;
    }
    
/**
 *  Get the mandanten available to this user
 */
    public function getMandanten()
    {
        if (empty($this->mandanten)) {
            $this->mandanten = xarMod::apiFunc('ledgerba','user','get_mandanten');
        }
        return $this->mandanten;
    }

/**
 *  Retrieve any user specific settings ("fields":
 *  - the number of item on a listings page
 *  - the mandant we are configuring
 */
    public function checkInput($fields=array())
    {
        if (empty($fields)) {
            // The number of items displayed in listings on a page
            if (!isset($this->items_per_page[xarMod::getName()])) $this->items_per_page[xarMod::getName()] = xarModVars::get(xarMod::getName(), 'items_per_page');
            if(!xarVarFetch('items_per_page',  'int',   $data['items_per_page'],   $this->items_per_page[xarMod::getName()],     XARVAR_NOT_REQUIRED)) {return;}
            $this->items_per_page[xarMod::getName()] = $data['items_per_page'];

            // The current mandant of this user
            if(!xarVarFetch('current_mandant',  'int',   $data['current_mandant'],   0,     XARVAR_NOT_REQUIRED)) {return;}
            if (!empty($data['current_mandant'])) $this->current_mandant = $data['current_mandant'];
            else $data['current_mandant'] = $this->current_mandant;

            // The mandant this user is configuring
            if(!xarVarFetch('config_mandant',  'int',   $data['config_mandant'],   0,     XARVAR_NOT_REQUIRED)) {return;}
            if (!empty($data['config_mandant'])) xarModUserVars::set('ledgerba', 'config_mandant', $data['config_mandant']);
        } else {
        }
        return $data;
    }
}
?>