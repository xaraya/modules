/*this function determines whether to give another field focus based on whether
  the current field has reached its max length (i.e phone number part 1 has
  reached max length of 3 so focus is given to phone number part 2, etc.*/
function checkNumberLen(field,maxLength,focusField){
   if(field.value.length==maxLength)
      focusField.focus();

}
/*this function checks a value to be sure it is numeric*/
function checkNumericy(field) {
   if(field.value=="." && field.value.length==1)
      field.value="0"+field.value;
   else
   {
      while (isNaN(field.value))
         field.value = field.value.substr(0, field.value.length - 1);
   }
}

/*
 * Returns true if all elements on the arg form are filled except
 * the ones in the array exceptionFields. The parameter fieldNames 
 * is an array that is filled with the names of the elements not 
 * completed. This function is overloaded such that the third 
 * parameter is not neccessary if you do not want the field names
 * of the fields not filled out. 
 *
 * This function uses two methods to attain the names to populate
 * the fieldNames array with:
 *
 *    1. The form elements title attribute.
 * 
 *    2. The fieldMap array.
 *
 * If you (do not)/(can not) set an elements title attribute you must
 * build the fieldMap associative array to obtain the proper name
 * in the fieldNames array. The fieldMap array is a mapping from 
 * an elements name attribute to the verbose description of 
 * the field that you would like the fieldNames array to be 
 * populated with. Use the buildFieldMap function to build the 
 * fieldMap.
 */
function isFilledExcept(form, exceptionFields, fieldNames) {
   if (typeof(fieldNames) == "object") {
      for (var i = 0; i < form.elements.length; i++) {
         if (!inArray(form.elements[i].name, exceptionFields)) {
            if (!isElementChecked(form.elements[i], form)) {
               if (form.elements[i].title && form.elements[i].title != "") {
                  fieldNames.push(form.elements[i].title);
               } else {
                  fieldNames.push(fieldMap[form.elements[i].name]);
               }
            }
         }
      }
      if (fieldNames.length > 0)
         return false;
      
      return true;
   } else {
      for (var i = 0; i < form.elements.length; i++) {
         if (!inArray(form.elements[i].name, exceptionFields)) {
            if (!isElementChecked(form.elements[i], form)) {
               return false;
            }
         }
      }
      return true;
   }
}

/*
 * Returns true if elementObj is checked or set in some way as 
 * defined by the the type of object it is. Note that this function
 * does not check hiddens, submits, or buttons.
 */
function isElementChecked(elementObj, form) {
   switch (elementObj.type) {
   case "checkbox" :
      if (elementObj.checked) {
         return true;
      }
      return false;
   case "text" :
      if (elementObj.value != "") {
         return true;
      }
      return false;
   case "textarea" :
      if (elementObj.value != "") {
         return true;
      }
      return false;
   case "radio" :
      var buttonArray = form.elements[elementObj.name];
      for (var j = 0; j < buttonArray.length; j++) {
         if (buttonArray[j].checked)
            return true;
      }
      return false;
   case "select-one" :
      if (elementObj.options[elementObj.selectedIndex].text != "") {
         return true;
      }
      return false;
   case "select-multiple":
      var theOptions = elementObj.options;
      for (var i = 0; i < theOptions.length; i++) {
         if (theOptions[i].selected && theOptions[i].text == "")
            return false;
      }
      return true;
   case "submit" :
   case "button" :
   case "hidden" :
      return true; // i dont care if a hidden or buttons are set
   default :
      alert("function: elementsChecked(elementObj, form) - unknown type: '" + elementObj.type + "' " + "object name: '" + elementObj.name + "'");
   }
}

/*
 * Returns true if the string is in the array.
 */
function inArray(string, array) {
   for (var i = 0; i < array.length; i++) {
      if (array[i] == string)
         return true;
   }
   return false;
}

function buildFieldMap(fieldNames, fieldDescriptions) {
   if (fieldNames.length != fieldDescriptions.length)
      alert("Could not build fieldMap array\nfieldNames.length != fieldDescriptions.length"); 
   
   for (var i = 0; i < fieldNames.length; i++) {
      fieldMap[fieldNames[i]] = fieldDescriptions[i];
   }  
}

//fieldNames is produced by the isFilledExcept function. (assuming it's called with a fieldNames parameter)
function errorHandler(condition){
  var output = "";
  if(!condition)
  {
    for (var i = 0; i < fieldNames.length; i++) 
      output = output + "\n" + fieldNames[i];
    alert("The following fields must be filled in: " + output);
  }
  else
  {
    return true;   
  }
  //Clearing Variables 
  output = "";
  fieldNames = new Array();
  return false;
}
function setRate(f,obj)
{
  var optionText=new String;
  optionText=obj.options[obj.selectedIndex].text;
  var startIndex=optionText.indexOf("(")+1;
  var endIndex=(optionText.length-startIndex)-1;
  if(startIndex!=-1)
    f.rate.value=optionText.substr(startIndex,endIndex);
}

/*
 * This function populates the daySelect object based on the month and
 * year objects. The month will be a select object also, but the year
 * may be a select or a text box.
 */
function populateSelect(monthSelect, daySelect, yearObject) {
   // 31 day months: January, March, May, July, August, October, December
   // 30 day months: April, June, September, November
   // [29|28] day month: February
  
   var months31 = new RegExp("01|03|05|07|08|10|12");
   var months30 = new RegExp("04|06|09|11");
   
   // i always set one more options than the number of days 
   // to account for the empty option
   if (months31.test(monthSelect.value)) {  
      if (daySelect.length != 32) {
         write2DigitNumToSelect(daySelect, 32);  
      }
     
   } else if (months30.test(monthSelect.value)) {
      if (daySelect.length != 31)
         write2DigitNumToSelect(daySelect, 31);  
     
   } else {
      if (yearObject.value != "" && isLeapYear(parseInt(yearObject.value))) {
         if (daySelect.length != 30)
            write2DigitNumToSelect(daySelect, 30);  
      } else if (daySelect.length != 29)
         write2DigitNumToSelect(daySelect, 29);
   }
}

/*
 * This is a helper function to the populateSelect that clears options 
 * out of the select object passed in and writes numOptionsSet two digit 
 * numbers back into it. This function ensures an empty string is 
 * written to the first position.
 */
function write2DigitNumToSelect(selectObj, numOptionsToSet) {
   // clear old values
   var numOptions = selectObj.length;
   for (var i = 0; i < numOptions; i++) {
      selectObj.options[0] = null; 
   }
   // set new ones
   for (var i = 0; i < numOptionsToSet; i++) {
      var element = document.createElement("OPTION");
      element.text = element.value = (i < 10) ? '0' + i : i;
      selectObj.options.add(element);
   }
   selectObj.options[0].text = "";   
}

/*
 * This algorithm was obtained from: http://www.mitre.org/research/y2k/docs/PROB.html#Leap
 * 
 * It follows the following logic:
 * 
 * 1. Years divisible by four are leap years, unless...
 *
 * 2. Years also divisible by 100 are not leap years, except...
 *
 * 3. Years divisible by 400 are leap years.
 */
function isLeapYear(year) {
   if ((year % 400) == 0) {
      return true;
   } else if ((year % 4) == 0 && (year % 100) != 0) {
      return true;
   }
   return false;
}

/*
 * Code from http://www.breakingpar.com/bkp/home.nsf/Doc?OpenNavigator&U=CA99375CC06FB52687256AFB0013E5E9
 */
function getSelectedRadio(buttonGroup) {
   // returns the array number of the selected radio button or -1 if no button is selected
   if (buttonGroup[0]) { // if the button group is an array (one button is not an array)
      for (var i=0; i<buttonGroup.length; i++) {
         if (buttonGroup[i].checked) {
            return i
         }
      }
   } else {
      if (buttonGroup.checked) { return 0; } // if the one button is checked, return zero
   }
   // if we get to this point, no radio button is selected
   return -1;
} 

/*
 * Code from http://www.breakingpar.com/bkp/home.nsf/Doc?OpenNavigator&U=CA99375CC06FB52687256AFB0013E5E9
 */ 
function getSelectedRadioValue(buttonGroup) {
   // returns the value of the selected radio button or "" if no button is selected
   var i = getSelectedRadio(buttonGroup);
   if (i == -1) {
      return "";
   } else {
      if (buttonGroup[i]) { // Make sure the button group is an array (not just one button)
         return buttonGroup[i].value;
      } else { // The button group is just the one button, and it is checked
         return buttonGroup.value;
      }
   }
}
/*this function determines if an email address appears to be in the correct format (i.e. has @ and . contained in the string)
  true or false is returned*/
function isEmailAddr(email)
{
  var result = false
  var theStr = new String(email)
  var index = theStr.indexOf("@");
  if (index > 0)
  {
    var pindex = theStr.indexOf(".",index);
    if ((pindex > index+1) && (theStr.length > pindex+1))
	   result = true;
  }
  return result;
}


