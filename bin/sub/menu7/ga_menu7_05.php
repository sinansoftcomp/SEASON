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


if ($_GET['SDATE1']) {
	$sdate1 =  $_GET['SDATE1'];
}else{
	$sdate1 =  date("Y-m");
}

$where="";
if($_GET['gubun'] or $_GET['gubun'] == '0'){
	$where .= " and msg_type= '".$_GET['gubun']."' ";
}else{
	$where = "";
}

$sdatec = substr($sdate1,0,4).substr($sdate1,5,2);

/* ------------------------------------------------------------
	End Date �ʱⰪ ����
------------------------------------------------------------ */

// �⺻ ������ ����
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 500;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$sql = "
	select *
	from(
		select a.cmid,msg_type,
				case when call_status in('7000','4100','6600') then '����' else '����' end call_status ,
				convert(varchar,send_time,120) send_time,
				substring(dest_phone,1,3)+'-'+substring(dest_phone,4,4)+'-'+substring(dest_phone,7,4) dest_phone,
				send_phone,msg_body,a.scode,a.skey,
				row_number() over(order by send_time desc) rnum
		from daoubiz.dbo.BIZ_LOG_".$sdatec." a 
		where scode = 'GAPLUS' $where
		) p
	where rnum between ".$limit1." AND ".$limit2 ;

$qry	= sqlsrv_query( $mscon, $sql );
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}
/*
echo "<pre>";
echo $sql;
echo "</pre>";
*/

$sql= "
		select chaamt
		from company a 
		where a.SCODE =  '".$_SESSION['S_SCODE']."'  
		 " ;
$qry = sqlsrv_query( $mscon, $sql );
$comp_hando  = sqlsrv_fetch_array($qry);

$sql= "
	select sum(sms_suc) sms_suc,sum(sms_fail) sms_fail,sum(mms_suc) mms_suc,sum(mms_fail) mms_fail,sum(at_suc) at_suc,sum(at_fail) at_fail ,
			sum(sms_suc) + sum(mms_suc) + sum(at_suc) tot_suc , sum(sms_fail) + sum(mms_fail) + sum(at_fail) tot_fail ,
			sum(sms_suc)*25 + sum(mms_suc)*80 hando
	from(
		select isnull(case when msg_type = '0' and call_status = '4100' then 1 end,0) sms_suc,
				isnull(case when msg_type = '0' and call_status != '4100' then 1 end,0) sms_fail,
				isnull(case when msg_type = '5' and call_status = '6600' then 1 end,0) mms_suc,
				isnull(case when msg_type = '5' and call_status <> '6600' then 1 end,0) mms_fail,
				isnull(case when msg_type = '6' and call_status = '7000' then 1 end,0) at_suc,
				isnull(case when msg_type = '6' and call_status <> '7000' then 1 end,0) at_fail
		from daoubiz.dbo.BIZ_LOG_".$sdatec." 
		where scode = 'GAPLUS' $where
		) aa
	";

$qry = sqlsrv_query( $mscon, $sql );
$totalResult_sms  = sqlsrv_fetch_array($qry);


 // ������ �� �Ǽ�
 //�˻� ������ ���ϱ� 
$sql= "
		select count(*) CNT
		from daoubiz.dbo.BIZ_LOG_".$sdatec." 
		where scode = 'GAPLUS' $where
	";

$qry = sqlsrv_query( $mscon, $sql );
$totalResult  = sqlsrv_fetch_array($qry);


sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1".$_GET['SDATE1']."&gubun=".$_GET['gubun'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

?>

<!-- html���� -->
<style>
body{background-image: none;}
</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>

			<div class="box_wrap sel_btn">
				<form name="searchFrm" id="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
					<span  class="ser_font" style="font-size: large;"> ���ۿ�</span> 
					<span class="input_type date ml10" style="width:114px;margin-left: 0px;">
						<input type="text" class="Cal_ym" placeholder="YYYY-MM" id="SDATE1" name="SDATE1" value="<?=$sdate1?>" readonly>
					</span> 

					<select name="gubun" id="gubun"style="width:150px;FONT-SIZE: 14px;"> 
						<option value="">���۱��м���</option>
						
						<option value="0" <?if($_GET['gubun']=="0") echo "selected"?>>SMS</option>
						<option value="5" <?if($_GET['gubun']=="5") echo "selected"?>>MMS</option>
						<option value="6" <?if($_GET['gubun']=="6") echo "selected"?>>�˸���</option>

					</select>	

					<span class="btn_wrap" style="margin-left: 10px;">				
						<a class="btn_s white btn_search btn_off"  style="margin: 0; min-width:100px;">��ȸ</a>
					</span>	 
				</form>
			</div>

			<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
				<table class="gridhover">
					<colgroup>
						<col style="width:11%">
						<col style="width:6%">
						<col style="width:8%">
						<col style="width:7%">
						<col style="width:auto">
						<col style="width:7%">
					</colgroup>
					<thead>
					<tr>				
						<th>���۽ð�</th>
						<th>���۱���</th>	
						<th>��������</th>	
						<th>�����ڹ�ȣ</th>
						<th>���۳���</th>
						<th>���ۻ��</th>
					</tr>
					</thead>
					<tbody>
						<tr class="summary sticky"style="top:33px">
							<th class="sumtext">�� ���۰Ǽ� : <?=$totalResult['CNT']?>��</th>
							<th class="sumtext">�����Ǽ� : <?=$totalResult_sms['tot_suc']?>��</th>
							<th class="sumtext">���аǼ� : <?=$totalResult_sms['tot_fail']?>��</th>
							<th></th>							
							<th class="sumtext" style="text-align:left">
									[ SMS������ : <?=$totalResult_sms['sms_suc']?>�� &nbsp&nbsp&nbsp&nbsp SMS���а� : <?=$totalResult_sms['sms_fail']?>�� ]&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
									[ MMS������ : <?=$totalResult_sms['mms_suc']?>�� &nbsp&nbsp&nbsp&nbsp SMS���а� : <?=$totalResult_sms['mms_fail']?>�� ]&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
									[ �˸��强���� : <?=$totalResult_sms['at_suc']?>�� &nbsp&nbsp&nbsp&nbsp SMS���а� : <?=$totalResult_sms['at_fail']?>�� ]
							</th>		
							<th class="sumtext">�ܿ��ѵ� : <?=number_format($comp_hando['chaamt']-$totalResult_sms['hando'])?>��</th>
						</tr>					
						<?if(!empty($listData)){?>
						<?foreach($listData as $key => $val){extract($val);?>
						<tr>
							<td style="text-align:center"><?=$send_time?></td>
							<td style="text-align:center"><?=$conf['sms_gubun'][$msg_type]?></td>
							<td style="text-align:center" <?if($call_status == "����"){?>class="font_blue"<?}else{?>class="font_red"<?}?>><?=$call_status?></td>
							<td style="text-align:center"><?=$dest_phone?></td>		
							<td style="text-align:left"><?=$msg_body?></td>
							<td style="text-align:left"><?=$skey?></td>
						</tr>
						<?}}?>
					</tbody>
				</table>
			</div>
			<div style="text-align: center">		
				<ul class="pagination pagination-sm itemlist" style="margin: 5px 5px 0 5px">
				  <?=$pagination->create_links();?>
				</ul>
			</div>
		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

$(document).ready(function(){	
	// ��ȸ
	$(".btn_search").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		$("form[name='searchFrm']").submit();
	});   


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>