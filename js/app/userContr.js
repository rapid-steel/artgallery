var userContr = (function() {

  return function($scope, $resource, $stateParams) {

    $scope.user = {};
    $scope.messages = {};

    $scope.user = JSON.parse($('#user').val());

    $('#user').remove();

    $scope.$on('addToFav', function(event, image_id) {
      $scope.user.favorites.push(image_id);
    });

    $scope.$on('removeFromFav', function(event, image_id) {
      $scope.user.favorites.splice($scope.user.favorites.indexOf(image_id), 1);
    });

    $scope.$on('subscribe', function(event,user_id) {
      $scope.user.subscribes.push(user_id);
    });

    $scope.$on('unSubscribe', function(event, user_id) {
      $scope.user.subscribes.splice($scope.user.subscribes.indexOf(user_id), 1);
    });

    $scope.$on('delete', function(event, title) {
      $scope.messages.delete = title;
    });

    $scope.$on('deleteMes', function(event) {
      $scope.user.sys_messages_count--;
    });

    $scope.$on('deleteSub', function(event) {
      $scope.user.subscribes_count--;
    });

  };

})();