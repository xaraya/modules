var kupu = null;
var kupuui = null;
function startKupu() {
	var frame = document.getElementById('kupu-editor'); 
	var kupu = initKupu(frame); 
	var kupuui = kupu.getTool('ui'); 
	kupu.initialize();
}