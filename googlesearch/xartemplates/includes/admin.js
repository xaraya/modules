function checkAll(me, checkornot)
{
  var i;

  for(i=0; i<me.form.elements.length; i++) {
    if (me.form.elements[i].type == 'checkbox') {
      me.form.elements[i].checked = checkornot;
    }// if
  }// for

  if (checkornot) {
    me.value = 'De-Select All';
  } else {
    me.value = 'Select All';
  }// if
}