app.factory("userService", function ($http,CONFIG) {
  return{
    login: function (data) {
      var _serializedData = $.param({"reqmethod": 'login', "user_name":data.username,"password":data.password});
      var response = $http({
          method: 'POST',
          url: CONFIG.HTTP_HOST,
          data : _serializedData,
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          }
      });
      return response;
    },
    logout: function (data) {
      var _serializedData = $.param({"reqmethod": 'logout', "token":data});
      var response = $http({
          method: 'POST',
          url: CONFIG.HTTP_HOST,
          data : _serializedData,
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          }
      });
      return response;
    },
    checkPassword: function (data) {
      console.log(data);
      var _serializedData = $.param({"reqmethod": 'checkPassword', "token":data.token,"password":data.password});
      var response = $http({
          method: 'POST',
          url: CONFIG.HTTP_HOST,
          data : _serializedData,
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          }
      });
      return response;
    },
    changePassword: function (data) {
      console.log(data);
      var _serializedData = $.param({"reqmethod": 'changePassword', "token":data.token,"password":data.password});
      var response = $http({
          method: 'POST',
          url: CONFIG.HTTP_HOST,
          data : _serializedData,
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          }
      });
      return response;
    },
    addUser: function (data) {
      console.log(data);
      var _serializedData = $.param({"reqmethod": 'register', "user_data":data});
      var response = $http({
          method: 'POST',
          url: CONFIG.HTTP_HOST,
          data : _serializedData,
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          }
      });
      return response;
    },
    getUserList: function () {
      var response = $http.get(CONFIG.HTTP_HOST+"/?reqmethod=getUsers");
      return response;
    },
    getUser: function (id) {
      var response = $http.get(CONFIG.HTTP_HOST+"/?reqmethod=getUserById&id="+id);
      return response;
    },
    getUserDetails: function (token) {
      var response = $http.get(CONFIG.HTTP_HOST+"/?reqmethod=getUserDetails&token="+token);
      return response;
    },
    manageUser: function (data,option) {
      var _serializedData = $.param({"reqmethod": 'user',"operation":option, "user_data":data});
      var response = $http({
          method: 'POST',
          url: CONFIG.HTTP_HOST,
          data : _serializedData,
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          }
      });
      return response;
    },
    updateProfile: function (data) {
      var _serializedData = $.param({"reqmethod": 'updateProfile',"user_data":data});
      var response = $http({
          method: 'POST',
          url: CONFIG.HTTP_HOST,
          data : _serializedData,
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          }
      });
      return response;
    }
  }
});
app.factory('buildingPlan', function ($http,CONFIG) {
  return{
   addbuildingPlan : function(file, uploadUrl,data){
      var fd = new FormData();
      fd.append('file', file);
      fd.append('reqmethod', "upload");
      fd.append('name', data.name);
      fd.append('regdNo', data.regdNo);
      fd.append('date', data.date);

      var response = $http.post(uploadUrl, fd, {
         transformRequest: angular.identity,
         headers: {'Content-Type': undefined , 'accessToken':localStorage.getItem('accessToken')}
      });
      return response;
   },
   getAllBuildingPlan : function(){
     console.log('121212121');
     var response = $http.get(CONFIG.HTTP_HOST+"/?reqmethod=allBuildingPlan",{
        headers: {'accessToken':localStorage.getItem('accessToken')}
      });
     return response;
   },
   updateAcceptance : function(obj){
     var _serializedData = $.param({"reqmethod": 'updateAcceptance',"data":obj});
     var response = $http({
         method: 'POST',
         url: CONFIG.HTTP_HOST,
         data : _serializedData,
         headers: {
             'Content-Type': 'application/x-www-form-urlencoded'
         }
     });
     return response;
   },
   buildingPlanstatus: function () {
    var response = $http.get(CONFIG.HTTP_HOST + "/?reqmethod=planCount", {
        headers: {
            'accessToken': localStorage.getItem('accessToken')
        }
    });
    return response;
    console.log('response');
  },
  loadPlanByStatus:function(status){
    var response = $http.get(CONFIG.HTTP_HOST + "?reqmethod=getPlans&status="+status,{
      headers: {
          'accessToken': localStorage.getItem('accessToken')
      }
    });
    return response;
  },
  loadPlanByID:function(id){
    var response = $http.get(CONFIG.HTTP_HOST + "?reqmethod=buildingPlanByID&id="+id,{
      headers: {
          'accessToken': localStorage.getItem('accessToken')
      }
    });
    return response;
  }
 }
});
