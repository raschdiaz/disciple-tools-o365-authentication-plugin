"use strict";

(function ($) {
  var goInactiveTimeout;

  function goInactive() {
    console.log('goInactive()')
    // SEND AJAX TO UPDATE USER META
  }

  function startTimer() {
    console.log('startTimer()')
    goInactiveTimeout = setTimeout(
      goInactive,
      settings.timeout * 1000
    );
  }

  function resetTimer() {
    console.log('resetTimer()')
    clearTimeout(goInactiveTimeout);
    try {
      startTimer();
    } catch (e) {
      if (e instanceof TypeError) {
        console.log(e, true);
      } else {
        console.log(e, true);
      }
    }
  }

  function eventListeners() {
    console.log("eventListeners()");
    console.log("settings");
    console.log(settings);
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

  $(function () {
    eventListeners();
  });
})(jQuery);
