
	<!-- 하단 고정 -->
	<div id="toolbar" style="transform: translate(0px, 0rem);">
		<div class="toolbar_content">
			<ul class="bottom_quick">
				<li class="bottom-menu" data-name="1">
					<a href="/bin/mainmobile.php"><img src="/bin/image/mobile_main_bottom1.png" class="icon" id="bottom-menu1">
					<span>Home</span>
					</a>
				</li>

				<li class="bottom-menu" data-name="2">
					<a href="<?=$conf['mobileDir']?>/m_main_post.php"><img src="/bin/image/mobile_main_bottom2.png" class="icon" id="bottom-menu2">
					<span>알림장</span>
					</a>
				</li>

				<li class="bottom-menu" data-name="3">
					<a href="<?=$conf['mobileDir']?>/m_main_gongji.php"><img src="/bin/image/mobile_main_bottom3.png" class="icon" id="bottom-menu3">
					<span>공지사항</span>
					</a>
				</li>

				<li class="bottom-menu" data-name="4">
					<a href="<?=$conf['mobileDir']?>/m_main_community.php"><img src="/bin/image/mobile_main_bottom4.png" class="icon" id="bottom-menu4">
					<span>커뮤니티</span>
					</a>
				</li>
			</ul>
		</div>
	</div><!-- toolbar End -->

</div>



<script>

// 하단 메뉴 클릭 시 클래스 추가 및 이미지 변경
$(".bottom_quick li").on("click", function () {
	$(".bottom_quick li").removeClass("on");
	$(this).addClass("on");

	var num = $(this).data('name');
	
	//alert(num);
	for(var i=1; i<=4; i++){
		if(i == num){
			$('#bottom-menu'+i).attr("src","/bin/image/mobile_main_bottom"+i+"_on.png");
		}else{
			$('#bottom-menu'+i).attr("src","/bin/image/mobile_main_bottom"+i+".png");
		}
	}
});


$(document).ready(function(){

	var num = '<?=$page?>';

	// bottom 첫번째 li 자동선택
	$('.bottom_quick>li:eq('+num+')').trigger("click");

});

</script>


</body>
</html>
<?mssql_close ( $mscon );?>