<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");
?>

<style>
body{background-image: none;}

/* 일정관리(달력) */ 
.fc-sun {color:#e31b23}
.fc-sat {color:#007dc3}

.fc-event-title.fc-sticky{white-space: normal;}


.fc-event-container{cursor: pointer;}
.fc-event-container:hover{background: #FFBB00;}

/* Tab */
.tab_home{overflow:hidden; margin-bottom: 5px; border-bottom:1px solid #eee}
.tab_home:after{display:block; content:''; clear:both;}
.tab_home li{position: relative; float: left; width: 120px; height: 30px;text-align: center; border: 1px solid #E7E7E7; border-radius:10px 10px 0 0; background:#fff; box-sizing:border-box;line-height: 30px;margin-right:10px; border-bottom: none;}
.tab_home li:first-child{border-left: 1px solid #E7E7E7; border-radius:10px 10px 0 0;  border-bottom: none;}

.tab_home li.on{color:#6799FF; font-weight: 700; border-bottom: none; }
.tab_home li:hover{cursor: pointer;}


/* 공지 */
.new {color:#F15F5F; padding-left:5px;font-size:10px;line-height:20px;}
@keyframes blink-effect {
  50% {opacity: 0;}
}

.blink {animation: blink-effect 1s step-end infinite;}
.list_plus{float:right;font-size:14px;line-height:20px;margin-right:10px;margin-top:3px;}
.list_plus:hover{cursor: pointer;}
#gongjilist{overflow-y:auto;padding:0 10px;font-size:14px;height:94vh;}

li.listgongji a:hover{color:#5F00FF;}

/* 활동현황 */

.home_left{float:left;width:65%;height:100%;}
.home_right{float:right;width:35%;padding-left:10px;box-sizing: border-box;height:100%;}

.home_sales{padding-left:5px;height:24vh;min-height:195px;}
.sales_table{display:inline-block;width:33.1%;box-sizing:border-box;font-size:14px;text-align:center;}

.sales_month{box-sizing:border-box;width:99.99%;color:#fff;padding:5px 0 5px;}
.ilj_table li{float: left;width:33.33%;box-sizing:border-box;padding:7px 0 7px;}
.data_table{overflow:hidden;box-sizing:border-box;font-weight:600;}
.ilj_table li span{display:block;margin-bottom:9px;}
.sum_table{overflow:hidden;width:99.99%;box-sizing:border-box;border-top:none;}
.sum_table li{float: left;width:33.33%;box-sizing:border-box;padding:10px 0 10px;font-weight:700;}

/* 전전월 */
.month_red{border:1px solid #ff9292;background:#ff9292;}
.ilj_red{border-bottom:1px solid #ff9292;}
.ilj_red li{border-right:1px solid #ff9292;}
.left_red_border{border-left:1px solid #ff9292;}
.num_red{color:#870000;}


/* 전월 */
.month_blue{border:1px solid #55c8ff;background:#55c8ff;}
.ilj_blue{border-bottom:1px solid #55c8ff;}
.ilj_blue li{border-right:1px solid #55c8ff;}
.left_blue_border{border-left:1px solid #55c8ff;}
.num_blue{color:#133782;}

/* 당월 */
.month_green{border:1px solid #80c269;background:#80c269;}
.ilj_green{border-bottom:1px solid #80c269;}
.ilj_green li{border-right:1px solid #80c269;}
.left_green_border{border-left:1px solid #80c269;}
.num_green{color:#308213;}


/* 교육현황 */
.ilj_gray {border-bottom: 1px solid #eee;}
.ilj_gray li{border-right:1px solid #eee;}
.left_gray_border{border-left:1px solid #eee;}


.ilj_gray span.left {
	display:inline-block;
	width:30%;
    font-size: 14px;
    font-weight: 500;
	margin-top:5px;
	text-align:center;
}

.ilj_gray span.right {
	display:inline-block;
	width:50%;
    font-size: 14px;
    font-weight: 700;
	margin-top:5px;
	text-align:right;
}

.header_bcolor{background-color:#f7f7f7;font-weight:700;}

</style>

<link href='<?=$conf['homeDir']?>/js/fullcalendar/fullcalendar.css' rel='stylesheet' />
<script src='<?=$conf['homeDir']?>/js/fullcalendar/lib/moment.min.js'></script>
<script src='<?=$conf['homeDir']?>/js/fullcalendar/fullcalendar.js'></script>
<script src='<?=$conf['homeDir']?>/js/fullcalendar/locale/ko.js'></script>
<script src='<?=$conf['homeDir']?>/js/fullcalendar/popper.min.js'></script>

<div style="padding:5px 5px" class="div_grid2">
	<div class="home_left">
		<div class="home_sales" style="min-height:230px;">
			<ul id="tab" class="tab_home" style="margin-bottom: 0;">
				<li class="on"  data-tab="tab-11" id="tb11">활동현황</li>
			</ul>

			<!-- 전전월 -->
			<div class="sales_table">
				<li class="sales_month month_red">
					<span><?=$m_fi.'월'?></span>
				</li>

				<ul class="ilj_red ilj_table" style="overflow:hidden;">
					<li class="left_red_border">일반</li>
					<li>장기</li>
					<li>자동차</li>
				</ul>

				<ul class="ilj_red ilj_table data_table num_red">
					<li class="left_red_border">
						<span><?=number_format($listData_kwn[0]['kwn_cnt1']).'건'?></span>
						<span><?=number_format($listData_kwn[0]['kwn_amt1'])?></span>
					</li>
					<li>
						<span><?=number_format($listData_kwn[0]['kwn_cnt2']).'건'?></span>
						<span><?=number_format($listData_kwn[0]['kwn_amt2'])?></span>				
					</li>
					<li>
						<span><?=number_format($listData_kwn[0]['kwn_cnt3']).'건'?></span>
						<span><?=number_format($listData_kwn[0]['kwn_amt3'])?></span>						
					</li>
				</ul>

				<ul class="sum_table num_red" style="border:1px solid #ff9292;border-top:none;">
					<li class="font600" style="border-right:1px solid #ff9292;background:#ffdbdb;"><?=$m_fi?>월 합계</li>
					<li class="font700"><?=number_format($listData_kwn[0]['kwn_cnt4']).'건'?></li>
					<li class="font700"><?=number_format($listData_kwn[0]['kwn_amt4'])?></li>
				</ul>
			</div>


			<!-- 전월 -->
			<div class="sales_table">
				<li class="sales_month month_blue">
					<span><?=$m_se.'월'?></span>
				</li>

				<ul class="ilj_blue ilj_table" style="overflow:hidden;">
					<li class="left_blue_border">일반</li>
					<li>장기</li>
					<li>자동차</li>
				</ul>

				<ul class="ilj_blue ilj_table data_table num_blue">
					<li class="left_blue_border">
						<span><?=number_format($listData_kwn[1]['kwn_cnt1']).'건'?></span>
						<span><?=number_format($listData_kwn[1]['kwn_amt1'])?></span>
					</li>
					<li>
						<span><?=number_format($listData_kwn[1]['kwn_cnt2']).'건'?></span>
						<span><?=number_format($listData_kwn[1]['kwn_amt2'])?></span>				
					</li>
					<li>
						<span><?=number_format($listData_kwn[1]['kwn_cnt3']).'건'?></span>
						<span><?=number_format($listData_kwn[1]['kwn_amt3'])?></span>						
					</li>
				</ul>

				<ul class="sum_table num_blue" style="border:1px solid #55c8ff;border-top:none;">
					<li class="font600" style="border-right:1px solid #55c8ff;background:#ace2fb;"><?=$m_se?>월 합계</li>
					<li class="font700"><?=number_format($listData_kwn[1]['kwn_cnt4']).'건'?></li>
					<li class="font700"><?=number_format($listData_kwn[1]['kwn_amt4'])?></li>
				</ul>
			</div>


			<!-- 당월 -->
			<div class="sales_table">
				<li class="sales_month month_green">
					<span><?=$m_th.'월'?></span>
				</li>

				<ul class="ilj_green ilj_table" style="overflow:hidden;">
					<li class="left_green_border">일반</li>
					<li>장기</li>
					<li>자동차</li>
				</ul>

				<ul class="ilj_green ilj_table data_table num_green">
					<li class="left_green_border">
						<span><?=number_format($listData_kwn[2]['kwn_cnt1']).'건'?></span>
						<span><?=number_format($listData_kwn[2]['kwn_amt1'])?></span>
					</li>
					<li>
						<span><?=number_format($listData_kwn[2]['kwn_cnt2']).'건'?></span>
						<span><?=number_format($listData_kwn[2]['kwn_amt2'])?></span>				
					</li>
					<li>
						<span><?=number_format($listData_kwn[2]['kwn_cnt3']).'건'?></span>
						<span><?=number_format($listData_kwn[2]['kwn_amt3'])?></span>						
					</li>
				</ul>

				<ul class="sum_table num_green" style="border:1px solid #80c269;border-top:none;">
					<li class="font600" style="border-right:1px solid #80c269;background:#a8e393;"><?=$m_th?>월 합계</li>
					<li class="font700"><?=number_format($listData_kwn[2]['kwn_cnt4']).'건'?></li>
					<li class="font700"><?=number_format($listData_kwn[2]['kwn_amt4'])?></li>
				</ul>
			</div>


		</div> <!-- 활동현황 -->


		<div style="padding-left:5px;height:74vh;overflow-y:auto;">
			<!--<div class="tit_wrap">
				<h3><i class="fa-solid fa-chart-line font_purple mgr5"></i>일정관리</h3>
			</div>-->
			<div style="">
				<div id="calendar"></div>
			</div>
		</div>

	</div> <!--- End home_left -->

	<div id="gongji" class="home_right">
		<div class="home_sales" style="min-height:230px;">
			<ul id="tab" class="tab_home" style="margin-bottom: 0;">
				<li class="on"  data-tab="tab-31" id="tb31">교육현황</li>
				<a style="pointer-events : none;"><span class="list_plus" style="font-size:13px; color:#6d6464;font-weight:600;">마지막 업로드 : <?=$eupdate?></span></a>
			</ul>

			<div class="sales_table" style="width:100%;">
				<ul class="ilj_gray ilj_table" style="overflow:hidden;">
					<li class="left_gray_border header_bcolor">생명</li>
					<li class="header_bcolor">손보</li>
					<li class="header_bcolor">제3보험</li>
				</ul>

				<ul class="ilj_gray ilj_table data_table">
					<li class="left_gray_border num_red">
						<span class="left">이수완료</span>
						<span class="right"><?=number_format($row['sangbit1'])?></span>
						<span class="left">미이수</span>
						<span class="right"><?=number_format($row['sangbit2'])?></span>
						<span class="left">미경과</span>
						<span class="right"><?=number_format($row['sangbit3'])?></span>
						<span class="left" style="margin-bottom:5px;">해당없음</span>
						<span class="right" style="margin-bottom:5px;"><?=number_format($row['sangbit4'])?></span>
					</li>
					<li class="num_blue">
						<span class="left">이수완료</span>
						<span class="right"><?=number_format($row['sonbit1'])?></span>
						<span class="left">미이수</span>
						<span class="right"><?=number_format($row['sonbit2'])?></span>
						<span class="left">미경과</span>
						<span class="right"><?=number_format($row['sonbit3'])?></span>
						<span class="left" style="margin-bottom:5px;">해당없음</span>
						<span class="right" style="margin-bottom:5px;"><?=number_format($row['sonbit4'])?></span>
					</li>
					<li class="num_green">
						<span class="left">이수완료</span>
						<span class="right"><?=number_format($row['thirdbit1'])?></span>
						<span class="left">미이수</span>
						<span class="right"><?=number_format($row['thirdbit2'])?></span>
						<span class="left">미경과</span>
						<span class="right"><?=number_format($row['thirdbit3'])?></span>
						<span class="left" style="margin-bottom:5px;">해당없음</span>
						<span class="right" style="margin-bottom:5px;"><?=number_format($row['thirdbit4'])?></span>
					</li>
				</ul>
			</div>

		</div>

		<div class="" style="height:74vh;">
			<ul id="tab2" class="tab_home">
				<li class="sub on"  data-tab="tab21" id="tb21">공지사항</li>
				<a href="javascript:parent.change_parent_url('/bin/sub/menu7/ga_menu7_03.php');"><span class="list_plus"><i class="fa-regular fa-square-plus mgr3"></i>더보기<span></a>
			</ul>
			
			<div id="gongjilist" style="height:70vh;">	
				<?if(!empty($listData)){?>
				<?foreach($listData as $key => $val){extract($val);
					if($topsort == 'Y'){
						$bold = 'font700';
					}else{
						$bold = 'font400';
					}
				?>
					<li class="listgongji" style="padding-bottom:5px;line-height:26px;overflow: hidden; white-space: nowrap; -ms-text-overflow: ellipsis; -o-text-overflow: ellipsis;text-overflow: ellipsis;" onclick="gongji_read('<?=$seq?>')"><a>
						<img src="../../img/icon1.jpg" class="icon mgr5">
						<span style="color:#A6A6A6">[<?=date("Y.m.d",strtotime($idate))?>]</span>
						<span style="margin-left:5px;" class="<?=$bold?>"><?=$title?></span>
						<?if($fileyn == 'Y'){?><i class="fa-regular fa-floppy-disk" style="color:#1359dd"></i><?}?>
						<?if($newbit == 'Y'){?><span class="new blink">New!</span><?}?>
					</a></li>
				<?}}?>
			</div> <!-- gongjilist -->


			<div id="edulist" style="display:none;">

			</div> <!-- edulist -->


		</div>
	</div> <!-- End right -->
</div>


<script type="text/javascript">

// 공지사항 상세
function gongji_read(seq){
	//location.href='ga_menu7_03_write.php';
	var left = Math.ceil((window.screen.width - 800)/2);
	var top = Math.ceil((window.screen.height - 500)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu7/ga_menu7_03_read.php?seq="+seq,"gongji","width=800px,height=500px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

// 탭에 따른 리스트 조회
$(".sub").click(function(){
	
	var tabnum = $(this).data('tab');
	
	if(tabnum == 'tab21'){
		$("#gongjilist").css("display","");
		$("#edulist").css("display","none");
	}else{
		$("#gongjilist").css("display","none");
		$("#edulist").css("display","");		
	}
}); 

$(document).ready(function(){	

	// 높이계산(vh > px 계산)
	const vh = window.innerHeight;
	var calheight = (vh/100)*72;

	// 일정관리 달력 불러오기(툴팁은 부트스트랩 css 참조)
	$('#calendar').fullCalendar({
		defaultDate: '<?=date("Y-m-d")?>',
		height:calheight,
		editable: false,
		expandRows: true,
		eventLimit: true, // allow "more" link when too many events
		eventRender: function(eventObj, $el) {

			$el.popover({
				title: eventObj.title,
				content: eventObj.description,
				delay: {"hide": 100},
				trigger: 'hover',	// click, hover
				placement: 'right',	// top, left, right
				container: 'body'
			});
		},	
		eventSources: [
			{
				url: '<?=$conf['homeDir']?>/sub/menumain/home_calendar_json_data.php',
				success: function(event) {
					var moment = $('#calendar').fullCalendar('getDate');
					//ajaxLodingTarket('ren_menu1_05_layer_asmst_list.php',$('.asmstlist'),'default='+moment.format());
				},
			}

			// any other sources...

		],
		eventClick: function(event) {
			if (event.ymd) {
				//ajaxLodingTarket('ren_menu1_05_layer_asmst_list.php',$('.asmstlist'),'YDATE='+event.ymd);
			}
		},

	}); // End 일정관리

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>