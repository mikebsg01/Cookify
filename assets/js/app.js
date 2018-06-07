$(document).ready(function() {
  M.AutoInit();

  $('#logout-link').click(function (event) {
    event.preventDefault();

    $('#logout-form').submit();
  });

  $('.app-close-alert').click(function (event) {
    event.preventDefault();

    $(this).parent().fadeOut(200);
  });
});