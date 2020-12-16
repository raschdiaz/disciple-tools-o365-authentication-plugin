"use strict";

(function ($) {
  var goInactiveTimeout,
    userIsActive = false;

  function eventListeners() {
    $(document).on("mousemove", resetTimer);
    $(document).on("mousedown", resetTimer);
    $(document).on("keydown", resetTimer);
    $(document).on("DOMMouseScroll", resetTimer);
    $(document).on("mousewheel", resetTimer);
    $(document).on("touchmove", resetTimer);
    $(document).on("MSPointerMove", resetTimer);
    $(document).on("ready", resetTimer);
    startTimer();
  }

  function startTimer() {
    goInactiveTimeout = setTimeout(goInactive, settings.timeout * 1000);
  }

  function resetTimer() {
    clearTimeout(goInactiveTimeout);
    try {
      // Don't send unnecessary ajax requests
      if (!userIsActive) {
        userIsActive = true;
        sendUserActivityAjax();
      }
      startTimer();
    } catch (error) {
      console.error(error);
    }
  }

  function goInactive() {
    userIsActive = false;
    // SEND AJAX TO UPDATE USER META
    sendUserActivityAjax();
  }

  function sendUserActivityAjax() {
    let value = userIsActive ? "1" : "0";

    $.post(settings.ajax_url, {
      action: "update_user_activity",
      user_is_active: value,
    })
      .done(function (done) {
        if (done.includes("wp_logout()")) {
          window.open(settings.user_logout_uri);
          window.location.replace(settings.home_url);
        }
      })
      .fail(function (error) {
        console.error(error);
      });
  }

  $(function () {
    eventListeners();
  });
})(jQuery);
