$(function () {
  const currentYear = new Date().getFullYear();
  $("#copyright").html(
    `&copy; ${currentYear} Asif Islam. All rights reserved.`
  );

  $("#loginForm").on("input", "#email, #password", function () {
    var email = $("#email").val();
    var password = $("#password").val();
    var errorMessage = "";

    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!email || !emailPattern.test(email)) {
      errorMessage += "Please enter a valid email address.<br>";
    }

    if (!password) {
      errorMessage += "Password cannot be empty.<br>";
    } else if (password.length < 4) {
      errorMessage += "Password is too short.<br>";
    }

    if (errorMessage) {
      $("#errorMessage").html(errorMessage).show();
      $('#loginForm button[type="submit"]').prop("disabled", true);
    } else {
      $("#errorMessage").hide();
      $('#loginForm button[type="submit"]').prop("disabled", false);
    }
  });
});

function successMessageNotify($message) {
  toastr.options = {
    positionClass: "toast-top-center",
  };
  toastr.info($message);
}

function errorMessageNotify($message) {
  toastr.options = {
    positionClass: "toast-top-center",
  };
  toastr.error($message);
}
