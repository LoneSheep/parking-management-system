<?php

// Turn on all error reporting
error_reporting(E_ALL);

// Alternatively you can use ini_set
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
//error_reporting(0);
include('includes/dbconnection.php');

if(isset($_POST['submit']))
{
    $fname=$_POST['firstname'];
    $lname=$_POST['lastname'];
    $licensenumber=$_POST['licensenumber'];
    $position=$_POST['position'];
    $nip = isset($_POST['nip']) && !empty($_POST['nip']) ? $_POST['nip'] : NULL;
    $nim = isset($_POST['nim']) && !empty($_POST['nim']) ? $_POST['nim'] : NULL;
    $email=$_POST['email'];
    $password=md5($_POST['password']);
    $ret=mysqli_query($con, "select Email from tblregusers where Email='$email'");
    $result=mysqli_fetch_array($ret);
    if($result>0){
        echo '<script>alert("This email already associated with another account")</script>';
    }
    else{
        $query=mysqli_prepare($con, "insert into tblregusers(FirstName, LastName, LicenseNumber, Position, NIP, NIM, Email, Password, status) value(?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $query->bind_param("ssssisss", $fname, $lname, $licensenumber, $position, $nip, $nim, $email, $password);
        $query->execute();
        if ($query->affected_rows > 0) {
            echo '<script>alert("You have successfully registered")</script>';
        }
        else {
            echo '<script>alert("Something Went Wrong. Please try again")</script>';
            echo "Error: " . mysqli_error($con);
        }
    }
}

  ?>
<!doctype html>
 <html class="no-js" lang="">
<head>
    
    <title>VPMS-Signup Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="../admin/assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="../admin/assets/css/style.css">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script type="text/javascript">

        $(document).ready(function(){
            $('#type').change(function(){
                if($(this).val() == "Staff"){
                    $('#NIP').show();
                    $('#NIM').hide();
                }
                else if($(this).val() == "Mahasiswa"){
                    $('#NIM').show();
                    $('#NIP').hide();
                }
                else{
                    $('#NIP').hide();
                    $('#NIM').hide();
                }
            });
        });

    function checkpass()
    {
    if(document.signup.password.value!=document.signup.repeatpassword.value)
    {
    alert('Password and Repeat Password field does not match');
    document.signup.repeatpassword.focus();
    return false;
    }
    return true;
    } 
</script>

    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->
</head>
<body class="bg-dark">

    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-logo">
                        <h2 style="color: #fff">Register Account</h2>
                </div>

                <div class="login-form">
                    <form method="post" onsubmit="return checkpass();">
                         
                        <div class="form-group">
                            <label>First Name</label>
                           <input type="text" name="firstname" placeholder="Your First Name..." required="true" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                           <input type="text" name="lastname" placeholder="Your Last Name..." required="true" class="form-control">
                        </div>

                        <div class="form-group">
                        <label>Position</label>
                        <select id="type" name="position" required="true" class="form-control">
                            <option value="">Choose...</option>
                            <option value="Staff">Staff</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                        </select>
                        </div>

                        <div class="form-group" id="NIP" style="display: none;">
                        <label>NIP</label>
                        <input type="number" name="nip" placeholder="NIP" maxlength="18" class="form-control">
                        </div>

                        <div class="form-group" id="NIM" style="display: none;">
                        <label>NIM</label>
                        <input type="number" name="nim" placeholder="NIM" maxlength="11" class="form-control" >
                        </div>

                        <div class="form-group">
                        <label>Vehicle License Number</label>
                        <input type="text" name="licensenumber" placeholder="B 1234 CDE" required="true" class="form-control" pattern="[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}" title="Please enter the license number in the format 'X 1234 XYZ'">
                        </div>

                        <div class="form-group">
                            <label>Email address</label>
                           <input type="email" name="email" placeholder="Email address" required="true" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                           <input type="password" name="password" placeholder="Enter password" required="true" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Repeat Password</label>
                            <input type="password" name="repeatpassword" placeholder="Enter repeat password" required="true" class="form-control">
                        </div>
                        <div class="checkbox">
                            
                            <label class="pull-right">
                                <a href="forgot-password.php">Forgotten Password?</a>
                            </label>
                            <label class="pull-left">
                                <a href="login.php">Signin</a>
                            </label>

                        </div>
                        <button type="submit" name="submit" class="btn btn-success btn-flat m-b-30 m-t-30">REGISTER</button>
                       
                       
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="../admin/assets/js/main.js"></script>

</body>
</html>
