<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

/*-----------------------------------------------------------------
����� ��೻��
-----------------------------------------------------------------*/
$inscode	= $_GET['inscode'];
$kcode		= $_GET['kcode'];
$kskey		= $_GET['kskey'];

// �⺻ ������ ����
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 100;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

// �������� ����Ʈ
if($_GET['kskey']){

	//�˻� ������ ���ϱ� 
	$sql= "
		select *
		from(
			select 
					a.yymm,
					isnull(a.IPMST1,0) IPMST1,
					isnull(a.IPMST2,0) IPMST2,
					isnull(a.IPMST3,0) IPMST3,
					isnull(a.IPMST4,0) IPMST4,
					isnull(b.su1,0) su1,
					isnull(b.su2,0) su2,
					isnull(b.su3,0) su3,
					isnull(b.su4,0) su4,
					isnull(a.IPMST4,0) - isnull(b.su4,0) CATOT,
					isnull(c.SUNAB,0) SUNAB,
					isnull(d.kwncnt,0) kwncnt,
					row_number()over(order by a.yymm desc) rnum
			from (
				SELECT a.yymm,
						sum(case when a.insilj = '1' then  
								isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
								+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0)  else 0  end) IPMST1 ,
						sum(case when a.insilj = '2' then  
								isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
								+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0)  else 0  end) IPMST2,
						sum(case when a.insilj = '3' then  
								isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
								+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0)  else 0  end) IPMST3,
						sum(isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
							+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0))      IPMST4
							
				FROM INS_IPMST(nolock) a 
					left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
					left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
				where a.scode = '".$_SESSION['S_SCODE']."' and c.skey = '".$_GET['kskey']."'
				group by a.yymm) a 	
					left outer join (
									SELECT a.yymm, 
											sum(case when a.INSILJ = '1' then isnull(a.SUAMT,0)  else 0  end) su1 ,
											sum(case when a.INSILJ = '2' then isnull(a.SUAMT,0)  else 0  end) su2 ,
											sum(case when a.INSILJ = '3' then isnull(a.SUAMT,0)  else 0  end) su3 ,
											sum(isnull(a.SUAMT,0)) su4
									FROM SUDET(nolock) a 
										left outer join swon(nolock)  c on  a.scode = c.scode and  a.SKEY = c.SKEY
									where a.scode = '".$_SESSION['S_SCODE']."' and a.skey = '".$_GET['kskey']."'
									group by a.yymm ) b on a.yymm = b.yymm
					left outer join (
									SELECT substring(a.ipdate,1,6) yymm, SUM(a.SAMT) SUNAB
									FROM INS_SUNAB(nolock) a 
										left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
										left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
									where a.scode ='".$_SESSION['S_SCODE']."'  and c.skey = '".$_GET['kskey']."'
									group by substring(a.ipdate,1,6) ) c on a.yymm = c.yymm
					left outer join(
									select substring(kdate,1,6) yymm,
										   count(*) kwncnt
									from kwn
									where scode = '".$_SESSION['S_SCODE']."' and kskey = '".$_GET['kskey']."'
									group by substring(kdate,1,6)) d on a.yymm = d.yymm
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
					count(*) CNT,
					sum(isnull(a.IPMST1,0)) IPMST1,
					sum(isnull(a.IPMST2,0)) IPMST2,
					sum(isnull(a.IPMST3,0)) IPMST3,
					sum(isnull(a.IPMST4,0)) IPMST4,
					sum(isnull(b.su1,0)) su1,
					sum(isnull(b.su2,0)) su2,
					sum(isnull(b.su3,0)) su3,
					sum(isnull(b.su4,0)) su4,
					sum(isnull(d.kwncnt,0)) kwncnt,
					sum(isnull(a.IPMST4,0) - isnull(b.su4,0)) CATOT,
					sum(isnull(c.SUNAB,0)) SUNAB
			from(
				SELECT a.yymm,
						sum(case when a.insilj = '1' then  
								isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
								+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0)  else 0  end) IPMST1 ,
						sum(case when a.insilj = '2' then  
								isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
								+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0)  else 0  end) IPMST2,
						sum(case when a.insilj = '3' then  
								isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
								+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0)  else 0  end) IPMST3,
						sum(isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
							+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0))      IPMST4
							
				FROM INS_IPMST(nolock) a 
					left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
					left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
				where a.scode = '".$_SESSION['S_SCODE']."' and c.skey = '".$_GET['kskey']."'
				group by a.yymm) a 
					left outer join (
									SELECT a.yymm, 
											sum(case when a.INSILJ = '1' then isnull(a.SUAMT,0)  else 0  end) su1 ,
											sum(case when a.INSILJ = '2' then isnull(a.SUAMT,0)  else 0  end) su2 ,
											sum(case when a.INSILJ = '3' then isnull(a.SUAMT,0)  else 0  end) su3 ,
											sum(isnull(a.SUAMT,0)) su4
									FROM SUDET(nolock) a 
										left outer join swon(nolock)  c on  a.scode = c.scode and  a.SKEY = c.SKEY
									where a.scode = '".$_SESSION['S_SCODE']."' and a.skey = '".$_GET['kskey']."'
									group by a.yymm ) b on a.yymm = b.yymm
					left outer join (
									SELECT substring(a.ipdate,1,6) yymm, SUM(a.SAMT) SUNAB
									FROM INS_SUNAB(nolock) a 
										left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
										left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
									where a.scode ='".$_SESSION['S_SCODE']."'  and c.skey = '".$_GET['kskey']."'
									group by substring(a.ipdate,1,6) ) c on a.yymm = c.yymm
					left outer join(
									select substring(kdate,1,6) yymm,
										   count(*) kwncnt
									from kwn
									where scode = '".$_SESSION['S_SCODE']."' and kskey = '".$_GET['kskey']."'
									group by substring(kdate,1,6)) d on a.yymm = d.yymm		" ;
	 
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
		from swon(nolock) a
			left outer join bonbu(nolock) b on a.scode = b.scode and a.bonbu = b.bcode
			left outer join jisa(nolock) c on a.scode = c.scode and a.jisa = c.jscode
			left outer join jijum(nolock) d on a.scode = d.scode and a.jijum = d.jcode
			left outer join team(nolock) e on a.scode = e.scode and a.team = e.tcode
			left outer join swon(nolock) s on a.scode = s.scode and a.mcode = s.skey
			left outer join common(nolock) f on a.scode = f.scode and f.code = 'COM006' and a.pos = f.codesub
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

.rowspan th{
    padding: 3px 0;
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
<div class="tb_type01 rowspan" style="height:650px;overflow-y:auto;border-top: 1px solid #47474a;">
	<table id="sort_sub08" class="gridhover" >
		<colgroup>
 
			<col width="60px">
			<col width="60px">

			<col width="85px">
			<col width="85px">
			<col width="85px">
			<col width="105px">

			<col width="85px">
  			<col width="85px">
			<col width="85px">
			<col width="105px">


			<col width="100px">
			<col width="auto">
		</colgroup>

		<thead>
			<tr class="rowTop">
 
				<th rowspan=2 align="center">���</th>
				<th rowspan=2 align="center">�Ǽ�</th>
				
				<th colspan=4 align="left" style="border-right: 1px solid #c7c7c7;">���Լ�����</th>
				<th colspan=4 align="left" style="border-right: 1px solid #c7c7c7;">���޼�����</th>
				<th rowspan=2 align="center" style="border-left:1px solid #c7c7c7;">������(A-B)</th>
				<th rowspan=2 align="center" style="border-left:1px solid #c7c7c7;">�����ݾ�</th>
			</tr>
			<tr> 
	 
				<th align="center">�Ϲ�</th>
				<th align="center">���</th>
				<th align="center">�ڵ���</th>
				<th align="center" style="border-right: 1px solid #c7c7c7;">�Ұ�(A)</th>

				<th align="center">�Ϲ�</th>
				<th align="center">���</th>
				<th align="center">�ڵ���</th>
				<th align="center" style="border-right: 1px solid #c7c7c7;">�Ұ�(B)</th>
  
			</tr>
		</thead>		


		<tbody>
			<tr  class="summary" style="top:46px">
				<th class="sumtext"><?='��  ��'?></th>
				<th class="sum01"><?=number_format($totalResult['kwncnt'])?></th>
	
				<th class="sum02" ><?=number_format($totalResult['IPMST1'])?></th>
				<th class="sum02"><?=number_format($totalResult['IPMST2'])?></th>
				<th class="sum02"><?=number_format($totalResult['IPMST3'])?></th>
				<th style="text-align: right;padding-right: 10px;color: crimson; border-right: 1px solid #c7c7c7;" ><?=number_format($totalResult['IPMST4'])?></th>
	 


				<th class="sum02" ><?=number_format($totalResult['su1'])?></th>
				<th class="sum02" ><?=number_format($totalResult['su2'])?></th>
				<th class="sum02" ><?=number_format($totalResult['su3'])?></th>
				<th class="sum02" style="border-right: 1px solid #c7c7c7;"><?=number_format($totalResult['su4'])?></th>

				<th class="sum02" style="border-right: 1px solid #c7c7c7;"><?=number_format($totalResult['CATOT'])?></th>
				<th class="sum02" style="border-right: 1px solid #c7c7c7;"><?=number_format($totalResult['SUNAB'])?></th>
			
			</tr>

 
			<?if(!empty($listData)){?>

			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" rol-data='<?=$swonskey?>', rol-yymm='<?=$yymm?>'>
				<td align="center"><?if(trim($yymm)) echo date("Y-m",strtotime($yymm.'01'))?></td>	
				<td align="right" ><?=number_format($kwn_cnt)?></td>

				<td align="right" ><?=number_format($IPMST1)?></td>
				<td align="right" ><?=number_format($IPMST2)?></td>
				<td align="right" ><?=number_format($IPMST3)?></td>
				<td align="right"  style="color: crimson; border-right: 1px solid #c7c7c7;" ><?=number_format($IPMST4)?></td>
	 


				<td align="right" ><?=number_format($su1)?></td>
				<td align="right" ><?=number_format($su2)?></td>
				<td align="right" ><?=number_format($su3)?></td>
				<td align="right"   style=" color: crimson; border-right: 1px solid #c7c7c7;"><?=number_format($su4)?></td>

				<td align="right"   style=" border-right: 1px solid #c7c7c7;"><?=number_format($CATOT)?></td>
				<td align="right"   style=" border-right: 1px solid #c7c7c7;"><?=number_format($SUNAB)?></td>


				<td></td>
			</tr>
			<?}}else{?>
				<tr>
					<td style="color:#8C8C8C" colspan=24>�˻��� �����Ͱ� �����ϴ�</td>
				</tr>
			<?}?>
		</tbody>
	</table>
</div><!-- // tb_type01 -->

<div style="text-align: center">		
	<ul class="pagination pagination-sm pop_sub08" style="margin: 10px">
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
	sortTable("sort_sub08", idx, 2);
})


// ���� display 
function kwn_display(){


}


$(document).ready(function(){

	// page �Լ� ajax������ ����� ���� ó��
	$(".pop_sub08 a").click(function(){
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
		
		//kwn_display(); 
	})

});


</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>