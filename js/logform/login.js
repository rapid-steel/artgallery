(function() {

 angular.module('logForm', ['ngMessages'])
   .directive ('passStrength', passStrength)
   .directive ('validString', validString)
   .directive ('freeName', ['$http', freeName])
  .controller('logFormContr', logFormContr);

})();