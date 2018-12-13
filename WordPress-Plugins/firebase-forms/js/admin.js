var file;
var userId;
var token;
var user;
/**
 * On Window load we first show spinner one page data lod we init firbase object and fetch uploaded data 
 */
window.onload = function() {
	initApp();	
	messaging.onMessage(function(payload) {
		if (Notification.permission !== "granted"){
		    Notification.requestPermission();
		} else {
		  var notification = new Notification(payload.notification.title, {
		      body: payload.notification.body
		  });
		}
	});
};

/**
 * initApp handles setting up UI event listeners and registering Firebase auth
 * listeners: - firebase.auth().onAuthStateChanged: This listener is called when
 * the user is signed in or out, and that is where we update the UI.
 */
var initApp = function () {
	// Listening for auth state changes.
	firebase.auth().onAuthStateChanged(function(userdata) {
		if (userdata) {
			//jQuery(document.body).append('<script type="text/javascript" src="'+pluginUrl+'/firebase-forms/js/form-1.js?v2" />');
			user = userdata;
			startloading();
			userId = firebase.auth().currentUser.uid;	
			
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

///////////////////////////////////////COMMON FUNCTIONS/////////////////////////////////////////////////////////////////////

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
