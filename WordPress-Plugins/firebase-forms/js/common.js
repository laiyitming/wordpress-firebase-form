var file;
var userId;
var uemail;
var token;
var user;
var oldstep = 1;

/**
 * On Window load we first show spinner one page data lod we init firbase object and fetch uploaded data 
 */
window.onload = function() {
	
	//Alert user if third party cookies are disabled 
	document.cookie = "ThirdPartyCookie=yes;";
	if (document.cookie.indexOf("ThirdPartyCookie=") <= -1) {		
		alert("3rd party cookies are not enabled, please enable them to access page");		
	}
	initApp();
	
	//Switch pages if page parameters passed in URL
	var url_string = window.location.href;
	var url = new URL(url_string);
	var pgvar = url.searchParams.get("pg");
	var headvar = url.searchParams.get("head");
	var stepvar = url.searchParams.get("step");
	if(stepvar == undefined){
		stepvar = 0;
		oldstep = 1;
	}
	if(pgvar != undefined && pgvar.length > 0 && headvar != undefined && headvar.length > 0){
		jQuery( "#contestlnk" ).trigger( "click" );						
		//Refer to js file of bootstrap sidebar navigation plugin javascript file for function boot_loadPage()
		boot_loadPage( pgvar, headvar, stepvar);		
	}else{
		boot_loadPage( 1, 1, stepvar);
	}
};

var previewForm = function(frm){
	var htm = "";
	jQuery("#formcon"+frm+" input,#formcon"+frm+" textarea, #formcon"+frm+" img").each(function() {
	    if(this.src != undefined && this.src.length > 0){
	    	htm += '<img src="'+this.src+'" width="250px" /><br />';
	    }else if(this.type != 'button' && jQuery(this).data("label") != undefined){
	    	htm += '<label>'+jQuery(this).data("label")+'</label>: '+jQuery(this).val()+'<br />';
	    }else if(this.type != 'file' && this.type != 'button'){
	    	htm += jQuery(this).val()+'<br />';
	    }else if(this.type == 'file' && this.files[0] != undefined){
    	    htm += '<img src="'+ window.URL.createObjectURL(this.files[0])+'" width="250px" /><br />';    	   	    	
	    }
	});
	jQuery("#formvalues").html(htm);
	jQuery('#form1modal').modal('show');
	jQuery("#modalsubmit").attr("onclick", "jQuery('#form1modal').modal('hide');submitForm"+frm+"()");
}


/**
 * initApp handles setting up UI event listeners and registering Firebase auth
 * listeners: - firebase.auth().onAuthStateChanged: This listener is called when
 * the user is signed in or out, and that is where we update the UI.
 */
var initApp = function () {
	// Listening for auth state changes.
	firebase.auth().onAuthStateChanged(function(userdata) {
		if (userdata) {
			user = userdata;
			startloading();
			userId = firebase.auth().currentUser.uid;	
			
			messaging.requestPermission()
			.then(function() {
			  console.log('Notification permission granted.');
			  messaging.getToken()
			  .then(function(currentToken) {
			    if (currentToken) {
			    	token = currentToken;
			    	var currentdate = new Date(); 
			    	var datetime = currentdate.getFullYear()+"-"+(currentdate.getMonth()+1)+"-"+currentdate.getDate() + " "
	                + currentdate.getHours() + ":"  
	                + currentdate.getMinutes() + ":" 
	                + currentdate.getSeconds();
					var tockendata = new Object();
					tockendata.token = token;
					tockendata.createdate = datetime;
					var userTknref = firebase.database().ref().child('wp_fb_tokens/' + userId);
					userTknref.once('value', function(snapshot) {
						var tokensString = JSON.stringify(snapshot);
						  if (tokensString.indexOf(currentToken) < 0 || tokensString == 'null' ) {
							  firebase.database().ref('wp_fb_tokens/' + userId).push(tockendata).then(function(data){});
						  }										
					});
			    }
			  });
			})
			.catch(function(err) {
			  console.log('Unable to get permission to notify.', err);
			});
			
			stoploading();
			// User is signed in.
			var displayName = user.displayName;
			var email = user.email;
			var emailVerified = user.emailVerified;
			var photoURL = user.photoURL;
			var isAnonymous = user.isAnonymous;
			var uid = user.uid;
			var formData = {
					'action': 'loginuser','uid' : uid,'name' : displayName,'email' : email,'photo' : photoURL,'anonymous' : isAnonymous
			};
			jQuery.ajax({
				type: 'POST', url: myAjax.ajaxurl,data: formData, dataType: 'json',encode: true})
				.done(function(data) {			
					if(data.success == 1){
						window.location.reload();
					}
				});	
			var providerData = user.providerData;
			
		} else {
			// User is signed out.
			var formData = {
					'action': 'loginuser','logout' : 1};
			jQuery.ajax({
				type : 'POST', url: myAjax.ajaxurl,data : formData,dataType: 'json',encode : true})
				.done(function(data) {  			
					if(data.success == 1){
						window.location.reload();
					}
				});	
			stoploading();
		}
	});
	
	// FirebaseUI config.
	var uiConfig = {
        callbacks: {
          signInSuccess: function(currentUser, credential, redirectUrl) {
            stoploading();
            return false;
          },
          uiShown: function() {
            startloading();
          }
        },
        // Will use popup for IDP Providers sign-in flow instead of the default, redirect.
        signInFlow: 'popup',
       // signInSuccessUrl: '/test-firebase-form',
        signInOptions: [
			// Leave the lines as is for the providers you want to offer your users.
			firebase.auth.GoogleAuthProvider.PROVIDER_ID,
			firebase.auth.FacebookAuthProvider.PROVIDER_ID,
			firebase.auth.EmailAuthProvider.PROVIDER_ID,
			firebase.auth.PhoneAuthProvider.PROVIDER_ID
        ],
        // Terms of service url.
        tosUrl: 'firebase/tands'
    };

    // Initialize the FirebaseUI Widget using Firebase.
    var ui = new firebaseui.auth.AuthUI(firebase.auth());
    // The start method will wait until the DOM is loaded.
    if(jQuery("#firebaseui-auth-container").html() != undefined){
    	ui.start('#firebaseui-auth-container', uiConfig);    
    }
}

/**
 * Logout logic
 */
jQuery(document).on('click', '#logout', function(){
	var userTokenref = firebase.database().ref().child('wp_fb_tokens/' + userId);
	userTokenref.on('value', function(tokndata){
		tokndata.forEach(function(element) {
			if(element.val().token == token){
				userTokenref.child(element.key).remove().then(function(){					
					firebase.auth().signOut().then(function() {
						// User is signed out.
						var formData = {
								'action': 'loginuser','logout' : 1};
						jQuery.ajax({
							type : 'POST', url: myAjax.ajaxurl,data : formData,dataType: 'json',encode : true})
							.done(function(data) {  			
								window.location.reload();				
							});	
					}, function(error) {
					  console.error('Sign Out Error', error);
					});					
				});
			}
		});
	});		
});


///////////////////////////////////////COMMON FUNCTIONS/////////////////////////////////////////////////////////////////////

/**
 * This function return file object of selected file to upload
 */
var getFileName = function (fileInput) {
	file = fileInput.files[0];
	fname = file.name;
	filename = fname.substring(0, fname.lastIndexOf('.'));
	document.getElementById("pdesc").value = filename;
};

/**
 * This function get called to show loading spinner
 */
var startloading = function (){
	if(document.getElementById("overlaydiv") != undefined){
		jQuery("#overlaydiv").fadeIn();
	}
}

/**
 * This function get called to stop loading spinner
 */
var stoploading = function (){
	if(document.getElementById("overlaydiv") != undefined){
		jQuery("#overlaydiv").fadeOut();
	}
}

/**
 * This function return file name with firebase root
 */
var getName = function (url) {
	url = decodeURI(url);
	url = url.substring(0, (url.indexOf("#") == -1) ? url.length : url.indexOf("#"));
	url = url.substring(0, (url.indexOf("?") == -1) ? url.length : url.indexOf("?"));
	url = url.substring(url.lastIndexOf("/") + 1, url.length);
	url = url.replace(/%2F/gi, "/");
	return url;
}

/**
 * This function returns image file extension by file mime type
 */
var getImageExtension = function(mimetype){
	switch(mimetype) {
	case "image/png": 
		return ".png";
		break;
	case "image/gif":
		return ".gif";
		break;
	case "image/bmp":
		return ".bmp";
		break;
	case "image/jpg":
		return ".jpg";
		break;
	case "image/jpeg":
		return ".jpeg";
		break;
	default:
		return false;
	}
}
/**
 * This function return unique number which we are using to store file name
 */
var getUniqueNumber = function(){
	var id = Date.now() + Math.random();
	return parseInt(id);
}
var select_step = function(stepno){
	console.log("SelectStep: "+stepno+ " => "+oldstep);
	jQuery("#step-"+oldstep).removeClass("btn-primary");
	jQuery("#step-"+oldstep).addClass("btn-passed");
	jQuery("#step-"+stepno).removeAttr("disabled");
	jQuery("#step-"+stepno).addClass("btn-primary");
	oldstep = stepno;
}

var showError = function(eleid, msg){
	jQuery( "#"+eleid+"error" ).html(msg);
	jQuery( "#"+eleid+"error" ).css("display", "block");
	jQuery( "#"+eleid ).focus();
	
	jQuery('html, body').animate({
        scrollTop: (jQuery("#"+eleid).offset().top - 200)
    }, 2000);
}

//Data Validation Functions
//Function will return true if value include only alphabets and numeric characters 
function alphanumeric(inputtxt){var regexpat = /^[0-9a-zA-Z]+$/;if((inputtxt.match(regexpat))){return true;}else{return false;}}
//Function will return true if value include only numeric characters
function onlynumeric(inputtxt){var regexpat = /^[0-9]+$/;if((inputtxt.match(regexpat))){return true;}else{return false;}}
//Function will return true if value include only alphabets characters
function onlyalphabets(inputtxt){var regexpat = /^[A-Za-z]+$/;if((inputtxt.match(regexpat))){return true;}else{return false;}}
//Function will return true if value contain decimal point 
function checkdecimal(inputtxt){var regexpat = /^[-+]?[0-9]+\.[0-9]+$/;if((inputtxt.match(regexpat))){return true;}else{return false;}}
//Function will return true if value is valid email address
function checkemaill(inputtxt){if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(inputtxt)){return (true)} return (false);}
