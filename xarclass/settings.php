<?php
/**
 * Handles all security settings.
 */
class SecuritySettings
{
    var $modid;

    var $itemtype;

    var $exclude_groups;

    var $default_group_level;

    var $owner_table = null;
    var $owner_primary_key = null;
    var $owner_column = null;

    var $default_item_levels;

    var $default_module_levels;

    var $levels = array(
        'overview'  => SECURITY_OVERVIEW
        , 'read'    => SECURITY_READ
        , 'comment' => SECURITY_COMMENT
        , 'write'   => SECURITY_WRITE
        , 'manage'  => SECURITY_MANAGE
        , 'admin'   => SECURITY_ADMIN
        //, 'none'    => SECURITY_NONE
    );

    /**
     * The default contructor.
     *
     * @return SecuritySettings
     */
    function SecuritySettings($modid=0, $itemtype=0)
    {
        $this->modid    = $modid;
        $this->itemtype = $itemtype;
    }

    /**
     * Static method to generate a new SecuritySettings object,
     *
     * @param integer $modid
     * @param integer $itemtype
     * @return SecuritySettings
     */
    function factory($modid=0, $itemtype=0)
    {
        $vars = array("settings", "settings.$modid", "settings.$modid.$itemtype");
        while( empty($params) && ($var = array_pop($vars)) != null )
        {
            $params =@ unserialize(xarModGetVar('security', $var));
        }
        if( is_object($params) )
        {
            //clone object This should really be a factory
            $settings = $params;
        }
        else
        {
            $settings = new SecuritySettings($modid, $itemtype);
            // Array of params old style. Convert to object.
            $settings->array_to_object($params);
        }
        $settings->modid = $modid;
        $settings->itemtype = $itemtype;

        return $settings;
    }

    /**
     * Converts the old style array stored settings into the new SecuritySettings object
     *
     * @param array $array_params
     */
    function array_to_object($array_params)
    {
        $this->exclude_groups = $array_params['exclude_groups'];

        if( !is_null($array_params['owner']) )
        {
            $this->owner_table = @$array_params['owner']['table'];
            $this->owner_column = @$array_params['owner']['column'];
            $this->owner_primary_key = @$array_params['owner']['primary_key'];
        }

        // Convert default group level
        $this->default_group_level = new SecurityLevel();
        foreach( $this->levels as $label => $value )
        {
            if( $label != 'none' )
            {
                $this->default_group_level->$label = $array_params['default_group_level'] & $value;
            }
        }

        // Convers the default item levels for new items
        if( is_array($array_params['levels']) )
        {
            foreach( $array_params['levels'] as $role => $level_set )
            {
                $this->default_item_levels[$role] = new SecurityLevel($level_set);
            }
        }
    }

    /**
     * Saves the current SecuritySettings.
     *
     * @return boolean
     */
    function save()
    {
        $var_name = "settings";
        $var_name .= ".$this->modid";
        $var_name .= ".$this->itemtype";

        // Need to store module levels in the database so check function will work.
        //Security::create($this->default_module_levels, $this->modid, $this->itemtype, 0);

        return xarModSetVar('security', $var_name, serialize($this));
    }
}

?>