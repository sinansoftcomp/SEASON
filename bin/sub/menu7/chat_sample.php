<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$sql = "";		

$qry	= sqlsrv_query( $mscon, $sql );
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);


?>

 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css"/>

<!-- html���� -->
<style>
body{background-image: none;}

.chat_wrap { border:1px solid #999; width:300px; padding:5px; font-size:13px; color:#333}
.chat_wrap .inner{background-color:#acc2d2; border-radius:5px; padding:10px; overflow-y:scroll;height: 400px;}
.chat_wrap .item{margin-top:15px}
.chat_wrap .item:first-child{margin-top:0px}
.chat_wrap .item .box{display:inline-block; max-width:180px; position:relative}
.chat_wrap .item .box::before{content:""; position:absolute; left:-8px; top:9px; border-top:0px solid transparent; border-bottom:8px solid transparent;border-right:8px solid #fff;}
.chat_wrap .item .box .msg {background:#fff; border-radius:10px; padding:8px; text-align:left; word-break: break-word;}
.chat_wrap .item .box .time {font-size:11px; color:#999; position:absolute; right: -75px; bottom:5px; width:70px}
.chat_wrap .item.mymsg{text-align:right}
.chat_wrap .item.mymsg .box::before{left:auto; right:-8px; border-left:8px solid #fee600; border-right:0;}
.chat_wrap .item.mymsg .box .msg{background:#fee600}
.chat_wrap .item.mymsg .box .time{right:auto; left:-75px}
.chat_wrap .item .box{transition:all .3s ease-out; margin:0 0 0 20px;opacity:0}
.chat_wrap .item.mymsg .box{transition:all .3s ease-out; margin:0 20px 0 0;}
.chat_wrap .item.on .box{margin:0; opacity: 1;}

input[type="text"]{border:0; width:100%;background:#ddd; border-radius:5px; height:30px; padding-left:5px; box-sizing:border-box; margin-top:5px}
input[type="text"]::placeholder{color:#999}
</style>


<script type="text/javascript">
	$(function(){
		$("input[type='text']").keypress(function(e){
			if(e.keyCode == 13 && $(this).val().length){
				var _val = $(this).val();
				var _class = $(this).attr("class");
				$(this).val('');
				var _tar = $(".chat_wrap .inner").append('<div class="item '+_class+'"><div class="box"><p class="msg">'+_val+'</p><span class="time">'+currentTime()+'</span></div></div>');

				var lastItem = $(".chat_wrap .inner").find(".item:last");
				setTimeout(function(){
					lastItem.addClass("on");
				},10);

				var position = lastItem.position().top + $(".chat_wrap .inner").scrollTop();
				console.log(position);

				$(".chat_wrap .inner").stop().animate({scrollTop:position},500);
			}
		});

	});
	
	var currentTime = function(){
		var date = new Date();
		var hh = date.getHours();
		var mm = date.getMinutes();
		var apm = hh >12 ? "����":"����";
		var ct = apm + " "+hh+":"+mm+"";
		return ct;
	}

</script>


<div class="container">
	<div class="content_wrap">
		<fieldset>

		<div class="chat_wrap">
			<div class="inner">

				<!--<div class="item">
					<div class="box">
						<p class="msg">�ȳ��ϼ���</p>
						<span class="time">���� 10:05</span>
					</div>
				</div>

				<div class="item mymsg">
					<div class="box">
						<p class="msg">�ȳ��ϼ���</p>
						<span class="time">���� 10:05</span>
					</div>
				</div> -->

			</div>

			<input type="text" class="mymsg" placeholder="���� �Է�">
			<input type="text" class="yourmsg" placeholder="���� �Է�">
		</div>

		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">



$(document).ready(function(){	


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>