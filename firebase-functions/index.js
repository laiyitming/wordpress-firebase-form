const functions = require('firebase-functions');
const request = require('request');
const admin = require('firebase-admin');
const fetch = require('node-fetch');

admin.initializeApp(functions.config().firebase);

exports.imageInsert = functions.database.ref('/wp_fb_images/{conId}')
.onWrite((snapshot, context) => {	
	// Exit when the data is deleted.
	if (!snapshot.after.exists() && !snapshot.before.exists()) {		
		return false;
	}
	if(context.auth == undefined){
		console.log("No user id set");
		return false;		
	}
	var conidforef = context.params.conId;
	var uid = context.auth.uid;
	var uemail = context.auth.token.email;
	
	return admin.database().ref("wp_fb_contests/" + conidforef).once('value').then(function(snapshot) {
		
		var contestdata = {};
		if(snapshot.val() != null){
			contestdata = snapshot.val();
		}		
		
		var coname = contestdata.name;		
		admin.database().ref("wp_fb_users/" + uid).once('value').then(function(snapshot) {
			var userdata = {};
			if(snapshot.val() != null){
				userdata = snapshot.val();
			}
			
			var username = userdata.name;					
			admin.database().ref("wp_fb_images/" + conidforef+"/" + context.auth.uid).once('value').then(function(snapshot) {
				var postdata = [];
				if(snapshot.val() != null){
					var imgarraydata = snapshot.val();
					for (var imgobj in imgarraydata){						
						var value = imgarraydata[imgobj];
						var imgd = {
								 "photo_id": imgobj,
							      "createdate": value.createdate,
							      "desc": value.desc,
							      "status": value.status,
							      "url": value.url
						}
						postdata.push(imgd);	
				    }							
					var body = {								
						"contest_name": coname,
						"contest_id": conidforef,
						"user_name": username,
						"user_email": uemail,
						"user_id" : context.auth.uid,
						"images": postdata 	
					};
				}else{
					var body = {								
							"contest_name": coname,
							"contest_id": conidforef,
							"user_name": username,
							"user_email": uemail,
							"user_id" : context.auth.uid,
							"images": {} 	
					};
				}
				
				var reqbody = {
					"index": "contest",
					"id": context.auth.uid+conidforef,
					"type": "users",
					"post_data": body
				}
				
				console.log("Request BODY");
				console.log(JSON.stringify(reqbody));
				
				request({
			        method: 'POST',
			        url: 'https://thjqglvtba.execute-api.ap-southeast-1.amazonaws.com/prod/postes/',
			        headers: {
						'x-api-key':'ct1Wg8Nl2eS2wnXNftBF2bAdsuLW8qr8JY5j170g',
			            'Content-Type': 'application/json'
			        },
			        json: true,
			        body: reqbody
		
			    }, function (error, response, body) {
			        console.log('Request Error: '+error);
			        console.log('Requeat response: '+JSON.stringify(response, null, '  '));			        
			        var wpuserimages = admin.database().ref("wp_fb_temp_images/" + conidforef+"/" + context.auth.uid);
					wpuserimages.remove().then(function() {
						console.log("Firebase function to update contest images executed successfully");
						return true;
					}).catch(function(error) {
					     console.log("-- User remove failed: " + error.message);
					     return false;
					});		
			    });				
			});	
		});	
	});	
});

exports.imageUpdate = functions.database.ref('/wp_fb_images/{conId}/{userId}')
.onWrite((snapshot, context) => {	
	// Exit when the data is deleted.
	if (!snapshot.after.exists() && !snapshot.before.exists()) {		
		return false;
	}
	if(context.auth != undefined){	
		console.log("User is logged in");
		return false;		
	}
	var conidforef = context.params.conId;
	var uid = context.params.userId;	
	
	return admin.database().ref("wp_fb_contests/" + conidforef).once('value').then(function(snapshot) {
		var contestdata = {};
		if(snapshot.val() != null){
			contestdata = snapshot.val();
		}
		var coname = contestdata.name;		
		admin.database().ref("wp_fb_users/" + uid).once('value').then(function(snapshot) {
			var userdata = {};
			if(snapshot.val() != null){
				
				userdata = snapshot.val();		
				var username = userdata.name;
				var uemail = userdata.email;
				admin.database().ref("wp_fb_images/" + conidforef+"/" + uid).once('value').then(function(snapshot) {
					var postdata = [];
					if(snapshot.val() != null){
						var imgarraydata = snapshot.val();
						for (var imgobj in imgarraydata){
							console.log("imgobj:");
							console.log(JSON.stringify(imgobj));
							var value = imgarraydata[imgobj];
							var imgd = {
									"photo_id": imgobj,
									"createdate": value.createdate,
									"desc": value.desc,
									"status": value.status,
									"places": value.places,
									"url": value.url
							}
							postdata.push(imgd);		
						}							
						var body = {								
							"contest_name": coname,
							"contest_id": conidforef,
							"user_name": username,
							"user_email": uemail,
							"user_id" : uid,
							"images": postdata 	
						};
					}else{
						var body = {								
								"contest_name": coname,
								"contest_id": conidforef,
								"user_name": username,
								"user_email": uemail,
								"user_id" : uid,
								"images": {} 	
							};
					}
					
					var reqbody = {
						"index": "contest",
						"id": uid+conidforef,
						"type": "users",
						"post_data": body
					}
					
					console.log("Request BODY");
					console.log(JSON.stringify(reqbody));
					
					request({
						method: 'POST',
						url: 'https://thjqglvtba.execute-api.ap-southeast-1.amazonaws.com/prod/postes/',
						headers: {
							'x-api-key':'ct1Wg8Nl2eS2wnXNftBF2bAdsuLW8qr8JY5j170g',
							'Content-Type': 'application/json'
						},
						json: true,
						body: reqbody
			
					}, function (error, response, body) {
						console.log('Data posted to error '+error);
						console.log('Data posted to response '+JSON.stringify(response, null, '  '));
						console.log('Data posted to body '+body);*/
						
						var wpuserimages = admin.database().ref("wp_fb_temp_images/" + conidforef+"/" + uid);
						console.log("++ wpuserimages: "+JSON.stringify(wpuserimages));
						wpuserimages.remove().then(function() {
							console.log("Firebase function to update contest images executed successfully");
							return true;
						}).catch(function(error) {
							 console.log("-- User remove failed: " + error.message);
							 return false;
						});		
				    });				
				});	
			}
		});	
	});	
});