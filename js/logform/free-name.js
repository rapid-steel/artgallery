var freeName = (function() {

  return function($http) {

    return {
      restrict: 'A',
      require: 'ngModel',
      link: function($scope, elem, attr, controller) {

        var model = 'logForm.'+attr.ngModel+'.$viewValue';

        $scope.$watch(model, function(val) {

          $http.post('php/register.php', {
            attr_key: attr.ngModel,
            attr_value: val || ''}
          )
            .then( function successCallback(response) {
              var isFree = response.data==1? true : false;
              controller.$setValidity('free-name', isFree);
            });
        });
      }
    };

  };

})();