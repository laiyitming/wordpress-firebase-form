var mconid = "";
var contestRef = firebase.database().ref()
.child('wp_fb_contests')
.orderByChild('name')
.equalTo('Christmas Contest')
.once('value').then(snapshot => {
	var condata = snapshot.val();
	for (var key in condata) {
		mconid = key;
	}
	if(mconid.length == 0 || mconid == ""){
		alert("There is no contest running for now");
		window.location.href = "/";
	}
	userId = firebase.auth().currentUser.uid;	
	console.log(userId);
	var imgdbref = firebase.database().ref().child('wp_fb_images/' +mconid+ '/' +userId);
	imgdbref.on('value', function(imgsnap){
		if(imgsnap.val() != null){
			jQuery("#uploadedfile").html("");
			var imghtml = "";
			var imgarraydata = imgsnap.val();
			for(var rkey in imgarraydata){
			    var record = imgarraydata[rkey]
				imghtml += "<div id='"+rkey+"' style='margin-bottom:10px;'>" +
								"<img src='"+record.url+"' id='contestphoto' style='width:20%;height:50%;' />&nbsp;&nbsp;" +											
								"<textarea class='form-control' style='width:30%;display:inline;'  id='desc"+rkey+"'>"+record.desc+"</textarea>" +
								"<div style='margin-top:10px;' >" +
										"<span onclick='updateIt(this)' data-key='"+rkey+"' class='btn btn-primary updateimg'>Update it</span>&nbsp;&nbsp;&nbsp;" +
										"<span onclick='deleteIt(this)' data-key='"+rkey+"' data-uri='"+record.url+"' class='btn btn-danger'>Delete it</span>" +
								"</div><br />" +
								"<hr>" +
							"</div>";
	
			}				
			jQuery("#uploadedfile").html(imghtml);					
		}
	});	
});

/**
 * This function get called on submit form 
 */
var submitForm2 = function (fl=true){
	startloading();
	userId = firebase.auth().currentUser.uid;
	if(file != undefined){
		updateIt(this, true);
		// upload file to firebase storage
		var storageRef = firebase.storage().ref();
		var metadata = {
				contentType: file.type
		};
		var upid = getUniqueNumber();		
		var filename = 'wp_fb_images/' +mconid+ '/' + userId+'/photo_' + upid+getImageExtension(file.type);
		var uploadTask = storageRef.child(filename).put(file, metadata);
		var currentdate = new Date();
		var datetime = currentdate.getFullYear()+"-"+(currentdate.getMonth()+1)+"-"+currentdate.getDate() + " "
	    + currentdate.getHours() + ":"  
	    + currentdate.getMinutes() + ":" 
	    + currentdate.getSeconds();
		// Listen for state changes, errors, and completion of the upload.
		uploadTask.on(firebase.storage.TaskEvent.STATE_CHANGED, // or
		// 'state_changed'
		function(snapshot) {				
		}, function(error) {				
			alert("Got Error!!!, error code : "+error.code);
		}, function() {	
			// Upload completed successfully, now we can get the download
			var downloadURL = uploadTask.snapshot.downloadURL;
			var imgdesc = document.getElementById("caption").value;
			var imgdata = {desc:imgdesc, url:downloadURL, createdate: datetime, status: 0};
			var database = firebase.database();			
			firebase.database().ref('wp_fb_images/' +mconid+ '/' + userId).push(imgdata).then(function(imgInfo){				
				stoploading();
				//alert("Form updated successfully.");
				firebase.database().ref('wp_fb_temp_images/' +mconid+ '/' + userId).push(imgdata).then(function(imgInfo){
					jQuery("#contestphoto").val('');
					jQuery("#caption").val('');
					jQuery("#preview").attr('src', '');
					file = null;						
					jQuery('html, body').animate({
				        scrollTop: (jQuery("#submitbtn").offset().top - 300)
				    }, 2000);
				});					
			});					
		});
	}else{
		showError("contestphoto", "Photo is required");
		stoploading();
	}	
}

/**
 * This function get called when we update any uploaded image
 */
var updateIt = function (imgObj, multiple = false){
	startloading();
	var updates = {};
	userId = firebase.auth().currentUser.uid;	
	if(!multiple){
		var key = jQuery(imgObj).data("key");
		var newval = jQuery("#desc"+key).val();
		updates['/wp_fb_images/' +mconid+ '/' +userId+ '/' + key+'/desc'] = newval;
	}else{
		jQuery(".updateimg").each(function() {
			var key = jQuery(this).data("key");
			var newval = jQuery("#desc"+key).val();
			updates['/wp_fb_images/' +mconid+ '/' + userId + '/' + key+'/desc'] = newval;
		});
	}
	firebase.database().ref().update(updates).then(function(){	
		if(!multiple){
			alert("Photo description updated successfully.");
		}
		stoploading();
	}).catch(function(error) {
		stoploading();
		alert("There is something wrong on server, please try after sometime.");		
	});
}

/**
 * This function get called when we delete any uploaded image
 */
var deleteIt = function (imgObj){
	var storageRef = firebase.storage().ref();
	var uri = jQuery(imgObj).data("uri");
	var key = jQuery(imgObj).data("key");
	var filename = getName(uri);
	var desertRef = storageRef.child(filename);
	userId = firebase.auth().currentUser.uid;
	startloading();
	desertRef.delete().then(function() {
		var imgdbref = firebase.database().ref().child('wp_fb_images/' +mconid+ '/' + userId);
		imgdbref.child(key).remove().then(function(){
			jQuery("#"+key).remove();
			stoploading();
			alert("Photo deleted successfully.");
		});
	}).catch(function(error) {
		stoploading();
		alert("There is something wrong on server, please try after sometime.");		
	});
}

var showPreviewImage = function(event){
    var output = document.getElementById('preview');
    output.src = URL.createObjectURL(event.target.files[0]);
}
