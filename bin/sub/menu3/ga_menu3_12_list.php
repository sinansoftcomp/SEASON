<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");


$sdate1	= $_REQUEST['SDATE1'];
$sdate2	= $_REQUEST['SDATE2'];


//print_r($_REQUEST);


$where = "";

// ������ Ʈ�� ���ý� �Ҽ�����(swon ��Ī : s2 - kdman(����α���)) 
if($_REQUEST['id']){
	
	$Ngubun = substr($_REQUEST['id'],0,2);

	if($Ngubun == 'N1'){
		$bonbu = substr($_REQUEST['id'],2,10);
		$where  .= " and s2.bonbu = '".$bonbu."' " ;
	}else if($Ngubun == 'N2'){
		$jisa = substr($_REQUEST['id'],2,10);
		$where  .= " and s2.jisa = '".$jisa."' " ;
	}else if($Ngubun == 'N3'){
		$jijum = substr($_REQUEST['id'],2,10);
		$where  .= " and s2.jijum = '".$jijum."' " ;
	}else if($Ngubun == 'N4'){
		$team = substr($_REQUEST['id'],2,10);
		$where  .= " and s2.team = '".$team."' " ;
	}else if($Ngubun == 'N5'){
		$ksman = substr($_REQUEST['id'],2,10);
	}
}

// ���ڵ���ȯ(��ȸ�� postó�� / ����¡�� getó���ϴٺ��� �ѱ����ڵ� ��ȯ�� ��Ȳ�� ���� ��ȯ..������Ʈ ó�� �ƴϸ� �̷����� �ʿ�x)
if($_REQUEST['pageyn'] == 'Y'){
	$searchF1Text = $_REQUEST['searchF1Text'];
}else{
	$searchF1Text = iconv("UTF-8","EUCKR",$_REQUEST['searchF1Text']);
}

if($_REQUEST['searchF1'] && $_REQUEST['searchF1Text']){
	if($_REQUEST['searchF1'] == 'tel'){
		$where  .= " and (a.tel like replace('%".$_REQUEST['searchF1Text']."%','-','') or a.htel like replace('%".$_REQUEST['searchF1Text']."%','-','')) ";
	}else if($_REQUEST['skey'] && $_REQUEST['searchF1'] == 's1'){	//	�������
		$where  .= " and a.gskey = '".$_REQUEST['skey']."' ";	
	}else if($_REQUEST['skey'] && $_REQUEST['searchF1'] == 's2'){	//	�����
		$where  .= " and a.kskey = '".$_REQUEST['skey']."' ";	
	}else{		
		$where  .= " and ".$_REQUEST['searchF1']." like '%".$searchF1Text."%' ";	
	}
}

// �����
if($_REQUEST['inscode']){
	$where  .= " and a.inscode = '".$_REQUEST['inscode']."' " ;
}


// ��ǰ��
if($_REQUEST['insilj']){
	$where  .= " and a.insilj = '".$_REQUEST['insilj']."' " ;
}


// ������(��ȸ�� postó�� / ����¡�� getó���ϴٺ��� �ѱ����ڵ� ��ȯ�� ��Ȳ�� ���� ��ȯ..������Ʈ ó�� �ƴϸ� �̷����� �ʿ�x)
if($_REQUEST['pageyn'] == 'Y'){
	$kstbit = $_REQUEST['kstbit'];
}else{
	$kstbit = iconv("UTF-8","EUCKR",$_REQUEST['kstbit']);
}
if($_REQUEST['kstbit']){
	$where  .= " and replace(a.kstbit,' ','') = '".$kstbit."' " ;
}


// �⺻ ������ ����
$page = ($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$page_row	= 50;
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

//�˻� ������ ���ϱ� 
$sql= "
	select *
	from(
		select 
				a.kcode,
				a.insilj,
				a.inscode,
				f.name insname,
				case when isnull(s2.bonbu,'') != '' then substring(b.bname,1,2) else '' end +
				case when isnull(s2.bonbu,'') != '' and (isnull(s2.jisa,'') != '' or isnull(s2.team,'') != '')  then ' > ' else '' end +
				case when isnull(s2.jisa,'') != '' then substring(c.jsname,1,2) else '' end +
				case when isnull(s2.jisa,'') != '' and isnull(s2.jijum,'') != '' then ' > ' else '' end +
				case when isnull(s2.jijum,'') != '' then substring(d.jname,1,4) else '' end +
				case when isnull(s2.jijum,'') != '' and isnull(s2.team,'') != '' then ' > ' else '' end +
				case when isnull(s2.team,'') != '' then e.tname else '' end as sosok,
				a.ksman,
				a.kdman,
				s1.sname gskey_nm,
				s2.sname kskey_nm,
				dbo.GetCutStr(s1.sname,10,'..') gskey_Cnm,
				dbo.GetCutStr(s2.sname,10,'..') kskey_Cnm,
				a.kname,
				dbo.GetCutStr(a.kname,16,'..') kname_c,
				dbo.GetCutStr(a.pname,16,'..') pname_c,
				case when isnull(a.htel,'') != '' then a.htel else a.tel end telno,
				a.addr+' '+a.addr_dt addr,
				a.pname,
				a.kdate,
				a.fdate,
				a.tdate,
				a.item,
				a.itemnm,
				a.mamt,
				a.hamt,
				a.samt,
				a.kstbit,
				a.nbit,
				a.nterm,
				i.ipdate mx_ipdate,
				i.ncnt mx_ncnt,
				i.istbit,
				a.agency,
				row_number()over(order by a.kdate desc, f.name, a.kname) rnum
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
			left outer join (select *
							 from (select row_number()over(partition by kcode order by ipdate desc, ino desc, ncnt desc) num, *  from INS_SUNAB(nolock) where scode = '".$_SESSION['S_SCODE']."' ) tbl
							 where tbl.num = 1) i on a.inscode = i.inscode and  a.kcode = i.kcode
		where a.scode = '".$_SESSION['S_SCODE']."'
		  and a.kdate between '".str_replace('-','',$sdate1)."' and '".str_replace('-','',$sdate2)."'  ".$where."
		) p
	where rnum between ".$limit1." AND ".$limit2 ;

$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

/*
echo '<pre>';
echo $sql;
echo '</pre>';
*/

// ������ �� �Ǽ�
//�˻� ������ ���ϱ� 
$sql= "
		select 
				count(*) CNT,
				sum(mamt) sum_mamt,
				sum(samt) sum_samt
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
		  and a.kdate between '".str_replace('-','',$sdate1)."' and '".str_replace('-','',$sdate2)."' ".$where." " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 


// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=".$sdate1."&SDATE2=".$sdate2."&inscode=".$_REQUEST['inscode']."&insilj=".$_REQUEST['insilj']."&kstbit=".$kstbit."&searchF1=".$_REQUEST['searchF1']."&searchF1Text=".$searchF1Text."&skey=".$_REQUEST['skey']."&id=".$_REQUEST['id']."&pageyn=Y",
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);
// #FAF4C0 , #EBF7FF #A566FF
?>

<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
	<table id="sort_table" class="gridhover" style="min-width: 3000px;">
		<colgroup>
			<col width="150px">
			<col width="100px">
			<col width="80px">

			<col width="160px">
			<col width="80px">
			<col width="80px">

			<col width="120px">
			<col width="120px">		
			
			<col width="90px">
			<col width="90px">			
			<col width="300px">

			<col width="80px">
			<col width="150px">

			<col width="70px">
			<col width="80px">
			<col width="80px">
			<col width="80px">
			<col width="80px">

			<col width="80px">
			<col width="80px">

			<col width="100px">
			<col width="90px">
			<col width="120px">
			<col width="auto">
		</colgroup>
		<thead>
			<tr class="rowTop">
				<th align="center">���ǹ�ȣ</th>
				<th align="center">�����</th>
				<th align="center">���豸��</th>	
				
				<th align="center">�Ҽ�</th>
				<th align="center">�������</th>
				<th align="center">�����</th>

				<th align="center">�����</th>
				<th align="center">�Ǻ�����</th>
				
				<th align="center">�����</th>
				<th align="center">���������</th>		
				<th align="center">��ǰ</th>

				<th align="center">�������</th>
				<th align="center">��ళ������ ~ ��������</th>

				<th align="center">����ȸ��</th>
				<th align="center">����������</th>
				<th align="center">������</th>
				<th align="center">��������</th>
				<th align="center">�����������</th>

				<th align="center">��������ڵ�</th>
				<th align="center">������ڵ�</th>

				<th align="center">���Թ��</th>
				<th align="center">���ԱⰣ</th>
				<th align="center">��ȭ��ȣ</th>		
				<th align="center">�ּ�</th>
			</tr>
		</thead>

		<tbody>
			<tr class="summary sticky" style="top:32px">
				<th class="sumtext" ><?='��  ��'?></th>
				<th class="sum01"><?=number_format($totalResult['CNT']).'��'?></th>
	
				<th colspan=6></th>
				<th class="sum02"><?=number_format($totalResult['sum_mamt'])?></th>
				<th class="sum01"><?=number_format($totalResult['sum_samt'])?></th>

				<th colspan=14></th>
			
			</tr>


			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);
				if(strlen($telno) == 9){
					$tel = substr($telno,0,2).'-'.substr($telno,2,3).'-'.substr($telno,5,4);
				}else if(strlen($telno) == 10){
					$tel = substr($telno,0,3).'-'.substr($telno,3,3).'-'.substr($telno,6,4);
				}else if(strlen($telno) == 11){
					$tel = substr($telno,0,3).'-'.substr($telno,3,4).'-'.substr($telno,7,4);
				}else{
					$tel = substr($telno,0,3).'-'.substr($telno,3,4).'-'.substr($telno,7,4);
				}

				// ��ǰ�� ���� �ڸ���
				$item_nm = $itemnm; // title ���� �������� ����
				if(mb_strlen($itemnm) > 30){
					$itemnm = mb_substr($itemnm,0,30,'euc-kr').'...';
				}else{
					$itemnm = $itemnm;
				}
			
			?>

			<tr class="rowData" rol-date='<?=$kcode?>' rol-date2='<?=$inscode?>'>
				<td align="left"><?=$kcode?></td>
				<td align="left"><?=$insname?></td>
				<td align="left"><?=$conf['insilj'][$insilj]?></td>

				<td align="left"><?=$sosok?></td>
				<td align="left" title="gskey_nm"><?=$gskey_Cnm?></td>
				<td align="left" title="kskey_nm"><?=$kskey_Cnm?></td>

				<td align="left" title="kname"><?=$kname_c?></td>
				<td align="left" title="pname"><?=$pname_c?></td>				
				
				<td align="right" class="font_blue"><?=number_format($mamt)?></td>
				<td align="right"><?=number_format($samt)?></td>
				<td align="left" title="<?=$item_nm?>"><?=$itemnm?></td>

				<td><?if(trim($kdate)) echo date("Y-m-d",strtotime($kdate))?></td>		
				<td><?if(trim($fdate)) echo date("Y-m-d",strtotime($fdate)).' ~ '.date("Y-m-d",strtotime($tdate))?></td>			
				
				<td align="right"><?=number_format($mx_ncnt)?></td>
				<td><?if(trim($mx_ipdate)) echo date("Y-m-d",strtotime($mx_ipdate))?></td>		
				<td align="left"><?=$kstbit?></td>
				<td align="left"><?=$istbit?></td>
				<td align="left"><?=$kstbit?></td>

				<td align="left"><?=$ksman?></td>
				<td align="left"><?=$kdman?></td>

				<td align="left"><?=$nbit?></td>	
				<td align="left"><?=$nterm?></td>	
				<td align="center"><?=$tel?></td>		
				<td align="left"><?=$addr?></td>
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
	<ul class="pagination pagination-sm kwnlist" style="margin: 5px 5px 0 5px">
	  <?=$pagination->create_links();?>
	</ul>
</div>

<script type="text/javascript">


// ��� Ŭ��
$(".rowTop > th").click(function(){
	var trData = $(this).parent();

	var idx = $(trData).find("th").index($(this));

	// include/bottom.php ����	
	sortTable("sort_table", idx, 2);
})

$(document).ready(function(){

	var page="<?=$_REQUEST['page']?>";
	if(!page){
		page = "1";
	}

	$("#page").val(page);

	// page �Լ� ajax������ ����� ���� ó��
	$(".kwnlist a").click(function(){
		$('#page').val('<?=$page?>');
		var res = $(this).attr("href").split("?");
		if(res[0] && res[1]){
			//alert(res[0]+"//"+res[1]);
			 //data_right_jojik div id�� ����
			ajaxLodingTarget(res[0],res[1],event,$('#kwnlist'));    
		}
		return false;
	});

	// ����Ʈ Ŭ���� �󼼳��� ��ȸ
	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));

		var kcode	= $(".rowData").eq(idx).attr('rol-date');
		var inscode	= $(".rowData").eq(idx).attr('rol-date2');

		KwnIns(inscode,kcode);
		

	})


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>

