$(document).ready(function(){
	// Activate tooltip
	$('[data-toggle="tooltip"]').tooltip();
	
	// Select/Deselect checkboxes
	var checkbox = $('table tbody input[type="checkbox"]');
	$("#selectAll").click(function(){
		if(this.checked){
			checkbox.each(function(){
				this.checked = true;                        
			});
		} else{
			checkbox.each(function(){
				this.checked = false;                        
			});
		} 
	});
	checkbox.click(function(){
		if(!this.checked){
			$("#selectAll").prop("checked", false);
		}
	});
	$("#btnAgregarItem").click(function(event) {
	   return false;
    });
	$("#btnRemoverItem").click(function(event) {
	   return false;
    });
	$("#idBoton").click(function(event) {
       $("#idDivision").load('pagina.html div#cargar-soloesto');
    });

});


function agregarItem(IDdesde, IDhasta){
    var option = document.createElement("option");
    option.text = document.getElementById(IDdesde).value;
    document.getElementById(IDhasta).add(option);
    removerItem(IDdesde);
	selectTodos(IDhasta);
  }
 function removerItem(IDelemento){
	var comboBox = document.getElementById(IDelemento);
    comboBox = comboBox.options[comboBox.selectedIndex];
    comboBox.remove();
	selectTodos(IDelemento);
  }
  function selectTodos(IDelemento) {
    var elementos = document.getElementById(IDelemento);
    elementos = elementos.options;
    for (var i = 0; i < elementos.length; i++) {
        elementos[i].selected = "true";
    }
}