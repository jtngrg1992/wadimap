function switchView(){
	if(source=='wadi_uae_uploads')
		source='wadi_uae'
	else
		source='wadi_uae_uploads'
	$.ajax({
		type: 'POST',
		url: 'setsource.php',
		data: {source:source},
		async : false,
		success : function(data){alert(data)
			location.reload();
		}
	})

}

function logout(){
	$.ajax({
		type : 'POST',
		url: 'logout.php',
		async:false,
		success : function(data){alert("Logged-out")
		location.reload()}
	})
}
function login(){
	$('.login-dialog').slideToggle()
}

$('#upload').on('click',function(){
	$('.upload-dialog').slideToggle()
})

j=[]
function populateCat(){
	
	j=[]
	if(cate=="NA")
		return
	for (k in cate){
		if(cate[k]!=null){
			// console.log(cate[k])
			temp=cate[k]['Category'].split("/")
			temp=temp.pop()
			temp=temp.trim()
			j.push(temp)}}
		populate()
}
function populate(){
	for (k in j){
		$('.category').append("<option>"+j[k]+"</option>")
	}
}
//for removing duplicates from items array
function checkItem(item){
		for (k in items){
			if(items[k]==String(item))
				return true
		}
		}

//to sanitize  the data
function sanitize(){
	//removing null indexes
	//wadi.pop()
	
}
//for jumping to next device-wadi
$('#next-device').on('click',function(){
	$('#preloader').fadeIn('fast');
	$('#status').fadeIn()
	if(items.length>0){
		$.post('map.php',{item:wadi,souq:souq,items:items,cat:catname,source:source},function(data){console.log(data)})
	}
		id+=1
		count=String(parseInt(count)+1)
		if(parseInt(count)>parseInt(total))
		{
			alert("No more items to show in this category")	
			$('#preloader').fadeOut('slow');
			return
		}
		loadDevices_wadi()
		// loadDevices_souq(id)
		$('.right-pane').empty()
		items=[]

	$('#preloader').fadeOut('slow');
	

})
//for jumping back to prev device-wadi
$('#prev-device').on('click',function(){
	if(count==0)
		return;
	count-=1
	loadDevices_wadi(count)
	loadDevices_souq(count)
})

//for jumping to specific page
$('#jump-to-page').click(function(){
	page=$('#page').val()
	if(!page)
		return
	if(parseInt(page)>total){
		alert("No more products to show in this category")	
		return	
	}
	count2=String(parseInt(page)+parseInt(jumpid) - 1)
	id=parseInt(count2)
    count=page
    loadDevices_wadi(count2);
	// $('#pagination').html(page + '/' + total)
})
mappings=""
//for checking if a device is already mapped
function checkMap(item){
	
	$.ajax({type:'POST',url:'checkmap.php',data:{item:wadi,source:source},success: function(data){
		if(data=="error"){
				mappings="false"
				}
			else{
				data=JSON.parse(data)
				if(data[0]==null){
					mappings="false"
					}
				else{
					mappings=data
				}
			}
	 },async:false})
}
//for drawing already mapped items
function drawMap(map)
{
	$('.center-pane').empty()
	souq=[]
	
	for(i=0;i<map.length-1;i++){
		temp={}
		temp.name=map[i].souq_name
		temp.url=map[i].souq_url
		temp.img=map[i].souq_img
		temp.oldprice=map[i].souq_price_old
		temp.newprice=map[i].souq_price_new
		souq.push(temp)
	}
	draw(souq)
	
}

//for loading souq
f="";
function loadDevices_souq(i)
{	

	  $.ajax({
        type: "POST",
        url: "get_souq.php",
        data:{wadi:wadi.Device_ID,source:source},
        async:false,
        success: function(data) {
        	f=JSON.parse(data)

    	}
     });
	  	souq=[]
	  	f.pop()
	  	for (a in f){
	  		temp={}
	  		temp.img=f[a]['souq_img']
			temp.oldprice=f[a]['souq_oldprice']
			temp.newprice=f[a]['souq_newprice']
			temp.url=f[a]['souq_url']
			temp.name=f[a]['souq_name']
			souq.push(temp)
			
		}
		
	draw(souq)
}
//for drawing a matching souq device
function draw(souq){
	i=0
	$('.center-pane').empty()
	if (souq.length==0)
		$('.center-pane').html("<B>No equivalent product(s) found on uae.souq.com</B>")
	for(item=0;item<souq.length;item++)
	{	
		if(souq[item].img=="http://cf1.souqcdn.com/public/style/img/blank.gif"){
		image='img/placeholder.png'
		c='device-placeholder'
	}	
	
	
	else{	
		image=souq[item].img
		c='device-image'
	}
	price=splprice=""
	if(souq[item].oldprice){
		splprice=souq[item].newprice
		price=souq[item].oldprice
	}
	else{
		price=souq[item].newprice
		$('.device-price-new').css('display','none')
	}

	name=souq[item].name
	$('.center-pane').append("<div onclick=something('"+i+"') wadi='"+count+"'class='souq-device' id='"+souq[item].url+"'><div class="+c+"><img src="+image+"></div><a href="+souq[item].url+" ><div class='device-name'>"+souq[item].name+"</div></a><div class='device-price'><div class='device-price-old'>Price: "+price+"</div><div class='device-price-new'>Spl. Price: "+splprice+"</div></div><div class='check-overlay' id='check-"+i+"'></div></div>");
	i++
}
}

function remove(item){
	for (i in items){
		if(items[i]==item){
			items.splice(i,1)
			return
		}
	}
}




