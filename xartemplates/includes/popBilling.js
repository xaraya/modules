function popBillingAddr() { 
	var checkbox = document.getElementById("popBilling");
	var first_name = document.getElementById('first_name');
	var last_name = document.getElementById('last_name');
	var street_addr = document.getElementById('street_addr');
	var city_addr = document.getElementById('city_addr');
	var state_addr = document.getElementById('state_addr');
	var postal_code = document.getElementById('postal_code');

	var hasInnerText = (document.getElementsByTagName("body")[0].innerText != undefined) ? true : false;

	// IE usees innerText & FF uses textContent
	if(!hasInnerText){
		var first_name2 = document.getElementById('first_name2').textContent;
		var last_name2 = document.getElementById('last_name2').textContent;
		var street_addr2 = document.getElementById('street_addr2').textContent;
		var city_addr2 = document.getElementById('city_addr2').textContent;
		var state_addr2 = document.getElementById('state_addr2').textContent;
		var postal_code2 = document.getElementById('postal_code2').textContent;
	} else{
		var first_name2 = document.getElementById('first_name2').innerText;
		var last_name2 = document.getElementById('last_name2').innerText;
		var street_addr2 = document.getElementById('street_addr2').innerText;
		var city_addr2 = document.getElementById('city_addr2').innerText;
		var state_addr2 = document.getElementById('state_addr2').innerText;
		var postal_code2 = document.getElementById('postal_code2').innerText;
	}

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
	  state_addr.value = '-Select-';
	} 

} 