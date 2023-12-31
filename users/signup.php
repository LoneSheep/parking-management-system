<?php

session_start();
error_reporting(0);
include('../dbconnection.php');
require '../vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if(isset($_POST['submit']))
{
    $fname=$_POST['firstname'];
    $lname=$_POST['lastname'];
    $licensenumber=$_POST['licensenumber'];
    $position=$_POST['position'];
    $nip = isset($_POST['nip']) && !empty($_POST['nip']) ? $_POST['nip'] : NULL;
    $nim = isset($_POST['nim']) && !empty($_POST['nim']) ? $_POST['nim'] : NULL;
    $email=$_POST['email'];
    $password=password_hash($_POST['password'], PASSWORD_DEFAULT);
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
            // Fetch the id of the user that was just inserted
            $userId = $query->insert_id;

            // Assume a default orderID and amount for the payment
            $defaultOrderID = 'ORD-' . date('YmdHis'); // This could be any string unique to this order
            $defaultAmount = 12000; // Set this to your actual default amount

            // Insert into tblpayments
            $payment_query = mysqli_prepare($con, "insert into tblpayments(user_id, orderID, amount, payment_status) values (?, ?, ?, 'PENDING')");
            $payment_query->bind_param("isd", $userId, $defaultOrderID, $defaultAmount);
            $payment_query->execute();
            if ($payment_query->affected_rows > 0) {
                
                $mail = new PHPMailer(true);
 
                try {
                    //Enable verbose debug output
                    $mail->SMTPDebug = 0;//SMTP::DEBUG_SERVER;
        
                    //Send using SMTP
                    $mail->isSMTP();
        
                    //Set the SMTP server to send through
                    $mail->Host = 'smtp.gmail.com';
        
                    //Enable SMTP authentication
                    $mail->SMTPAuth = true;
                    <!-- @TODO: replace with your username and password -->
                    //SMTP username
                    $mail->Username = 'your_email@gmail.com';
                    
                    //SMTP password
                    $mail->Password = 'your_password';
        
                    //Enable TLS encryption;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        
                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
                    $mail->Port = 587;
        
                    //Recipients
                    $mail->setFrom('your_email@gmail.com', 'Your Name');
        
                    //Add a recipient
                    $mail->addAddress($email, $fname . ' ' . $lname);
        
                    $mail->Subject = 'Registration Successful';
                    $mail->Body = 'Dear ' . $fname . ', your registration was successful. Thank you for joining us!';
        
                    $mail->send();
                    
                } catch (Exception $e) {
                        echo '<script>alert("You have successfully registered, but there was an issue sending the notification email.")</script>';
                        echo 'Mailer Error: ' . $mail->ErrorInfo;
                    }

                    header("Location: login.php");
                    echo '<script>alert("You have successfully registered")</script>';

                    } else {
                        echo '<script>alert("User registered but failed to create a payment record")</script>';
                    }
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
                    <form id="register-form" method="post" onsubmit="handleRegister(event);">
                         
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
                        <input type="text" name="licensenumber" placeholder="X 1234 XYZ" required="true" class="form-control" pattern="[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}" title="Please enter the license number in the format 'X 1234 XYZ'">
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
                                <a href="forgot-password.php">Forgot Password?</a>
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
