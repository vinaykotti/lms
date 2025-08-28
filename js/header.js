function toggleDropdown() {
  document.getElementById("dropdown").classList.toggle("show");
}

window.onclick = function (event) {
  if (!event.target.closest(".user-menu")) {
    document.getElementById("dropdown").classList.remove("show");
  }
};
