<?php
session_start();
error_reporting(0);
include('../dbconnection.php');
require '../vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;


if (strlen($_SESSION['vpmsaid']==0)) {
  header('location:logout.php');
  } else{
      
// For deleting
if(isset($_GET['del']) && !empty($_GET['del'])) {
    $userID = $_GET['del'];

    // Fetch the user's QR code image URL
    $sql = "SELECT qrimage FROM tblregusers WHERE ID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $qrImage = $user['qrimage'];
    $stmt->close();

    if ($qrImage !== null) {
        // Parse the URL to get the file name
        $qrImageComponents = parse_url($qrImage);
        $qrImageFileName = basename($qrImageComponents['path']);

        // Authenticate with Google Cloud
        $storage = new StorageClient([
            'projectId' => 'my-project-388313',
            'keyFilePath' => '../my-project-388313-8d498336248d.json'
        ]);

        // The name of the bucket you're using
        $bucketName = 'parkingsystem2023';

        // Delete the file from the bucket
        $bucket = $storage->bucket($bucketName);
        $object = $bucket->object($qrImageFileName);

        try {
            $object->delete();
        } catch (Exception $e) {
            // Do nothing if the file does not exist in the bucket
        }
    }

    // Delete the user's payments from tblpayments
    $sql = "DELETE FROM tblpayments WHERE user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->close();

    // Now you can delete the user from the database
    $sql = "DELETE FROM tblregusers WHERE ID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->close();

    $con->close();
    echo "<script>alert('Data Deleted');</script>";
    echo "<script>window.location.href='reg-users.php'</script>";
}

  ?>
<!doctype html>

<html class="no-js" lang="">
<head>
   
    <title>VPMS - Manage Category</title>
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
                                    <li class="active">Registered Users</li>
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
                                <strong class="card-title">Registered Users</strong>
                            </div>
                            <div class="card-body">
                                <input class="form-control" id="filterInput" type="text" placeholder="Search.." oninput="filterTable()">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>Name</th>
                                            <th>Vehicle Registration Number</th>
                                            <th>Position</th>
                                            <th>Email</th>
                                            <th>Vehicle Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="output">
                                        <?php
                                        $ret = mysqli_query($con, "select * from tblregusers WHERE status = 'accepted'");
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_array($ret)) {
                                        ?>
                                            <tr>
                                                <td><?php echo $cnt; ?></td>
                                                <td><?php echo $row['FirstName']; ?> <?php echo $row['LastName']; ?></td>
                                                <td><?php echo $row['LicenseNumber']; ?></td>
                                                <td><?php echo $row['Position']; ?></td>
                                                <td><?php echo $row['Email']; ?></td>
                                                <td><?php echo $row['vStatus']; ?></td>
                                                <td>
                                                    <a href="edit-users.php?id=<?php echo $row['ID']; ?>" class="btn btn-primary">Edit</a>
                                                    <a href="reg-users.php?del=<?php echo $row['ID']; ?>" class="btn btn-danger" onClick="return confirm('Are you sure you want to delete?')">Delete</a>
                                                </td>
                                            </tr>
                                        <?php $cnt = $cnt + 1;
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- .animated -->
        </div><!-- .content -->
        <div class="clearfix"></div>

<?php include_once('includes/footer.php');?>

</div>


<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
<script src="assets/js/main.js"></script>
<script>
    function filterTable() {
        // Get the input value and convert it to lowercase for case-insensitive search
        var input = document.getElementById("filterInput").value.toLowerCase();
        var tableRows = document.getElementById("output").getElementsByTagName("tr");

        // Loop through all the table rows and hide those that don't match the search input
        for (var i = 0; i < tableRows.length; i++) {
            var row = tableRows[i];
            var rowData = row.getElementsByTagName("td");

            // Hide the row if the search input doesn't match any of the row's data
            var shouldHide = true;
            for (var j = 1; j < rowData.length - 1; j++) { // Skip the first and last columns (NO and Action)
                var cellData = rowData[j].innerText.toLowerCase();
                if (cellData.includes(input)) {
                    shouldHide = false;
                    break;
                }
            }

            // Toggle the row's visibility based on the search input
            row.style.display = shouldHide ? "none" : "";
        }
    }
</script>

</body>
</html>
<?php }  ?>