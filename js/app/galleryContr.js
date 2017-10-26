var galleryContr = (function() {

  return function($scope, $http, $resource, $state, $stateParams) {

    var SMRes = $resource('php/sysmessages.php', {id: '@sm_id'}, {
      delete: {method: 'DELETE'}
    }),
      reqParams = {
      offset: function() {
        return $scope.gallery.images.length;
      },
      limit: function() {
        var winHeight = $(window).height(),
          winWidth = $(window).width(),
          cols = winWidth>992? 4 : winWidth>768? 3 : 2;

        return $scope.gallery.images.length? cols : Math.ceil(winHeight/300)*cols;
      }
    },
      reqAnswer = false,
      ResSub = $resource('php/subscribe.php/?user_id=:user_id&sub_id=:sub_id', {id: '@id'}, {
      save: {method: 'POST'},
      delete: {method: 'DELETE'}
    });

    $scope.gallery = {};
    $scope.gallery.images = [];
    $scope.gallery.user = '';
    $scope.gallery.search = $stateParams.search? $stateParams.search : '';

    $scope.pageParams = {
      user_id: $stateParams.id? $stateParams.id : $scope.user.user_id,
      order: $stateParams.order?  $stateParams.order : 'i.date',
      mode: $stateParams.mode? $stateParams.mode : '',
      tag: $stateParams.tag? $stateParams.tag : '',
      search: $scope.gallery.search
    };

    $http({
      url: 'php/gallery.php',
      method: 'GET',
      params: {
        user_id: $scope.pageParams.user_id,
        order: $scope.pageParams.order,
        tag: $scope.pageParams.tag,
        mode: $scope.pageParams.mode,
        search: $scope.pageParams.search,
        limit: reqParams.limit(),
        offset: 0
      }}).then(function successCallback(data) {

      $scope.gallery.images = data.data;
      reqAnswer = true;

      if($scope.pageParams.tag==''&&$scope.pageParams.mode!='sub'&&$scope.pageParams.mode!='search') {

        $http({
          url: 'php/tags.php',
          method: "GET",
          params: {user_id: $stateParams.id? $stateParams.id : $scope.user.user_id, mode: $scope.pageParams.mode}
        }).then(function successCallback(json) {
          $scope.gallery.tagSize = json.data;
          for(var tag in $scope.gallery.tagSize) {
            $scope.gallery.tagSize[tag] = +$scope.gallery.tagSize[tag] + 12 + 'px';
          }
        });

      }

      if($scope.pageParams.user_id!=-1) {
        $http({
          url: 'php/userdata',
          method: "GET",
          params: {user_id: $scope.pageParams.user_id}
        })
          .then(function successCallback(json) {
            $scope.gallery.user = json.data;
            $scope.gallery.user.profilePath = 'url("content/users/profile/' + $scope.gallery.user.profile + '"';
        });
      }

    });


    $(document).on('scroll', function (event) {

      if (($(this).scrollTop() + $(window).height() >= $(this).height() - 3)&&(reqAnswer)) {
        reqAnswer = false;

        $http({
          url: 'php/gallery.php',
          method: 'GET',
          params: {
            user_id: $scope.pageParams.user_id,
            order: $scope.pageParams.order,
            tag: $scope.pageParams.tag,
            mode: $scope.pageParams.mode,
            search: $scope.pageParams.search,
            limit: reqParams.limit(),
            offset: reqParams.offset()
          }}).then(function successCallback(data) {
          if (data.data.length) {
            data.data.forEach(function(item) {
              $scope.gallery.images.push(item);
            });
            reqAnswer = true;
          }
        });
      }
    });

    $scope.searchOnEnter = function(event) {

      if(event.keyCode==13)
        $state.transitionTo("search",
          {search: $scope.gallery.search},
          {reload: false, inherit: true, notify: true});

    };


    $scope.subscribe = function() {
      var resp = ResSub.save({
        user_id: $scope.pageParams.user_id,
        sub_id: $scope.user.user_id
      }, function() {
        if (resp) {
          $scope.$emit('subscribe', $scope.pageParams.user_id);
        }
      });
    };

    $scope.unsubscribe = function() {
      var resp = ResSub.delete({
        user_id: $scope.pageParams.user_id,
        sub_id: $scope.user.user_id
      }, function() {
        if (resp) {
          $scope.$emit('unSubscribe', $scope.pageParams.user_id);
        }
      });
    };

    $scope.removeSub = function(sm_id, index) {
      var resp = SMRes.delete({sm_id: sm_id}, function() {
        if (resp)
          $scope.gallery.images.splice(index, 1);
        $scope.$emit('deleteSub');
      });
    };


  };

})();