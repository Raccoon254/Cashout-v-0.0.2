<?php
require_once '../../db_connect.php';
session_start();

if (!isset($_SESSION['email'])) {
  header("Location: index.php"); //redirect to login page if the user is not logged in
}

if (!isset($_SESSION['start_time'])) {
  $_SESSION['start_time'] = time();
}

$time_spent = time() - $_SESSION['start_time'];
$hours = floor($time_spent / 3600);
$mins = floor(($time_spent - ($hours * 3600)) / 60);
$secs = $time_spent - ($hours * 3600) - ($mins * 60);

// Retrieve the user's name and balance from the database
$email = $_SESSION['email'];
$query = "SELECT * FROM users WHERE email=:email";
$stmt = $conn->prepare($query);
$stmt->bindParam(':email', $email);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $row['name'];
$balance = $row['balance'];
$value = $row['referral_code'];
$multiplier = 50;

// Get the balance and previous balance for the logged in user
$email = $_SESSION['email']; // get the email from the logged in user
$sql = "SELECT balance, previous FROM users WHERE email=:email";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$balance = $row['balance'];
$previous = $row['previous'];

// Calculate the difference between the balance and previous balance
$difference = $balance - $previous;
$graphGenerator;

// Check if the difference is positive or negative
if ($difference > 0) {
  $graphGenerator = "p";
} elseif ($difference < 0) {
  $graphGenerator = "n";
} else {
  $graphGenerator = "c";
}

// Update the previous balance with the current balance
$update_sql = "UPDATE users SET previous=:balance WHERE email=:email";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bindParam(':balance', $balance);
$update_stmt->bindParam(':email', $email);
$update_stmt->execute();

$sql = "SELECT COUNT(*) as count FROM users WHERE referred_by = :value";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':value', $value);
$stmt->execute();
$rowT = $stmt->fetch(PDO::FETCH_ASSOC);
$countT = $rowT["count"];
$balanceFromReferring = $countT * $multiplier;



$stmt = $conn->prepare("SELECT SUM(prize) FROM spin WHERE email=:email");

// Bind the parameter to a value
$stmt->bindValue(':email', $email);

// Execute the query
$stmt->execute();

// Fetch the result into a variable
$balanceFromSpins = $stmt->fetchColumn();

echo '



<!DOCTYPE html>
<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>[ Spin To Win | Dashboard ]</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/raccoon/fav.png">

    <!-- @TODO make the font-size for the text on the page responsive too -->
    <link rel="stylesheet" href="./main.css" type="text/css" />
    <script type="text/javascript" src="./Winwheel.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenMax.min.js"></script>


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="../../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../../assets/vendor/js/helpers.js"></script>

    <script src="../../assets/js/config.js"></script>
  </head>



  <style>
    .homeIcon{
      font-size: 40px;
    }
  </style>




  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="../../html/dashboard.php" class="app-brand-link">
              <span class="app-brand-logo demo">
              
              </span>
              <span class="app-brand-text demo menu-text fw-bolder ms-2"><img width="150px" src="../../assets/img/raccoon/Cash Type Blend.png" alt="" srcset=""></span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item active">
              <a href="../../html/dashboard.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
              </a>
            </li>

            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Pages</span>
            </li>

            
                        <!-- Cards -->
                        <li class="menu-item">
                          <a href="../../html/settings.php" class="menu-link">
                            <i class="menu-icon bx bx-user-circle" ></i>
                            <div data-i18n="Basic">Account</div>
                          </a>
                        </li>
                    <style>
                      .myBadge{
                        padding: 0px;
                        margin: 0px 2px 0px;
                        width: 20px;
                        height: 20px;
                      }
                    </style>
            <!-- Cards -->
            <li class="menu-item">
              <a href="#" class="menu-link">
                <i class="menu-icon bx bx-dollar-circle"></i>
                <div data-i18n="Basic">Spin Win</div><span class="myBadge badge bg-white text-secondary"><i class="bx bxs-lock-alt" ></i></span>
              </a>
            </li>
            <!-- Cards -->
            <li class="menu-item">
              <a href="../../html/underRaccoonDev.html" class="menu-link">
                <i class="menu-icon bx bx-play-circle"></i>
                <div data-i18n="Basic">Ad to Cash</div><span class=" myBadge badge bg-white text-primary"><i class="bx bxs-lock-alt" ></i></span>
              </a>
            </li>

            <li class="menu-item">
              <a class="menu-link" href="../../html/underRaccoonDev.html">
                <i class="menu-icon bx bx-message-alt-add"></i>
                <div data-i18n="Support">Advertise</div><span class="myBadge badge bg-white text-success"><i class="bx bxs-lock-alt" ></i></span>
              </a>
            </li>

            <li class="menu-item">
              <a class="menu-link" href="../../html/index.php">
                <i class="menu-icon bx bx-power-off me-2"></i>
                <div data-i18n="Support">Logout</div>
              </a>
            </li>

            <li class="menu-item">
              <a class="menu-link" href="">
                <i class="menu-icon bx bx-info-circle"></i>
                <div data-i18n="About">About</div>
              </a>
            </li>
          </ul>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar"
          >
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <!-- Search -->
              <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                  <i class="bx bx-search fs-4 lh-0"></i>
                  <input
                    type="text"
                    class="form-control border-0 shadow-none"
                    placeholder="Search..."
                    aria-label="Search..."
                  />
                </div>
              </div>
              <!-- /Search -->

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
              

                <div class="mx-4">
                            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                              <button style="color:blue" type="button" class="btn btn-outline-primary">
                              <i class="bx bxs-credit-card-alt bx-tada bx-rotate-90" ></i>
                              </button>
                              <button style="color:blue" type="button" class="btn btn-outline-primary">
                              ';
                              echo "$balance";
                              echo'
                              </button>
                            </div>
                          </div>

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img width="200px" src="../../assets/img/dp.png" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <img width="200px" src="../../assets/img/dp.png" alt class="w-px-40 h-auto rounded-circle" />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-semibold d-block">
                            ';
echo "$name";
echo '
                            </span>
                            <small class="text-muted">User</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">My Profile</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-cog me-2"></i>
                        <span class="align-middle">Settings</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="#">
                        <span class="d-flex align-items-center align-middle">
                          <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                          <span class="flex-grow-1 align-middle">Transactions</span>
                          <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                        </span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="index.php">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav>

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row justify-content-around">



              <div class="spinHolder col-md-auto">
              <div align="center">
                  <br />
                  <div class="arrow">
                      <div class="square"></div>
                  </div>
                  <!-- Always set canvas to largest desired size, i.e. desktop PC size, it will be scaled down for smaller devices but never scaled up <button onClick="startSpin();">click me</button>-->
                  <canvas onClick="startSpin();" id="canvas" class="canvas" style="border: 2px dashed blue ; border-radius:50%" width="502" height="502"
                  data-responsiveMinWidth="180" data-responsiveScaleHeight="true">
                      
                  </canvas>

<style>
  #canvas-container {
    width: 100%;
    max-width: 502px;
  }

  #canvas {
    width: 100%;
    max-width: 450px;
    height: auto;
  }

  /* Inline breakpoints */
  @media (min-width: 576px) {
    #canvas-container {
      max-width: 526px;
    }
  }

  @media (min-width: 768px) {
    #canvas-container {
      max-width: 560px;
    }
  }

  @media (min-width: 992px) {
    #canvas-container {
      max-width: 580px;
    }
  }

  @media (min-width: 1200px) {
    #canvas-container {
      max-width: 590px;
    }
  }
</style>

                  <br /><br />
                  <p align="center">Tap the wheel to spin.</p>
              </div>
              <script src="./spin.js"></script>
</div>










';

$stmt = $conn->prepare("SELECT * FROM spin WHERE email=:email ORDER BY date DESC LIMIT 10");
$stmt->bindParam(':email', $email);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '
              <div class="card col-md-auto" style=" background-color: #fff0">
                <h5 class="card-header">Spin History</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Amount</th>
                        <th>Date</th>
                      </tr>
                    </thead>
                    
                    <tbody class="table-border-bottom-0">
                    ';
if (count($result) == 0) {
  echo "<p>Data will appear here after you spin.</p>";
} else {
  foreach ($result as $row) {
    echo '
                      <tr class="table-default">
                        <td>';
    echo $row['prize'];
    echo '</td>
                        <td><span class="badge bg-label-primary me-1">';
    echo $row['date'];
    echo '</span></td>
                      </tr>
                      ';
  }
}

echo '
                    </tbody>
                  </table>
                </div>
              </div>




              </div>


<!-- / Content -->

<!-- Footer -->
<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
        <div class="mb-2 mb-md-0">
            ©
            <script>
            document.write(new Date().getFullYear());
            </script>
            , made with ❤️ by
            <a href="https://steve1is2the3best4designer.on.drv.tw/stevosoro.com" target="_blank"
                class="footer-link fw-bolder">raccoon254</a>
        </div>
    </div>
</footer>
<!-- / Footer -->

<div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
</div>
<!-- / Layout page -->
</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="../../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../../assets/vendor/libs/popper/popper.js"></script>
<script src="../../assets/vendor/js/bootstrap.js"></script>
<script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

<script src="../../assets/vendor/js/menu.js"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="../../assets/vendor/libs/apex-charts/apexcharts.js"></script>

<!-- Main JS -->
<script src="../../assets/js/main.js"></script>

<!-- Page JS -->
<script src="../../assets/js/dashboards-analytics.js"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>

';
$conn = null;
