<?php
    include('../dbconnection.php');
    require '../vendor/autoload.php';

    use Google\Cloud\Storage\StorageClient;
    
    include "../phpqrcode/qrlib.php"; 

    // Get user ID from session
    $userID = $_SESSION['vpmsuid'];

    // Generate a random token
    $token = bin2hex(random_bytes(16)); // 16 bytes = 128 bits

    // Fetch user's qrimage from the database
    $sql = "SELECT qrimage FROM tblregusers WHERE ID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $qrImage = $user['qrimage'];
    $stmt->close();

    // If the user doesn't already have a QR image
    if (empty($qrImage)) {
        $text = $token; 
        $filename = uniqid().".png";
        $ecc = 'H';
        $pixel_Size = 7;
        $frame_Size = 7;

        // Generate QR code image data into a variable
        ob_start();
        QRcode::png($text, false, $ecc, $pixel_Size, $frame_Size);  // False as second argument to output to buffer instead of file
        $imageData = ob_get_clean();

        // Authenticate with Google Cloud
        $storage = new StorageClient([
            'projectId' => '../my-project-388313',
            'keyFilePath' => '../my-project-388313-8d498336248d.json'
        ]);

        // The name of the bucket you're using
        $bucketName = 'parkingsystem2023';

        // Upload the file to the bucket
        $bucket = $storage->bucket($bucketName);
        $bucket->upload(
            $imageData,  // Use $imageData instead of file
            [
                'name' => $filename,  // Name the object with the $filename
                'metadata' => [
                    'contentType' => 'image/png',  // Set content type so GCS knows it's an image
                ],
            ]
        );

        // Generate a public URL for the object
        $qrImage = sprintf('https://storage.googleapis.com/%s/%s', $bucketName, $filename);

        // prepare sql statement
        $sql = "UPDATE tblregusers SET qrimage = ?, token = ? WHERE ID = ?";

        // create a prepared statement
        $stmt = $con->prepare($sql);

        // bind parameters
        $stmt->bind_param("ssi", $qrImage, $token, $userID);

        // execute the query
        $stmt->execute();

        // close the statement
        $stmt->close();
    }

    // close the connection
    $con->close();

    echo "<center><img src='".$qrImage."'></center>";
?>
