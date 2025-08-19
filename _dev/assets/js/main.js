// Funzioni JS globali


// JS vanilla per gestire il togle della lente di ricerca
document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.querySelector('.search-toggle');
  const overlay = document.querySelector('.search-form-overlay');

  toggleBtn.addEventListener('click', function (e) {
    e.preventDefault();
    overlay.classList.remove('d-none');
  });

  // Chiudi cliccando fuori dal form
  overlay.addEventListener('click', function (e) {
    if (!e.target.closest('.search-form-container')) {
      overlay.classList.add('d-none');
    }
  });

  // Optional: ESC per chiudere
  document.addEventListener('keydown', function (e) {
    if (e.key === "Escape") {
      overlay.classList.add('d-none');
    }
  });
});




