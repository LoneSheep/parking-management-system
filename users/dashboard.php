<?php

session_start();
error_reporting(0);
include('../dbconnection.php');
require '../vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;

if (strlen($_SESSION['vpmsuid']==0)) {
  header('location:logout.php');
  } else{ ?>


<!doctype html>

 <html class="no-js" lang="">
<head>
    
    <title> User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <link rel="apple-touch-icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="shortcut icon" href="https://i.imgur.com/QRAUqs9.png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="../admin/assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="../admin/assets/css/style.css">
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->
    <link href="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/jqvmap@1.5.1/dist/jqvmap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/weathericons@2.1.0/css/weather-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.css" rel="stylesheet" />

    <!-- @TODO: replace SET_YOUR_CLIENT_KEY_HERE with your client key -->
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="SET_YOUR_CLIENT_KEY_HERE">
    </script>
    <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->

   <style>
    #weatherWidget .currentDesc {
        color: #ffffff!important;
    }
        .traffic-chart {
            min-height: 335px;
        }
        #flotPie1  {
            height: 150px;
        }
        #flotPie1 td {
            padding:3px;
        }
        #flotPie1 table {
            top: 20px!important;
            right: -10px!important;
        }
        .chart-container {
            display: table;
            min-width: 270px ;
            text-align: left;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        #flotLine5  {
             height: 105px;
        }

        #flotBarChart {
            height: 150px;
        }
        #cellPaiChart{
            height: 160px;
        }

    </style>
</head>

<body>
    
        <?php include_once('includes/header.php');?>
        <!-- Content -->
        <div class="content">
        <!-- Animated -->
        <div class="animated fadeIn">
            <!-- Widgets  -->
            <div class="row">
                <div class="col-lg-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-widget-five">
                                <?php
                                $uid=$_SESSION['vpmsuid'];
                                $ret=mysqli_query($con,"select * from tblregusers where ID='$uid'");
                                while ($row=mysqli_fetch_array($ret)) { ?> 
                                    <div class="stat-icon dib flat-color-1">
                                        Welcome <?php  echo $row['FirstName'];?> <?php  echo $row['LastName'];?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    // Define the total number of parking spaces
                    $total_parking_spaces = 50;

                    // Get the count from tblregusers where vStatus is 'IN'
                    $query_regusers = mysqli_query($con, "SELECT ID FROM tblregusers WHERE vStatus = 'IN'");
                    $active_parking_spaces_regusers = mysqli_num_rows($query_regusers);

                    // Get the count from tblguest where vStatus is 'IN'
                    $query_guests = mysqli_query($con, "SELECT ID FROM tblguest WHERE vStatus = 'IN'");
                    $active_parking_spaces_guests = mysqli_num_rows($query_guests);

                    // Calculate the total number of active parking spaces
                    $total_active_parking_spaces = $active_parking_spaces_regusers + $active_parking_spaces_guests;

                    // Calculate the number of available parking spaces
                    $available_parking_spaces = $total_parking_spaces - $total_active_parking_spaces;

                    ?>

                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-1">
                                    <i class="fa-solid fa-motorcycle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-text">
                                                
                                                    <?php echo $total_active_parking_spaces . '/' . $total_parking_spaces; ?>
                                                
                                            </div>
                                            <div class="stat-heading">Parking Occupied/Total</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Widgets -->      
        </div>
        <!-- .animated -->
    </div>

        <!-- /.content -->
        
        
        <div class="clearfix"></div>
        <?php
        $stmt = $con->prepare('SELECT status FROM tblregusers WHERE ID = ?');
        $stmt->bind_param('i', $_SESSION['vpmsuid']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user['status'] == 'pending') {
            ?>
            <div style="text-align:center">
                <button id="pay-button" class="btn btn-success btn-flat m-b-30 m-t-30">PAY</button>
            </div>

            <?php
          }
        
          if ($user['status'] == 'accepted') {

            
    
            include "../phpqrcode/qrlib.php"; 

            // Get user ID from session
            $userID = $_SESSION['vpmsuid'];

            // Generate a random token
            $token = bin2hex(random_bytes(16)); // 16 bytes = 128 bits

            // Fetch user's qrimage from the database
            $sql = "SELECT qrimage, Status FROM tblregusers WHERE ID = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $qrImage = $user['qrimage'];
            $status = $user['Status'];
            $stmt->close();

            // If the user doesn't already have a QR image and status is "Accepted"
            if (empty($qrImage)) {
                $text = $token; 
                $filename = uniqid().".png";
                $ecc = 'H';
                $pixel_Size = 7;
                $frame_Size = 7;

                // Start output buffering
                ob_start();
                // Generate the QR code
                QRcode::png($text, false, $ecc, $pixel_Size, $frame_Size);  // False as second argument to output to buffer instead of file
                // Capture the output into a variable
                $imageData = ob_get_contents();
                // Clean (erase) the output buffer and turn off output buffering
                ob_end_clean();

                <!-- @TODO: replace projectId with your Google Cloud Project ID and replace keyFilePath with your own Service Key path  -->
                // Authenticate with Google Cloud
                $storage = new StorageClient([
                    'projectId' => 'your-project-id',
                    'keyFilePath' => 'path/to/your/credentials.json'
                ]);
                <!-- @TODO: insert your Cloud Storage Bucket name -->
                // The name for the new bucket
                $bucketName = 'your-bucket-name';

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
        }
        ?>                              


        <!-- Footer -->
        <?php include_once('includes/footer.php');?>


    <!-- /#right-panel -->

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="../admin/assets/js/main.js"></script>

    <!--  Chart js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.bundle.min.js"></script>

    <!--Chartist Chart-->
    <script src="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartist-plugin-legend@0.6.2/chartist-plugin-legend.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot-pie@1.0.0/src/jquery.flot.pie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot-spline@0.0.1/js/jquery.flot.spline.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/simpleweather@3.1.0/jquery.simpleWeather.min.js"></script>
    <script src="../admin/assets/js/init/weather-init.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/moment@2.22.2/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.js"></script>
    <script src="../admin/assets/js/init/fullcalendar-init.js"></script>
    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            window.snap.pay('<?php echo $_SESSION['snapToken']; ?>', {
            onSuccess: function(result){
                alert("payment success!"); console.log(result);
            },
            onPending: function(result){
                alert("wating your payment!"); console.log(result);
            },
            onError: function(result){
                alert("payment failed!"); console.log(result);
            },
            onClose: function(){
                alert('you closed the popup without finishing the payment');
            }
            })
    });
    </script>



    <!--Local Stuff-->
    <script>
        jQuery(document).ready(function($) {
            "use strict";

            // Pie chart flotPie1
            var piedata = [
                { label: "Desktop visits", data: [[1,32]], color: '#5c6bc0'},
                { label: "Tab visits", data: [[1,33]], color: '#ef5350'},
                { label: "Mobile visits", data: [[1,35]], color: '#66bb6a'}
            ];

            $.plot('#flotPie1', piedata, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        innerRadius: 0.65,
                        label: {
                            show: true,
                            radius: 2/3,
                            threshold: 1
                        },
                        stroke: {
                            width: 0
                        }
                    }
                },
                grid: {
                    hoverable: true,
                    clickable: true
                }
            });
            // Pie chart flotPie1  End
            // cellPaiChart
            var cellPaiChart = [
                { label: "Direct Sell", data: [[1,65]], color: '#5b83de'},
                { label: "Channel Sell", data: [[1,35]], color: '#00bfa5'}
            ];
            $.plot('#cellPaiChart', cellPaiChart, {
                series: {
                    pie: {
                        show: true,
                        stroke: {
                            width: 0
                        }
                    }
                },
                legend: {
                    show: false
                },grid: {
                    hoverable: true,
                    clickable: true
                }

            });
            // cellPaiChart End
            // Line Chart  #flotLine5
            var newCust = [[0, 3], [1, 5], [2,4], [3, 7], [4, 9], [5, 3], [6, 6], [7, 4], [8, 10]];

            var plot = $.plot($('#flotLine5'),[{
                data: newCust,
                label: 'New Data Flow',
                color: '#fff'
            }],
            {
                series: {
                    lines: {
                        show: true,
                        lineColor: '#fff',
                        lineWidth: 2
                    },
                    points: {
                        show: true,
                        fill: true,
                        fillColor: "#ffffff",
                        symbol: "circle",
                        radius: 3
                    },
                    shadowSize: 0
                },
                points: {
                    show: true,
                },
                legend: {
                    show: false
                },
                grid: {
                    show: false
                }
            });
            // Line Chart  #flotLine5 End
            // Traffic Chart using chartist
            if ($('#traffic-chart').length) {
                var chart = new Chartist.Line('#traffic-chart', {
                  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                  series: [
                  [0, 18000, 35000,  25000,  22000,  0],
                  [0, 33000, 15000,  20000,  15000,  300],
                  [0, 15000, 28000,  15000,  30000,  5000]
                  ]
              }, {
                  low: 0,
                  showArea: true,
                  showLine: false,
                  showPoint: false,
                  fullWidth: true,
                  axisX: {
                    showGrid: true
                }
            });

                chart.on('draw', function(data) {
                    if(data.type === 'line' || data.type === 'area') {
                        data.element.animate({
                            d: {
                                begin: 2000 * data.index,
                                dur: 2000,
                                from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                                to: data.path.clone().stringify(),
                                easing: Chartist.Svg.Easing.easeOutQuint
                            }
                        });
                    }
                });
            }
            // Traffic Chart using chartist End
            //Traffic chart chart-js
            if ($('#TrafficChart').length) {
                var ctx = document.getElementById( "TrafficChart" );
                ctx.height = 150;
                var myChart = new Chart( ctx, {
                    type: 'line',
                    data: {
                        labels: [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul" ],
                        datasets: [
                        {
                            label: "Visit",
                            borderColor: "rgba(4, 73, 203,.09)",
                            borderWidth: "1",
                            backgroundColor: "rgba(4, 73, 203,.5)",
                            data: [ 0, 2900, 5000, 3300, 6000, 3250, 0 ]
                        },
                        {
                            label: "Bounce",
                            borderColor: "rgba(245, 23, 66, 0.9)",
                            borderWidth: "1",
                            backgroundColor: "rgba(245, 23, 66,.5)",
                            pointHighlightStroke: "rgba(245, 23, 66,.5)",
                            data: [ 0, 4200, 4500, 1600, 4200, 1500, 4000 ]
                        },
                        {
                            label: "Targeted",
                            borderColor: "rgba(40, 169, 46, 0.9)",
                            borderWidth: "1",
                            backgroundColor: "rgba(40, 169, 46, .5)",
                            pointHighlightStroke: "rgba(40, 169, 46,.5)",
                            data: [1000, 5200, 3600, 2600, 4200, 5300, 0 ]
                        }
                        ]
                    },
                    options: {
                        responsive: true,
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        }

                    }
                } );
            }
            //Traffic chart chart-js  End
            // Bar Chart #flotBarChart
            $.plot("#flotBarChart", [{
                data: [[0, 18], [2, 8], [4, 5], [6, 13],[8,5], [10,7],[12,4], [14,6],[16,15], [18, 9],[20,17], [22,7],[24,4], [26,9],[28,11]],
                bars: {
                    show: true,
                    lineWidth: 0,
                    fillColor: '#ffffff8a'
                }
            }], {
                grid: {
                    show: false
                }
            });
            // Bar Chart #flotBarChart End
        });
    </script>
</body>
</html>
<?php } ?>
