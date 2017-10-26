var validString = (function() {

  return function() {

    return {
      require: 'ngModel',
      link: function($scope, elem, attr, controller) {
        controller.$parsers.unshift(function validString(viewValue) {
          controller.$setValidity('valid-string', !/\W/.test(viewValue));
        });
        controller.$formatters.unshift(function validString(modelValue) {
          controller.$setValidity('valid-string', !/\W/.test(modelValue));
        });
      }
    };

  };

})();