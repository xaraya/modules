<!--
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2003 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Modified by: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------
-->

<script type="text/javascript"><!--
var form = "";
var submitted = false;
var error = false;
var error_message = "";

function check_input(field_name, field_size, message) {
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var field_value = form.elements[field_name].value;

    if (field_value == '' || field_value.length < field_size) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_radio(field_name, message) {
  var isChecked = false;

  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var radio = form.elements[field_name];

    for (var i=0; i<radio.length; i++) {
      if (radio[i].checked == true) {
        isChecked = true;
        break;
      }
    }

    if (isChecked == false) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_select(field_name, field_default, message) {
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var field_value = form.elements[field_name].value;

    if (field_value == field_default) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_password(field_name_1, field_name_2, field_size, message_1, message_2) {
  if (form.elements[field_name_1] && (form.elements[field_name_1].type != "hidden")) {
    var password = form.elements[field_name_1].value;
    var confirmation = form.elements[field_name_2].value;

    if (password == '' || password.length < field_size) {
      error_message = error_message + "* " + message_1 + "\n";
      error = true;
    } else if (password != confirmation) {
      error_message = error_message + "* " + message_2 + "\n";
      error = true;
    }
  }
}

function check_password_new(field_name_1, field_name_2, field_name_3, field_size, message_1, message_2, message_3) {
  if (form.elements[field_name_1] && (form.elements[field_name_1].type != "hidden")) {
    var password_current = form.elements[field_name_1].value;
    var password_new = form.elements[field_name_2].value;
    var password_confirmation = form.elements[field_name_3].value;

    if (password_current == '' || password_current.length < field_size) {
      error_message = error_message + "* " + message_1 + "\n";
      error = true;
    } else if (password_new == '' || password_new.length < field_size) {
      error_message = error_message + "* " + message_2 + "\n";
      error = true;
    } else if (password_new != password_confirmation) {
      error_message = error_message + "* " + message_3 + "\n";
      error = true;
    }
  }
}

<!--
FIXME
function check_form(form_name) {
  if (submitted == true) {
    <xar:mlstring>This page has already been confirmed. Please click OK and wait till the process has finished.<xar:mlstring>
    return false;
  }

  error = false;
  form = form_name;
  error_message = "'Errors have occured during the process of your form!\nPlease make the following corrections:\n\n'";

<xar:if condition="ACCOUNT_GENDER == 'true'">
<?php if () echo '  check_radio("gender", "Please select your gender.");' . "\n"; ?>
</xar:if>

  check_input("firstname", 'ENTRY_LAST_NAME_MIN_LENGTH', "Your first name must consist of at least  ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' characters.");
  check_input("lastname", <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>, "Your last name must consist of at least ' . ENTRY_LAST_NAME_MIN_LENGTH . ' characters.");

<?php if (ACCOUNT_DOB == 'true') echo '  check_input("dob", ' . ENTRY_DOB_MIN_LENGTH . ', "' . ENTRY_DATE_OF_BIRTH_ERROR . '");' . "\n"; ?>

  check_input("email_address", <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>, 'Your eMail-address must consist of at least  ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' characters.');
  check_input("street_address", <?php echo ENTRY_STREET_ADDRESS_MIN_LENGTH; ?>, 'Street/Nr must consist of at least ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' characters.');
  check_input("postcode", <?php echo ENTRY_POSTCODE_MIN_LENGTH; ?>, 'Your ZIP Code must consist of at least ' . ENTRY_POSTCODE_MIN_LENGTH . ' characters.');
  check_input("city", <?php echo ENTRY_CITY_MIN_LENGTH; ?>, 'City must consist of at least ' . ENTRY_CITY_MIN_LENGTH . ' characters.');

<?php if (ACCOUNT_STATE == 'true') echo '  check_input("state", ' . ENTRY_STATE_MIN_LENGTH . ', 'Your state must consist of at least ' . ENTRY_STATE_MIN_LENGTH . ' characters.');' . "\n"; ?>

  check_select("country", "", 'Please select your country out of the list.');

  check_input("telephone", <?php echo ENTRY_TELEPHONE_MIN_LENGTH; ?>, 'Your Telephone number must consist of at least ' . ENTRY_TELEPHONE_MIN_LENGTH . ' characters.');

  check_password("password", "confirmation", <?php echo ENTRY_PASSWORD_MIN_LENGTH; ?>, 'Your password must consist of at least ' . ENTRY_PASSWORD_MIN_LENGTH . ' characters.', 'Your passwords do not match.');
  check_password_new("password_current", "password_new", "password_confirmation", <?php echo ENTRY_PASSWORD_MIN_LENGTH; ?>, 'Your password must consist of at least ' . ENTRY_PASSWORD_MIN_LENGTH . ' characters.', 'Your new password must consist of at least ' . ENTRY_PASSWORD_MIN_LENGTH . ' characters.', 'Your passwords do not match.');

  if (error == true) {
    alert(error_message);
    return false;
  } else {
    submitted = true;
    return true;
  }
}
-->
//--></script>