
window.onload = function() {
    var verificationIcon = document.getElementById('verificationIcon');
    verificationIcon.classList.add("bxs-x-circle");
    verificationIcon.classList.add("text-danger");


    const submitButton = document.querySelector('#activateOnEmail'); 
const classNameToCheck = 'activateTrue'; 

if (!document.body.classList.contains(classNameToCheck)) {
  submitButton.disabled = true;
}

  }


  window.onload = function() {
    var verificationIcon = document.getElementById('verificationIcon');
    verificationIcon.classList.add("bxs-x-circle");
    verificationIcon.classList.add("text-danger");


    const submitButton = document.querySelector('#activateOnEmail'); 
const classNameToCheck = 'activateTrue'; 

if (!document.body.classList.contains(classNameToCheck)) {
  submitButton.disabled = true;
}

  }




function showDiv() {

    
}

canS = true;

function sendMe() {
  // get the email from the form
  var email = $('#email').val();

  // check if request can be sent
  if (canS) {
    // send an AJAX request to the server to send the email
    $.ajax({
      type: 'POST',
      url: 'mail.php', // replace with your server-side script URL
      data: {
        email: email
      },
      success: function(response) {
        $('#message').text(response); // display the response message
      }
    });
    
    var emailInput = document.getElementById("email");
    var errorDiv = document.getElementById("errorDiv");

    if (emailInput.value !== "") {
        errorDiv.classList.add("show");
    } else {
        errorDiv.classList.remove("show");
    }

    // set flag to false to prevent further requests
    canS = false;

    // allow next request after 1 minute
    setTimeout(function() {
      canS = true;
    }, 60000); // 1 minute in milliseconds
  } else {
    // display error message if request cannot be sent
    $('#message').text('Please wait for 1 minute before sending another request');
  }
}

$(document).ready(function() {
  let canSendEmail = true; // initialize flag to true

  function sendVerificationEmail(email) {
    // send an AJAX request to the server to send the email
    $.ajax({
      type: 'POST',
      url: 'mail.php', // replace with your server-side script URL
      data: {
        email: email
      },
      success: function(response) {
        $('#message').text(response); // display the response message
      }
    });
  }

  function handleSendEmailClick(e) {
    e.preventDefault(); // prevent default form submission

    // get the email from the form
    var email = $('#email').val();

    // check if request can be sent
    if (canSendEmail) {
      sendVerificationEmail(email);

      // set flag to false to prevent further requests
      canSendEmail = false;

      // allow next request after 1 minute
      setTimeout(function() {
        canSendEmail = true;
      }, 60000); // 1 minute in milliseconds
    } else {
      // display error message if request cannot be sent
      $('#message').text('Please wait for 1 minute before sending another request');
    }
  }

  function handleVerifyOtpClick(e) {
    e.preventDefault(); // prevent default form submission

    // Verify The Otp From Gmail
    var errorDiv = document.getElementById("errorDiv");
    let otpCode = document.getElementById('otp').value;
    let thisOtpCode = document.getElementById('message').textContent;
    //Get The data from the DOM
    if (otpCode === thisOtpCode) {
      var verificationIcon = document.getElementById('verificationIcon');
      verificationIcon.classList.remove('text-danger');
      verificationIcon.classList.remove("bxs-x-circle");
      verificationIcon.classList.add("bxs-check-shield");
      verificationIcon.classList.add("activateTrue");

      //Enable the Submit Button
      const submitButton = document.querySelector('#activateOnEmail');
      submitButton.disabled = false;
      errorDiv.classList.remove("show");
    } else {
      alert("No match");
    }
  }

  $('#sendVerificationEmail').click(handleSendEmailClick);
  $('#verifyOtp').click(handleVerifyOtpClick);
});

function showDiv() {

  var email = $('#email').val();

  // check if request can be sent
  if (canS) {
    // send an AJAX request to the server to send the email
    $.ajax({
      type: 'POST',
      url: 'mail.php', // replace with your server-side script URL
      data: {
        email: email
      },
      success: function(response) {
        $('#message').text(response); // display the response message
      }
    });
    
    var emailInput = document.getElementById("email");
    var errorDiv = document.getElementById("errorDiv");

    if (emailInput.value !== "") {
        errorDiv.classList.add("show");
    } else {
        errorDiv.classList.remove("show");
    }

    // set flag to false to prevent further requests
    canS = false;

    // allow next request after 30 seconds
    var count = 30;
    var countdown = setInterval(function() {
      $('#errMessage').text('Please wait for ' + count + ' seconds before sending another request');
      count--;
      if (count === 0) {
        clearInterval(countdown);
        canS = true;
        $('#errMessage').text('You can now send another request');
      }
    }, 1000);

  } else {
    // display error message if request cannot be sent
    $('#errMessage').text('Please wait for ' + count + ' seconds before sending another request');
  }

  var emailInput = document.getElementById("email");
  var errorDiv = document.getElementById("errorDiv");

  if (emailInput.value != "") {
    errorDiv.classList.remove("hidden");
  } else {
  }
}





$(document).ready(function() {
    $('#sendVerificationEmail').click(function(e) {
      e.preventDefault(); // prevent default form submission
  
      // get the email from the form
        var email = $('#email').val();
        

        //bxs-x-circle  bxs-check-shield
  
      // send an AJAX request to the server to send the email
      $.ajax({
        type: 'POST',
        url: 'mail.php', // replace with your server-side script URL
        data: {
          email: email
        },
        success: function(response) {
          $('#message').text(response); // display the response message
        }
      });
    });

    $('#verifyOtp').click(function(e) {
        e.preventDefault(); // prevent default form submission
    
        // get the email from the form
        var email = $('#email').val();
    
        // Verify The Otp From Gmail
        var errorDiv = document.getElementById("errorDiv");
        let otpCode = document.getElementById('otp').value;
        let thisOtpCode = document.getElementById('message').textContent;
        //Get The data from the DOM
        if (otpCode === thisOtpCode) {

            var errorDiv = document.getElementById("errorDiv");
            var verificationIcon = document.getElementById('verificationIcon');


            verificationIcon.classList.remove('text-danger');
            verificationIcon.classList.remove("bxs-x-circle");
            verificationIcon.classList.add("bxs-check-shield");
            verificationIcon.classList.add("activateTrue");


            //Enable the Submit Button
            const submitButton = document.querySelector('#activateOnEmail'); 
            submitButton.disabled = false;
            errorDiv.classList.add("hidden");

          } else {
              alert("No match");
          }
        
      });

});

  

function closeErrorDiv() {
    var errorDiv = document.getElementById("errorDiv");
    errorDiv.classList.add("hidden");
}
  



//TODO Finish editing the verification TOAST
//DO client side verification###