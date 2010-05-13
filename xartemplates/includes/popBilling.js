function popBillingAddr() { 

	var hasInnerText = (document.getElementsByTagName("body")[0].innerText != undefined) ? true : false;

	var line2 = false;
	if (document.getElementById('street_line_2_ship') != null)
	{
		var line2 = true;
	}

	// IE usees innerText & FF uses textContent
	if(!hasInnerText){
		var name_first_ship = document.getElementById('name_first_ship').textContent;
		var name_last_ship = document.getElementById('name_last_ship').textContent;
		var street_line_1_ship = document.getElementById('street_line_1_ship').textContent;
		if(line2){
			var street_line_2_ship = document.getElementById('street_line_2_ship').textContent;
		}
		var city_addr_ship = document.getElementById('city_addr_ship').textContent;
		var state_addr_ship = document.getElementById('state_addr_ship').textContent;
		var postal_code_ship = document.getElementById('postal_code_ship').textContent;
	} else{
		var name_first_ship = document.getElementById('name_first_ship').innerText;
		var name_last_ship = document.getElementById('name_last_ship').innerText;
		var street_line_1_ship = document.getElementById('street_line_1_ship').innerText;
		if(line2){
			var street_line_2_ship = document.getElementById('street_line_2_ship').innerText;
		}
		var city_addr_ship = document.getElementById('city_addr_ship').innerText;
		var state_addr_ship = document.getElementById('state_addr_ship').innerText;
		var postal_code_ship = document.getElementById('postal_code_ship').innerText;
	}

	var checkbox = document.getElementById("popBilling");
	var name_first = document.getElementById('name_first');
	var name_last = document.getElementById('name_last');
	var street_line_1 = document.getElementById('street_line_1');
	if(line2){
		var street_line_2 = document.getElementById('street_line_2');
	}
	var city_addr = document.getElementById('city_addr');
	var state_addr = document.getElementById('state_addr');
	var postal_code = document.getElementById('postal_code');

	if(checkbox.checked) {
	  name_first.value = name_first_ship;
	  name_last.value = name_last_ship;
	  city_addr.value = city_addr_ship;
	  state_addr.value = state_addr_ship;
	  street_line_1.value = street_line_1_ship;
	  if(line2){
		street_line_2.value = street_line_2_ship;
	  }
	  postal_code.value = postal_code_ship;
	  }
	  else{ 
	  name_first.value = '';
	  name_last.value = '';
	  street_line_1.value = '';
	  if(line2){
		  street_line_2.value = '';
	  }
	  city_addr.value = '';
	  state_addr.value = '-Select-';
	  postal_code.value = '';
	} 

} 