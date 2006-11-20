<?php
/* --------------------------------------------------------------
   $Id: message_stack.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(message_stack.php,v 1.5 2002/11/22); www.oscommerce.com
   (c) 2003  nextcommerce (message_stack.php,v 1.6 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License

   Example usage:

   $messageStack = new messageStack();
   $messageStack->add('Error: Error 1', 'error');
   $messageStack->add('Error: Error 2', 'warning');
   if ($messageStack->size > 0) echo $messageStack->output();

   --------------------------------------------------------------*/

  class messageStack extends tableBlock
  {
    var $size = 0;

    function messageStack()
    {
      $this->errors = array();

      if (isset($_SESSION['messageToStack'])) {
        for ($i = 0, $n = sizeof($_SESSION['messageToStack']); $i < $n; $i++) {
          $this->add($_SESSION['messageToStack'][$i]['text'], $_SESSION['messageToStack'][$i]['type']);
        }
        unset($_SESSION['messageToStack']);
      }
    }

    function add($message, $type = 'error')
    {
      if ($type == 'error') {
        $this->errors[] = array('params' => 'class="messageStackError"', 'text' => xtc_image(xarTplGetImage('icons/error.gif'), ICON_ERROR) . '&#160;' . $message);
      } elseif ($type == 'warning') {
        $this->errors[] = array('params' => 'class="messageStackWarning"', 'text' => xtc_image(xarTplGetImage('icons/warning.gif'), ICON_WARNING) . '&#160;' . $message);
      } elseif ($type == 'success') {
        $this->errors[] = array('params' => 'class="messageStackSuccess"', 'text' => xtc_image(xarTplGetImage('icons/success.gif'), ICON_SUCCESS) . '&#160;' . $message);
      } else {
        $this->errors[] = array('params' => 'class="messageStackError"', 'text' => $message);
      }

      $this->size++;
    }

    function add_session($message, $type = 'error')
    {
      if (!isset($_SESSION['messageToStack'])) {
        $_SESSION['messageToStack'] = array();
      }

      $_SESSION['messageToStack'][] = array('text' => $message, 'type' => $type);
    }

    function reset()
    {
      $this->errors = array();
      $this->size = 0;
    }

    function output()
    {
      $this->table_data_parameters = 'class="messageBox"';
      return $this->tableBlock($this->errors);
    }
  }
?>