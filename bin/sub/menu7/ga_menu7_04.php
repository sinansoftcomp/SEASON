<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

/*
	���Ѱ���
	bin/include/source/auch_chk.php
*/
$pageTemp	= explode("/",$_SERVER['PHP_SELF']);
$auth = auth_Ser($_SESSION['S_MASTER'], $pageTemp[count($pageTemp)-1], $_SESSION['S_SKEY'], $mscon);
if($auth != "Y"){
	sqlsrv_close($mscon);
	alert('�ش� �޴��� ���� ������ �����ϴ�. �����ڿ��� ���� �ٶ��ϴ�.');
	exit;
}

?>

<!-- html���� -->
<style>
body{background-image: none;}

.addsch{float:right;margin-top:3px;margin-right:10px;font-size:13px;font-weight:600;}
.addsch:hover{color:#5F00FF;font-weight:700;cursor:pointer;}

</style>

<link href='<?=$conf['homeDir']?>/js/fullcalendar/fullcalendar.css' rel='stylesheet' />
<script src='<?=$conf['homeDir']?>/js/fullcalendar/lib/moment.min.js'></script>
<script src='<?=$conf['homeDir']?>/js/fullcalendar/fullcalendar.js'></script>
<script src='<?=$conf['homeDir']?>/js/fullcalendar/locale/ko.js'></script>


<div class="container">
	<div class="content_wrap">
		<fieldset>

			<div class="div_grid" style="overflow-y:auto;margin-top:5px;">	
				<div class="con_left" style="width:65%;">
					<div id="calendar"></div>
				</div>

				<div class="con_right" style="width:35%;">
					<p class="htitle" style="display:inline-block;">��������</p>
					<a onclick="fn_addsch('');" class="addsch"><span><i class="fa-regular fa-square-plus mgr3"></i>�����߰�<span></a>
					<div class="schlist"></div>
				</div>
			</div>

		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

function fn_addsch(seq){
	var left = Math.ceil((window.screen.width - 500)/2);
	var top = Math.ceil((window.screen.height - 400)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu7/ga_menu7_04_pop.php?seq="+seq ,"schd","width=500px,height=260px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();	
}

// ����ȸ
function reset(){

	location.reload();

	//ajaxLodingTarket('ga_menu7_04_schlist.php',$('.schlist'),'default=<?=date('Y-m-d')?>');
}

$(document).ready(function(){	

	// ���̰��(vh > px ���)
	const vh = window.innerHeight;
	var calheight = (vh/100)*92;	

	// �������� �޷� �ҷ�����(������ ��Ʈ��Ʈ�� css ����)
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
					ajaxLodingTarket('ga_menu7_04_schlist.php',$('.schlist'),'default='+moment.format());
				},
			}

			// any other sources...

		],
		eventClick: function(event) {
			if (event.ymd) {
				ajaxLodingTarket('ga_menu7_04_schlist.php',$('.schlist'),'sdate='+event.ymd);
			}
		},

	}); // End ��������

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>