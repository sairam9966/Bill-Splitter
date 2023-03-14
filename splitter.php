<?php
session_start();
require('./includes/db.php');
if (!$_SESSION['user']) {
  header("location:login.php");
}

$groupname = $_GET['groupname'];
$adminnumber = $_SESSION['user'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous">
  </script>
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
  <script src="https://kit.fontawesome.com/1043f0857c.js" crossorigin="anonymous"></script>
  <title>Splitter</title>

  <style>
    h5 {
      display: block;
      color: blue;
      text-align: center;

      height: 40px;
      width: 100%;
      background-color: pink
    }

    h4 {
      display: block;
      color: yellowgreen;
      text-align: center;
      height: 50px;
      width: 100%;
      background-color: black;
    }
  </style>
  </ </head>

<body class="container">
  <table class=" table">
    <thead class="thead-dark">
      <tr>
        <th scope="col">id</th>
        <th scope="col">Name of Member</th>
        <th scope="col">Phone number</th>
        <th scope="col">Mail id</th>
        <th scope="col">Amount Spended</th>
      </tr>
    </thead>
    <tbody><?php $memmbers = 0;
            $count = 0;
            $members = 0;

            $paymentlist = array();

            $needtopay = array();

            $size = 0;
            $teamsize = "select * from teams where adminnumber='$adminnumber' and groupname='$groupname'";
            $distinct = "SELECT DISTINCT paidnumber FROM `expenses` WHERE adminnumber='. $adminnumber .' and groupname='. $groupname.'";
            $exeteam = mysqli_query($conn, $teamsize);

            $distinctexecuted = mysqli_query($conn, $distinct);

            while ($rows = mysqli_fetch_assoc($exeteam)) {


              $size++;
              $flag = 0;

              while ($row = mysqli_fetch_assoc($distinctexecuted)) {
                $members++;
                $count++;
              }
            }




            $distinct = "select name,phno,mail from users where phno in (SELECT DISTINCT paidnumber FROM `expenses` WHERE adminnumber='" . $adminnumber . "' and groupname='" . $groupname . "')";

            $distinctexecuted = mysqli_query($conn, $distinct);

            while ($row = mysqli_fetch_assoc($distinctexecuted)) {
              $members++;
              $count++;
            ?>
        <th scope="row"><?php echo $count;
                        ?></th>
        <td><?php echo $row['name'];
            ?></td>
        <td><?php echo $row['phno'];
            ?></td>
        <td><?php echo $row['mail'];
            ?></td><?php $fetchpaidamount = "select amount from transexpenses WHERE adminnumber='" . $adminnumber . "' and groupname='" . $groupname . "' and paymentnumber='" . $row['phno'] . "'";

                    $executefetchpaidamount = mysqli_query($conn, $fetchpaidamount);

                    if (!$executefetchpaidamount) {
                      echo mysqli_error($conn);
                    }

                    while ($rows = mysqli_fetch_assoc($executefetchpaidamount)) {
                    ?><td><?php echo $rows['amount'] ?></td><?php $paymentlist[$row['phno']] = $rows['amount'];
                                                          }

                                                            ?>
        </tr><?php
            }

              ?>
    </tbody>
  </table>
  <h6>Dash board which consists of all the transactions of the group </h6><br>
  <table class="table">
    <thead class="thead-dark">
      <tr>
        <th scope="col">id</th>
        <th scope="col">paid number</th>
        <th scope="col">Amount</th>
        <th scope="col">description</th>
        <th scope="col">date</th>
        <th scope="col">time</th>
      </tr>
    </thead>
    <tbody><?php $count = 0;
            $totalexpenses = 0;
            $retreive = " SELECT * FROM `expenses` WHERE adminnumber='" . $adminnumber . "' and groupname='" . $groupname . "'";

            $data = mysqli_query($conn, $retreive);

            while ($row = mysqli_fetch_assoc($data)) {
              $count++;
              $totalexpenses = $totalexpenses + (int)$row['amount'];
            ?><tr>
          <th scope="row"><?php echo $count;
                          ?></th>
          <td><?php echo $row['paidnumber'];
              ?></td>
          <td><?php echo $row['amount'];
              ?></td>
          <td><?php echo $row['description'];
              ?></td>
          <td><?php echo $row['date'];
              ?></td>
          <td><?php echo $row['time'];
              ?></td>
        </tr><?php
            }

              ?></tbody>
  </table><?php echo "<br>";
          echo "<br>";
          ?><h2>Total Expenses in the group : <?php echo $totalexpenses;
                                              ?></h2><?php echo "<br>";
                                                      ?><h2>Total number of members in the group : <?php echo $members;
                                                                                                    ?></h2><?php echo "<br>";



                                                                                                            $averageamount = $totalexpenses / $members;
                                                                                                            ?><h5>
    average
    amount : <?php echo round($averageamount, 2);
              ?></h5><?php

                      foreach ($paymentlist as $number => $amount) {
                        $needtopay[$number] = $averageamount - $amount;
                      }

                      echo "<br>";
                      $positives = array();

                      $negatives = array();

                      $percentages = array();

                      $negativesum = 0;

                      foreach ($needtopay as $number => $amount) {
                        if ($amount < 0) {
                          $negatives[$number] = $amount;
                          $negativesum = $negativesum + abs($amount);
                      ?><h5><?php echo $number . " need to get :" . round(abs($amount), 2);
                            ?></h5><?php
                                  } else {
                                    $positives[$number] = $amount;
                                    ?><h5><?php echo $number . " need to pay :" . round(abs($amount), 2);
                                          ?></h5><?php
                                                }
                                              }


                                              foreach ($positives as $number => $amount) {
                                                $percentages[$number] = $amount / $negativesum;
                                              }


                                              echo "<br>";
                                              echo "<br>";

                                              $deletenotification = "delete from notification where adminnumber='$adminnumber' and groupname='$groupname'";
                                              $notificationdeleted = mysqli_query($conn, $deletenotification);

                                              foreach ($positives as $number => $amount) {
                                                $percentage = $percentages[$number];

                                                foreach ($negatives as $number2 => $amount2) {
                                                  // echo "<br>";
                                                  $amounttopay = abs($percentage * $amount2);
                                                  ?><h4><?php echo $number . " need to give to " . $number2 . " of " . round($amounttopay, 2);
                                                        ?></h4><?php

                                                                $insertnotification = "insert into notification values
(DEFAULT, '$adminnumber', '$groupname', '$number', '$number2', '$amounttopay', 0, 0, DEFAULT)";
                                                                $executenotification = mysqli_query($conn, $insertnotification);
                                                              }
                                                            }


                                                                ?>
</body>

</html>