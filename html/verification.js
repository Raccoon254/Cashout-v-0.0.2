

  window.onload = function() {
    var verificationIcon = document.getElementById('verificationIcon');
    verificationIcon.classList.add("bx-loader-alt");
    verificationIcon.classList.add("text-danger");


const submitButton = document.querySelector('#activateOnEmail'); 
const classNameToCheck = 'activateTrue'; 

if (!document.body.classList.contains(classNameToCheck)) {
  submitButton.disabled = true;
}

  }



canSend = true;
function showDiv() {
  var email = $('#email').val();
  $('#thisEmail').text(email);

  // check if request can be sent


  var emailInput = document.getElementById("email");
  var errorDiv = document.getElementById("errorDiv");

  if (emailInput.value != "") {
    errorDiv.classList.remove("hidden");
    if (canSend) {
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
      canSend = false;
  
      // allow next request after 30 seconds
      var count = 30;
      var countdown = setInterval(function() {
        $('#errMessage').text('Please wait for ' + count + ' seconds before sending another request');
        count--;
        if (count === 0) {
          clearInterval(countdown);
          canSend = true;
          $('#errMessage').text('You can now send another request');
        }
      }, 1000);
  
    } else {
      // display error message if request cannot be sent
      alert("Please Wait");
      $('#errMessage').text('Please wait for ' + count + ' seconds before sending another request');
    }
  } else {
    alert("Enter Email");
  }
}

  

function closeErrorDiv() {
    var errorDiv = document.getElementById("errorDiv");
    errorDiv.classList.add("hidden");
}
  


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
      verificationIcon.classList.remove("bx-loader-alt");
      verificationIcon.classList.remove("bx-spin");
      verificationIcon.style.fontSize="medium";
      verificationIcon.classList.add("bxs-check-shield");
      verificationIcon.classList.add("activateTrue");


      //Enable the Submit Button
      const submitButton = document.querySelector('#activateOnEmail'); 
      submitButton.disabled = false;
    errorDiv.classList.add("hidden");
    $('#message').text("");
    } else {
        alert("Wrong Code Please Retry");
    }
  
});



//TODO Finish editing the verification TOAST
//DO client side verification###