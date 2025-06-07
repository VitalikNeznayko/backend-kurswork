function showMessage(message, type, targetDivId = "editMessage") {
  const messageDiv = document.getElementById(targetDivId);
  if (messageDiv) {
    if (message) {
      messageDiv.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    } else {
      messageDiv.innerHTML = "";
    }
  }
}
const sortButtons = document.querySelectorAll(".sort-btn");
if (sortButtons) {
  sortButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const sort = this.dataset.sort;
      const url = new URL(this.dataset.url, window.location.origin);

      fetch(url, {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((res) => res.text())
        .then((html) => {
          document.getElementById("topicsContainer").innerHTML = html;
          document
            .querySelectorAll(".sort-btn")
            .forEach((btn) => btn.classList.remove("active"));
          this.classList.add("active");
          window.history.replaceState({}, "", url);
        });
    });
  });
}
