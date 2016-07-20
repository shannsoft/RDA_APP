app.controller("Main_Controller",function($scope,$rootScope,$state,$localStorage,userService,Util,buildingPlan){
  /*******************************************************/
  /*************This is use for check user login**********/
  /*******************************************************/

  $scope.getUseDetails = function(){
    if(localStorage.getItem('accessToken')){
      $scope.is_loggedin = true;
      $rootScope.user_type = localStorage.getItem('userType');
      userService.getUserDetails(localStorage.getItem('accessToken')).then(function(pRes) {
          if(pRes.status == 200){
            $scope.profile = pRes.data.data;
          }
        },function(err) {
        console.log(">>>>>>>>>>>>>   ",err);
      })
    }
    else{
      $scope.is_loggedin = false;
    }
  }
  /*******************************************************/
  /*************This is use for user login****************/
  /*******************************************************/
  $scope.signIn = function(user){
    userService.login(user).then(function(pRes) {
      if(pRes.data.statusCode == 200){
        $scope.is_loggedin = true;
        $rootScope.user_type = pRes.data.data[0].user_type;
        console.log(pRes.data.data[0]);
        localStorage.setItem('accessToken',pRes.data.data[0].token);
        localStorage.setItem('userType',pRes.data.data[0].user_type);
        $scope.getUseDetails();
        $state.go("dashboard");
      }
      else{
          Util.alertMessage('danger', pRes.data.message);
      }
    },
    function(err) {
      console.log(">>>>>>>>>>>>>   ",err);
    })
  }
  /*******************************************************/
  /*************This is use for user signout**************/
  /*******************************************************/
  $scope.signOut = function(){
    userService.logout(localStorage.getItem('accessToken')).then(function(pRes) {
      if(pRes.status == 200){
        console.log(pRes.data.message);
        $scope.is_loggedin = false;
        localStorage.setItem('accessToken','');
        $state.go("login");
      }
    },
    function(err) {
      console.log(">>>>>>>>>>>>>   ",err);
    })
  }

  $scope.planstatus = function () {
    buildingPlan.buildingPlanstatus().then(function(response){
      console.log(response);
      $scope.planstatus = response.data.data;
    });
  }
  $scope.viewplanstatus = function (status){
    localStorage.setItem('status',status);
    $state.go("buildingPlanStatuslist");
  }
});
app.controller("userController",function($scope,$state,$localStorage,userService,$stateParams,Util){
  /*******************************************************/
  /*************This is use for get the user list*********/
  /*******************************************************/
  $scope.getUserList = function(){
    userService.getUserList().then(function(pRes) {
      if(pRes.status == 200){
        $scope.userList = pRes.data.data;
      }
    },
    function(err) {
      console.log(">>>>>>>>>>>>>   ",err);
    })
  }
  /*******************************************************/
  /*************This is use for fo to edit page***********/
  /*******************************************************/
  $scope.goToEdit = function(id){
    // localStorage.setItem('user_id',id);
    $state.go('edituser',{id:id});
  }
  /*******************************************************/
  /*************This is use for load user details*********/
  /*******************************************************/
  $scope.loadUserDetails = function(){
    var obj = {
      "id":$stateParams.id
    }
    userService.manageUser(obj,'get').then(function(pRes) {
      if(pRes.status == 200){
        $scope.userDetails = pRes.data.data[0];
        console.log($scope.userDetails);
      }
    },
    function(err) {
      console.log(">>>>>>>>>>>>>   ",err);
    })
  }
  /*******************************************************/
  /*************This is use for delete user***************/
  /*******************************************************/
  $scope.deleteUser = function(id){
    var obj = {
      "id":id
    }
    userService.manageUser(obj,'delete').then(function(pRes) {
      if(pRes.status == 200){
        $scope.getUserList();
      }
    },
    function(err) {
      console.log(">>>>>>>>>>>>>   ",err);
    })
  }
  /*******************************************************/
  /*************This is use for change user status********/
  /*******************************************************/
  $scope.changeStatus = function(id,status,index){
    var obj = {
      "id":id,
      "status":status
    }
    userService.manageUser(obj,'update').then(function(pRes) {
      if(pRes.status == 200){
        $scope.userList[index].status = status.toString();
      }
    },
    function(err) {
    })
  }
  /*******************************************************/
  /***********This is use for update user details*********/
  /*******************************************************/
  $scope.updateUser = function(){
    var obj = {
      "id":$scope.userDetails.id,
      "first_name":$scope.userDetails.first_name,
      "last_name":$scope.userDetails.last_name,
      "email":$scope.userDetails.email,
      "mobile":$scope.userDetails.mobile,
    }
    userService.manageUser(obj,'update').then(function(pRes) {
      if(pRes.status == 200){
        Util.alertMessage('success', pRes.data.message);
      }
    },
    function(err) {
      Util.alertMessage('danger', pRes.data.message);
    })
  }
  /*******************************************************/
  /*************This is use for add new user**************/
  /*******************************************************/
  $scope.addUser = function(){
    userService.addUser($scope.user).then(function(pRes) {
      if(pRes.status == 200){
        $state.go('users');
      }
    },
    function(err) {
      console.log(">>>>>>>>>>>>>   ",err);
    })
  }
  /*******************************************************/
  /*************This is use for add new user**************/
  /*******************************************************/
  $scope.currentTab = 'myprofile';
  $scope.changeTab = function(tab){
    $scope.currentTab = tab;
  }
  /*******************************************************/
  /*************This is use for add new user**************/
  /*******************************************************/
  $scope.currentTab = 'myprofile';
  $scope.checkCurrentPassword = function(pwd){
    console.log(pwd);
    var obj = {
      "token":localStorage.getItem('accessToken'),
      "password":pwd
    }
    userService.checkPassword(obj).then(function(pRes) {
      console.log(pRes);
      $scope.is_correct_pwd = (pRes.data.statusCode == 200) ? true : false;
    },function(err) {
      console.log(">>>>>>>>>>>>>   ",err);
    })
  }
  /*******************************************************/
  /*************This is use for change password***********/
  /*******************************************************/
  $scope.changePassword = function(){
    var obj = {
      "token":localStorage.getItem('accessToken'),
      "password":$scope.password.new
    }
    userService.changePassword(obj).then(function(pRes) {
        if(pRes.status == 200)
          Util.alertMessage('success', pRes.data.message);
          $scope.password = {};
      },function(err) {
      console.log(">>>>>>>>>>>>>   ",err);
    })
  }
  /*******************************************************/
  /*************This is use for change password***********/
  /*******************************************************/
  $scope.getLogedInUser = function(){

  }
  /*******************************************************/
  /*************This is use for change password***********/
  /*******************************************************/
  $scope.updateMyProfile = function(){
    var obj = {
      'token': localStorage.getItem('accessToken'),
      'first_name':$scope.profile.first_name,
      'last_name':$scope.profile.last_name,
      'email':$scope.profile.email,
      'mobile':$scope.profile.mobile
    }
    userService.updateProfile(obj).then(function(pRes) {
        if(pRes.status == 200){
          Util.alertMessage('success', pRes.data.message);
        }
      },function(err) {
      console.log(">>>>>>>>>>>>>   ",err);
    })
  }
});
app.controller("BuildingPlanController",function($scope,$stateParams,$rootScope,$state,$localStorage,userService,buildingPlan,CONFIG,Util){
  $scope.buildingPlan = {};
  /*******************************************************/
  /*********This is use for load building list************/
  /*******************************************************/
  $scope.loadbuildingPlan = function(){
    buildingPlan.getAllBuildingPlan().then(function(response){
      console.log(response);
      $scope.planList = response.data.data;
    });
  }
  /*******************************************************/
  /*********This is use for load building list************/
  /*******************************************************/
  $scope.planAcceptance = function(remark,status,id){
    var obj = {
      "id":id,
      "status":status,
      "remark":remark,
      "verifier_id":$scope.profile.id
    }
    buildingPlan.updateAcceptance(obj).then(function(response){
      console.log(response);
      $scope.loadbuildingPlan();
    });
  }
  /*******************************************************/
  /*********This is use for upload a building plan********/
  /*******************************************************/
  $scope.uploadBuildingPlan = function(){
     var file = $scope.myFile;
     console.log('file is ' );
     console.dir(file);
     console.log($scope.buildingPlan.date);
     $scope.buildingPlan.date = moment($scope.buildingPlan.date).format("YYYY-MM-DD");
     var obj = {
       "name":$scope.buildingPlan.name,
       "regd_No":$scope.buildingPlan.regdNo + "/RDA",
       "fileNo" :"BP/RDA/" + $scope.buildingPlan.fileNo_type + "/" + $scope.buildingPlan.fileNo_date + "/" +$scope.buildingPlan.fileNo_year,
       "date" : $scope.buildingPlan.date
      };
     console.log(obj);
     buildingPlan.addbuildingPlan(file,obj).then(function(response){
       if(response.data.statusCode == 200){
         Util.alertMessage('success', response.data.message);
       }
       else{
         Util.alertMessage('danger', response.data.message);
       }
     })
  };
  /*
  * adding codes for the date picker start
  */
  $scope.open2 = function() {
   $scope.popup2.opened = true;
  };
  $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
  $scope.format = $scope.formats[0];
  $scope.altInputFormats = ['M!/d!/yyyy'];
  $scope.popup2 = {
    opened: false
  };



  function getDayClass(data) {
    var date = data.date,
      mode = data.mode;
    if (mode === 'day') {
      var dayToCheck = new Date(date).setHours(0,0,0,0);

      for (var i = 0; i < $scope.events.length; i++) {
        var currentDay = new Date($scope.events[i].date).setHours(0,0,0,0);

        if (dayToCheck === currentDay) {
          return $scope.events[i].status;
        }
      }
    }

    return '';
  }



  /*
  * adding codes for the date picker ends
  */

  $scope.open2 = function() {
   $scope.popup2.opened = true;
 };
//codes for loading plan by status
 $scope.loadPlanByStatus = function(){
   $scope.status = localStorage.getItem('status');
   buildingPlan.loadPlanByStatus($scope.status).then(function(response){
     console.log(response.data.data);
     $scope.planList = response.data.data;
   })
 };
 //codes for edit plan
 $scope.goToeditplan = function(id){
   console.log(id);
   $state.go('editplan',{id:id});
 }
 //plans are loaded according to id
 $scope.loadPlanByID = function(){
   var planid = $stateParams.id;
   buildingPlan.loadPlanByID(planid).then(function(pRes) {
     if(pRes.status == 200){
       $scope.buildingPlan = pRes.data.data;
       var tempReg = $scope.buildingPlan.regdNo.split('/');
       var tempfileNo = $scope.buildingPlan.fileNo.split('/');
       $scope.buildingPlan.regd_No = tempReg[0];
       $scope.buildingPlan.fileNo_type = tempfileNo[2];
       $scope.buildingPlan.fileNo_date = tempfileNo[3];
       $scope.buildingPlan.fileNo_year = tempfileNo[4];
     }
   },function(err) {
     console.log(">>>>>>>>>>>>>   ",err);
   });
 }
 //codes for update building plan
 $scope.updatebuildingplan = function(){
    var file = $scope.myFile;
    $scope.buildingPlan.date = moment($scope.buildingPlan.date).format("YYYY-MM-DD");
    var obj = {
      "name":$scope.buildingPlan.name,
      "regd_No":$scope.buildingPlan.regd_No + "/RDA",
      "fileNo" :"BP/RDA/" + $scope.buildingPlan.fileNo_type + "/" + $scope.buildingPlan.fileNo_date + "/" +$scope.buildingPlan.fileNo_year,
      "date" : $scope.buildingPlan.date,
      "id":$scope.buildingPlan.id
    }
    buildingPlan.updatebuildingplan(file,obj).then(function(response){
      if(response.status == 200){
        Util.alertMessage('success', response.data.message);
      }
      else{
        Util.alertMessage('danger', response.data.message);
      }
    })
 }
 //codes for uploading tender
 $scope.uploadtender=function(){
   var file = $scope.myFile;
   var uploadUrl = CONFIG.HTTP_HOST;
   buildingPlan.uploadtender(file, $scope.tender).then(function(response){
     if(response.data.statusCode == 200){
      Util.alertMessage('success', response.data.message);
     }
     else{
       Util.alertMessage('danger', response.data.message);
       //console.log('error');
     }
   })
 }
 //codes for uploading advertisement
 $scope.adv = {};
 $scope.uploadadv = function(){
   buildingPlan.uploadadv($scope.adv.details).then(function(response){
     console.log(response);
     if(response.data.statusCode == 200){
       Util.alertMessage('success', response.data.message);
     }
     else{
       Util.alertMessage('danger', response.data.message);
     }
   });
 }
})
