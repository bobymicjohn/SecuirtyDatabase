<?php
    require("../config.php");
    require("../basicFunctions.php");
	doLogInCheck();

    //Get Supervisees
    $query = "
        SELECT
          *
        FROM Security_Officer
        WHERE Super_SSN = null
        ORDER BY Last_Name
    ";

    try{
        $supervisees = $db->prepare($query);
        $result = $supervisees->execute();
        $supervisees->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

    //Get officers
    $query = "
        SELECT
          *
        FROM Security_Officer
        WHERE Super_SSN = null
        ORDER BY Last_Name
    ";

    try{
        $officers = $db->prepare($query);
        $result = $officers->execute();
        $officers->setFetchMode(PDO::FETCH_ASSOC);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

    //Get Supervisors
    $query = "
        SELECT
            *
        FROM Security_Officer
        WHERE
        Super_SSN = null
    ";

    try{
        $supers = $db->prepare($query);
        $result = $supers->execute();
        $supers->setFetchMode(PDO::FETCH_ASSOC);
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

    <title>Shift Management System</title>

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
                <a class="navbar-brand" href="home.php">System Administrator Terminal</a>
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
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                            <!-- /input-group -->
                        </li>
                        <li>
                            <a href="home.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="user.php"><i class="fa fa-user fa-fw"></i> Profile</a>
                        </li>
                        <li>
                            <a href="alarms.php"><i class="fa fa-exclamation-triangle fa-fw"></i> Alarms</a>
                        </li>
                        <li>
                            <a href="tickets.php"><i class="fa fa-ticket fa-fw"></i> Tickets</a>
                        </li>
                        <li>
                            <a href="shifts.php"><i class="fa fa-users fa-fw"></i> Shifts</a>
                        </li>
                        <li>
                            <a href="buildings.php"><i class="fa fa-building fa-fw"></i> Buildings</a>
                        </li>
                        <li>
                            <a href="spots.php"><i class="fa fa-map fa-fw"></i> Spots</a>
                        </li>
                        <li>
                            <a href="cameras.php"><i class="fa fa-camera fa-fw"></i> Cameras</a>
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
                    <h1 class="page-header">Officer Management</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>Officers</h4><b>
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Supervisor</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $supervisees->fetch()) { ?>
                                <tr>
                                  <td><?php echo $row['Last_Name']; ?></td>
                                  <td><?php echo $row['First_Name']; ?></td>
                                  <td>(<?php echo substr($row['Phone_Number'], 0, 3); ?>) <?php echo substr($row['Phone_Number'], 3, 3); ?> - <?php echo substr($row['Phone_Number'], 6, 4); ?></td>
                                  <td><?php echo $row['Email']; ?></td>
                                  <td><?php echo $row['Address'];  ?></td>
                                  <td><?php

                                  $query = "
                                      SELECT
                                        *
                                      FROM Security_Officer
                                      WHERE
                                      Officer_SSN = :ssn
                                  ";

                                  $query_params = array(
                                      ':ssn' => $row['Super_SSN']
                                  );

                                  try{
                                      $supervisor = $db->prepare($query);
                                      $result = $supervisor->execute($query_params);
                                  }
                                  catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                  echo $row['First_Name']; ?> <?php echo $row['Last_Name']; ?></td>
                                </tr>
                                <?php } ?>
                            <tbody>
                          </table>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><b>Supervisors</b></h4>
                        </div>
                        <table width="100%" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Supervisees</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php while($row = $supers->fetch()) { ?>
                                <tr>
                                  <td><?php echo $row['Last_Name']; ?></td>
                                  <td><?php echo $row['First_Name']; ?></td>
                                  <td><?php echo $row['Phone_Number']; ?></td>
                                  <td><?php echo $row['Email']; ?></td>
                                  <td><?php echo $row['Address']; ?></td>
                                  <td><ul><?php $query = "
                                        SELECT
                                          *
                                        FROM Security_Officer
                                        WHERE Super_SSN = :ssn
                                    ";

                                    $query_params = array(
                                      ':ssn' => $row['SSN']
                                    );

                                    try{
                                        $supervs = $db->prepare($query);
                                        $result = $supervs->execute($query_params);
                                        $supervs->setFetchMode(PDO::FETCH_ASSOC);
                                    }
                                    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
                                    while($row2 = $supervs->fetch()) { ?>
                                      <li><?php echo $row2['First_Name']; ?> <?php echo $row2['Last_Name']; ?></li>
                                    <?php } ?>
                                  </ul>
                                </td>
                              <?php } ?>
                            <tbody>
                          </table>
                    </div>

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
                              <input class="form-control" name="start" id="start" type="time" value="<?php echo $profile['First_Name'] ?>" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">End Time:</label>
                            <div class="col-lg-8">
                              <input class="form-control" name="end" id="end" type="time" value="<?php echo $profile['Last_Name'] ?>" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-2 control-label">Officer:</label>
                            <div class="col-lg-8">
                              <label for="ssn">Select an Officer:</label>
                                <select class="form-control" id="ssn" name="ssn">
                                  <?php while($row = $supervisees2->fetch()) { ?>
                                    <option value="<?php echo $row['SSN'] ?>"><?php echo $row['Last_Name'] ?>, <?php echo $row['First_Name'] ?></option>
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
                    <hr>
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

    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>