<?php
    require_once("../config.php");
    require_once("../basicFunctions.php");
	doSuperUserLogInCheck();

	$thisSSN = getUserSSN();
    //Get Supervisees
    $query = "
        SELECT
          *
        FROM Security_Officer
        WHERE
        Super_SSN = :ssn
        ORDER BY Last_Name
    ";

    $query_params = array(
        ':ssn' => $thisSSN
    );

    try{
        $supervisees = $db->prepare($query);
        $result = $supervisees->execute($query_params);
        $supervisees->setFetchMode(PDO::FETCH_ASSOC);
		$superviseesList=$supervisees->fetchAll();
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $num_supervisees = $supervisees->rowCount();

    //Get Shifts
    $query = "
        SELECT
            *
        FROM Shift_Assignment AS S INNER JOIN Security_Officer AS O ON S.Officer_SSN = O.SSN
        WHERE
        S.Officer_SSN IN
        (SELECT
            SSN
        FROM Security_Officer
        WHERE (Start_Time > NOW())
			AND
			(
				Super_SSN = :ssn
				OR
				(SSN = :ssn AND Super_SSN IS NULL)
			)
		)
		ORDER BY S.Start_Time, Created_Time
    ";

    $query_params = array(
        ':ssn' => $thisSSN
    );

    try{
        $shifts = $db->prepare($query);
        $result = $shifts->execute($query_params);
        $shifts->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $num_shifts = $shifts->rowCount();

    $query = "
        SELECT
            *
        FROM Shift_Assignment AS S INNER JOIN Security_Officer AS O ON S.Officer_SSN = O.SSN
        WHERE
        S.Officer_SSN IN
        (SELECT
            SSN
        FROM Security_Officer
        WHERE (Start_Time < NOW())
			AND
			(
				Super_SSN = :ssn
				OR
				(SSN = :ssn AND Super_SSN IS NULL)
			)
		)
		ORDER BY S.Start_Time, Created_Time
    ";

    $query_params = array(
        ':ssn' => $thisSSN
    );

    try{
        $shifts_old = $db->prepare($query);
        $result = $shifts_old->execute($query_params);
        $shifts_old->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $num_shifts = $num_shifts + $shifts_old->rowCount();


    //Check if has supervisor
    $query = "
        SELECT
          First_Name, Last_Name
        FROM Security_Officer
        WHERE
			SSN = :ssn
			AND
			Super_SSN IS NULL
    ";

    $query_params = array(
        ':ssn' => $thisSSN
    );

    try{
        $checkHasSuper = $db->prepare($query);
        $result = $checkHasSuper->execute($query_params);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
	$hasSupervisor = !($checkHasSuper->rowCount() > 0);
	$checkHasSuperRow = $checkHasSuper->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Shift Management System</title>

    <!-- Bootstrap Core CSS -->
    <link type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="../vendor/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link type="text/css" href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link type="text/css" href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link type="text/css" href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery -->
    <script type="text/javascript" src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script type="text/javascript" src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../vendor/moment/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="../vendor/bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">

<?php
	$isLoadingNavBar = true;
	require("navBar.php");
	$isLoadingNavBar = false;
?>

        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Shift Management</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>Supervisees</b></h4>
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Shifts</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php foreach($superviseesList as $row) { ?>
                                <tr>
                                  <td><?php echo $row['Last_Name']; ?></td>
                                  <td><?php echo $row['First_Name']; ?></td>
                                  <td>(<?php echo substr($row['Phone_Number'], 0, 3); ?>) <?php echo substr($row['Phone_Number'], 3, 3); ?> - <?php echo substr($row['Phone_Number'], 6, 4); ?></td>
                                  <td><?php echo $row['Email']; ?></td>
                                  <td><?php

                                  $query = "
                                      SELECT
                                        *
                                      FROM Shift_Assignment
                                      WHERE
                                      Officer_SSN = :ssn
                                  ";

                                  $query_params = array(
                                      ':ssn' => $row['SSN']
                                  );

                                  try{
                                      $supervisee_shifts = $db->prepare($query);
                                      $result = $supervisee_shifts->execute($query_params);
                                      $supervisee_shifts->setFetchMode(PDO::FETCH_ASSOC);
                                  }
                                  catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                  $num_supervisee_shifts = $supervisee_shifts->rowCount();
                                  echo $num_supervisee_shifts; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                          </table>
                    </div>
                    
                    <hr>
                    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>All Shifts</b></h4>
                            There are currently <b> <?php echo $num_shifts ?> </b> shifts under your management.
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Duration</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Spots</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $shifts->fetch()) { ?>
                                <tr>
                                  <td>Start: <?php echo $row['Start_Time'];?><br>End: <?php echo $row['End_Time']; ?></td>
                                  <td><?php echo formatDurationS($row['Duration_s']); ?></td>
                                  <td><?php echo $row['Last_Name']; ?></td>
                                  <td><?php echo $row['First_Name']; ?></td>
                                  <td><ul><?php $query = "
                                        SELECT
                                          Coverage_Description
                                        FROM Spot AS sp NATURAL JOIN Spot_Assignment AS sa
                                        WHERE sa.Shift_UUID = :shift_uuid
                                    ";

                                    $query_params = array(
                                      ':shift_uuid' => $row['Shift_UUID']
                                    );

                                    try{
                                        $spots = $db->prepare($query);
                                        $result = $spots->execute($query_params);
                                        $spots->setFetchMode(PDO::FETCH_ASSOC);
                                    }
                                    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                    while($row2 = $spots->fetch()) { ?>
                                      <li><?php echo $row2['Coverage_Description']; ?></li>
                                    <?php } ?>
                                  </ul>
                                </td>
                                <td>
                                  <form action="../php/delete_shift.php" method="post" role="form" data-toggle="validator">
                                    <div class="form-group">
                                      <input type="hidden" value="<?php echo $row['Shift_UUID']; ?>" name="delete" id="delete">
                                      <button type="submit" tabindex="4" class="form-control btn btn-xs btn-danger">
                                        <i class="fa fa-trash fa-fw"></i></button>
                                    </div>
                                  </form>
                                </td>
                              <?php } ?>
                              <?php while($row = $shifts_old->fetch()) { ?>
                                <tr class="warning">
                                  <td>Start: <?php echo $row['Start_Time'];?><br>End: <?php echo $row['End_Time']; ?></td>
                                  <td><?php echo formatDurationS($row['Duration_s']); ?></td>
                                  <td><?php echo $row['Last_Name']; ?></td>
                                  <td><?php echo $row['First_Name']; ?></td>
                                  <td><ul><?php $query = "
                                        SELECT
                                          Coverage_Description
                                        FROM Spot AS sp NATURAL JOIN Spot_Assignment AS sa
                                        WHERE sa.Shift_UUID = :shift_uuid
                                    ";

                                    $query_params = array(
                                      ':shift_uuid' => $row['Shift_UUID']
                                    );

                                    try{
                                        $spots = $db->prepare($query);
                                        $result = $spots->execute($query_params);
                                        $spots->setFetchMode(PDO::FETCH_ASSOC);
                                    }
                                    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                    while($row2 = $spots->fetch()) { ?>
                                      <li><?php echo $row2['Coverage_Description']; ?></li>
                                    <?php } ?>
                                  </ul>
                                </td>
                                <td>
                                  <form action="../php/delete_shift.php" method="post" role="form" data-toggle="validator">
                                    <div class="form-group">
                                      <input type="hidden" value="<?php echo $row['Shift_UUID']; ?>" name="delete" id="delete">
                                      <button type="submit" tabindex="4" class="form-control btn btn-xs btn-danger" disabled>
                                        <i class="fa fa-trash fa-fw"></i></button>
                                    </div>
                                  </form>
                                </td>
                              <?php } ?>
                            </tbody>
                          </table>
                    </div>
                    
                    <hr>
                    
                    <div class="panel panel-info">
                        <div class="panel-success">
                          <div class="panel-heading">
                              <h4><b>Create New Shift</b></h4>
                          </div>
                          <br>
                        <form class="form-horizontal" action="../php/newShift.php" method="post" role="form">
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Start Time:</label>
                            <div class="col-lg-8">
                                <input class="form-control" name="start" id="datetimepicker_start" type="text" required>
								<script type="text/javascript">
                                    $(function () {
                                        $('#datetimepicker_start').datetimepicker({format:'MM/DD/YYYY HH:mm'});
                                    });
                                </script>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">End Time:</label>
                            <div class="col-lg-8">
                                <input class="form-control" name="end" id="datetimepicker_end" type="text" required>
								<script type="text/javascript">
                                    $(function () {
                                        $('#datetimepicker_end').datetimepicker({format:'MM/DD/YYYY HH:mm'});
                                    });
                                </script>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Officer:</label>
                            <div class="col-lg-8">
                              <label for="ssn">Select an Officer:</label>
                                <select class="form-control" id="ssn" name="ssn">
                                  <?php foreach($superviseesList as $row) { ?>
                                    <option value="<?php echo $row['SSN']; ?>"><?php echo $row['Last_Name'] ?>, <?php echo $row['First_Name'] ?></option>
                                  <?php } ?>
									<?php if(!$hasSupervisor) { ?>
                                    	<option value="<?php echo $thisSSN; ?>" selected><?php echo $checkHasSuperRow['Last_Name'] ?>, <?php echo $checkHasSuperRow['First_Name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Spots:</label>
                            <div class="col-lg-8">
                              <label for="spots">Select Spots:</label>
                                <select multiple="multiple" class="form-control" name="spots[]" id="spots">
                                  <?php
                                  $query = "
                                      SELECT
                                        *
                                      FROM Spot
                                  ";

                                  try{
                                      $spots = $db->prepare($query);
                                      $result = $spots->execute($query_params);
                                      $spots->setFetchMode(PDO::FETCH_ASSOC);
                                  }
                                  catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                  while($row = $spots->fetch()) { ?>
                                    <option value="<?php echo $row['Spot_UUID']?>"><?php echo $row['Coverage_Description'] ?></option>
                                  <?php } ?>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-8">
                              <input type="submit" class="btn btn-primary" value="Create Shift">
                            </div>
                          </div>
                        </form>
                  		</div>
                    	<!-- /.panel -->
                  	</div>
                    <!-- /.panel -->
                    
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->


</body>

</html>
