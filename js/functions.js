 $(function () {
	$('form').on('submit', function (e) {

	  var btn = $(this).find("button[type=submit]:focus" )[0].name;
	  if(btn != "selected"){
		$('#crypto').find('option').remove();
	  }
	  e.preventDefault();	
	  $.ajax({
		type: 'post',
		url: 'php/functions.php',
		data: {base : $('#market').find(":selected").text(),
		       btn: btn,
			   selected: $('#crypto').find(":selected").text()},
		success: function (data) {
		 if(btn == "load"){
			$('#crypto').find('option').remove(); 
			$.each(JSON.parse(data), function(i, value) {
				$('#crypto').append($('<option>').text(value).attr('value', value));
			});
		 }else{
			$('#result').val(data); 
		 }			  
		}
	  });
	});
  });