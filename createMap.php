<?php include('header.php');?>
<div class="spacer"></div>
        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container">
                <!-- /.row -->
				<div class="row" id="alertsuccess">
					<div class="alert alert-success alert-dismissable col-lg-8">
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						Your Image Target uploaded successfully. Click on the link to view the map <a id="staticurl" target="_blank"  class="alert-link"><i class="fa fa-link"></i></a>
					 </div>
				</div>
				<div class="row">
					<div class="col-lg-8 col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3>Map</h3>
								<p>Pan the map to select an area. You can also search for a specific area using the search box. When done click "Save Map" to save. HotCity will notify you when the link is ready. 
								<p><strong><i>Your map needs approximately 10 minutes to be ready for scan through your mobile device</i></strong></p>
							</div>
							<div class="panel-body">
							   <input id="pac-input" class="form-control" type="text" placeholder="Search...">
							   <button class="btn btn-success" id="done"><i class="fa fa-check fa-lg"></i> Save Map</button>
							   <div id="map"></div>
							   <img id="loading" src="img/ajax-loader.gif" class="hidden" style="position:absolute; top:40%;left:50%;margin-left:-20px"></img>
							</div>
							
						</div>
					</div>
				</div><!--row-->
            </div>
        </div>
        <!-- /#page-wrapper -->

<?php include('footer.php'); ?>
<script>
 var userid = <?php echo json_encode($_SESSION['user']);?>;
 localStorage.setItem('user', userid);
</script>
<script src="js/hotcity.js"></script>
