<!-- Container for the demo -->
<div class="col-md-12" id="formcon2">
	<h2>Christmas Contest</h2>	
	
	<div class="form-group">		
		<label for="nric">Photo</label>
		<input onchange="showPreviewImage(event); getFileName(this);" type="file" id="contestphoto" accept=".png, .jpg, .bmp, .JPEG, .JPG, .svg, .tiff, .gif">
        <img src="" id="preview" style="max-width:300px;" />		
         <span class="error-message" id="contestphotoerror" ></span>			
	</div>		
	<div class="form-group">
		<label for="uname">Photo Description</label>
		<input type="caption" class="form-control" id="caption">
		<span class="error-message" id="captionerror" ></span>
	</div>	
	<div class="form-group">
		<button class="btn btn-primary pull-right" onclick="submitForm2(false)">Add more photo</button>
	</div>
	<hr />	
	<div class="form-group">
		<div id="uploadedfile" style="padding: 10px;"></div>
	</div>
	<div class="form-group">
		<!--<button class="btn btn-default" id="submitbtn" onclick="previewForm(1)" name="submitbtn">Preview</button>&nbsp;&nbsp;&nbsp;
		--><button class="btn btn-default" id="submitbtn" onclick="submitForm2()" name="submitbtn">Submit</button>
	</div>
</div> 