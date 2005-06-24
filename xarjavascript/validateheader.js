/*Function included into the header
*/
function validate(form)
  {
    var error_msg = ''

    //Check Required Fields
    //Check if the jump_to drop down is day or week.  If so, make sure
    //we have a month, day, and year
    if (form.jump_to.value == 'day' || form.jump_to.value == 'week')
    {
      error_msg += checkMonth(form)
      error_msg += checkDay(form)
      error_msg += checkYear(form)
    }
    //Check if the jump_to drop down is month.  If so, make sure
    //we have a month and year
    if (form.jump_to.value == 'month')
    {
      form.jump_day.selectedIndex = 1
      error_msg += checkMonth(form)
      error_msg += checkYear(form)
    }
    //Check if the jump_to drop down is year.  If so, make sure
    //we have a year
    if (form.jump_to.value == 'year')
      error_msg += checkYear(form)
    
    if (error_msg != '')
    {
      error_msg = "You must select a value for these fields:\n" + error_msg
      alert(error_msg)
      return false;
    }
    return true
  }
  
  function checkMonth(form)
  {
    if (form.jump_month.value == '')
      return "Month\n"
    return '';
  }
    function checkDay(form)
  {
    if (form.jump_day.value == '' || form.jump_day.value == '00')
      return "Day\n"
    return '';
  }
  function checkYear(form)
  {
    if (form.jump_year.value == '')
      return "Year\n"
    return '';
  }