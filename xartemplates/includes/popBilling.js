function popBillingAddr() { 

	var hasInnerText = (document.getElementsByTagName("body")[0].innerText != undefined) ? true : false;

	var isnull=false;
	var lines=0;
	var street_line_ship = new Array();

	// IE usees innerText & FF uses textContent
	if(!hasInnerText){
		var name_first_ship = document.getElementById('name_first_ship').textContent;
		var name_last_ship = document.getElementById('name_last_ship').textContent;

		for (n=1;isnull!=true;n++){
			lines++;
			street_line_ship[n] = document.getElementById('street_line_'+n+'_ship').textContent;
			var next=n+1;
			if (document.getElementById('street_line_'+next+'_ship') == null){
				var isnull = true;
			}
		}

		var city_addr_ship = document.getElementById('city_addr_ship').textContent;
		var state_addr_ship = document.getElementById('state_addr_ship').textContent;
		var postal_code_ship = document.getElementById('postal_code_ship').textContent;
	}else{
		var name_first_ship = document.getElementById('name_first_ship').innerText;
		var name_last_ship = document.getElementById('name_last_ship').innerText;
		var street_line_1_ship = document.getElementById('street_line_1_ship').innerText;

		for (n=1;isnull!=true;n++){
			lines++;
			street_line_ship[n] = document.getElementById('street_line_'+n+'_ship').innerText;
			var next=n+1;
			if (document.getElementById('street_line_'+next+'_ship') == null){
				var isnull = true;
			}
		}

		var city_addr_ship = document.getElementById('city_addr_ship').innerText;
		var state_addr_ship = document.getElementById('state_addr_ship').innerText;
		var postal_code_ship = document.getElementById('postal_code_ship').innerText;
	}

	var checkbox = document.getElementById("popBilling");
	var name_first = document.getElementById('name_first');
	var name_last = document.getElementById('name_last');
	var city_addr = document.getElementById('city_addr');
	var state_addr = document.getElementById('state_addr');
	var postal_code = document.getElementById('postal_code');

	if(checkbox.checked) {
	  name_first.value = name_first_ship;
	  name_last.value = name_last_ship;
	  for (n=1; n<=lines; n++){
		document.getElementById('street_line_'+n).value = street_line_ship[n];
		}
	  city_addr.value = city_addr_ship;
	  state_addr.value = state_addr_ship;
	  postal_code.value = postal_code_ship;
	}else{ 
	  name_first.value = '';
	  name_last.value = '';
	  for (n=1;n<=lines;n++){
		document.getElementById('street_line_'+n).value = '';
		}
	  city_addr.value = '';
	  state_addr.value = 0;
	  postal_code.value = '';
	} 
} 