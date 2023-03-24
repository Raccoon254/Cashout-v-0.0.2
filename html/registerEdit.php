<?php
require_once '../db_connect.php';

// check if the user has been referred
if (isset($_GET['x'])) {
    $code = htmlspecialchars($_GET['x']);
} else {
    $code = "";
}
// check if the form has been submitted
if (isset($_POST['submit'])) {
    // retrieve form data
    $name = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $refer_code = generateReferralCode($conn);
    $balance = 0;
    if ($code == null) {
        $referral_code = $_POST['referralCode'];
    } else {
        $referral_code = $code;
    }

    // check if referral code is valid
    if ($referral_code) {
        $query = "SELECT * FROM users WHERE referral_code=:referral_code";
        $stmt = $conn->prepare($query);
        $stmt->execute(array(':referral_code' => $referral_code));
        if ($stmt->rowCount() == 0) {
            $referral_code = null; // if the referral code is invalid, set it to null
        }
    }

    // check if the email already exists
    $query = "SELECT * FROM users WHERE email=:email";
    $stmt = $conn->prepare($query);
    $stmt->execute(array(':email' => $email));
    if ($stmt->rowCount() > 0) {
        echo '
        <div class="bs-toast toast toast-placement-ex m-2 fade bg-danger top-50 start-50 translate-middle show" role="alert" aria-live="assertive" aria-atomic="true" data-delay="1000">
                <div class="toast-header">
                  <i class="bx bx-bell me-2"></i>
                  <div class="me-auto fw-semibold">Error</div>
                  <small>1 second ago</small>
                  <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">Either you name or Email is already registered.</div>
              </div>
      ';
    } else {
        $query = "SELECT * FROM users WHERE name=:name";
        $stmt = $conn->prepare($query);
        $stmt->execute(array(':name' => $name));
        if ($stmt->rowCount() > 0) {
            echo "Sorry, that name already exists. Please try again.";
        } else {
            // insert the new user into the Users table
            $query = "INSERT INTO users (name, email, password, referral_code, balance, referred_by)
                  VALUES (:name, :email, :password, :refer_code, :balance, :referral_code)";
            $stmt = $conn->prepare($query);
            $result = $stmt->execute(array(':name' => $name, ':email' => $email, ':password' => $password, ':refer_code' => $refer_code, ':balance' => $balance, ':referral_code' => $referral_code));
            if ($result) {
                if ($referral_code) {
                    $emailAssocUserQuery="SELECT * FROM users WHERE referral_code=:referral_code";
                    $stmt = $conn->prepare($emailAssocUserQuery);
                    $stmt->execute(array(':referral_code' => $referral_code));
                    $rowUser = $stmt->fetch();
                    $emailAssocUser = $rowUser['email'];
                    $referrerName=$rowUser['name'];

                    $referredAssocUser = $rowUser['referred_by'];
                    $emailAssocJoining="$email";
                    $descriptionAssoc="";
                    $earningsUpdateDetails="INSERT INTO refferraldetails(referrer, referred, type) VALUES (:emailAssocUser, :emailAssocJoining, :descriptionAssoc)";
                    $query_upd = "UPDATE users SET balance = balance + 50 where referral_code = :referral_code";

                    $dd=$referredAssocUser;
                    $em="SELECT email FROM users WHERE referral_code=:dd";
                    $stmt = $conn->prepare($em);
                    $stmt->execute(array(':dd' => $dd));
                    $ro = $stmt->fetch();
                    

                    $emai= $ro['email'];
                    
                    $emailOne="$emailAssocUser";
                    $emailTwo="$emai";
                    $emailCurrent="$email";
                    $amountOne=50;
                    $amountTwo=20;
                    $amountCurrent=100;
                    $typeOne='direct';
                    $typeTwo='indirect';
                    $typeCurrent='Joined';
    
                    $queryUpdateForSecondLevel = "UPDATE users SET balance = balance + 20 where referral_code = :referredAssocUser";
                    $stmt = $conn->prepare($queryUpdateForSecondLevel);
                    $stmt->execute(array(':referredAssocUser' => $referredAssocUser));
    
                    $transactionUpdateOne="INSERT INTO transactions(email, amount, type) VALUES (:emailOne,:amountOne,:typeOne)";
                    $stmt = $conn->prepare($transactionUpdateOne);
                    $stmt->execute(array(':emailOne' => $emailOne, ':amountOne' => $amountOne, ':typeOne' => $typeOne));
    
                    $transactionUpdateTwo="INSERT INTO transactions(email, amount, type) VALUES (:emailTwo,:amountTwo,:typeTwo)";
                    $stmt = $conn->prepare($transactionUpdateTwo);
                    $stmt->execute(array(':emailTwo' => $emailTwo, ':amountTwo' => $amountTwo, ':typeTwo' => $typeTwo));
    
                    $transactionUpdateCurrent="INSERT INTO transactions(email, amount, type) VALUES (:emailCurrent,:amountCurrent,:typeCurrent)";
                    $stmt = $conn->prepare($transactionUpdateCurrent);
                    $stmt->execute(array(':emailCurrent' => $emailCurrent, ':amountCurrent' => $amountCurrent, ':typeCurrent' => $typeCurrent));
    
                    $earningsUpdateDetails="INSERT INTO refferraldetails(referrer, referred, type) VALUES (:emailAssocUser,:emailAssocJoining,:descriptionAssoc)";
                    $stmt = $conn->prepare($earningsUpdateDetails);
                    $stmt->execute(array(':emailAssocUser' => $emailAssocUser, ':emailAssocJoining' => $emailAssocJoining, ':descriptionAssoc' => $descriptionAssoc));
    
                    $query_upd = "UPDATE users SET balance = balance + 50 where referral_code = :referral_code";
                    $stmt = $conn->prepare($query_upd);
                    $stmt->execute(array(':referral_code' => $referral_code));
    
                }
                // redirect the user to the login page
                header("Refresh:1; url=index.php");
            } else {
                echo "Sorry, there was an error. Please try again.";
            }
        }
    }
}    

function generateReferralCode($conn)
{
    // Generate a random string of 6 characters
    $code = strtoupper(substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789", 6)), 0, 6));

    // Check if the code is already in use
    $check_query = "SELECT * FROM users WHERE referral_code=:code";
    $stmt = $conn->prepare($check_query);
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if (count($result) > 0) {
        // If the code is already in use, generate a new one
        generateReferralCode($conn);
    } else {
        // If the code is unique, return it
        return $code;
    }
}

//create the form

//echo"$nameUser";

echo '



<!DOCTYPE html>

<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <meta name="description"
        content="Cashout is a simple and convenient platform for managing your finances. It allows you to track your spending, move money between accounts, and make purchases easily and securely. Whether youre paying bills, buying groceries, or saving for a big purchase, Cashout is the perfect tool to help you keep your finances organized and under control.">
    

    <title>Join Cashout | Earn</title>
    

    
    <!--------------------------Added Jquerry------------------------------------------------------>
  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="./verification.js"></script>
        <link rel="stylesheet" href="./custom.css" class="template-customizer-core-css" />
    <!-------------------------------------------------------------------------------->


    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/avatars/Cash OUT Co .png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register Card -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="bg-success app-brand justify-content-center" style="border: 1px solid blue; border-radius: 10px; padding: 5px 0px 5px 0px;">
                <a href="index.html" class="app-brand-link gap-2">
                  <span class="">
                    <img width="200px" src="../assets/img/avatars/Cash out Typography .png" alt="Cash Out Logo" />
                  </span>
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2">Cash Flow Starts Here 🚀</h4>
              <p class="mb-4">Make your cash and advert management easy and fun!</p>

              <form id="formAuthentication" class="mb-3" action="registerEdit.php" method="POST">
                <div class="mb-3">
                  <label for="username" class="form-label">Name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    placeholder="Enter your full name"
                    autofocus
                  />
                </div>

                <div class="mb-3">
                
                  <label for="email" class="form-label">Email
                  <span class="text-primary"><i id="verificationIcon" class="bx bx-flashing" ></i></span>
                  </label>
                  <div class="input-wrapper">
                  <button type="button" class="rounded btn border-secondary email-button"  onclick="showDiv()">Send OTP</button>
                  <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" />
                </div>

                                
                 
               <!------------------------------------------------------------------------------->
                                  
                <div id="errorDiv" class=" mt-2 rounded bg-success">
                
                
                  <div class="toast-header pb-0">
                    <i class="bx bx-bell me-2"></i>
                    <div class="me-auto fw-semibold">Notification</div>
                    <small>Alert</small>
                    <button type="button" class="btn-close" onclick="closeErrorDiv()" aria-label="Close"></button>
                  </div>
                  <div class="toast-body">

                  OTP Verification Required ! <br>
                  <div class="d-flex mb-2">

      
                                    
                  <input
                    type="text"
                    class="form-control w-100"
                    id="otp"
                    name="otp"
                    placeholder="Enter OTP"
                    autofocus
                    required
                  />
                  
                  
                </div>
                <input id="verifyOtp" class="btn btn-primary d-block btn-user w-100" type="submit" name="verifyOtp" value="Verify OTP"/>
                  
                  </div>
                </div>
                
               <!------------------------------------------------------------------------------->

               <div id="message" class="hidden"></div>
               
               <div id="errMessage" class=""></div>



               <!-------------------------------------->


               
                  </div>
                
                <div class="mb-3 form-password-toggle">
                  <label class="form-label" for="password">Password</label>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password"
                      required
                    />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
                </div>
                ';


                if ($code==null) {
                  echo'
                  <div class="mb-3">
                  <label for="referralCode" class="form-label">Referral Code &nbsp;</label><span class="text-danger" style="font-weight: 2000;"><b>[ Optional* ]</b></span>
                  <input
                    type="text"
                    class="form-control"
                    id="referralCode"
                    name="referralCode"
                    placeholder="Enter Referral Code"
                    autofocus
                  />
                </div>
                  ';
                } else {
                    ;
$queryUser = "SELECT * FROM users WHERE referral_code = :referral_code";
$stmt = $conn->prepare($queryUser);
$stmt->bindParam(':referral_code', $code);
$stmt->execute();
$rowUser = $stmt->fetch(PDO::FETCH_ASSOC);
$nameUser = $rowUser['name'];

        
                  echo'
                  <div class="mb-3">
                  <label for="referralCode" class="form-label">Referred By &nbsp;</label>
                  <input
                    type="text"
                    class="form-control"
                    id="referralCode"
                    name="referralCode"
                    placeholder="'; echo"$nameUser"; echo'"
                    readonly
                  />
                </div>
                  ';
                }
                
                echo'
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" required />
                    <label class="form-check-label" for="terms-conditions">
                      I agree to
                      <a href="javascript:void(0);">privacy policy & terms</a>
                    </label>
                  </div>
                </div>

                <!----------------------->
                <input id="activateOnEmail" class="btn btn-primary d-block btn-user w-100" type="submit" name="submit" value="Sign Up"/>



                
              </form>

              <p class="text-center">
                <span>Already have an account?</span>
                <a href="index.php">
                  <span>Sign in instead</span>
                </a>
              </p>
            </div>
          </div>
          <!-- Register Card -->
        </div>
      </div>
    </div>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="./verification.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>




';
$conn=null;