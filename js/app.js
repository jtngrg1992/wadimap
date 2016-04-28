var app=angular.module('app',['ngRoute','ngCookies','angular-loading-bar']);
app.config(['$routeProvider',function($routeProvider){
	$routeProvider.
		when('/',{
			templateUrl:'templates/login.html',
			controller:'loginCTRL'
		}).
		when('/devices',{
			templateUrl:'templates/home.html',
			controller:'deviceLoad'
		}).
        when('/upload',{
            templateUrl:'templates/upload.html',
            controller:'uploadCTRL'
        }).
		otherwise({
			redirectTO:'/'
		});
}]);

app.factory('initialLoad', function($http){
    return {
        categories: function(source){

        	return $http.get('api.php?catlist&source='+source);
        		
            
        },
        login: function(user,pwd){
        	return $http.post('api.php?login',{user:user,pwd:pwd});
            // return "Factory says \"Goodbye " + text + "\"";
        },
        getId:function(cat,source){
        	url='api.php?getid&source='+source+'&cat='+cat;
        	return $http.get(url);
        },
        getWadi:function(id,source){
        	url='api.php?getwadi&source='+source+'&id='+id;
            console.log(url);
        	return $http.get(url);
        },
        getSouq:function(id,source){
        	url='api.php?getsouq&source='+source+'&wadi='+id;
        	return $http.get(url);
        },
        putmap:function(wadi,souq,user,source){
            url='api.php?map&source=' + source;
            return $http.post(url,{wadi:wadi,souq:souq,user:user});
        }
    }               
});