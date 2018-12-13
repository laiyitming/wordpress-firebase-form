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
			<button class="btn-danger" id="logout">Logout</button>
		</div>
	</div>
	<h1>Approve Contest Photos</h1>
	<?php if(isset($success) && strlen(trim($success)) > 0){?>
	<div>
		<div class="alert alert-success">
		  <strong>Success!</strong> <?php echo $success?>
		</div>
	</div>
	<?php }?>
	<?php if(isset($error) && count($error) > 0){?>
	<div>
		<div class="alert alert-danger">
		  <strong>Error!</strong> 
		  <?php foreach ($error as $msg){?>
		  	<p><?php echo $msg?></p>
		  <?php }?>
		</div>
	</div>
	<?php }?>
	<form method="POST" action="#" >
		<div class="metabox-holder">
			Change selected images status to 
			<select name="action" >
				<option value="">-</option>
				<option value="1">Approve</option>
				<option value="0">Reject</option>
			</select> <input type="submit" value="Change" />
			<table class="table table-hover">
				<thead>
					<tr>
						<th width="20%">
							<input type="checkbox" name="" id="checkall" value="" />
							ID
						</th>
						<th width="20%">
							Name
						</th>
						<th width="20%">
							Photo
						</th>
						<th width="30%">Description</th>
						<th width="10%">Status</th>								
					</tr>
				</thead>
				<tbody>
			    	<?php if(isset($all_images) && count($all_images) > 0){
			    			foreach($all_images as $uid => $data){
			    				foreach($data as $k => $image){?>
						    	<tr>
						    		<td><input class="recsel" type="checkbox" name="imgs[]" value="<?php echo $uid."$$$$".$k?>" /> <?php echo $k?> </td>
						    		<td><?php echo $all_users[$uid]->username ?></td>
						    		<td><img src="<?php echo $image->url?>" style="max-height:100px;max-width:100px;"/></td>					    		
						    		<td><?php echo $image->desc ?></td>
						    		<td><?php echo $image->status == 1 ? "Active":"Inactive" ?></td>
						    	</tr>
			    		<?php }
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
<script>
jQuery("#checkall").click(function(){
	if(this.checked){
		jQuery(".recsel").prop('checked', true);
	}else{
		jQuery(".recsel").prop('checked', false);
	}
});
</script>