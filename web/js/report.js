/*get invoice data*/
function getReport(){
	var client_id = $('#form_client').val();
	var product_id = $('#form_product').val();
	var relative_date = $('#form_relative_date').val();

	if(!client_id || !relative_date){
		alert("Client and Relative Date are mandatory!");
		return;
	}

	$('#overlay').show();
	$.ajax({
   		url: report_url,
		data: { 'client_id': client_id,'product_id':product_id,'relative_date':relative_date },   
		dataType: 'json',
		success: function(report_data) {						
			$("#report_result").show();
			$("#report_result tr").slice(1).remove()
			for(report in report_data){
				var data = "<tr>";
					data += "<td>"+report_data[report]['invoice_num']+"</td>";
					data += "<td>"+report_data[report]['invoice_date']+"</td>";					
					data += "<td>"+report_data[report]['product_description']+"</td>";
					data += "<td>"+report_data[report]['qty']+"</td>";
					data += "<td>"+report_data[report]['price']+"</td>";
					data += "<td>"+report_data[report]['total']+"</td>";
					data += "</tr>";

				$("#report_result").append(data);
			}
			$('#overlay').hide();	
		}
	});
}

$(document).ready(function(){
	$('#form_client').val('');
	$('#form_product').val('');
	$('#form_relative_date').val('');
});

/*get product data on change of client*/
jQuery("#form_client").on("change",function(){
	var client_id = this.value;

	if(!client_id){ 
		$('#form_product').find('option').remove();	
		$('#form_product').append("<option value=''></option>");
		return; 
	}

	$('#overlay').show();
	$.ajax({
   		url: product_url,
		data: { 'client_id': client_id },   
		dataType: 'json',
		success: function(product_data) {			
			$('#form_product').find('option').remove();	
			$('#form_product').append("<option value=''></option>");
			for(product in product_data){
					$('#form_product').append("<option value='"+product+"'>"+product_data[product]+"</option>");
			}
			$('#overlay').hide();	
		}
	});
});