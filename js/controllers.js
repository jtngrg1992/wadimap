
app.controller('initial',function($scope,initialLoad,$cookies,$rootScope){
	$rootScope.loading=false;
	$scope.category=[];
	$scope.category.push("--Select a category to view related data--");
	$scope.cat=$scope.category[0];
	if($cookies.get('source'))
		$rootScope.source=$cookies.get('source');
		
	else
		$rootScope.source='wadi_uae';

	console.log('source:' + $rootScope.source);

	$scope.loadCat=function(){
		$rootScope.loading=true;
		url='api.php?catlist&source=$rootScope.source';

		initialLoad.categories($rootScope.source).success(function(response){

		if(response.success=='true')
			response.data.pop();
			response.data.forEach(function(cat){
				$scope.category.push(cat.Category);
				$rootScope.loading=false;
			});
			});
		
	};

	if($cookies.get('user')){
		$scope.user=$cookies.get('user');
		console.log("user: " + $scope.user);
		$scope.loadCat();	
	}
	
	$scope.logout=function(){
		// $rootScope.loading=true;
		$cookies.remove('user');
		window.location.assign("#/");
		// $rootScope.loading=false;
	}

	$scope.selCat=function(){
		$rootScope.loading=true;
		if($scope.cat=='--Select a category to view related data--'){
			$rootScope.loading=false;
			return;
		}
		cat=encodeURIComponent($scope.cat.trim());
		//getting id of the first element of selected category
		initialLoad.getId(cat,$rootScope.source).success(function(response){
			if(response.success=='true')
				$rootScope.count=response.count
				$rootScope.id=response.data[0].ID;
				$rootScope.jumpID=1;
				//getting wadi device
				$scope.getWadi($rootScope.id,$scope.source);
				$rootScope.loading=false;

		});
		// $rootScope.loading=false;
	}

	$scope.getWadi=function(id,source){
		if($rootScope.jumpID>$rootScope.count){
			alert("No such page found");
			return;}

		initialLoad.getWadi(id,source).success(function(response){
				if(response.success=='true'){
					$rootScope.wadi=response.data[0];
					$scope.getSouq($rootScope.source,$rootScope.wadi.Device_ID)
					console.log("OURS: " + $rootScope.id + "GOT: " + $rootScope.wadi.ID)
			if(parseInt($rootScope.id)!=parseInt($rootScope.wadi.ID)){
				$rootScope.jumpID=parseInt($rootScope.wadi.ID)-parseInt($rootScope.id) + 1;
			}	
				}

		})
		

	}

	$scope.getSouq=function(source,id){
		initialLoad.getSouq(id,source).success(function(response){
			if(response.success=='true'){
				response.data.pop();
				$rootScope.souq=response.data;
				
			};

		});
		
        	
	};

	$scope.switch=function(event){
		$cookies.put("source",event);
		location.reload();
	}

});  ///initial controller ends

app.controller('deviceLoad',function($cookies,$scope,initialLoad,$rootScope){

	$scope.map=function(){
		initialLoad.putmap($rootScope.wadi,maps,$cookies.get('user'),$rootScope.source).success(function(response){
			console.log(response);
		});
	}
	$scope.jumpToNext=function(){
		if(maps.length>1)
			$scope.map();
		$rootScope.loading=true;
		if($rootScope.jumpID>=$rootScope.count){
			alert("No more devices to show");
			$rootScope.loading=false;
			return;	
		}
		$rootScope.page=parseInt($rootScope.id)+parseInt($rootScope.jumpID);
		$rootScope.jumpID=parseInt($rootScope.jumpID)+1;
		initialLoad.getWadi($rootScope.page,$rootScope.source).success(function(response){
			$rootScope.wadi=response.data[0];
			console.log("OURS: " + $rootScope.id + "GOT: " + $rootScope.wadi.ID)
			if(parseInt($rootScope.id)!=parseInt($rootScope.wadi.ID)){
				$rootScope.jumpID=parseInt($rootScope.wadi.ID)-parseInt($rootScope.id) + 1;
			}
			//getting souq devices
			initialLoad.getSouq($rootScope.wadi.Device_ID,$rootScope.source).success(function(response){
				response.data.pop();
				$rootScope.souq=response.data;
				$rootScope.loading=false;
			});
		});
		
	};

	$scope.jumpToPrevious=function(){
		$rootScope.jumpID=parseInt($rootScope.jumpID)-1;
		$rootScope.page=parseInt($rootScope.id)+parseInt($rootScope.jumpID);
		
		initialLoad.getWadi($rootScope.page,$rootScope.source).success(function(response){
			$rootScope.wadi=response.data[0];
			//getting souq devices
			initialLoad.getSouq($rootScope.wadi.Device_ID,$rootScope.source).success(function(response){
				response.data.pop();
				$rootScope.souq=response.data;
			});
		});
	};

	$scope.toggleOverlay=function(id){
	
		$('#check-'+id).toggle();
		flag=0;
		$scope.souq.forEach(function(souq){
			if(souq.lookup_id==id){
				maps.forEach(function(map){
					if(map.lookup_id==id){
						i=maps.indexOf(map);
						maps.splice(i,1);
						flag=1;
					}
				});
				if(flag==0)
					maps.push(souq);
			}
				
		});
		
	};

});//deviceLoad controller ends

app.controller('loginCTRL',function($scope,initialLoad,$cookies,$rootScope){
	$scope.submit=function(){
		$rootScope.loading=true;
		user=$scope.user;
		
		pwd=$scope.pwd;
		initialLoad.login(user,pwd).success(function(response){
			console.log(response)
			if(response.success=='true'){
				$scope.user=user;
				$cookies.put("user",user);
				window.location.assign("#/devices");
				$rootScope.loading=false;
				
			}
			else
				alert("Invalid credentials");
			$rootScope.loading=false;
		});	
	};
});//login controller ends