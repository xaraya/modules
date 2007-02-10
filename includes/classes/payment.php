<?php
/* -----------------------------------------------------------------------------------------
   $Id: payment.php,v 1.1 2003/09/06 22:13:53 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(payment.php,v 1.36 2003/02/11); www.oscommerce.com
   (c) 2003  nextcommerce (payment.php,v 1.11 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  // include needed functions
  require_once(DIR_FS_INC . 'xtc_count_payment_modules.inc.php');
  require_once(DIR_FS_INC . 'xtc_in_array.inc.php');

  class payment {
    var $modules, $selected_module;

    // class constructor
    function payment($module = '') {
      global $PHP_SELF;

      if (defined('MODULE_PAYMENT_INSTALLED') && xarModAPIFunc('commerce','user','not_null',array('arg' => MODULE_PAYMENT_INSTALLED))) {
        $this->modules = explode(';', MODULE_PAYMENT_INSTALLED);

        $include_modules = array();

        if ( (xarModAPIFunc('commerce','user','not_null',array('arg' => $module))) && (in_array($module . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)), $this->modules)) ) {
          $this->selected_module = $module;

          $include_modules[] = array('class' => $module, 'file' => $module . '.php');
        } else {
          reset($this->modules);
          while (list(, $value) = each($this->modules)) {
            $class = substr($value, 0, strrpos($value, '.'));
            $include_modules[] = array('class' => $class, 'file' => $value);
          }
        }
    // load unallowed modules into array
    $unallowed_modules = explode(',', $_SESSION['customers_status']['customers_status_payment_unallowed']);

        for ($i = 0, $n = sizeof($include_modules); $i < $n; $i++) {
          if (xtc_in_array(str_replace('.php', '', $include_modules[$i]['file']), $unallowed_modules) != 'false') {
            // check if zone is alowed to see module
            if (constant(MODULE_PAYMENT_ . strtoupper(str_replace('.php', '', $include_modules[$i]['file'])) . _ALLOWED) != '') {
              $unallowed_zones = explode(',', constant(MODULE_PAYMENT_ . strtoupper(str_replace('.php', '', $include_modules[$i]['file'])) . _ALLOWED));
            } else {
              $unallowed_zones = array();
            }
            if (in_array($_SESSION['delivery_zone'], $unallowed_zones) == true || count($unallowed_zones) == 0) {
              include(DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $include_modules[$i]['file']);
              include(DIR_WS_MODULES . 'payment/' . $include_modules[$i]['file']);
              $GLOBALS[$include_modules[$i]['class']] = new $include_modules[$i]['class'];
            }
          }
        }
        // if there is only one payment method, select it as default because in
        // checkout_confirmation.php the $payment variable is being assigned the
        // $HTTP_POST_VARS['payment'] value which will be empty (no radio button selection possible)
        if ( (xtc_count_payment_modules() == 1) && (!is_object($_SESSION['payment'])) ) {
          $_SESSION['payment'] = $include_modules[0]['class'];
        }

        if ( (xarModAPIFunc('commerce','user','not_null',array('arg' => $module))) && (in_array($module, $this->modules)) && (isset($GLOBALS[$module]->form_action_url)) ) {
          $this->form_action_url = $GLOBALS[$module]->form_action_url;
        }
      }
    }

    // class methods
    /* The following method is needed in the checkout_confirmation.php page
       due to a chicken and egg problem with the payment class and order class.
       The payment modules needs the order destination data for the dynamic status
       feature, and the order class needs the payment module title.
       The following method is a work-around to implementing the method in all
       payment modules available which would break the modules in the contributions
       section. This should be looked into again post 2.2.
     */
    function update_status() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module])) {
          if (function_exists('method_exists')) {
            if (method_exists($GLOBALS[$this->selected_module], 'update_status')) {
              $GLOBALS[$this->selected_module]->update_status();
            }
          } else { // PHP3 compatibility
            @call_user_method('update_status', $GLOBALS[$this->selected_module]);
          }
        }
      }
    }

    function javascript_validation() {
      $js = '';
      if (is_array($this->modules)) {
        $js = '<script type="text/javascript"><!-- ' . "\n" .
              'function check_form() {' . "\n" .
              '  var error = 0;' . "\n" .
              '  var error_message = "' . JS_ERROR . '";' . "\n" .
              '  var payment_value = null;' . "\n" .
              '  if (document.checkout_payment.payment.length) {' . "\n" .
              '    for (var i=0; i<document.checkout_payment.payment.length; i++) {' . "\n" .
              '      if (document.checkout_payment.payment[i].checked) {' . "\n" .
              '        payment_value = document.checkout_payment.payment[i].value;' . "\n" .
              '      }' . "\n" .
              '    }' . "\n" .
              '  } else if (document.checkout_payment.payment.checked) {' . "\n" .
              '    payment_value = document.checkout_payment.payment.value;' . "\n" .
              '  } else if (document.checkout_payment.payment.value) {' . "\n" .
              '    payment_value = document.checkout_payment.payment.value;' . "\n" .
              '  }' . "\n\n";

        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $js .= $GLOBALS[$class]->javascript_validation();
          }
        }

        $js .= "\n" . '  if (payment_value == null) {' . "\n" .
               '    error_message = error_message + "' . JS_ERROR_NO_PAYMENT_MODULE_SELECTED . '";' . "\n" .
               '    error = 1;' . "\n" .
               '  }' . "\n\n" .
               '  if (error == 1) {' . "\n" .
               '    alert(error_message);' . "\n" .
               '    return false;' . "\n" .
               '  } else {' . "\n" .
               '    return true;' . "\n" .
               '  }' . "\n" .
               '}' . "\n" .
               '//--></script>' . "\n";
      }

      return $js;
    }

    function selection() {
      $selection_array = array();

      if (is_array($this->modules)) {
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $selection = $GLOBALS[$class]->selection();
            if (is_array($selection)) $selection_array[] = $selection;
          }
        }
      }

      return $selection_array;
    }

    function pre_confirmation_check() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          $GLOBALS[$this->selected_module]->pre_confirmation_check();
        }
      }
    }

    function confirmation() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->confirmation();
        }
      }
    }

    function process_button() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->process_button();
        }
      }
    }

    function before_process() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->before_process();
        }
      }
    }

    function after_process() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->after_process();
        }
      }
    }

    function get_error() {
      if (is_array($this->modules)) {
        if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
          return $GLOBALS[$this->selected_module]->get_error();
        }
      }
    }
  }
?>