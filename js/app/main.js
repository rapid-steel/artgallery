(function() {

  angular.module('gallery', ['ngResource', 'ui.router'])
    .factory('ResImage', function($resource) {
      return $resource('php/image.php', {id: '@id'}, {
        update: {method: 'PUT'},
        save: {method: 'POST'},
        delete: {method: 'DELETE'}
      });
    })
    .controller('loaderContr', loaderContr)
    .controller('galleryContr', galleryContr)
    .controller('imgContr', imgContr)
    .controller('profileContr', profileContr)
    .controller('sysMesContr', sysMesContr)
    .controller('userContr', userContr)

    .config(function ($stateProvider, $urlRouterProvider) {

      $stateProvider
        .state('myGallery', {
          url: "/home",
          templateUrl: "html/templates/gallery.html"
        })
        .state('recent', {
          url: "/recent",
          params: {id: -1, mode: 'recent'},
          templateUrl: "html/templates/gallery.html"
        })
        .state('popular', {
          url: "/popular",
          params: {id: -1, order: 'i.fav_n + i.com_n', mode: 'popular'},
          templateUrl: "html/templates/gallery.html"
        })
        .state('search', {
          url: "/search/:search",
          params: {mode: 'search', id: -1},
          templateUrl: "html/templates/gallery.html"
        })
        .state('byTag', {
          url: "/tagged/:tag",
          params: {id: -1, mode: 'tag'},
          templateUrl: "html/templates/gallery.html"
        })
        .state('myGalleryByTag', {
          url: "/tagged/:tag",
          params: {mode: 'tag'},
          templateUrl: "html/templates/gallery.html"
        })
        .state('userGallery', {
          url: "/user/:id",
          templateUrl: "html/templates/gallery.html"
        })
        .state('userGalleryByTag', {
          url: "/user/:id/tagged/:tag",
          params: {mode: 'tag'},
          templateUrl: "html/templates/gallery.html"
        })
        .state('favorites', {
          url: "/favorites",
          params: {mode: 'fav'},
          templateUrl: "html/templates/gallery.html"
        })
        .state('userFavorites', {
          url: "/user/:id/favorites",
          params: {mode: 'fav'},
          templateUrl: "html/templates/gallery.html"
        })
        .state('userFavoritesByTag', {
          url: "/user/:id/favorites/tagged/:tag",
          params: {mode: 'fav'},
          templateUrl: "html/templates/gallery.html"
        })
        .state('subscribes', {
          url: "/subscribes",
          params: {mode: 'sub'},
          templateUrl: "html/templates/gallery.html"
        })
        .state('image', {
          url: "/image/:image_id",
          templateUrl: "html/templates/image.html"
        })
        .state('load', {
          url: "/load",
          templateUrl: "html/templates/load.html"
        })
        .state('edit', {
          url: "/edit",
          params: {image: ''},
          templateUrl: "html/templates/load.html"
        })
        .state('messages', {
          url: "/messages",
          templateUrl: "html/templates/sys_messages.html"
        })
        .state('profile', {
        url: "/profile",
        templateUrl: "html/templates/profile.html"
      });

      $urlRouterProvider.otherwise('/popular');
    });


})();