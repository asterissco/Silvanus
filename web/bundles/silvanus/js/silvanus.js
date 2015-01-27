
function confirm_redirect(path,message){

		bootbox.confirm(message, function(result) {
			
			if(result){
	
				window.location.href=path;
			
			}
		
		}); 		

}
	
function confirm_submit(form,message){


		bootbox.confirm(message, function(result) {
			
			if(result){
	
				form.submit();
			
			}
		
		}); 		
	

}
