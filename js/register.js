// js/register.js
document.addEventListener("DOMContentLoaded", function () {
  const registerForm = document.getElementById("register-form");

  registerForm.addEventListener("submit", function (event) {
    event.preventDefault();
    const formData = new FormData(registerForm);

    fetch("backend/register.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.text())
      .then((data) => {
        alert(data);
        if (data.trim() === "Registration successful!") {
          window.location.href = "login.html";
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
      });
  });
});
