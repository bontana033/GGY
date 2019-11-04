

<head>
    <link rel="stylesheet" type="text/css" href="/css/map_css.css">
    <style>
    #map_category_name {
        padding-top : 40px;
        text-align: center;
        font-size : 18px;
        line-height : 20px;
        color : #AAAAAA;
        font-family : NanumBarunGothic;
    }
    #map_category_list_wrapper{
        display : grid;
        grid-template-columns : 33% 33% 33%;
    }
    #map_category_list_wrapper * {
        color : #AAAAAA;
    }
    img { 
        width : 100px;
        height : 100px;
    }
    #map_table{
        /* border : solid black; */
    }
    #map_tr{
        /* padding : 20px;
        margin : 20px; */
        /* border : 3px solid green; */
    }
    .spot_iter{
        padding : 15px;
        /* margin : 20px; */
    }
    .map_table_i_1{
        /* font: Regular 16px/19px */
        font-size : 16px;
        line-height : 19px;
        
    }
    .map_table_i_2{
        /* display : inline; */
        /* float  :left; */
        color: #AAAAAA;
        /* padding : 20px;
        margin : 20px; */
    }
    #map_container{
        margin-top : 50px;
        display : grid;
        grid-template-columns : 30% 70%;
        height : 80%;
    }
    
    #map_list{
        border : 1px solid green;
        overflow : scroll;
    }
    #kakao_map{
        border : 1px solid red;
    }
    #pagination_list{
        padding : 20px;
        text-align : center;
    }
    
    </style>
</head>
<div class="container">
    <p id="map_category_name">Map</p>
    <div id="map_category_list_wrapper">
        <div></div>
        <div>
            <ul class="nav nav-justified" id="map_category_list"></ul>
        </div>
        <div></div>
    </div>
<div id="map_container">
    <div id="map_list">
<?php
foreach ($list as $lt)
{
?>
				<div class="spot_iter">
                    <!-- <tr id="map_tr"> -->
                        <span class="map_table_i_1"><?php echo $lt->title ?></span>
                        <br>
                        <span class="map_table_i_2"><?php echo $lt->desc ?></span>
                        <br>
                    <!-- </tr> -->
                </div>
<?php
}
?>
            
                    <div id="pagination_list">
                        <?php echo $pagination;?>
                    </div>
            
    </div>
    <div id="kakao_map">
    </div>
</div>




    <div id="map_wrapper">
        <!-- <div id="item1">
        </div>
        <div id="item2">
            <div id="right_map">
            </div>
        </div> -->
    </div>
</div>
<script>
    <?php if(isset($category_list)){ ?>
		var categorization_category = <?php echo json_encode($category_list); ?>;
		var categorization_subcategory  = <?php echo json_encode($subcategory_list); ?>;
			
		// 카테고리 뷰에 추가하기 
        $("#map_category_list").append(
                '<li class="map_category_iter">'+
                '   <a href="/index/map_page">all</a>'+
                '</li>'
        );
		for(var i = 0 ; i<categorization_category.length ; i++){
			$("#map_category_list").append(
                '<li class="map_category_iter">'+
                '   <a href="/index/map_page?category='+categorization_category[i].category+'">'+categorization_category[i].category+'</a>'+
                '</li>'
            );
		}

		// 서브카테고리 뷰에 추가하기 
	// 	<?php if(isset($_GET['category'])){ ?>
	// 			for(var i=0 ; i<categorization_subcategory.length; i++){
	// 					$("#map_subcategory_list").append(
    //                     '<li class="map_subcategory_iter">'+
    //                         '<a href="/index/map_page?category=<?php echo $_GET['category'];?>&subcategory='+categorization_subcategory[i].subcategory+'">'+
    //                             categorization_subcategory[i].subcategory+
    //                         '</a>'+
    //                     '</li>');
	// 			}
	// 	<?php } ?>
	// <?php } ?>
    
    // 카카오 지도 API
    var container = document.getElementById('kakao_map');
    var options = {
            center : new kakao.maps.LatLng(37.76632121829326, 128.90701331720723),
            // center: new kakao.maps.LatLng(33.450701, 126.570667),
            level: 7
    };
    var map = new kakao.maps.Map(container, options);
    var mapTypeControl = new kakao.maps.MapTypeControl();
    // 지도에 컨트롤을 추가해야 지도위에 표시됩니다
    // kakao.maps.ControlPosition은 컨트롤이 표시될 위치를 정의하는데 TOPRIGHT는 오른쪽 위를 의미합니다
    map.addControl(mapTypeControl, kakao.maps.ControlPosition.TOPRIGHT);
    // 지도 확대 축소를 제어할 수 있는  줌 컨트롤을 생성합니다
    var zoomControl = new kakao.maps.ZoomControl();
    map.addControl(zoomControl, kakao.maps.ControlPosition.RIGHT);



    // marker initiation
    var marker = new kakao.maps.Marker({ 
        // 지도 중심좌표에 마커를 생성합니다 
        position: map.getCenter()
    }); 
    kakao.maps.event.addListener(map, 'click', function(mouseEvent) {        
        // 클릭한 위도, 경도 정보를 가져옵니다 
        // var latlng = mouseEvent.latLng; 
        // alert(typeof(latlng));
        // 마커 위치를 클릭한 위치로 옮깁니다
        // marker.setPosition(latlng);
        // marker.setMap(map);
        // customOverlay.setMap(null);
    }); 
    // 맨처음 spot을 클릭할 때 다른 모든 marker를 setMap(null)해줘야 함. 그다음부터는 비효율적인 setMap(null)을 방지하기 위한 bool변수
    var initMarker = 0;
    

    // overlay initiation
    // 마커 오버레이(마커 위에 정보 팜업)
    var content = 
    '<div id="map_overlay">'+
    '   <div id="overlay_one">'+
    '      <a class="overlay_link" href="">'+
    '          <div id="overlay_image_wrapper">'+
    '               <image id="overlay_image">'+
    '          </div>'+
    '      </a>'+
    '   </div>'+
    '   <div id="overlay_two">'+
    '      <p id="overlay_title"></p>'+
    '      <p id="overlay_like"></p>'+
    '      <a class="overlay_link" href="">보러가기</a>&nbsp;&nbsp;'+
    '      <a onclick="closeOverlay()">닫기</a>'+
    '   </div>'+
    '</div>';

    customOverlay = new kakao.maps.CustomOverlay({
        position: null,
        content: content,
        xAnchor: 0.5,
        yAnchor: 1.3
    });
    // 카카오 지도 API 끝


    // get_curMarker = function(){
    //     return new kakao.maps.Marker({ 
    //         position: new kakao.maps.LatLng(list[i].y, list[i].x),
    //     }); 
    // };
    // item1에 spot 리스트 넣기
    var list = <?php echo json_encode($list)?>;
    var markerlist = [];
    for(var i=0; i<list.length; i++){
        $("#item1").append(
        '<div class="spot_iter" id="list_id_'+i+'">'
            +'<div class="spot_iter_left">'
                +'<div class="spot_iter_left_wrapper">'
                +'<p class="spot_iter_title">'
                    +list[i].title + '&nbsp;&nbsp;&nbsp;'
                    +'<span class="spot_iter_category">'
                    +list[i].category
                    +'</span>'
                +'</p>'
                +'</div>'
            +'</div>'
            // +' <span class="category_button"><a href="/index/map?category='+list[i].category+'"></a>'+list[i].category+'</a> | '+list[i].subcategory+'</span>'
            +'<div class="spot_iter_image_wrapper">'
                +'<img src="/image/'+list[i].imagepath+'">'
            +'</div>'
        +'</div>'
        );
        var curMarker = new kakao.maps.Marker({ 
            position: new kakao.maps.LatLng(list[i].y, list[i].x),
        }); 
        curMarker.title = list[i].title;
        curMarker.like = list[i].like;
        curMarker.imagepath = list[i].imagepath;
        curMarker.id = list[i].id;
        
        // var curMarker = new get_curMarker();
        curMarker.setMap(map);
        markerlist.push(curMarker);
        kakao.maps.event.addListener(curMarker, 'click', function(mouseEvent) {        
            var latLngToMove = this.getPosition();
            customOverlay.setPosition(latLngToMove);
            if(customOverlay.getMap() == null)
                customOverlay.setMap(map);
            $("#overlay_title").text(this.title);
            $("#overlay_like").html('<img src="/image/heart_pin.png">&nbsp;'+this.like);
            $("#overlay_image").attr('src', '/image/'+this.imagepath);
            $(".overlay_link").attr('href', '/index/spot_view?id='+this.id);
            map.setCenter(latLngToMove);
        }); 
    }

    // 스팟 클릭시 화면 옮기기 & 핀 세우기
    $(".spot_iter").click(function(){
        var index = $(this).index();
        var entity = list[index];
        var latLngToMove = new kakao.maps.LatLng(entity.y, entity.x);
        map.setCenter(latLngToMove);

        //marker
        if(!initMarker){
            for(var i =0 ;i<markerlist.length ; ++i){
                // markerlist[i].setMap(null);
            }
            initMarker = true;
        }
        marker.setPosition(latLngToMove);
        marker.setMap(map);

        //overlay
        customOverlay.setPosition(latLngToMove);
        if(customOverlay.getMap() == null)
            customOverlay.setMap(map);
        $("#overlay_title").text(entity.title);
        $("#overlay_like").html('<img src="/image/heart_pin.png">&nbsp;'+entity.like);
        $("#overlay_image").attr('src', '/image/'+entity.imagepath);
        $(".overlay_link").attr('href', '/index/spot_view?id='+entity.id);
    });
    function closeOverlay(event){
        customOverlay.setMap(null);
    };
    // function all_marker_show(){
    //     for(var i = 0 ; i < markerlist.length ; ++i){
    //         markerlist[i].setMap(map);
    //     }
    //     customOverlay.setMap(null);
    //     initMarker = false;
    // }
</script>

<style>
</style>