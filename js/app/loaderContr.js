var loaderContr = (function() {

  return function($scope, $stateParams, $state, ResImage) {

    $scope.restart = function() {
      $scope.loader = {};
      $scope.loader.loadedImage = 0;

      if($stateParams.image) {
        $scope.loader = $stateParams.image;
        $scope.loader.mode = 'edit';
        $scope.loader.tags = $scope.loader.tags.join('; ');
      }

      $scope.loader.messages = {
        error: false,
        saved: false,
        updated: false
      };
    };


    $scope.restart();

    $(document).ready(function() {

      $('#loadImg').on('change', function() {

        var data = new FormData(),
          req = new XMLHttpRequest();

        $scope.loader.loadedImage = 1;

        data.append('image', $('#loadImg')[0].files[0]);

        req.onload = function() {

          if(req.responseText) {
            $scope.loader.loadedImage = req.responseText;
          } else {
            $scope.loader.messages.error = true;
          }

          $scope.$apply();

        };

        req.open('POST', 'php/loadimg.php');
        req.send(data);

      });
    });


    $scope.saveImg = function() {

      var data = {
        filename: $scope.loader.loadedImage || false,
        image_id: $scope.loader.image_id,
        name: $scope.user.name,
        title: $scope.loader.title,
        tags: $scope.loader.tags || '',
        description: $scope.loader.description || ''
      };

      ResImage.update(data, function() {
        $scope.loader.messages.updated = true;
      });

    };


    $scope.loadNewImg = function() {

      var data = {
        filename: $scope.loader.loadedImage,
        user_id: $scope.user.user_id,
        name: $scope.user.name,
        title: $scope.loader.title,
        tags: $scope.loader.tags || '',
        description: $scope.loader.description || ''
      },
        resp = ResImage.save(data, function() {
         $scope.loader.image_id = resp.id;
        $scope.loader.messages.saved = true;
      });
    };


  };

})();