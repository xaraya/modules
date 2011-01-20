<?php
sys::import('modules.roles.class.role');

class RegistrationCompact extends Role
{
    public function createItem(Array $data = array())
    {
        $this->properties['name']->value = $this->properties['uname']->value;

		$this->properties['state']->value = xarModVars::get('registration','defaultuserstate');
		if (xarModVars::get('registration', 'explicitapproval')) {
			$this->properties['state']->value = $fieldvalues['state'] = xarRoles::ROLES_STATE_PENDING;
		}
		if (xarModVars::get('registration', 'requirevalidation')) {
			$this->properties['state']->value = $fieldvalues['state'] = xarRoles::ROLES_STATE_NOTVALIDATED;
		}

        $id = parent::createItem($data);
        return $id;
    }
}
?>
