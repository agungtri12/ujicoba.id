// Toggle Navbar Menu on small screens
document.getElementById("navbarToggle").addEventListener("click", function() {
    const navbarLinks = document.getElementById("navbarLinks");
    if (navbarLinks.style.display === "flex") {
        navbarLinks.style.display = "none";
    } else {
        navbarLinks.style.display = "flex";
    }
});
