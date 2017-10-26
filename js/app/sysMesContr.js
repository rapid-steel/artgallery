var sysMesContr = (function() {

  return function ($scope, $resource){
    var ResComments = $resource('php/comments.php/?image_id=:image_id', {id: '@comment_id'}, {
      save: {method: 'POST'}
    }),
    SMRes = $resource('php/sysmessages.php/?user_id=:user_id', {id: '@sm_id'}, {
      delete: {method: 'DELETE'}
    });

    $scope.sysMessages = SMRes.query({user_id: $scope.user.user_id});
    $scope.newComment={body: ''};

    $scope.answerTo = function(item) {

      $scope.newComment = {image_id: item.attr.image_id,
        user_id: $scope.user.user_id,
        answerTo: item.attr.comment_id,
        body: ''}

    };

   $scope.addComment = function(item) {

      ResComments.save($scope.newComment, function(responce) {
        $scope.newComment={};
        $scope.deleteMessage(item);
      });

    };

    $scope.deleteMessage = function(item) {
      var resp = SMRes.delete({sm_id: item.sm_id}, function() {
        if (resp)
          $scope.sysMessages.splice($scope.sysMessages.indexOf(item), 1);
          $scope.$emit('deleteMes');
      });
    };

  };


})();