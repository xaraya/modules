  var isCancel=false;  
  
  //Arrays are built in the php file so we can check the colors and names without
  //going back to the server
  #$js_colors_array#  
  #$js_names_array#
  
  //Checks to see if a string is contained in an array. Returns true if found.
  function searchArray(compareString, searchArray)
  {
    for (var i = 0; i != searchArray.length;i++)
    {
      if (searchArray[i] == compareString)
        return true
    }
    return false
  }
  
  //Named this validate2 since the navigation header has a validation function too.
  //The double amp gives problems in Xar 0.9.12. Need to get another error check
  function validate2(form)
  {
    var error_msg = ''
    if (form.cat_name.value == '')
      error_msg += "The category name is a required field.\n"
    if (form.color.value == '')
      error_msg += "The color is a required field.\n"
    if (searchArray(form.cat_name.value,Names) && form.cat_name.defaultValue != form.cat_name.value)
      error_msg += "The name you chose is already being used for another category. Choose another.\n"
    if (searchArray(form.color.value,Colors) && form.color.defaultValue != form.color.value)
      error_msg += "The color you chose is already being used for another category. Choose another.\n"
    if (error_msg != '')
    {
      error_msg = "Please fix these errors first:\n" + error_msg + "\n"
      alert(error_msg)
      return false
    }
    return true
  }  
