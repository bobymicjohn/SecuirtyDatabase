<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doLogInCheck();

    //Get All Videos
    $query = "
        SELECT
          Thumbnail, Start_Time, End_Time, Duration_us, Resolution_Height, Resolution_Width, Video_Format, Record_UUID, Camera_UID
        FROM Surveillance_Video
        ORDER BY Start_Time
    ";

    try{
        $videos = $db->prepare($query);
        $result = $videos->execute();
        $videos->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Surveillance Video Terminal</title>

    <!-- Bootstrap Core CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="home.php">Security Officer Terminal</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="user.php"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="../php/logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="home.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <?php if(isSysAdmin($_SESSION['User_UUID'])) { ?>
                        <li>
                            <a href="officers.php"><i class="fa fa-users fa-fw"></i> Officers</a>
                        </li>
                        <?php } ?>
                        <?php if(isSuperUser($_SESSION['User_UUID'])) { ?>
                        <li>
                            <a href="user.php"><i class="fa fa-user fa-fw"></i> Profile</a>
                        </li>
                        <?php } else { ?>
                          <li>
                              <a href="user.php"><i class="fa fa-user fa-fw"></i> Profile</a>
                          </li>
                        <?php } ?>
                        <li>
                            <a href="alarms.php"><i class="fa fa-exclamation-triangle fa-fw"></i> Alarms</a>
                        </li>
                        <li>
                            <a href="tickets.php"><i class="fa fa-ticket fa-fw"></i> Tickets</a>
                        </li>
                        <?php if(isSuperUser($_SESSION['User_UUID'])) { ?>
                        <li>
                            <a href="shifts.php"><i class="fa fa-calendar fa-fw"></i> Shifts</a>
                        </li>
                        <?php } ?>
                        <li>
                            <a href="buildings.php"><i class="fa fa-building fa-fw"></i> Buildings</a>
                        </li>
                        <li>
                            <a href="spots.php"><i class="fa fa-map fa-fw"></i> Spots</a>
                        </li>
                        <li>
                            <a href="cameras.php"><i class="fa fa-camera fa-fw"></i> Cameras</a>
                        </li>
                        <li>
                            <a href="videos.php"><i class="fa fa-film fa-fw"></i> Videos</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Surveillance Video Terminal</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>Surveillance Video</b></h4>
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Start Time</th>
                                    <th>Duration</th>
                                    <th>Spot</th>
                                    <th>Resolution</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $videos->fetch()) { ?>
                                <tr>
                                  <td>
                                      <?php echo '<img height="50" width="50" src="data:image/png;base64,'.base64_encode($row['Thumbnail']).'"/>'; ?>
                                  </td>
                                  <td><?php echo $row['Start_Time']; ?></td>
                                  <td><?php echo $row['Duration_us']; ?></td>
                                  <td><?php  $query = "
                                        SELECT
                                          Coverage_Description
                                        FROM Spot
                                        WHERE
                                        Spot_UUID = (
                                          SELECT Spot_UUID
                                          FROM Camera
                                          WHERE Camera_UID = :camera
                                          )                                  ";

                                    $query_params = array(
                                        ':camera' => $row['Camera_UID']
                                    );

                                    try{
                                        $spot = $db->prepare($query);
                                        $result = $spot->execute($query_params);
                                    }
                                    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                    echo implode(', ', $spot->fetch())?>
                                  </td>
                                  <td><?php echo $row['Resolution_Width']; ?> x <?php echo $row['Resolution_Height']; ?></td>
                                  <td>
                                    <form action="../php/showVideo.php" method="post" role="form" data-toggle="validator">
                                        <div class="form-group">
                                        <button type="submit" value="<?php echo $row['Record_UUID']; ?>" name="video_uuid" id="play" class="play-button btn btn-info btn-md"><i class="fa fa-play fa-fw"></i> Play Video</button>
                                        </div>
                                    </form>
                                  </td>
                                </tr>
                                
                                <?php } ?>
                            </tbody>
                          </table>
                          	<?php if(!empty($_SESSION['video_page_video_uuid'])) {?>
							<!-- Modal content-->
                            <div class="modal fade" id="myModal" role="dialog">
                            	<div class="modal-dialog">
                                	<div class="modal-content">
                                    	<div class="modal-header">
                                        	<button type="button" class="close" data-dismiss="modal">&times;</button>
                                        	<h4 class="modal-title">Video</h4>
                                    	</div>
                                    	<div class="modal-body">
                                        	<?php
											$query = "
												SELECT
												  Video_Data, Video_Format, Resolution_Height, Resolution_Width
												FROM Surveillance_Video
												WHERE
												Record_UUID = :record
											";
											
                                            $query_params = array(
                                                ':record' => $_SESSION['video_page_video_uuid']
                                            );
											
                                            try{
                                                $spot = $db->prepare($query);
                                                $result = $spot->execute($query_params);
                                            }
                                            catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                            $video = $spot->fetch();
                                        	?>
                                            <video width="100%" height="" controls>
                                            <?php echo '<source src="data:image/png;base64,'.base64_encode($video['Video_Data']).'" type="video/'.$video['Video_Format'].'"  />'; ?>
                                             Your browser does not support HTML5 video.
                                            </video>
										</div>
                                        <div class="modal-footer">
                                        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Modal content-->
                            <?php } ?>
                    </div>
                    <!-- /.panel-info -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>
    
    <?php if(!empty($_SESSION['video_page_video_uuid'])) {?>
    <script> 
	$(window).on('load',function()
	{
		$('#myModal').modal('show');
	});
	</script>
    <?php } ?>

</body>

</html>
<?php 
if(!empty($_SESSION['video_page_video_uuid'])) 
{
	unset($_SESSION['video_page_video_uuid']);
}
?>