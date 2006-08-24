<?php

php::import('server.accessrules');

/**
 * Provides access rule management facilities
**/
class Bean_Rules extends PHP_Bean 
{
    public $namespace = 'rules';

    /**
     * Add a rule to the system.
     *
     * @access   public
     * @param    string
     * @param    string
     * @param    string
     * @return   boolean
    **/
    function add($type, $user, $value ='') 
    {
        try 
        {
            $rules = $this->getRules($type, $user);
            $rules->add($value);
        } catch (Exception $e) 
        {
            return new ObjectServerException($e->getMessage(),$e->getCode());
        }
        return true;
    }
    
    /**
     * Remove a rule from the system.
     *
     * @access   public
     * @param    string
     * @param    string
     * @param    string
     * @return   boolean
    **/
    function remove($type, $user, $value = '') 
    {
        try 
        {
            $rules = $this->getRules($type, $user);
            // Got the rules of the right type for the user, add one
            $rules->remove($value);
        } catch (Exception $e) 
        {
            return new ObjectServerException($e->getMessage(),$e->getCode());
        }
        return true;
    }

    /**
     * Lists the existing access rules.
     *
     * @access  public
     * @param   string
     * @param   string
     * @return  array
    **/
    function get($type, $user = '') 
    {
        try 
        {
            $rules = $this->getRules($type, $user);
            $list = $rules->get();
        } catch (Exception $e)
        {
            return new ObjectServerException($e->getMessage(), $e->getCode());
        }
        return $list;
    }
    
    private function &getRules($type,$user)
    {
        $access =& $this->server->access;
        switch($type)
        {
            case 'host':
                $rules = $access->HostRules($user);
                break;
            case 'object':
                $rules = $access->ObjectRules($user);
                break;
            case 'identity':
                $rules = $access->IdentityRules($user);
                break;
            default:
                throw new Exception('Unknown rule type');
        } 
        return $rules;
    }
}

?>