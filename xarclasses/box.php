<?php
/* --------------------------------------------------------------
   $Id: box.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(box.php,v 1.5 2002/03/16); www.oscommerce.com
   (c) 2003  nextcommerce (box.php,v 1.5 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License

   Example usage:

   $heading = array();
   $heading[] = array('params' => 'class="menuBoxHeading"',
                      'text'  => BOX_HEADING_TOOLS,
                      'link'  => xarModURL('commerce','user',(basename($PHP_SELF), xtc_get_all_get_params(array('selected_box')) . 'selected_box=tools'));

   $contents = array();
   $contents[] = array('text'  => SOME_TEXT);

   $box = new box;
   echo $box->infoBox($heading, $contents);
   --------------------------------------------------------------
*/

  class box extends tableBlock
  {
    function box() {
      $this->heading = array();
      $this->contents = array();
    }

    function infoBox($heading, $contents)
    {
      $this->table_row_parameters = 'class="infoBoxHeading"';
      $this->table_data_parameters = 'class="infoBoxHeading"';
      $this->heading = $this->tableBlock($heading);

      $this->table_row_parameters = '';
      $this->table_data_parameters = 'class="infoBoxContent"';
      $this->contents = $this->tableBlock($contents);

      return $this->heading . $this->contents;
    }

    function menuBox($heading, $contents)
    {
      $this->table_data_parameters = 'class="menuBoxHeading"';
      if ($heading[0]['link']) {
        $this->table_data_parameters .= ' onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . $heading[0]['link'] . '\'"';
        $heading[0]['text'] = '&#160;<a href="' . $heading[0]['link'] . '" class="menuBoxHeadingLink">' . $heading[0]['text'] . '</a>&#160;';
      } else {
        $heading[0]['text'] = '&#160;' . $heading[0]['text'] . '&#160;';
      }
      $this->heading = $this->tableBlock($heading);

      $this->table_data_parameters = 'class="menuBoxContent"';
      $this->contents = $this->tableBlock($contents);

      return $this->heading . $this->contents;
    }
  }
?>