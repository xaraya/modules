$(document).ready(function() {  
	$("#isalias").change(function(event){ 
     var vis = (this.checked) ? 'block' : 'none';
	 document.getElementById('suppress_view_alias').style.display = vis;
   }); 
});