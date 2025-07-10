<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$page = ($_GET['page']) ? $_GET['page'] : 1;
$page_row	= $conf['pageRow']; // �������� ������ rows��  �⺻ conf 25��
$page_row	= "20"; // �������� ������ ���� 20���� �����Ұ�� �̷������� �ϸ� ��

$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

$srchText	=	str_replace("-","",$_GET['srchText']);

$sql	= "Select rnum,
				  *
			From (
					select 
							a.scode,
							a.gcode,
							a.kname,
							a.sbit,
							dbo.decryptkey(a.sjuno) sjuno,
							a.snum,
							case when a.sbit = '1' then dbo.decryptkey(a.sjuno) else a.snum end secdata,
							a.comnm,
							a.cupnm,
							a.emailsel,
							a.email,
							a.telbit,
							a.tel1,
							a.tel2,
							a.tel3,
							a.tel1+'-'+a.tel2+'-'+a.tel3 tel,
							a.htel1,
							a.htel2,
							a.htel3,
							a.htel1+'-'+a.htel2+'-'+a.htel3 htel,
							case when len(isnull(a.htel1,'')+'-'+isnull(a.htel2,'')+'-'+isnull(a.htel3,'')) > 2 then isnull(a.htel1,'')+'-'+isnull(a.htel2,'')+'-'+isnull(a.htel3,'') 
								 else isnull(a.tel1,'')+'-'+isnull(a.tel2,'')+'-'+isnull(a.tel3,'') end totaltel,
							a.addbit,
							a.post,
							a.addr,
							a.addr_dt,
							a.bigo,
							a.sugi,
							a.kdate,
							convert(varchar,a.idate,21) idate,
							a.iswon,
							b.sname isname,
							a.udate,
							a.uswon,
							c.sname usname,
							a.ksman,
							e.sname ksname,
							row_number()over(order by a.kdate desc, a.gcode desc) rnum
					from kwngo a
						left outer join swon b on a.scode = b.scode and a.iswon = b.skey
						left outer join swon c on a.scode = c.scode and a.uswon = c.skey
						left outer join swon e on a.scode = e.scode and a.ksman = e.skey
					where a.scode = '".$_SESSION['S_SCODE']."'
					  and (ltrim(a.kname) Like '%".$_GET['srchText']."%'  or ltrim(dbo.decryptkey(a.sjuno))  Like '%".$srchText."%' or ltrim(a.snum)  Like '%".$srchText."%' )
				 ) P
			WHERE rnum between ".$limit1." AND ".$limit2  ;

$result	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $row;
}

$sql	= "Select  Count(*) cnt
			from kwngo a
			Where a.scode = '".$_SESSION['S_SCODE']."'
			  and (ltrim(a.kname) Like '%".$_GET['srchText']."%'  or ltrim(dbo.decryptkey(a.sjuno))  Like '%".$srchText."%') ";

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 
$totalpage = ceil($totalResult['cnt'] / $page_row);

// ������ Ŭ���� ����
include_once($conf['rootDir'].'/include/class/pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?srchText=".$_GET['srchText'],
		'per_page' => $page_row,
		'total_rows' => $totalResult['cnt'],
		'cur_page' => $page,
));

?>
<style>
body{background-image: none;}
.box_wrap{margin-bottom:0px;padding:15px 20px;}
.tb_type01 th, .tb_type01 td {padding: 4px 0;}
</style>

<div class="box_wrap sel_btn">
	<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>">
		<input type="hidden" name='row' value='<?=$_GET['row']?>'>

		<input type="text" style="width:220px;font-size:12px;text-align:center;height:20px;margin-top:0px;" placeholder=" ���� OR �ֹ�/����ڹ�ȣ" name="srchText" id="srchText" class="srchText"  value=<?=$_GET['srchText']?>>
		<a href="#" class="btn_s white" id="SearchBtn">�˻�</a>
		<a href="#" class="btn_s white" onclick="self.close();">�ݱ�</a>

    </form>
</div>

<div class="tit_wrap" style="padding:0 20px">
	<div class="tb_type01 tb_fix">
		<table class="gridhover">
			<colgroup>
				<col width="20%">
				<col width="20%">
				<col width="20%">
				<col width="20%">
				<col width="20%">
			</colgroup>
			<thead>
			<tr>
				<th>����</th>
				<th>�ֹ�/����ڹ�ȣ</th>
				<th>����ó</th>
				<th>��������</th>
				<th>�����</th>				
			</tr>
			</thead>
			<tbody>
			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);
				if($sbit == '1'){
					$sec_data = substr($secdata,0,6).'-'.substr($secdata,6,7);
				}else{
					$sec_data = substr($secdata,0,3).'-'.substr($secdata,3,2).'-'.substr($secdata,5,5);
				}

				if($totaltel == '--'){
					$totaltel = '';
				}			
			?>
			<tr class="rowData" rol-data1='<?=$kname?>' rol-data2='<?=$sbit?>' rol-data3='<?=$sec_data?>' rol-data4='<?=$comnm?>' rol-data5='<?=$cupnm?>' rol-data6='<?=$telbit?>' rol-data7='<?=$tel?>' rol-data8='<?=$htelbit?>' rol-data9='<?=$htel?>' rol-data10='<?=$addbit?>' rol-data11='<?=$post?>' rol-data12='<?=$addr?>' rol-data13='<?=$addrdt?>' rol-data14='<?=$emailsel?>' rol-data15='<?=$email?>' rol-data16='<?=$bigo?>' >
				<td align="left"><?=$kname?></td>
				<td><?=$sec_data?></td>
				<td><?=$totaltel?></td>
				<td><?if(trim($kdate)) echo date("Y-m-d",strtotime($kdate))?></td>
				<td><?=$ksname?></td>				
			</tr>
			<?}}?>
			</tbody>
		</table>
	</div>
</div>

<div style="text-align: center">		
	<ul class="pagination pagination-sm" style="margin: 10px">
	  <?=$pagination->create_links();?>
	</ul>
</div>	


<script type="text/javascript">

	window.resizeTo("800", "800");                             // ������ ��������

$(document).ready(function(){
	$("input[name='srchText']").focus();

	$("#SearchBtn").on("click", function(){	
		$("form[name='searchFrm']").submit();
	});

	$(".rowData").click(function(){
		var idx			= $(".rowData").index($(this));
		var name		= $(".rowData").eq(idx).attr("rol-data1");
		var sbit		= $(".rowData").eq(idx).attr("rol-data2");
		var sec_data	= $(".rowData").eq(idx).attr("rol-data3");
		var comnm		= $(".rowData").eq(idx).attr("rol-data4");
		var cupnm		= $(".rowData").eq(idx).attr("rol-data5");
		var telbit		= $(".rowData").eq(idx).attr("rol-data6");
		var tel			= $(".rowData").eq(idx).attr("rol-data7");
		var htelbit		= $(".rowData").eq(idx).attr("rol-data8");
		var htel		= $(".rowData").eq(idx).attr("rol-data9");
		var addbit		= $(".rowData").eq(idx).attr("rol-data10");

		var post		= $(".rowData").eq(idx).attr("rol-data11");
		var addr		= $(".rowData").eq(idx).attr("rol-data12");
		var addrdt		= $(".rowData").eq(idx).attr("rol-data13");
		var emailsel	= $(".rowData").eq(idx).attr("rol-data14");
		var email		= $(".rowData").eq(idx).attr("rol-data15");
		var bigo		= $(".rowData").eq(idx).attr("rol-data16");


		var row = "<?=$_GET['row']?>";

		opener.setKwngoValue(row,name,sbit,sec_data,comnm,cupnm,telbit,tel,htelbit,htel,addbit,post,addr,addrdt,emailsel,email,bigo);
		self.close();

	});

});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>