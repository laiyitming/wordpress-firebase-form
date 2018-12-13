<!-- Container for the demo -->
<div class="col-md-12">
	<div class="form-group">
		<label for="uname">Name</label>
		<input type="uname" class="form-control" id="uname">
	</div>
	<div class="form-group">
		<label for="nric">NRIC</label>
		<input type="nric" class="form-control" id="nric">
	</div>
	<div class="form-group">
		<label for="nric">Photo</label>
		<input type="file" id="contestphoto" onchange="getFileName(this)" accept=".png, .jpg, .bmp, .JPEG, .JPG, .svg, .tiff, .gif">
		<input type="caption" class="form-control" id="caption" placeholder="Photo Description">
	</div>	
	<br />	
	<div class="form-group">
		<button class="btn btn-default" onclick="submitForm()">Submit</button>
	</div>
	<hr />	
	<div class="form-group">
		<div id="uploadedfile"> </div>
	</div>	
</div>