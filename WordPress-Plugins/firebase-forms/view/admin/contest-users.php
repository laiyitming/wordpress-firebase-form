<div id="overlaydiv" class="overlay" style="display:none;">
	<div class="overlay-content">
		<div class="loader"></div>
	</div>	
</div>
<div class="wrap" style="padding:15px;">
	<div class="row" style="margin-bottom:10px;">
		<!-- Container for the demo -->
		<div class="col-md-11">
			Hi <?php echo $_SESSION["name"];?> 
		</div>
		<div class="col-md-1">
			<button class="btn btn-warning" id="logout">Logout</button>
		</div>
	</div>
	<h1><?php echo $_GET["cname"]?> Users and Images</h1>
	
	<?php if(isset($success) && strlen(trim($success)) > 0){?>
	<br />
	<div>
		<div class="alert alert-success">
		  <strong>Success!</strong> <?php echo $success?>
		</div>
	</div>
	<?php }?>
	<?php if(isset($error) && count($error) > 0){?>
	<br />
	<div>
		<div class="alert alert-danger">
		  <strong>Error!</strong> 
		  <?php foreach ($error as $msg){?>
		  	<p><?php echo $msg?></p>
		  <?php }?>
		</div>
	</div>
	<?php }?>
	<div class="metabox-holder">	
		<form method="POST" action="#" >
			<div class="metabox-holder">
				<div class="row">
					<div class="col-md-6">
						Change selected images status to 
						<select name="action" >
							<option value="">-</option>
							<option value="1">Approve</option>
							<option value="0">Reject</option>
						</select> <input type="submit" value="Change" />
					</div>		
					<div class="col-md-6 text-right">
						<a href="<?php echo add_query_arg(array("page"=>"firebase-contest-list"));?>">Go Back >></a>
					</div>
				</div>
				<table class="table table-hover" style="margin-top:10px;">
					<thead>
						<tr>
							<th width="10%">
								<input type="checkbox" name="" id="checkall" value="" />
								ID
							</th>
							<th width="15%">
								Name
							</th>
							<th width="20%">
								Address
							</th>
							<th width="20%">
								Image
							</th>
							<th width="20%">
								Descrpition
							</th>
							<th width="10%">
								Image Date
							</th>
							<th width="5%">
								Status
							</th>					
						</tr>
					</thead>
					<tbody>					
				    	<?php if(isset($all_users) && count($all_users) > 0){
				    			foreach($all_users as $uid => $imagedata){
				    				$udata = $firebase->get("/wp_fb_temp_users/".$uid);
				    				$udata = json_decode($udata);
				    				foreach($imagedata as $k => $image){?>
							    	<tr>
							    		<td>
							    			<input class="recsel" id="chkbx<?php echo $k?>" type="checkbox" name="imgs[]" value="<?php echo $uid."$$$$".$k?>" />
											<label for="chkbx<?php echo $k?>"><?php echo $k;?></label> 
										</td>
										<td>
											<label for="chkbx<?php echo $k?>"><?php echo $udata->name;?></label> 
										</td>
										<td>
											<label for="chkbx<?php echo $k?>"><?php echo $udata->address;?></label> 
										</td>
										<td>
											<label for="chkbx<?php echo $k?>"><img class="myImg" src="<?php echo $image->url?>" style="max-height:100px;max-width:100px;"/></label> 
										</td>
										<td>
											<label for="chkbx<?php echo $k?>"><?php echo $image->desc;?></label> 
										</td>
										<td>
											<label for="chkbx<?php echo $k?>"><?php echo $image->createdate;?></label> 
										</td>
										<td>
											<label for="chkbx<?php echo $k?>"><?php echo $image->status == 1 ? "Active":"Inactive" ?></label> 
										</td>															
							    	</tr>
				    		<?php 	}	
				    			}	    	
				    		 }else{?>	    		
				    			<tr>
						    		<td colspan="7">No record found</td>			    		
						    	</tr>	    		
				    	<?php }?>
					</tbody>
				</table>		
			</div>
		</form>	
	</div>
</div>
<!-- The Modal -->
<div id="myModal" class="modal">
  <span class="close">&times;</span>
  <img class="modal-content" id="img01">
  <div id="caption"></div>
</div>
<script>
jQuery("#checkall").click(function(){
	if(this.checked){
		jQuery(".recsel").prop('checked', true);
	}else{
		jQuery(".recsel").prop('checked', false);
	}
});
//Get the modal
var modal = document.getElementById('myModal');

// Get the image and insert it inside the modal - use its "alt" text as a caption
var img = document.getElementById('myImg');
var modalImg = document.getElementById("img01");
var captionText = document.getElementById("caption");
jQuery(".myImg").on("click", function(){
	 modal.style.display = "block";
	 modalImg.src = this.src;
	 captionText.innerHTML = this.alt;
});

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() { 
    modal.style.display = "none";
}
modal.onclick = function() { 
    modal.style.display = "none";
}
</script>