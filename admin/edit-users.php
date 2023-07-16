<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['vpmsaid']==0)) {
  header('location:logout.php');
} else{
  $userid = intval($_GET['id']); // Get the id as integer
  $result = mysqli_query($con, "SELECT * FROM tblregusers WHERE ID = '$userid'");
  $user = mysqli_fetch_array($result);

  // Check if firstName is posted, indicating the form was submitted
  if(isset($_POST['firstName'])) {
    $firstName = mysqli_real_escape_string($con, $_POST['firstName']); // sanitize input
    $lastName = mysqli_real_escape_string($con, $_POST['lastName']); // sanitize input
    $licenseNumber = mysqli_real_escape_string($con, $_POST['licenseNumber']); // sanitize input
    $email = mysqli_real_escape_string($con, $_POST['email']); // sanitize input

    $update = mysqli_query($con,"UPDATE tblregusers SET FirstName = '$firstName', LastName = '$lastName', LicenseNumber = '$licenseNumber', Email = '$email' WHERE ID = '$userid'");
    
    if($update) {
      echo "<script>alert('User updated successfully.');</script>";
      echo "<script>window.location.href='reg-users.php'</script>";
    } else {
      echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
  }
?>

<!doctype html>
<html class="no-js" lang="">
<head>
    <title>VPMS - Edit User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
</head>
<body>
  <?php include_once('includes/sidebar.php');?>
  <?php include_once('includes/header.php');?>

  <div class="breadcrumbs">
            <div class="breadcrumbs-inner">
                <div class="row m-0">
                    <div class="col-sm-4">
                        <div class="page-header float-left">
                            <div class="page-title">
                                <h1>Dashboard</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="page-header float-right">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="dashboard.php">Dashboard</a></li>
                                    <li><a href="reg-users.php">Registered Users</a></li>
                                    <li class="active">Edit Users</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

  <div class="content">
    <div class="animated fadeIn">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <strong class="card-title">Edit User</strong>
            </div>
            <div class="card-body">
              <form method="post">
                <div class="form-group">
                  <label>First Name</label>
                  <input type="text" name="firstName" value="<?php echo $user['FirstName']; ?>" required class="form-control">
                </div>
                <div class="form-group">
                  <label>Last Name</label>
                  <input type="text" name="lastName" value="<?php echo $user['LastName']; ?>" required class="form-control">
                </div>
                <div class="form-group">
                  <label>License Number</label>
                  <input type="text" name="licenseNumber" value="<?php echo $user['LicenseNumber']; ?>" required class="form-control">
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <input type="text" name="email" value="<?php echo $user['Email']; ?>" required class="form-control">
                </div>
                <button type="submit" name="submit" class="btn btn-success">Update</button>
                <button type="button" class="btn btn-primary" onclick="location.href='reg-users.php'">Cancel</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include_once('includes/footer.php');?>

    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
<?php } ?>
