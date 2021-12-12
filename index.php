<?php
   header("Refresh:60");
   session_start();
   require("dbconnect.php");
   
   $dateShow =  (new \DateTime())->format('d/m/Y');
   $dateNow =  (new \DateTime())->format('Y-m-d');
   $dateNow = "'".$dateNow."'";
    
   $query = "SELECT ROW_NUMBER() OVER(PARTITION BY RecData) AS row_num,Humidity,Smoke,
                    IdTempData,TempData, TIME_FORMAT(RecData, '%H:%i') AS RecData
             FROM tbtempdata 
             WHERE RecData >= $dateNow";
   $resultchart = mysqli_query($conn, $query);
   $rs = mysqli_fetch_array($resultchart);
   if($rs <> NULL) {
        //for chart
        $RecData = array();
        $Tempurature = array();
        $Humidity = array();
        $Smoke = array();
        $resultchart = mysqli_query($conn, $query);        
        while($rs = mysqli_fetch_array($resultchart)){ 
        $RecData[] = "\"".$rs['RecData']."\""; 
        $Tempurature[] = "\"".$rs['TempData']."\""; 
        $Humidity[] = "\"".$rs['Humidity']."\""; 
        $Smoke[] = "\"".$rs['Smoke']."\""; 
        }
        $RecData = implode(",", $RecData); 
        $Tempurature = implode(",", $Tempurature); 
        $Humidity = implode(",", $Humidity); 
        $Smoke = implode(",", $Smoke); 

        $queryAVG = "SELECT TempData,Humidity,Smoke
                    FROM tbtempdata
                    WHERE RecData >= $dateNow
                    ORDER BY IdTempData DESC LIMIT 1";
        $resultAvg = mysqli_query($conn, $queryAVG);
        $rowAvg = mysqli_fetch_array($resultAvg);
        $AvgTemp = $rowAvg['TempData']; 
        $HumidityAvg = $rowAvg['Humidity'];
        $SmokeAvg = $rowAvg['Smoke'];
    } else {
        $AvgTemp = 0.00; 
        $HumidityAvg = 0.00;
        $SmokeAvg = 0.00;
    }
  
?>



<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบอุณภูมิห้อง Server</title>
    <link rel="icon" href="src/img/chart.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
        integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
        type="text/css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">

</head>

<style>
body {
    font-family: "Audiowide", sans-serif;
}

h1 {
    font-size: 45px;
}

 nav {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    /* background: #ccc; */
    padding: 40px;
}

section {
    display: -webkit-flex;
    display: flex;
}

article {
    -webkit-flex: 3;
    -ms-flex: 3;
    flex: 3;
    background-color: #f1f1f1;
    padding: 30px;
}

@media (max-width: 1000px) {
    section {
        -webkit-flex-direction: column;
        flex-direction: column;
    }
} */
</style>

<body>
    <nav class="navbar navbar-expand-md bg-primary navbar-dark">
        <a class="navbar-brand" href="#">ระบบอุณภูมิห้อง Server</a>
        <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav mr-auto">
            </ul>
            <ul class="navbar-nav navbar-right">
                <a href="login.php" class="btn btn-warning">Login</a>
            </ul>
        </div>
    </nav>
    <div class="container-fluid">
        <section>
            <nav>
                <h4>Date : <?php echo $dateShow; ?></h4><br>
                <label for="">Temp</label>
                <!-- <h1 style="color:#444444;background-color:#00ff00;text-align:center"> -->
                <h1 style="color:#ffffff;background-color:#ff0000;text-align:center">
                    <?php echo number_format($AvgTemp,2).' &#176;C'; ?></h1>
                <label for="">Humidity</label>
                <h1 style="color:#ffffff;background-color:#1E90FF;text-align:center">
                    <?php echo number_format($HumidityAvg,2).' %' ?></h1>
                <label for="">Smoke</label>
                <h1 style="color:#ffffff;background-color:#008000;text-align:center">
                    <?php echo number_format($SmokeAvg,2).' ppm' ?></h1>
            </nav>
            <article>
                <div class="row">
                    <div class="col-lg-8 col-md-12 col-12">
                        <canvas id="myChart1" style="width:100%;"></canvas>
                    </div>
                </div>
            </article>
        </section>
    </div>


    <script>
    var xValues = [<?php echo $RecData; ?>];

    new Chart("myChart1", {
        type: "line",
        data: {
            labels: xValues,
            datasets: [{
                label: "Temp",
                data: [<?php echo $Tempurature; ?>],
                borderColor: "#ff0000",
                fill: false
            }, {
                label: "Humidity",
                data: [<?php echo $Humidity; ?>],
                borderColor: "#1E90FF",
                fill: false
            }]
        },
        options: {
            legend: {
                display: true
            },
            scales: {
                yAxes: [{
                    ticks: {
                        min: 0,
                        max: 80
                    }
                }],
            }
        }
    });
    </script>


</body>

</html>