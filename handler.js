function toggleShowHide() {
   let passwordField = document.getElementById("pass");
   let showButton = document.querySelector(".show-password");

   if(passwordField.type === "password") {
      passwordField.type = "text";
      showButton.innerHTML = '<img src="/Midterm/images/eye-opened.png" alt="" style="width: 16px;">'
   } else {
      passwordField.type = "password";
      showButton.innerHTML = '<img src="/Midterm/images/eye-closed.png" alt="" style="width: 16px;">'
   }
}

function logOut() {
   window.location.href = "logout.php";
}

document.addEventListener("submit", function(event) { 
   handleError(event);
});

function handleError(event) {
   event.preventDefault();

   const formData = new FormData(document.querySelector("form"));
   const errorMessage = document.getElementById("errorMessage");

   fetch("server.php", {
      method: "POST",
      body: formData
   })
   .then(response => response.json())
   .then(data => {
      if(data.error == 'email') {
         errorMessage.innerHTML = data.message;
         errorMessage.style.display = "block";
         document.getElementById('email').focus();
      } else 
      if(data.error == 'pass') {
         errorMessage.innerHTML = data.message;
         errorMessage.style.display = "block";
         document.getElementById('pass').focus();
      } else
      if(data.error == 'name') {
         errorMessage.innerHTML = data.message;
         errorMessage.style.display = "block";
         document.getElementById('name').focus();
      } else 
      if(data.error == 'username') {
         errorMessage.innerHTML = data.message;
         errorMessage.style.display = "block";
         document.getElementById('username').focus();
      } else
      if(data.error == 'none' && data.message == "signup") {
         if(confirm("Registration successfully. Do you want to add 2FA?")) {
            add2FA(data.accEmail);
         } else {
            window.location.href = "login.php";
         }
      } else
      if(data.error == 'none' && data.message == 'login') {
         if(data.twoFA == "no") {
            localStorage.setItem('email', data.email);
            localStorage.setItem('twoFA', "no");
            window.location.href = "client.html";
         } else {
            localStorage.setItem('email', data.email);
            window.location.href = "otpVerify.php";
         }
      } else
      if(data.error == 'none' && data.message == 'admin') {
         window.location.href = "admin.php";
      }
   });
}

function toggleAdd2FA() {
   const email = localStorage.getItem('email');
   add2FA(email);
}
function add2FA(email) {
   fetch("server.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "add2FA_to_email=" + encodeURIComponent(email)
   })
      .then(response => response.json())
      .then(data => {
         if (data.success) {
            window.location.href = "login.php";
            alert("2FA successfully enabled! Now you can login to test!");
         } else {
            window.location.href = "login.php";
            alert("Failed to enable 2FA: " + data.message);
         }
      });
}

document.addEventListener("DOMContentLoaded", function() {
   if(document.getElementById('e')) {
      document.getElementById('e').innerHTML = localStorage.getItem('email');
   }
   if(document.getElementById('add-2fa')) {
      if(localStorage.getItem('twoFA') != 'no') {
         document.getElementById('add-2fa').style.display = "none";
         document.getElementsByClassName('welcome')[1].innerHTML = "Your account already set 2FA";
      }
   }
});

function movePointer(input, event) {
   const otpForm = document.getElementById('otpForm');
   if(event.key === "Backspace" && input.value === "") {
      const previousInput = input.previousElementSibling;
      if (previousInput && previousInput.classList.contains('otp-input')) {
         previousInput.focus();
         previousInput.value = "";
         previousInput.style.borderBottom = "2px solid white";
      }
   }
   if(input.value.length === input.maxLength) {
      const nextInput = input.nextElementSibling;
      if(nextInput && nextInput.classList.contains('otp-input')) {
         input.style.border = "none";
         nextInput.focus();
      } else {
         otpForm.submit();
         otpForm.reset();
         [...otpForm.querySelectorAll(".otp-input")].forEach(input => input.style.borderBottom = "2px solid white");
      }
   }
}