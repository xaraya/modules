function popBillingAddr() { 
	var checkbox = document.getElementById("popBilling");
	var first_name = document.getElementById('first_name');
	var first_name2 = document.getElementById('first_name2').innerHTML;
	var last_name = document.getElementById('last_name');
	var last_name2 = document.getElementById('last_name2').innerHTML;
	var street_addr = document.getElementById('street_addr');
	var street_addr2 = document.getElementById('street_addr2').innerHTML;
	var city_addr = document.getElementById('city_addr');
	var city_addr2 = document.getElementById('city_addr2').innerHTML;
	var state_addr = document.getElementById('state_addr');
	var state_addr2 = document.getElementById('state_addr2').innerHTML;
	var postal_code = document.getElementById('postal_code');
	var postal_code2 = document.getElementById('postal_code2').innerHTML;

if(checkbox.checked) {
  postal_code.value = postal_code2; 
  first_name.value = first_name2;
  last_name.value = last_name2;
  street_addr.value = street_addr2;
  city_addr.value = city_addr2;
  state_addr.value = state_addr2;
  }
  else{ 
  postal_code.value = '';
  first_name.value = '';
  last_name.value = '';
  street_addr.value = '';
  city_addr.value = '';
  state_addr.value = '';
} 

} 