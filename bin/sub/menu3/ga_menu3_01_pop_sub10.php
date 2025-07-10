<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

/*-----------------------------------------------------------------
����� ���Լ�����
-----------------------------------------------------------------*/
$inscode	= $_GET['inscode'];
$kcode		= $_GET['kcode'];
$kskey		= $_GET['kskey'];

// �⺻ ������ ����
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 100;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

// ���Լ����� ����Ʈ
if($_GET['kskey']){

	//�˻� ������ ���ϱ� 
	$sql= "
		select *
		from(
			select 
					a.*,    a.SAMT6 + a.SAMT7 + a.SAMT8+ a.SAMT9+ a.SAMT10+ a.SAMT11+ a.SAMT12+ a.SAMT13 as  KITA_HAP ,
					a.SAMT1 +a.SAMT2 +a.SAMT3 +a.SAMT4 +a.SAMT5 +a.SAMT6 + a.SAMT7 + a.SAMT8+ a.SAMT9+ a.SAMT10+ a.SAMT11+ a.SAMT12+ a.SAMT13 as S_TOTAL ,
					g.filename,
					d.kname kwn_kname,
					row_number()over(order by a.edate desc, a.kcode, a.ncnt desc ) rnum
			from INS_IPMST a	
				left outer join upload_history g on a.scode = g.scode and a.ipdate = g.upldate 
								and a.gubun = g.gubun and a.gubunsub = g.gubunsub and a.ino = g.uplnum
				left outer join inswon b on a.scode = b.scode and a.ksman = b.bscode
				left outer join swon c on a.scode = c.scode and b.skey = c.skey
				left outer join kwn d on a.scode = d.scode and a.inscode = d.inscode and a.kcode = d.kcode
			where a.scode = '".$_SESSION['S_SCODE']."'
			  and c.skey = '".$_GET['kskey']."'
			) p
		where rnum between ".$limit1." AND ".$limit2 ;

	$qry	= sqlsrv_query( $mscon, $sql );
	$listData = array();
	while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
		$listData[]	= $fet;
	}

	// ������ �� �Ǽ��� �հ� 
	//�˻� ������ ���ϱ� 
	$sql= "
			select 
					count(*) CNT,sum(samt1) samt1,sum(samt2) samt2,sum(samt3) samt3,sum(samt4) samt4,sum(samt5) samt5,
					sum(SAMT6 +  SAMT7 +  SAMT8+  SAMT9+  SAMT10+  SAMT11+  SAMT12+  SAMT13) as   kita_hap,  
					sum(SAMT1 + SAMT2 + SAMT3 + SAMT4 + SAMT5 + SAMT6 +  SAMT7 +  SAMT8+  SAMT9+  SAMT10+  SAMT11+  SAMT12+  SAMT13) as s_total ,sum(HSSU) hssu,
					sum(a.MAMT) mamt , sum(HWANAMT) hwanamt,  sum(BSU) bsu
			from INS_IPMST a
				left outer join upload_history g on a.scode = g.scode and a.ipdate = g.upldate 
								and a.gubun = g.gubun and a.gubunsub = g.gubunsub and a.ino = g.uplnum
				left outer join inswon b on a.scode = b.scode and a.ksman = b.bscode
				left outer join swon c on a.scode = c.scode and b.skey = c.skey
				left outer join kwn d on a.scode = d.scode and a.inscode = d.inscode and a.kcode = d.kcode
			where a.scode = '".$_SESSION['S_SCODE']."'
			  and c.skey = '".$_GET['kskey']."' " ;
	 
	$qry =  sqlsrv_query($mscon, $sql);
	$totalResult =  sqlsrv_fetch_array($qry);
	

	// ����� ������ ����
	$sql="
		select 
				a.skey,
				a.sname,
				a.bonbu,
				a.jisa,
				a.jijum,
				a.team,
				b.bname,
				c.jsname,
				d.jname,
				e.tname,
				case when isnull(a.bonbu,'') != '' then substring(b.bname,1,2) else '' end +
				case when isnull(a.bonbu,'') != '' and (isnull(a.jisa,'') != '' or isnull(a.team,'') != '')  then ' > ' else '' end +
				case when isnull(a.jisa,'') != '' then substring(c.jsname,1,4) else '' end +
				case when isnull(a.jisa,'') != '' and isnull(a.jijum,'') != '' then ' > ' else '' end +
				case when isnull(a.jijum,'') != '' then substring(d.jname,1,4) else '' end +
				case when isnull(a.jijum,'') != '' and isnull(a.team,'') != '' then ' > ' else '' end +
				case when isnull(a.team,'') != '' then e.tname else '' end as sosok,
				a.ydate,
				a.tbit,
				a.pbit,
				a.pos,
				f.subnm pos_nm,
				a.jik,
				case when len(a.htel1+a.htel2+a.htel3) >= 9 then isnull(a.htel1,'')+'-'+isnull(a.htel2,'')+'-'+isnull(a.htel3,'') else '' end htel,				
				a.mcode,
				case when isnull(a.mcode,'') != '' then s.sname+'('+a.mcode+')' else '' end mcode_nm
		from swon a
			left outer join bonbu b on a.scode = b.scode and a.bonbu = b.bcode
			left outer join jisa c on a.scode = c.scode and a.jisa = c.jscode
			left outer join jijum d on a.scode = d.scode and a.jijum = d.jcode
			left outer join team e on a.scode = e.scode and a.team = e.tcode
			left outer join swon s on a.scode = s.scode and a.mcode = s.skey
			left outer join common f on a.scode = f.scode and f.code = 'COM006' and a.pos = f.codesub
		where a.scode = '".$_SESSION['S_SCODE']."'	
		  and a.skey = '".$_GET['kskey']."' ";

	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet	= sqlsrv_fetch_array($qry));

}


// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?kcode=".$kcode."&inscode=".$kcode."&kskey=".$kskey,
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);


?>

<!-- html���� -->
<style>
body{background-image: none;}

.tb_type01.view{
	margin-bottom:10px;
}


div.tb_type01 th.obj {
    background: #92A2C9;
    color: #fff;
}


</style>



<div class="tit_wrap mt20">
	<span class="btn_wrap">
		<a href="#" class="btn_s white" style="min-width:100px;" onclick="pop_close();">�ݱ�</a>
	</span>
</div>

<!-- ���� �и��Ͽ� �������� ���� / �ƴ� ȭ�鸶�� �׸�ٸ��� �������� ���� �ϴ� ���������� ȭ�鸶�� ��-->
<div class="tb_type01 view">
	<table class="">
			<colgroup>
				<col width="10%">
				<col width="20%">
				<col width="10%">
				<col width="20%">
				<col width="10%">
				<col width="auto">
			</colgroup>
		<tbody class="kwndata">

			<tr>	
				<th>�����ȣ</th><td><?=$skey?></td>
				<th>����θ�</th><td><?=$sname?></td>
				<th>�Ҽ�����</th><td><?=$sosok?></td>
			</tr>
			<tr>	
				<th>��������</th><td><?if(trim($ydate)) echo date("Y-m-d",strtotime($ydate))?></td>
				<th>��������</th><td><?=$conf['swon_tbit'][$tbit]?></td>
				<th>��������</th><td><?=$conf['pbit'][$pbit]?></td>
			</tr>
			<tr>					
				<th>��ũ����</th><td><?=$mcode_nm?></td>
				<th>��������</th><td><?=$conf['jik'][$jik]?></td>
				<th>����</th><td><?=$pos_nm?></td>
			</tr>
			<tr>	
				<th>�޴���ȭ</th><td><?=$htel?></td>			
				<th></th><td></td>
				<th></th><td></td>
			</tr>
		</tbody>
	</table>
</div>

<!-- //box_gray -->
<div class="tb_type01" style="height:650px;overflow-y:auto;border-top: 1px solid #47474a;">
	<table id="sort_sub10" class="gridhover" >
		<colgroup>
			<col width="50px">
			<col width="90px">
			<col width="90px">
			<col width="70px">
			<col width="100px"> <!--��������-->

			<col width="100px">  <!--ȯ��-->
			<col width="100px">  <!--��Ȱ-->
			<col width="100px">  <!--���������-->
			<col width="100px">  <!--ȯ�꺸���-->
			
			<col width="100px">  <!--������-->
			<col width="auto">  <!--������-->

		</colgroup>
		<thead>
		<tr class="rowTop">
			<th align="center">�����</th>
			<th align="center">���ǹ�ȣ</th>
			<th align="center">�����</th>
			<th align="center">����ȸ��</th>					
			<th align="right">�������հ�</th>

			<th align="right">ȯ���ݾ�</th>
			<th align="right">��Ȱ�ݾ�</th>
			<th align="right">���������</th>
			<th align="right">ȯ�꺸���</th>
			
			<th align="center">������</th>
			<th align="center">������</th>
		</tr>
		</thead>
			<?if(!empty($listData)){?>						
			<tr style="background-color: bisque;">
			<th></th>
			<th></th>
			<th></th>
			<th class="font_red" style="text-align:right;font-weight:700;font-size: 13px;color: hotpink;padding:5px 0"><?=number_format($totalResult['CNT'])?>��</th>							
			<th class="font_red"style="text-align:right;font-weight:700;font-size: 13px;color: hotpink;padding:5px 0"><?=number_format($totalResult['s_total'])?></th>

			<th class="font_red"style="text-align:right;font-weight:700;font-size: 13px;color: hotpink;padding:5px 8px 5px 0;"><?=number_format($totalResult['hssu'])?></th>
			<th class="font_red"style="text-align:right;font-weight:700;font-size: 13px;color: hotpink;padding:5px 8px 5px 0;"><?=number_format($totalResult['bsu'])?></th>
			<th class="font_red"style="text-align:right;font-weight:700;font-size: 13px;color: hotpink;padding:5px 8px 5px 0;"><?=number_format($totalResult['mamt'])?></th>
			<th class="font_red"style="text-align:right;font-weight:700;font-size: 13px;color: hotpink;padding:5px 8px 5px 0;"><?=number_format($totalResult['hwanamt'])?></th>

			<th></th>
			<th></th>
			</tr>
			<?}?>
		<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" rol-data='<?=$ORIDATA?>' rol-file='<?=$filename?>' rol-iseq='<?=$ISEQ?> '>
				<td align="center"><?if(trim($YYMM)) echo date("Y-m",strtotime($YYMM))?></td>
				<td align="left"><?=$KCODE?></td>
				<td align="center" ><?=$kwn_kname?></td>
				<td class="font_red" style="text-align:right;font-weight:700;font-size: larger;" ><?=$NCNT?></td> 
				<td class="font_red" style="text-align:right;font-weight:700;font-size: larger;" ><?=number_format($S_TOTAL)?></td>

				<td align="right"><?=number_format($HSSU)?></td>
				<td align="right"><?=number_format($BSU)?></td>
				<td align="right"><?=number_format($MAMT)?></td>
				<td align="right"><?=number_format($HWANAMT)?></td>
				
				<td><?if(trim($EDATE)) echo date("Y-m-d",strtotime($EDATE))?></td>	
				<td align="center"><?=$ISTBIT?></td>	
				
			</tr>
			<?}}else{?>
				<tr>
					<td style="color:#8C8C8C" colspan=11>�˻��� �����Ͱ� �����ϴ�</td>
				</tr>
			<?}?>
		</tbody>
	</table>
</div><!-- // tb_type01 -->

<div style="text-align: center">		
	<ul class="pagination pagination-sm pop_sub10" style="margin: 10px">
	  <?=$pagination->create_links();?>
	</ul>
</div>


<script type="text/javascript">

// �ݱ�
function pop_close(){	
	window.close();
	//opener.location.reload();
}


// ��� Ŭ��
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));

	// include/bottom.php ����(������ summary ���� ��� ������������ Y�� ȣ��)
	sortTable("sort_sub10", idx, 2);
})


// �Աݻ� display 
function ins_display(oridata,filename,iseq){

	var left = Math.ceil((window.screen.width - 1200)/2);
	var top = Math.ceil((window.screen.height - 1000)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_62_list_pop.php?oridata="+oridata +"&filename=" +filename+"&iseq=" +iseq ,"insDt","width=1200px,height=800px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();

}


$(document).ready(function(){

	// page �Լ� ajax������ ����� ���� ó��
	$(".pop_sub10 a").click(function(){
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			ajaxLodingTarget(res[0],res[1],event,$('#kwnDt_data'));    
		}
		return false;
	});


	// ����Ʈ Ŭ���� �󼼳��� ��ȸ
	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));
		
		var oridata  = $(".rowData").eq(idx).attr('rol-data'); 
		var filename  = $(".rowData").eq(idx).attr('rol-file'); 
		var iseq  = $(".rowData").eq(idx).attr('rol-iseq'); 
		
		ins_display(oridata,filename,iseq); 
	})

});


</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>