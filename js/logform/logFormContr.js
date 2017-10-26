var logFormContr = (function() {

  return function($scope, $http) {

    $scope.mode = 'login';
    $scope.dataSend = {};

    $scope.reset = function() {

      ['login',
        'password',
        'repeatPassword',
        'name',
        'email'].forEach(function(elem) {
        $scope.logForm[elem].$modelValue = '';
      });

      $scope.logForm.$setPristine();
      $scope.logForm.$setUntouched();

    };

    $scope.send = function() {

      ['login',
        'password',
        'name',
        'email'].forEach(function(key) {
        $scope.dataSend[key] = $scope.logForm[key].$viewValue || '';
      });

      $scope.dataSend.mode = $scope.mode;

      $http.post('connect.php', $scope.dataSend)
        .then( function successCallback (response) {
          if (response.data) {
            if (response.data=='login')
              location.reload();
            else
              $scope.errorMessage = response.data;
          }
        });
    };

    document.body.onkeydown = function(event) {
      if(event.keyCode==13)                    // Enter
        if (($scope.mode=='login')||
          ($scope.mode=='register')&&$scope.logForm.$valid)
          $scope.send();
    };

  };

})();