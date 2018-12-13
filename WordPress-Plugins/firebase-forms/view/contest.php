<!--  <h1><?php echo $users->data->message->hits[0]->_source->contest ?></h1> -->

<?php if(isset($all_imags) && count($all_imags) > 0){ ?>
	<div class="row">
		<?php foreach($all_imags as $key => $udata){				
			foreach($udata->_source->images as $pushid => $record){ 
				//if($record->status == 1){?>
		 	<div class="col-md-4 imgcon" > 
				<img style="height:300px;" id="myImg" alt="<?php echo $record->desc?>" class="myImg" src="<?php echo $record->url?>" />
				<div style="font-size:14px; color #ccc;">by <?php echo $udata->_source->user_name?></div>
			</div>			
		<?php //}
			}
		}?>

		<?php foreach($all_imags as $key => $img){	
			foreach($img as $pushid => $record){ 
				if($record->status == 1){?>
		 	<div class="col-md-4 imgcon" > 
				<img id="myImg" class="myImg" src="<?php echo $record->url?>" />
			</div>			
		<?php }
			}
		}?>		 
	</div>	
<?php }?>

<!-- The Modal -->
<div id="myModal" class="modal">
  <span class="close">&times;</span>
  <img class="modal-content" id="img01">
  <div id="caption"></div>
</div>
<script>
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