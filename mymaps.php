<?php include('header.php');
?>
 
 <div id="page-wrapper">
        <!-- Page Header -->
           <div class="container-fluid">
             <!-- Page Header -->
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">My Maps
						<small></small>
					</h1>
				</div>
				<p text-align="justify">
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
				</p>
			</div>
			<!-- /.row -->   
			<!-- Projects Row -->
			<div style="height:600px;overflow-y:auto;width:100%">
			<div class="row">
				<?php 
					$images = array_map('basename', glob("Targets/".$_SESSION['user']."/*.*", GLOB_BRACE));
					foreach($images as $filename){
					?>
						<div class='col-md-3 portfolio-item'>
							<a href='#'>
								<img class='img-responsive' id="<?php echo str_replace(".jpg","", $filename) ?>" src="<?php echo "Targets/".$_SESSION['user']."/".$filename?>" >
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
			<!-- /.row -->
        <!-- /.row -->

        <hr>

		</div>
		</div>

<?php include('footer.php'); ?>


		<div class="modal fade target-modal" id="imagetargetModal" role="dialog" aria-hidden="true">
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
		var src = "Targets/"+<?php echo $_SESSION['user']?>+"/"+$(this).attr("id")+".jpg";
		$("#selecteTarget").attr("src", src);
		//$('#imagetargetModal').modal('show');
		$('#imagetargetModal').appendTo("body").modal('show');
		return false;
	});
	var printbtn = document.getElementById('print');
	printbtn.onclick = function(){
		window.print();
		return false;
	}
</script>
