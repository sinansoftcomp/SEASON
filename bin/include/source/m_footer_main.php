
	<!-- �ϴ� ���� -->
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
					<span>�˸���</span>
					</a>
				</li>

				<li class="bottom-menu" data-name="3">
					<a href="<?=$conf['mobileDir']?>/m_main_gongji.php"><img src="/bin/image/mobile_main_bottom3.png" class="icon" id="bottom-menu3">
					<span>��������</span>
					</a>
				</li>

				<li class="bottom-menu" data-name="4">
					<a href="<?=$conf['mobileDir']?>/m_main_community.php"><img src="/bin/image/mobile_main_bottom4.png" class="icon" id="bottom-menu4">
					<span>Ŀ�´�Ƽ</span>
					</a>
				</li>
			</ul>
		</div>
	</div><!-- toolbar End -->

</div>



<script>

// �ϴ� �޴� Ŭ�� �� Ŭ���� �߰� �� �̹��� ����
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

	// bottom ù��° li �ڵ�����
	$('.bottom_quick>li:eq('+num+')').trigger("click");

});

</script>


</body>
</html>
<?mssql_close ( $mscon );?>