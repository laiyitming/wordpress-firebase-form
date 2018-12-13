var age = document.getElementById("age");
var phonenumber = document.getElementById("phonenumber");
var dbref = firebase.database().ref().child('wp_fb_users/'+userId);
dbref.on('value', function(snap){
	if(snap.val() != null){
		if(snap.val().age != undefined){
			age.value = snap.val().age;
		}
		if(snap.val().phonenumber != undefined){
			phonenumber.value = snap.val().phonenumber; 
		}
	}
});

var submitForm3 = function(){
	startloading();
	userId = firebase.auth().currentUser.uid;
	var age = parseInt(document.getElementById("age").value);
	var phonenumber = document.getElementById("phonenumber").value;
	if(validateform3()){
		var database = firebase.database();
		firebase.database().ref('/wp_fb_users/' + userId).once('value').then(function(snapshot) {
			if(snapshot.val()){
				firebase.database().ref('wp_fb_users/' + userId).update({
					age: age,
					phonenumber: phonenumber
				}, function(err){console.log(err);}).then(function(){								
					stoploading();
					alert("Form updated successfully.");									
				});
			}
		});		
	}
	stoploading();	
}

/**
 * This function get called to validate form field client side
 */
var validateform3 = function (){	
	var error = [];
	var age = document.getElementById("age").value;
	var phonenumber = document.getElementById("phonenumber").value;
	if(age.length == 0){
		error.push("Age is required");
	}
	/*else if(!onlynumeric(age)){
		error.push("Enter valid value for Age");
	}*/
	if(phonenumber.length == 0){
		error.push("phonenumber is required");
	}
	if(error.length > 0){
		alert( error.join("\n") );
		return false;
	}
	return true;	
}
