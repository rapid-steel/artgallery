var imgContr = (function() {

  return function($scope, $http, $stateParams, $state, $resource, ResImage) {

    var ResComments = $resource('php/comments.php/?image_id=:image_id', {id: '@comment_id'}, {
        update: {method: 'PUT'},
        save: {method: 'POST'},
        delete: {method: 'DELETE'}
      }),
      ResFav = $resource('php/favorite.php/?user_id=:user_id&image_id=:image_id', {id: '@id'}, {
        save: {method: 'POST'},
        delete: {method: 'DELETE'}
    });

    $scope.image = {};

    $scope.newComment = {
      image_id: $stateParams.image_id,
      user_id: $scope.user.user_id,
      answerTo: 0,
      body: ''
    };


    $scope.editComMode = -1;
    $scope.viewComMode = 'plain';

    $scope.image = ResImage.get({image_id: $stateParams.image_id}, function() {
      $scope.image.tags = $scope.image.tags.split('; ');
    });

    $scope.comments = ResComments.query({image_id: $stateParams.image_id});

    $scope.editImage = function() {
      $state.transitionTo("edit", {image: $scope.image}, { reload: true, inherit: true, notify: true });
    };

    $scope.deleteImage = function() {
      var req = new ResImage({id: $scope.image.image_id});
      req.$delete(function() {
        if (req) {
          $scope.$emit('delete', $scope.image.title);
          $state.transitionTo("myGallery", null, { reload: false, inherit: true, notify: true });
        }
      });
    };

    $scope.addToFav = function() {

      var resp = ResFav.save({
        image_id: $scope.image.image_id,
        user_id: $scope.user.user_id,
        action: 'add'}, function() {
        if (resp) {
          $scope.image.fav_n++;
          $scope.$emit('addToFav', $scope.image.image_id);
        }
      });

    };

    $scope.removeFromFav = function() {

      var resp = ResFav.delete({
        image_id: $scope.image.image_id,
        user_id: $scope.user.user_id
      }, function() {
        if(resp) {
          $scope.image.fav_n--;
          $scope.$emit('removeFromFav', $scope.image.image_id);
        }
      });
    };

    $scope.addComment = function() {

      ResComments.save($scope.newComment, function(responce) {
        $scope.newComment.body='';
        $scope.newComment.answerTo = 0;
        if ($scope.viewComMode=='tree') {
          var i = 0;
          while ($scope.comments[i].comment_id!=responce.answerTo) i++;
          $scope.comments.splice(i+1, 0, responce);
        } else
          $scope.comments.push(responce);
        $scope.image.com_n++;
      });
    };

    $scope.editComment = function(index) {
      var height = $('.comment').eq(index).find('.body-text').height();
      $scope.editComMode = index;
      $('.comment').eq(index).find('.editArea').css('height', height);
    };

    $scope.saveComment = function(index) {
      ResComments.update($scope.comments[index], function(responce) {
        $scope.comments[index].body = responce.body;
        $scope.editComMode = -1;
      });
    };

    $scope.deleteComment = function(index) {
      ResComments.delete({id: $scope.comments[index].comment_id}, function(responce) {
        if(responce) {
          $scope.comments.splice(index,1);
          $scope.image.com_n--;
        }
      });
    };

    $scope.viewTree = function() {
      var copyArray = $scope.comments,
        sortedArray = [],
        answers = [],
        item, i;

      while(copyArray.length) {

        answers = [];
        item = copyArray[0];
        sortedArray.push(item);
        copyArray.splice(0,1);
        for(i = 0; i<copyArray.length; i++){
          if(item.comment_id==copyArray[i].answerTo){
            answers.push(copyArray[i]);
            copyArray.splice(i, 1);
            i--;
          }
        }
        copyArray = answers.concat(copyArray);
      }
      $scope.viewComMode = 'tree';
      $scope.comments = sortedArray;

    };

    $scope.viewPlain = function() {
      $scope.viewComMode = 'plain';
      $scope.comments.sort(function(com1, com2) {
        return com1.comment_id - com2.comment_id;
      });

    };

    $scope.getLeft = function (item) {
      return (($scope.viewComMode=='plain')||(item.answerTo==0))? '10px' : function() {
        var i = 0;
        while ($scope.comments[i].comment_id!=item.answerTo) i++;
        return parseFloat($('.comment').eq(i).css('left')) + 30 + 'px';
      };
    };
  };

})();