<div id="overlaydiv" class="overlay" style="display:none;">
	<div class="overlay-content">
		<div class="loader"></div>
	</div>	
</div>

<div class="row" style="margin-bottom:10px;">
	<!-- Container for the demo -->
	<div class="col-md-11">
		Hi <?php echo $_SESSION["name"];?> 
	</div>
	<div class="col-md-1">
		<button class="btn btn-warning" id="logout">Logout</button>
	</div>
</div>

<div class="row" style="margin-bottom:10px;" id="firebase_header">	
</div>

<!-- Modal -->
<div class="modal fade" id="form1modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Confirm Form Values</h4>
      </div>
      <div class="modal-body" id="formvalues">        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="modalsubmit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>