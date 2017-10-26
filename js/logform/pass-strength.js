var passStrength = (function() {

  function isStrengthEnough(string) {

    var upper = /[A-Z]/.test(string) ? 1 : 0,
      lower = /[a-z]/.test(string) ? 1 : 0,
      number = /\d/.test(string) ? 1 : 0;

    return upper+lower+number===3;

  }

  return function() {
    return {
      require: 'ngModel',
      link: function($scope, elem, attr, controller) {
        controller.$parsers.unshift(function passStrength(viewValue) {
          controller.$setValidity('pass-strength', isStrengthEnough(viewValue));
        });
        controller.$formatters.unshift(function passStrength(modelValue) {
          controller.$setValidity('pass-strength', isStrengthEnough(modelValue));
        });
      }
    };
  };

})();