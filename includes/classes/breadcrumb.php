<?php
/* -----------------------------------------------------------------------------------------
   $Id: breadcrumb.php,v 1.1 2003/09/06 22:13:53 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(breadcrumb.php,v 1.3 2003/02/11); www.oscommerce.com
   (c) 2003  nextcommerce (breadcrumb.php,v 1.5 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  class breadcrumb {
    var $_trail;

    function breadcrumb() {
      $this->reset();
    }

    function reset() {
      $this->_trail = array();
    }

    function add($title, $link = '') {
      $this->_trail[] = array('title' => $title, 'link' => $link);
    }

    function trail($separator = ' - ') {
      $trail_string = '';

      for ($i=0, $n=sizeof($this->_trail); $i<$n; $i++) {
        if (isset($this->_trail[$i]['link']) && xarModAPIFunc('commerce','user','not_null',array('arg' => $this->_trail[$i]['link']))) {
          $trail_string .= '<a href="' . $this->_trail[$i]['link'] . '" class="headerNavigation">' . $this->_trail[$i]['title'] . '</a>';
        } else {
          $trail_string .= $this->_trail[$i]['title'];
        }

        if (($i+1) < $n) $trail_string .= $separator;
      }

      return $trail_string;
    }
  }
?>