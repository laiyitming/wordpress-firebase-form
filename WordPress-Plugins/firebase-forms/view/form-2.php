<!-- Container for the demo -->
<div class="col-md-12" id="formcon2">
	<h2>Mothers Day Contest 2</h2>		
	<div class="form-group">		
		<label for="nric">Photo</label>
		<input onchange="showPreviewImage(event); getFileName(this);" type="file" id="contestphoto" accept=".png, .jpg, .bmp, .JPEG, .JPG, .svg, .tiff, .gif">
        <img src="" id="preview" style="max-width:300px;" />
        <span class="error-message" id="contestphotoerror" ></span>				
	</div>		
	<div class="form-group">
		<label for="pdesc">Photo Description</label>
		<textarea class="form-control" name="pdesc" id="pdesc"></textarea>	
	</div>	
	
	<div class="form-group">
		<label for="places">Places</label>
		<p>
		<select multiple class=" places" id="places" style="width:100%;">
			<option value="Bishan">Bishan</option>
			<option value="Bukit Merah">Bukit Merah</option>
			<option value="Marina Bay">Marina Bay</option>
			<option value="Newton">Newton</option>
			<option value="Novena">Novena</option>
			<option value="Orchard">Orchard</option>
			<option value="Queenstown">Queenstown</option>
			<option value="Toa Payoh">Toa Payoh</option>
			<option value="Changi">Changi</option>
		</select>
		</p>		
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