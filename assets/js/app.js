const menuToggle = document.getElementById("menu-toggle");
const sidebar = document.querySelector(".sidebar");
const mainContent = document.querySelector(".main-content");

menuToggle.addEventListener("click", () => {

    sidebar.classList.toggle("collapsed");
    mainContent.classList.toggle("expanded");

});