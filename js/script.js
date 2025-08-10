// js/script.js
document.addEventListener("DOMContentLoaded", function () {
  // Check session on page load
  fetch("backend/check_sessions.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        document.getElementById("username-display").textContent = data.username;
        loadUserParties();
      } else {
        window.location.href = "login.html";
      }
    })
    .catch((error) => {
      console.error("Session check failed:", error);
      window.location.href = "login.html";
    });

  // Logout button event listener
  const logoutButton = document.getElementById("logout-button");
  logoutButton.addEventListener("click", function () {
    fetch("backend/logout.php").then(() => {
      alert("You have been logged out.");
      window.location.href = "login.html";
    });
  });
});

function loadUserParties() {
  fetch("backend/get_user_data.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success" && data.parties.length > 0) {
        const firstParty = data.parties[0];
        document.getElementById("party-name-display").textContent =
          firstParty.name;
        // Placeholder for the next step:
        // loadCharactersForParty(firstParty.id);
      } else {
        document.getElementById("party-name-display").textContent =
          "No parties found.";
      }
    })
    .catch((error) => console.error("Error fetching user parties:", error));
}
