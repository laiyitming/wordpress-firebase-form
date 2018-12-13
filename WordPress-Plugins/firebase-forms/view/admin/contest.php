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
	<h1>Manage Contests</h1>
	<?php if(isset($success) && strlen(trim($success)) > 0){
	if(!isset($_GET["cid"])){unset($_POST);}?>
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
	<br />
	<form action="#" method="POST" >
		<div style="padding:0px 10px 15px 10px; border: 1px solid #c9c9c9;">
			<h4><?php echo isset($_GET["cid"]) && strlen(trim($_GET["cid"])) > 0 ? "Update":"Add New"?> Contest</h4>
			<div class="row">
				<div class="col-md-2 text-right">
					Name:
				</div>
				<div class="col-md-6">
					<input value="<?php echo isset($_POST["cname"]) ? $_POST["cname"]:"";?>" type="text" name="cname" id="cname" required />&nbsp;&nbsp;&nbsp;
				</div>
			</div><br />
			<div class="row">
				<div class="col-md-2 text-right">
					Auto Approve
				</div>
				<div class="col-md-6">
					<input type="checkbox" <?php echo isset($_POST["auto_approve"]) && $_POST["auto_approve"] == 1 ? "checked":"";?> name="autoapprove" id="autoapprove" />
				</div>
			</div><br />
			<div class="row">
				<div class="col-md-8">
					<?php if(isset($_GET["cid"]) && strlen(trim($_GET["cid"])) > 0){?>
						<input class="btn btn-primary" type="submit" value="Update Contest" />&nbsp;&nbsp;
						<a href="<?php echo remove_query_arg(array("cid"));?>"><input class="btn btn-default" type="button" value="Cancel" /></a>
					<?php }else{ ?>
						<input class="btn btn-primary" type="submit" value="Add New Contest" />
					<?php } ?>
				</div>
			</div>
		</div>
	</form>
	<div class="metabox-holder">			
		<table class="table table-hover">
			<thead>
				<tr>
					<th width="20%">
						ID
					</th>
					<th width="25%">
						Name
					</th>
					<th width="15%">
						Create Date
					</th>
					<th width="40%"></th>								
				</tr>
			</thead>
			<tbody>					
		    	<?php if(isset($all_contests) && count($all_contests) > 0){
		    			foreach($all_contests as $cid => $data){?>
					    	<tr>
					    		<td>
									<a href="<?php echo add_query_arg(array("cid"=>$cid));?>"><?php echo $cid;?></a>
								</td>
								<td>
									<?php echo $data->name;?>
								</td>
								<td>
									<?php echo $data->createdate;?>
								</td>
								<td class="text-center">
									<a href="<?php echo add_query_arg(array("page"=> "firebase-contest-users" ,"cidusers"=>$cid, "cname"=>$data->name));?>">Manage Users and Images</a>&nbsp;&nbsp;|&nbsp;&nbsp;
									<a style="color:red;" href="#" onclick="if(confirm('Really, do you want to delete this contest?')){window.location.href='<?php echo add_query_arg(array("dcid"=>$cid));?>';}else{return false;}">
										Delete Contest
									</a>
								</td>
					    	</tr>
		    		<?php }		    	
		    		 }else{?>	    		
		    			<tr>
				    		<td colspan="7">No record found</td>			    		
				    	</tr>	    		
		    	<?php }?>
			</tbody>
		</table>			
	</div>
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