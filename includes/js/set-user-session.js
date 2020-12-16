"use strict";

(function ($) {
  $(function () {
    //Set 'userSettings' object in localStorage
    Object.keys(userSettings).map(key => {
      localStorage.setItem(key, userSettings[key]);
    });
  });
})(jQuery);
