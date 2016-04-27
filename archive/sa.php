<?php
session_start();
if (isset($_SESSION['user'])){
    //fetching category labels
    include('connection.php');
    $sql="select distinct Category from wadi_sa";
    $result=$conn->query($sql);
    $cate=array();
    while($cate[]=mysqli_fetch_assoc($result)){}
    $cate=json_encode($cate);
    // print($cate);
    $conn->close();
    //fetching products
    if(isset($_SESSION['category'])){
        $catname=$_SESSION['category'];


    }

}
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class="upload-dialog" hidden>
    <form enctype="multipart/form-data" method="post" action="upload.php" accept=".csv">
        <input type="file" id="csv-file" name="csv-file">
        <input type="submit" id='submit' value='upload'>
    </form>
</div>
<div class="login-dialog" hidden>
    <form action="login.php?sa" method="post">
        <input type="text" name="user" placeholder="username">
        <input type="password" name="pwd" placeholder="password">
        <button type="submit">Submit</button>
    </form>
</div>
<div class="container-main">
    <div class="header">
        <div  class="cat-select">
            <select class="category" onchange="fu()" >
                <option disabled selected> -- select a category -- </option>
            </select>
            <button id='upload'>+</button>

        </div>
        <div class="welcome">

        </div>
    </div>
    <div class="left-pane">
        <div class="page-jump">
            <input type="number" id="page" placeholder="jump to page">
            <button id="jump-to-page">Go! </button>
        </div>
        <div class="device-name">
        </div>
        <div class="device-image">
        </div>
        <div class="device-desc">
        </div>
        <div class="device-btn">
            <button id="prev-device">
                Prev
            </button>
            <button id="next-device">
                Next
            </button>
        </div>
        <div id="pagination">
        </div>
    </div>
    <div class="center-pane">

    </div>

</div>

</body>
<!-- Preloader -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>

<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="js/script.js"></script>
<!-- Preloader -->
<script type="text/javascript">
    //<![CDATA[
    $(window).load(function() { // makes sure the whole site is loaded
        $('#status').fadeOut(); // will first fade out the loading animation
        $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
        $('body').delay(350).css({'overflow':'visible'});
    })
    //]]>
</script>
<script>
    wadi="NA";
    count=1;
    souq="NA"
    id=1;
    jumpid=0;
    total=0;
    items=[];
    category="<?php if(isset($cat))echo $cat;else echo 'NA';?>";
    catname="<?php if(isset($catname))echo $catname;else echo 'NA';?>";
    i_match=[{}];
    cate=<?php if(isset($cate))echo $cate;else echo '"NA"';?>;
    $(document).ready(function () {
        populateCat()
        $('.category>option').each(function(){if(this.value==catname)
            this.selected=true})
        if(catname!="NA"){
            getID()
            loadDevices_wadi()
        }
        //welcome div
        user="<?php if(isset($_SESSION['user']))echo $_SESSION['user']; else echo "NA";?>"
        if(user=="NA")
            $('.welcome').html("<button id='login' onclick='login()'>Login</button>")
        else {
            $('.welcome').html("Welcome,"+ user +"<button id='logout' onclick='logout()'>Logout</button>")
        }
    })
    function something(id) {
        if(checkItem(id)){
            $('#check-'+id).css("display","none")
            remove(id)
            return
        }
        items.push(id)
        $('#check-'+id).css("display","inline")
    }

    //for loading wadi devices
    function loadDevices_wadi()
    {	//making ajax to retrieve wadi product
        $.when($.ajax({
            type:'POST',
            url:'get_wadi.php?sa',
            async:false,
            data:{item:id},
            success:function(data){
                console.log(data)
                if(data=='found'){
                    id=id+1
                    count=parseInt(count)+1
                    console.log('searching for next device no' + id)
                    loadDevices_wadi()
                }
                else
                    wadi=JSON.parse(data)
            }
        })).then(function(){drawWadi()
            loadDevices_souq(id)})
    }
    function fu(){
        //alert(scategory)
        category=$('.category').val()
        count=1
        $.ajax({url:'category.php',
            method:"POST",
            data: {category:category},
            async:false,
            success: function(data){
                // alert(data)
                getID() //to load a wadi item
                loadDevices_wadi(id)
                location.reload()
            }})
    }

    function getID(){
        $.ajax({
            type:"POST",
            url:"get_id.php?sa",
            async:false,
            success:function(data){
                id=parseInt(data.split(';')[1])
                console.log("id - " +id)
                jumpid=id
                total=parseInt(data.split(';')[0])
                console.log("Total : "  +total)
            }

        })
    }

    function drawWadi(){
        $('.device-image').html("<img src="+wadi.Image+">")
        $('.device-name').html("<a href="+wadi.Url+">" + wadi.Name + "</a>")
        $('.device-desc').empty()
        for(key in wadi){
            if(wadi[key]!=null && wadi[key]!=""&& key!='ID'&& key!='Name'&&key!='Url'&&key!='Image')
                $('.device-desc').append(key + ": " + wadi[key] + "<br>")
        }
        $('#pagination').html(count + '/' + total)
    }


</script>
</html>