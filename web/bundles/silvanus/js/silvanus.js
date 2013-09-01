
function confirm_redirect(path,message){

		bootbox.confirm(message, function(result) {
			
			if(result){
	
				window.location.href=path;
			
			}
		
		}); 		

}
	
