<?php
// connect to database
include('../dbconnection.php');

// Include the Cloud Storage library.
require '../vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;

// Cloud Storage settings
$projectId = 'my-project-388313';
$keyFilePath = '../my-project-388313-8d498336248d.json';
$bucketName = 'parkingsystem2023';

// Create a Cloud Storage client.
$storage = new StorageClient([
    'projectId' => $projectId,
    'keyFilePath' => $keyFilePath
]);

// Get the bucket
$bucket = $storage->bucket($bucketName);

// sql to select all "Mahasiswa" users
$sql = "SELECT * FROM tblregusers WHERE Position = 'Mahasiswa'";

// execute the query
$result = $con->query($sql);

// loop through each "Mahasiswa" user
while($row = $result->fetch_assoc()) {
    $qrimage = $row['qrimage'];

    // remove the Cloud Storage URL prefix to get object name
    $objectName = str_replace('https://storage.googleapis.com/'.$bucketName.'/', '', $qrimage);

    // Delete the object from Cloud Storage
    $object = $bucket->object($objectName);
    $object->delete();

    // prepare sql statement to set qrimage value to NULL for this user
    $sql = "UPDATE tblregusers SET qrimage = NULL WHERE ID = ?";

    // create a prepared statement
    $stmt = $con->prepare($sql);

    // bind parameters
    $stmt->bind_param("i", $row['ID']);

    // execute the query
    $stmt->execute();
}

// close the statement
$stmt->close();

// close the connection
$con->close();

echo "QR codes deleted and qrimage values removed successfully.";

?>
