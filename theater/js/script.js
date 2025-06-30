document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.getElementById("menu-toggle");
    const body = document.body;

    menuToggle.addEventListener("click", function () {
        body.classList.toggle("sidebar-open");
    });
});
