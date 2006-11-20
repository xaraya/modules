<?php
/* --------------------------------------------------------------
   $Id: html_output.php,v 1.2 2003/09/28 14:45:40 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.26 2002/08/06); www.oscommerce.com
   (c) 2003  nextcommerce (html_output.php,v 1.7 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  ////
  // The HTML href link wrapper function
  function xarModURL('commerce','admin',($page = '', $parameters = '', $connection = 'NONSSL') {
    if ($page == '') {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine the page link!<br><br>Function used:<br><br>xarModURL('commerce','admin',(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
    }
    if ($connection == 'NONSSL') {
      $link = HTTP_SERVER . DIR_WS_ADMIN;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL == 'true') {
        $link = HTTPS_SERVER . DIR_WS_ADMIN;
      } else {
        $link = HTTP_SERVER . DIR_WS_ADMIN;
      }
    } else {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL<br><br>Function used:<br><br>xarModURL('commerce','admin',(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
    }
    if ($parameters == '') {
      $link = $link . $page . '?' . SID;
    } else {
      $link = $link . $page . '?' . $parameters . '&' . SID;
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

    return $link;
  }

  function xtc_catalog_href_link($page = '', $parameters = '', $connection = 'NONSSL') {
    if ($connection == 'NONSSL') {
      $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL_CATALOG == 'true') {
        $link = HTTPS_CATALOG_SERVER . DIR_WS_CATALOG;
      } else {
        $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
      }
    } else {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL<br><br>Function used:<br><br>xarModURL('commerce','admin',(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
    }
    if ($parameters == '') {
      $link .= $page;
    } else {
      $link .= $page . '?' . $parameters;
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

    return $link;
  }

  ////
  // The HTML image wrapper function
  function xtc_image(xarTplGetImage($src), $alt = '', $width = '', $height = '', $params = '') {
    $image = '<img src="' . $src . '" border="0" alt="' . $alt . '"';
    if ($alt) {
      $image .= ' title=" ' . $alt . ' "';
    }
    if ($width) {
      $image .= ' width="' . $width . '"';
    }
    if ($height) {
      $image .= ' height="' . $height . '"';
    }
    if ($params) {
      $image .= ' ' . $params;
    }
    $image .= '>';

    return $image;
  }

  ////
  // The HTML form submit button wrapper function
  // Outputs a button in the selected language
/*  function xtc_image_submit($image, $alt, $params = '') {

    return '<input type="image" src="' . $language'] . '/admin/images/buttons/' . $image . '" border="0" alt="' . $alt . '"' . (($params) ? ' ' . $params : '') . '>';
  }
*/
  ////
  // Draw a 1 pixel black line
  function xtc_black_line() {
    return xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'pixel_black.gif'), '', '100%', '1');
  }

  ////
  // Output a separator either through whitespace, or with an image
  function xtc_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
    return xtc_image(xarTplGetImage(DIR_WS_IMAGES . $image), '', $width, $height);
  }

  ////
  // Output a function button in the selected language
/*  function xtc_image_button($image, $alt = '', $params = '') {

    return xtc_image(xarTplGetImage($language . '/admin/images/buttons/' . $image), $alt, '', '', $params);
  }
*/
  ////
  // javascript to dynamically update the states/provinces list when the country is changed
  // TABLES: zones
  function xtc_js_zone_list($country, $form, $field) {
    $countries_query = new xenQuery("select distinct zone_country_id from " . TABLE_ZONES . " order by zone_country_id");
    $num_country = 1;
    $output_string = '';
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($countries = $q->output()) {
      if ($num_country == 1) {
        $output_string .= '  if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";
      } else {
        $output_string .= '  } else if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";
      }

      $states_query = new xenQuery("select zone_name, zone_id from " . TABLE_ZONES . " where zone_country_id = '" . $countries['zone_country_id'] . "' order by zone_name");

      $num_state = 1;
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($states = $q->output()) {
        if ($num_state == '1') $output_string .= '    ' . $form . '.' . $field . '.options[0] = new Option("' . PLEASE_SELECT . '", "");' . "\n";
        $output_string .= '    ' . $form . '.' . $field . '.options[' . $num_state . '] = new Option("' . $states['zone_name'] . '", "' . $states['zone_id'] . '");' . "\n";
        $num_state++;
      }
      $num_country++;
    }
    $output_string .= '  } else {' . "\n" .
                      '    ' . $form . '.' . $field . '.options[0] = new Option("' . TYPE_BELOW . '", "");' . "\n" .
                      '  }' . "\n";

    return $output_string;
  }

  ////
  // Output a form
/*  function xtc_draw_form($name, $action, $parameters = '', $method = 'post', $params = '') {
    $form = '<form name="' . $name . '" action="';
    if ($parameters) {
      $form .= xarModURL('commerce','admin',($action, $parameters);
    } else {
      $form .= xarModURL('commerce','admin',($action);
    }
    $form .= '" method="' . $method . '"';
    if ($params) {
      $form .= ' ' . $params;
    }
    $form .= '>';

    return $form;
  }
*/
  ////
  // Output a form input field
  function xtc_draw_input_field($name, $value = '', $parameters = '', $required = false, $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . $type . '" name="' . $name . '"';
    if ( ($GLOBALS[$name]) && ($reinsert_value) ) {
      $field .= ' value="' . htmlspecialchars(trim($GLOBALS[$name])) . '"';
    } elseif ($value != '') {
      $field .= ' value="' . htmlspecialchars(trim($value)) . '"';
    }
    if ($parameters != '') {
      $field .= ' ' . $parameters;
    }
    $field .= '>';

    if ($required) $field .= '&#160;<span class="fieldRequired">* Required</span>';

    return $field;
  }
  // Output a form small input field
  function xtc_draw_small_input_field($name, $value = '', $parameters = '', $required = false, $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . $type . '" size="3" name="' . $name . '"';
    if ( ($GLOBALS[$name]) && ($reinsert_value) ) {
      $field .= ' value="' . htmlspecialchars(trim($GLOBALS[$name])) . '"';
    } elseif ($value != '') {
      $field .= ' value="' . htmlspecialchars(trim($value)) . '"';
    }
    if ($parameters != '') {
      $field .= ' ' . $parameters;
    }
    $field .= '>';

    if ($required) $field .= '&#160;<span class="fieldRequired">* Required</span>';

    return $field;
  }

  ////
  // Output a form password field
  function xtc_draw_password_field($name, $value = '', $required = false) {
    $field = xtc_draw_input_field($name, $value, 'maxlength="40"', $required, 'password', false);

    return $field;
  }

  ////
  // Output a form filefield
  function xtc_draw_file_field($name, $required = false) {
    $field = xtc_draw_input_field($name, '', '', $required, 'file');

    return $field;
  }

  ////
  // Output a selection field - alias function for xtc_draw_checkbox_field() and xtc_draw_radio_field()
  function xtc_draw_selection_field($name, $type, $value = '', $checked = false, $compare = '') {
    $selection = '<input type="' . $type . '" name="' . $name . '"';
    if ($value != '') {
      $selection .= ' value="' . $value . '"';
    }
    if ( ($checked == true) || ($GLOBALS[$name] == 'on') || ($value && ($GLOBALS[$name] == $value)) || ($value && ($value == $compare)) ) {
      $selection .= ' CHECKED';
    }
    $selection .= '>';

    return $selection;
  }

  ////
  // Output a form checkbox field
  function xtc_draw_checkbox_field($name, $value = '', $checked = false, $compare = '') {
    return xtc_draw_selection_field($name, 'checkbox', $value, $checked, $compare);
  }

  ////
  // Output a form radio field
  function xtc_draw_radio_field($name, $value = '', $checked = false, $compare = '') {
    return xtc_draw_selection_field($name, 'radio', $value, $checked, $compare);
  }

  ////
  // Output a form textarea field
  function xtc_draw_textarea_field($name, $wrap, $width, $height, $text = '', $params = '', $reinsert_value = true) {
    $field = '<textarea name="' . $name . '" wrap="' . $wrap . '" cols="' . $width . '" rows="' . $height . '"';
    if ($params) $field .= ' ' . $params;
    $field .= '>';
    if ( ($GLOBALS[$name]) && ($reinsert_value) ) {
      $field .= $GLOBALS[$name];
    } elseif ($text != '') {
      $field .= $text;
    }
    $field .= '</textarea>';

    return $field;
  }

  ////
  // Output a form hidden field
/*  function xtc_draw_hidden_field($name, $value = '') {
    $field = '<input type="hidden" name="' . $name . '" value="';
    if ($value != '') {
      $field .= trim($value);
    } else {
      $field .= trim($GLOBALS[$name]);
    }
    $field .= '">';

    return $field;
  }
*/
  ////
  // Output a form pull down menu
/*  function xtc_draw_pull_down_menu($name, $values, $default = '', $params = '', $required = false) {
    $field = '<select name="' . $name . '"';
    if ($params) $field .= ' ' . $params;
    $field .= '>';
    for ($i=0; $i<sizeof($values); $i++) {
      $field .= '<option value="' . $values[$i]['id'] . '"';
      if ( ((strlen($values[$i]['id']) > 0) && ($GLOBALS[$name] == $values[$i]['id'])) || ($default == $values[$i]['id']) ) {
        $field .= ' SELECTED';
      }
      $field .= '>' . $values[$i]['text'] . '</option>';
    }
    $field .= '</select>';

    if ($required) $field .= '&#160;<span class="fieldRequired">* Required</span>';

    return $field;
  }
  */
?>