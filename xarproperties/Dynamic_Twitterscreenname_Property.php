<?php
/**
 * Twitter Module 
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */

/*
 * Handle Twitterscreennname Property
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */

class Dynamic_Twitterscreenname_Property extends Dynamic_Property
{
    function checkInput($name='', $value = null)
    {
        if (empty($name)) {
            $name = 'dd_'.$this->id;
        }
        // store the fieldname for validations who need them (e.g. file uploads)
        $this->fieldname = $name;
        if (!isset($value)) {
            if (!xarVarFetch($name, 'isset', $value,  NULL, XARVAR_DONT_SET)) {return;}
        }
        // look for the password
        $pass = $name.'_pass';
        if (!xarVarFetch($pass, 'isset', $password, '', XARVAR_NOT_REQUIRED)) return;
        if (!empty($password)) {
          $value = $value.','.$password;
        }
        return $this->validateValue($value);
    }

    function validateValue($value = null)
    {
        if (!isset($value)) {
            $value = $this->value;
        }
        if (!xarSecurityCheck('EditTwitter', 0)) $value = '';
        // see if we got something to validate
        if (!empty($value)) {
          // if we got a screen name and password we validate credentials
          if (strpos($value, ',') !== false) {
            list ($screen_name, $screen_pass) = explode(',', $value);
            // validate credentials
            if (!empty($screen_name) && !empty($screen_pass)) {
              $isvalid = xarModAPIFunc('twitter', 'user', 'rest_methods',
                array(
                  'area' => 'account',
                  'method' => 'verify_credentials',
                  'username' => $screen_name,
                  'password' => $screen_pass,
                  'cached' => true,
                  'refresh' => 60,
                  'superrors' => true
                ));
              // invalid user or password, let them know
              if (!$isvalid) {
                $this->invalid = xarML('Twitter Screen Name or Password');
                return false;
              }
            // we were expecting a screen name and password and didn't get both
            } else {
              $this->invalid = xarML('Twitter Screen Name or Password');
              return false;
            }
          // just got a screen name, use show user method
          } else {
            $isvalid = xarModAPIFunc('twitter', 'user', 'rest_methods',
              array(
                'area' => 'users',
                'method' => 'show',
                'username' => $value,
                'cached' => true,
                'refresh' => 3600,
                'superrors' => true
              ));
            // invalid user, let them know
            if (!$isvalid) {
              $this->invalid = xarML('Twitter Screen Name');
              return false;
            }
          }
        } 
        $this->value = $value;
        return true;
    }

//    function showInput($name = '', $value = null, $id = '', $tabindex = '')
    function showInput($args = array())
    {
        if (!xarSecurityCheck('EditTwitter', 0)) return '';
        extract($args);
        if (!isset($value)) {
            $value = $this->value;
        }
        if (empty($name)) {
            $name = 'dd_' . $this->id;
        }
        if (empty($id)) {
            $id = $name;
        }
        
        $screen_pass = '';
        if (!empty($value)) {
          // if we got a screen name and password we split for our form
          if (strpos($value, ',') !== false) {
            list ($screen_name, $screen_pass) = explode(',', $value);
            $value = $screen_name;
            $screen_pass = $screen_pass;
          }
        }
        
        $data=array();
        $data['value']= $value;
        $data['name'] = $name;
        $data['id']   = $id;
        $data['screen_pass'] = $screen_pass;
        $data['invalid']  = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) :'';
        
        $tplmodule = empty($tplmodule) ? 'twitter' : $tplmodule;
        $template = empty($template) ? 'twitterscreenname' : $template; 

        return xarTplProperty($tplmodule, $template, 'showinput', $data);
    }

    function showOutput($args = array())
    {
        if (!xarSecurityCheck('ReadTwitter', 0)) return '';
        extract($args);
        if (!isset($value)) {
            $value = $this->value;
        }
        $screen_pass = '';
        if (!empty($value)) {
          // if we got a screen name and password we split for our form
          if (strpos($value, ',') !== false) {
            list ($screen_name, $screen_pass) = explode(',', $value);
            $value = $screen_name;
            $screen_pass = $screen_pass;
          }
        }        
        $data = array();
        $data['value'] = $value;
        $data['name']  = $this->name;
        $data['id']    = $this->id;
        if (!empty($value)) {
          $data['user_element'] = xarModAPIFunc('twitter', 'user', 'rest_methods',
              array(
                'area' => 'users', 
                'method' => 'show',
                'username' => $value,
                'cached' => true,
                'refresh' => 3600,
                'superrors' => true
              ));
          if (!empty($this->_itemid)) {
            $userid = $this->_itemid;
          }
          if (empty($userid)) $userid = 0;
          if (!empty($data['user_element'])) {
            $data['user_element']['uid'] = $userid;
            $defaults = array();
            $settings = array();
            // get module defaults
            $defaults['user_timeline'] = xarModGetVar('twitter', 'user_timeline');
            $defaults['friends_display'] = xarModGetVar('twitter', 'friends_display');
            $defaults['profile_image'] = xarModGetVar('twitter', 'profile_image');
            $defaults['profile_description'] = xarModGetVar('twitter', 'profile_description');
            $defaults['profile_location'] = xarModGetVar('twitter', 'profile_location');
            $defaults['followers_count'] = xarModGetVar('twitter', 'followers_count');
            $defaults['friends_count'] = xarModGetVar('twitter', 'friends_count');
            $defaults['last_status'] = xarModGetVar('twitter', 'last_status');   
            $defaults['profile_url'] = xarModGetVar('twitter', 'profile_url');
            $defaults['statuses_count'] = xarModGetVar('twitter', 'statuses_count');   
            $defaults['favourites_display'] = xarModGetVar('twitter', 'favourites_display');
            // get the display settings for this users account if allowed
            foreach ($defaults as $key => $value) {
              // a value of 2 means users can over-ride this setting
              if ($value == 2) {
                // get the user settings
                $setting = xarModGetUserVar('twitter', $key, $data['user_element']['uid']);
              // any other value and we use the module default setting
              } else {
                $setting = $value;
              }
              $settings[$key] = empty($setting) ? false : $setting;
            }
            $data['user_settings'] = $settings;
          }
        } else {
          $data['user_element'] = '';
        }


        $tplmodule = empty($tplmodule) ? 'twitter' : $tplmodule;
        $template = empty($template) ? 'twitterscreenname' : $template; 
        return xarTplProperty($tplmodule, $template, 'showoutput', $data);
    }


    /**
     * Get the base information for this property.
     *
     * @returns array
     * @return base information for this property
     **/
     function getBasePropertyInfo()
     {
         $baseInfo = array(
                              'id'         => 991991,
                              'name'       => 'twitterscreenname',
                              'label'      => 'Twitter Screen Name',
                              'format'     => '991991',
                              'validation' => '',
                            'source'     => '',
                            'dependancies' => '',
                            'requiresmodule' => 'twitter',
                            'aliases' => '',
                            'args'         => '',
                            // ...
                           );
        return $baseInfo;
     }

}

?>
