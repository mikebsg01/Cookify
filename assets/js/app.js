$(document).ready(function() {
  M.AutoInit();

  $('#logout-link').click(function (event) {
    event.preventDefault();

    $('#logout-form').submit();
  });
});