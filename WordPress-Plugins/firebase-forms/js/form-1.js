firebase.auth().onAuthStateChanged(function(user) {
	if (user) {
		userId = firebase.auth().currentUser.uid;
		uemail = firebase.auth().currentUser.email;
		//uemail = context.auth.token.email;
		var name = document.getElementById("name");
		var address = document.getElementById("address");
		var dbref = firebase.database().ref().child('wp_fb_users/'+userId);
		dbref.on('value', function(snap){
			if(snap.val() != null){
				name.value = snap.val().name;
				address.value = snap.val().address;			
			}
		});	
	}
});

/**
 * This function get called on submit form 
 */
var submitForm1 = function (){
	startloading();
	userId = firebase.auth().currentUser.uid;
	uemail = firebase.auth().currentUser.email;
	var name = document.getElementById("name").value;
	var address = document.getElementById("address").value;	
	var currentdate = new Date();
	var datetime = currentdate.getFullYear()+"-"+(currentdate.getMonth()+1)+"-"+currentdate.getDate() + " "
    + currentdate.getHours() + ":"  
    + currentdate.getMinutes() + ":" 
    + currentdate.getSeconds();

	if(validateform()){			
		var database = firebase.database();				
		firebase.database().ref('wp_fb_users/' + userId).update({
			name: name,
			address: address,
			email: uemail,
			createdate: datetime
		}).then(function(){	
			firebase.database().ref('wp_fb_temp_users/' + userId).update({
				name: name,
				address: address,
				email: uemail
			}).then(function(){	
				stoploading();					
				alert("Form updated successfully.");			
			});							    	    						    		
		});		
	}else{		
		stoploading();
	}
}

/**
 * This function get called to validate form field client side
 */
var validateform = function (){
	var error = [];
	var name = document.getElementById("name").value;
	var address = document.getElementById("address").value;
	
	if(name.length == 0){
		error.push(1);  
		showError("name", "Name is required");
	}else{
		jQuery( "#nameerror" ).css("display", "none");
	}
	if(address.length == 0){
		error.push(1);  
		showError("address", "Address is required");
	}else{
		jQuery( "#addresserror" ).css("display", "none");
	}
	if(error.length > 0){
		return false;
	}
	return true;	
}


