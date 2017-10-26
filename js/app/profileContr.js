var profileContr = (function() {

  return function ($scope, $resource) {

    var ResProfile = $resource('php/userdata.php', {id: '@user_id'}, {
      update: {method: 'PUT'}
    });

    $scope.profMessages = {
      ok: false,
      error: false,
      errorImage: false
    };

    $scope.profile = ResProfile.get({user_id: $scope.user.user_id});


    $(document).ready(function() {


      $('#birthdate').datepicker({
        monthNames:["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
        dayNamesMin:["Вс","Пн","Вт","Ср","Чт","Пт","Сб"],
        firstDay:1,
        dateFormat:"yy-mm-dd"
      });

      $('.loadImg').on('change', function(event) {

        var data = new FormData(),
          id = $(event.target).attr('id'),
          req = new XMLHttpRequest();

        data.append('image', $(event.target)[0].files[0]);

        req.onload = function() {

          if(req.responseText) {
            $scope.profile[id] = req.responseText;
          } else {
            $scope.profMessages.errorImage = true;
          }

          $scope.$apply();

        };

        req.open('POST', 'php/loadimg.php');
        req.send(data);

      });
    });

    $scope.saveProfile = function() {

      $scope.profile.user_id = $scope.user.user_id;
      var resp = ResProfile.update($scope.profile, function() {
        var key = (resp)? 'ok' : 'error';
        $scope.profMessages[key] = true;
        setTimeout(function(){
          $scope.profMessages[key] =  false;
          $scope.$apply();
        }, 5000);
      });


    };



  };

})();
