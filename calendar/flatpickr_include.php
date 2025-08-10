<!-- Flatpickr Styles & Scripts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/de.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    if (document.querySelector("#date")) {
      flatpickr("#date", {
        dateFormat: "d.m.Y",
        locale: "de"
      });
    }

    if (document.querySelector("#time_start")) {
      flatpickr("#time_start", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
      });
    }

    if (document.querySelector("#time_end")) {
      flatpickr("#time_end", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
      });
    }
  });
</script>
