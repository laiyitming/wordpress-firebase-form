<!-- Container for the demo -->
<div class="col-md-12" id="formcon1">
	<h2>Profile</h2>
	<div class="form-group">
		<label for="name">Name</label>
		<input type="text" data-label="Name" class="form-control" id="name">
		<span class="error-message" id="nameerror" ></span>
	</div>
	<div class="form-group">
		<label for="address">Address</label>
		<textarea data-label="Address" class="form-control" id="address"></textarea>
		<span class="error-message" id="addresserror" ></span>
	</div>
	
		
	<div class="form-group">
		<button class="btn btn-default" id="submitbtn" onclick="previewForm(1)" name="submitbtn">Preview</button>&nbsp;&nbsp;&nbsp;
		<button class="btn btn-default" id="submitbtn" onclick="submitForm1()" name="submitbtn">Submit</button>
	</div>
</div>
