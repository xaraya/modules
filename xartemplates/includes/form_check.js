<script type="text/javascript">
<!--
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

function check_form(form_name) {
    if (submitted == true) {
        alert(#xarML('This page has already been confirmed. Please click okay and wait until the process has finished.')#);
        return false;
    }

    error = false;
    form = form_name;
    error_message = #xarML("Missing necessary information!\nPlease fill in correctly.\n\n")#;

    <xar:if condition="$configuration.account_gender == 'true'">
        check_radio("gender", #xarML("Please select your gender.")#);
    </xar:if>

    check_input("firstname", #$configuration.entry_first_name_min_length#, #xarML("Your first name must consist of at least #(1) characters.",$configuration.entry_first_name_min_length)#);

    check_input("lastname", #$configuration.entry_last_name_min_length#, #xarML("Your last name must consist of at least #(1) characters.",$configuration.entry_last_name_min_length)#);

    <xar:if condition="$configuration.account_dob == 'true'">
        check_input("dob", #$configuration.entry_dob_min_length#, #xarML("(e.g. 05/21/1970)")#);
    </xar:if>

    check_input("email_address", #$configuration.entry_email_address_min_length#, #xarML("Your email address must consist of at least #(1) characters.",$configuration.entry_email_address_min_length)#);

    check_input("street_address", #$configuration.entry_street_address_min_length#, #xarML("Your street address must consist of at least #(1) characters.",$configuration.entry_street address_min_length)#);

    check_input("postcode", #$configuration.entry_postcode_min_length#, #xarML("Your postal code must consist of at least #(1) characters.",$configuration.entry_postcode_min_length)#);

    check_input("city", #$configuration.entry_city_min_length#, #xarML("Your city name must consist of at least #(1) characters.",$configuration.entry_city_min_length)#);

    <xar:if condition="$configuration.account_state == 'true'">
        check_input("state", #$configuration.entry_state_min_length#, #xarML("Your state name must consist of at least #(1) characters.",$configuration.entry_state_min_length)#);
    </xar:if>

    check_select("country", "", #xarML("Please select your country from the list.")#);

    check_input("telephone", #$configuration.entry_telephone_min_length#, #xarML("Your telephone number must consist of at least #(1) digits.",$configuration.entry_telephone_min_length)#);

    check_password("password", "confirmation", #$configuration.entry_password_min_length#, #xarML("Your password must consist of at least #(1) characters.",$configuration.entry_password_min_length)#, #xarML("Your passwords do not match.")#);

    check_password_new("password_current", "password_new", "password_confirmation", #$configuration.entry_password_min_length#, #xarML("Your password must consist of at least #(1) characters.",$configuration.entry_password_min_length)#, #xarML("Your password must consist of at least #(1) characters.",$configuration.entry_password_min_length)#, #xarML("Your passwords do not match.")#);

    if (error == true) {
        alert(error_message);
        return false;
    } else {
        submitted = true;
        return true;
    }
}
//--></script>
