<?php
// connect to database
include('includes/dbconnection.php');

// Set the date you want
$setDate = '2024-06-01'; 

// sql to update status from 'accepted' to 'pending' for users with "Mahasiswa" position and registered before $setDate
$sql = "UPDATE tblregusers SET status = 'pending' WHERE RegDate <= ? AND status = 'accepted' AND Position = 'Mahasiswa'";

// create a prepared statement
$stmt = $con->prepare($sql);

// bind parameters
$stmt->bind_param("s", $setDate);

// execute the query
$stmt->execute();

// check if the query was successful
if($stmt->affected_rows > 0) {
  echo "Status updated successfully.";
} else {
  echo "No status updates needed.";
}

// close the statement
$stmt->close();

// close the connection
$con->close();
?>
