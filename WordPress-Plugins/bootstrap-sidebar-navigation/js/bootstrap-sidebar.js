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
var lastsel = "";
function boot_loadPage(pg, header, step = 0){	
	if(step == undefined || step == 0){		
		oldstep = 1;
	}
	startloading();
	history.replaceState("", "", "?pg="+pg+"&head="+header);
	jQuery( "#fbformcontainer" ).load( "/wp-content/plugins/firebase-forms/view/form-"+pg+".php", function() {
		jQuery(document.body)
		  .append('<script type="text/javascript" src="'+pluginUrl+'/firebase-forms/js/form-'+pg+'.js?v'+new Date().getTime()+'" />');
		jQuery("#nav_lnk"+pg+""+header).css("font-weight", "bold");
		jQuery("#nav_lnk"+lastsel).css("font-weight", "normal");
		lastsel = pg+""+header;
		if(header != 1){
			jQuery( "#firebase_header" ).load( "/wp-content/plugins/firebase-forms/view/headerlinks-"+header+".php", function(){
				if(step != 0){
					history.replaceState("", "", "?pg="+pg+"&head="+header+"&step="+step);
					select_step(step);
				}
			});
		}
	});
	stoploading();	
}
stoploading();