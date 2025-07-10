<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$inscode	= $_GET['inscode'];
$kcode		= $_GET['kcode'];

// �⺻ ������ ����
$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= 100;

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

// �������� ����Ʈ
if($_GET['kcode']){

	//�˻� ������ ���ϱ� 
	$sql= "
		select *
		from(
			select 
					a.*, g.filename, row_number()over(order by a.ncnt desc) rnum
			from INS_SUNAB(nolock) a	
				left outer join upload_history(nolock) g on a.scode = g.scode and a.ipdate = g.upldate 
								and a.gubun = g.gubun and a.gubunsub = g.gubunsub and a.ino = g.uplnum
			where a.scode = '".$_SESSION['S_SCODE']."'
			  and a.kcode = '".$_GET['kcode']."'
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
					sum(samt) samt
			from INS_SUNAB(nolock) a
			where a.scode = '".$_SESSION['S_SCODE']."'
			  and a.kcode = '".$_GET['kcode']."' " ;
	 
	$qry =  sqlsrv_query($mscon, $sql);
	$totalResult =  sqlsrv_fetch_array($qry);
	

	// ��������� ����
	$sql="
		select 
				a.kcode,
				a.insilj,
				a.inscode,
				f.name insname,
				case when isnull(s2.bonbu,'') != '' then substring(b.bname,1,2) else '' end +
				case when isnull(s2.bonbu,'') != '' and (isnull(s2.jisa,'') != '' or isnull(s2.team,'') != '')  then ' > ' else '' end +
				case when isnull(s2.jisa,'') != '' then substring(c.jsname,1,4) else '' end +
				case when isnull(s2.jisa,'') != '' and isnull(s2.jijum,'') != '' then ' > ' else '' end +
				case when isnull(s2.jijum,'') != '' then substring(d.jname,1,4) else '' end +
				case when isnull(s2.jijum,'') != '' and isnull(s2.team,'') != '' then ' > ' else '' end +
				case when isnull(s2.team,'') != '' then e.tname else '' end as sosok,
				a.ksman,
				a.kdman,
				case when isnull(a.gskey,'') != '' then s1.sname+'('+a.gskey+')' else '' end gskey_nm,
				case when isnull(a.kskey,'') != '' then s2.sname+'('+a.kskey+')' else '' end kskey_nm,
				a.kname,
				case when isnull(a.htel,'') != '' then a.htel else a.tel end telno,
				a.addr+' '+a.addr_dt addr,
				a.pname,
				a.kdate,
				a.fdate,
				a.tdate,
				a.item,
				a.itemnm,
				case when isnull(a.item,'') != '' then dbo.GetCutStr('('+a.item+')'+a.itemnm,50,'..') else '' end item_nm,
				a.mamt,
				a.hamt,
				a.samt,
				a.kstbit
		from kwn(nolock) a	
			left outer join inssetup(nolock) f on a.scode = f.scode and a.inscode = f.inscode
			left outer join inswon(nolock) is1 on a.scode = is1.scode and a.ksman = is1.bscode
			left outer join inswon(nolock) is2 on a.scode = is2.scode and a.kdman = is2.bscode
			left outer join swon(nolock) s1 on s1.scode = a.scode and s1.skey = is1.skey
			left outer join swon(nolock) s2 on s2.scode = a.scode and s2.skey = is2.skey
			left outer join bonbu(nolock) b on s2.scode = b.scode and s2.bonbu = b.bcode
			left outer join jisa(nolock) c on s2.scode = c.scode and s2.jisa = c.jscode
			left outer join jijum(nolock) d on s2.scode = d.scode and s2.jijum = d.jcode
			left outer join team(nolock) e on s2.scode = e.scode and s2.team = e.tcode
		where a.scode = '".$_SESSION['S_SCODE']."'	
		  and a.inscode = '".$_GET['inscode']."'
		  and a.kcode = '".$_GET['kcode']."'
	";

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
				<th>���ǹ�ȣ</th><td><?=$kcode?></td>
				<th>�����</th><td><?=$kname?></td>
				<th>�Ǻ�����</th><td><?=$pname?></td>
			</tr>
			<tr>	
				<th>�����</th><td><?=$insname?></td>
				<th>���豸��</th><td><?=$conf['insilj'][$insilj]?></td>
				<th>������</th><td><?=$kstbit?></td>
			</tr>
			<tr>					
				<th>�������</th><td><?=$gskey_nm?></td>
				<th>�����</th><td><?=$kskey_nm?></td>
				<th>�Ҽ�</th><td><?=$sosok?></td>
			</tr>
			<tr>	
				<th>�������</th><td><?if(trim($kdate)) echo date("Y-m-d",strtotime($kdate))?></td>				
				<th>�����</th><td class="font_blue"><?=number_format($mamt).' ��'?></td>
				<th>��ǰ</th><td title="<?=$itemnm?>"><?=$item_nm?></td>
			</tr>
		</tbody>
	</table>
</div>

<!-- //box_gray -->
<div class="tb_type01" style="height:650px;overflow-y:auto;border-top: 1px solid #47474a;">
	<table id="sort_sub02" class="gridhover" >
		<colgroup>
			<col width="100px"> 
			<col width="100px">
			<col width="100px">
			<col width="100px">
			<col width="100px">
			<col width="100px">
			<col width="100px">
			<col width="100px"> 
			<col width="100px">
			<col width="auto">

		</colgroup>
		<thead>
			<tr class="rowTop">		
				<th align="center">����ó����</th>
				<th align="center">�Աݳ����</th>						
				<th align="center">��������</th>
				<th align="right">����ȸ��</th>
				<th align="right">�����ݾ�</th>
				<th align="center">���谳����</th>
				<th align="center">����������</th>					
				<th align="right">�����ֱ�</th>
				<th align="right">���Թ��</th>
				<th align="right">������</th>				
			</tr>
		</thead>

			<?if(!empty($listData)){?>
			
			<tr class="summary">
				<th></th>
				<th></th>
				<th></th>
				<th class="sum02" ><?=number_format($totalResult['CNT'])?>��</th>							
				<th class="sum01" ><?=number_format($totalResult['samt'])?></th>				
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>				
			</tr>
			<?}?>

		<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" rol-data='<?=$ORIDATA?>' rol-file='<?=$filename?>' rol-iseq='<?=$ISEQ?> '>

				<td><?if(trim($IPDATE)) echo date("Y-m-d",strtotime($IPDATE))?></td>
				<td><?if(trim($YYMMDD)) echo date("Y-m-d",strtotime($YYMMDD))?></td>		
				<td><?if(trim($YYMM)) echo date("Y-m",strtotime($YYMM))?></td>		
				<td align="right" class="font_red"><?=number_format($NCNT)?></td>
				<td align="right" class="font_red"><?=number_format($SAMT)?></td>
				<td><?if(trim($ADATE)) echo date("Y-m-d",strtotime($ADATE))?></td>		
				<td><?if(trim($BDATE)) echo date("Y-m-d",strtotime($BDATE))?></td>		
				<td align="center"><?=$NJUKI?></td>
				<td align="center"><?=$NBIT?></td>
				<td align="center"><?=$ISTBIT?></td>
			</tr>
			<?}}else{?>
				<tr>
					<td style="color:#8C8C8C" colspan=14>�˻��� �����Ͱ� �����ϴ�</td>
				</tr>
			<?}?>
		</tbody>
	</table>
</div><!-- // tb_type01 -->

<div style="text-align: center">		
	<ul class="pagination pagination-sm pop_sub02" style="margin: 10px">
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
	sortTable("sort_sub02", idx, 2);
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
	$(".pop_sub02 a").click(function(){
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