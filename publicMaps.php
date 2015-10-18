<?php include('header.php');?>
<div class="spacer"></div>
 <div id="page-wrapper">
            <div class="container-fluid">
			
			<div style="height:600px;overflow-y:auto;width:100%">
			<div class="row">
				<?php 
					$images = glob("Targets/*/*.jpg");
					$imgs = array();
					foreach($images as $image){ $imgs[] = $image; }
					//print_r($imgs);
					//$images = array_map('basename', glob("Targets/".$_SESSION['user']."/*.*", GLOB_BRACE));
					foreach($imgs as $filename){
					?>
						<div class='col-md-3 portfolio-item'>
							<a href='#'>
								<img class='img-responsive' id="<?php echo str_replace(".jpg","", $filename) ?>" src="<?php echo $filename?>" >
							</a>
							<h3>
								<a href='#'>Map Name</a>
							</h3>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
						</div>
				<?php
					}
				?>
			</div>
			</div>
			      
            </div>
        </div>
        <!-- /#page-wrapper -->

<?php include('footer.php'); ?>

<div class="modal fade target-modal" id="publicModal" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
		  <div class="modal-content">
			<div class="modal-body">
			  <img id="selecteTarget" />
			</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-default"  id="print"><i class="fa fa-print fa-lg"></i></button>
            </div>
		  </div>
		</div>
	</div>
<script>
	$(".img-responsive").on('click',function(){
		var src = $(this).attr("src");
		$("#selecteTarget").attr("src", src);
		//$('#imagetargetModal').modal('show');
		$('#publicModal').appendTo("body").modal('show');
		return false;
	});
	var printbtn = document.getElementById('print');
	printbtn.onclick = function(){
		window.print();
		return false;
	}
</script>