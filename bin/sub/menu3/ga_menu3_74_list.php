<?
//error_reporting(E_ALL); ini_set('display_errors', 1);
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");



if(isset($_REQUEST['SDATE1'])){
	$FYYMM   = substr($_REQUEST['SDATE1'],0,4).substr($_REQUEST['SDATE1'],5,2);
	$TYYMM  =  substr($_REQUEST['SDATE2'],0,4).substr($_REQUEST['SDATE2'],5,2);
}else{
	$FYYMM = date("Ym");
	$TYYMM = date("Ym");
}



// ���ڵ���ȯ
$searchF1Text = iconv("UTF-8","EUCKR",$_REQUEST['searchF1Text']);
if($_REQUEST['searchF1'] && $_REQUEST['searchF1Text']){
	if($_REQUEST['searchF1'] == 'sjuno'){
		$where  .= " and (a.snum like '%".$_REQUEST['searchF1Text']."%' or Cast(dbo.DECRYPTKEY(a.sjuno) as varchar) like '%".$_REQUEST['searchF1Text']."%') ";
	}else if($_REQUEST['searchF1'] == 'tel'){
		$where  .= " and (a.tel like replace('%".$_REQUEST['searchF1Text']."%','-','') or a.htel like replace('%".$_REQUEST['searchF1Text']."%','-','')) ";
	}else if($_REQUEST['skey'] && $_REQUEST['searchF1'] == 's1'){	//	�������
		$where  .= " and a.ksman = '".$_REQUEST['skey']."' ";	
	}else if($_REQUEST['skey'] && $_REQUEST['searchF1'] == 's2'){	//	�������
		$where  .= " and a.kdman = '".$_REQUEST['skey']."' ";	
	}else{		
		$where  .= " and ".$_REQUEST['searchF1']." like '%".$searchF1Text."%' ";	
	}
}

$where = "";

// ������ Ʈ�� ���ý� �Ҽ�����(swon ��Ī : s2 - kdman(����α���)) 
if($_REQUEST['id']){
	
	$Ngubun = substr($_REQUEST['id'],0,2);

	if($Ngubun == 'N1'){
		$bonbu = substr($_REQUEST['id'],2,10);
		$where  .= " and e.bcode = '".$bonbu."' " ;
	}else if($Ngubun == 'N2'){
		$jisa = substr($_REQUEST['id'],2,10);
		$where  .= " and f.jscode = '".$jisa."' " ;
	}else if($Ngubun == 'N3'){
		$jijum = substr($_REQUEST['id'],2,10);
		$where  .= " and g.jcode = '".$jijum."' " ;
	}else if($Ngubun == 'N4'){
		$team = substr($_REQUEST['id'],2,10);
		$where  .= " and h.tcode = '".$team."' " ;
	}else if($Ngubun == 'N5'){
		$ksman = substr($_REQUEST['id'],2,10);
	}
}


/* ------------------------------------------------------
	�⵵ / �˻����� / �� ��ȸ�� ���� End
------------------------------------------------------ */




// �⺻ ������ ����
$page = ($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$page_row	= 500;
$limit1 = ($page-1)*$page_row+1;
$limit2 = $page*$page_row;

 

//--->�����Լ����� ����縦 Ÿ��Ʋ�� �����ϱ����� �ش���� ������ �������� ������ �ʿ��� �հ��ʵ� ��ġ�ϱ�����  ORDER BY D.NUM (Ÿ��Ʋ�� ���� �����´�  �ٰ������� data���Ī��)
$sql= "SELECT  d.NAME ,D.NUM 
			FROM INS_IPMST(nolock) a
											left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
											left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
											left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE

											left outer join bonbu(nolock) e on c.scode = e.scode and c.bonbu = e.bcode
											left outer join jisa(nolock)  f on c.scode = f.scode and c.jisa = f.jscode
											left outer join jijum(nolock) g on c.scode = g.scode and c.jijum = g.jcode
											left outer join team(nolock) h  on c.scode = h.scode and c.team = h.tcode

											where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' and b.sgubun = '1' $where
		 group by  d.NAME ,D.NUM
		 ORDER BY D.NUM 
		 " ;

$qry= sqlsrv_query( $mscon, $sql );
$titList[]	=array();
$instit="";
$instit_cnt = 0; 
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$titList[]	= $fet['NAME'];
	$instit =   $instit.'['.$fet['NAME'].']';
	$instit_cnt =  $instit_cnt + 1;    //����� Ÿ��Ʋ ����,  ���߿� Ÿ��Ʋ�� ���� ����� 
}
$instit = str_replace("][","],[",$instit); //--->Ÿ��Ʋ�� sql 	PVT(ũ�ν��ǿ��� ����Ѵ�) 	--Ÿ��Ʋ ����簡 �����̴�. 
 
if(!$instit){
	$instit = "[�Ｚ����]";
}

 /*
echo '<pre>';
PRINT_R($sql);
echo '</pre>';
 */ 

$sql= "
	select *
	from(
				select aa.SKEY swonskey , aa.*, row_number()over(order by aa.BNAME,aa.JSNAME,aa.JNAME,aa.TNAME, aa.SKEY		) rnum  from 
								(SELECT * FROM (
										SELECT isnull(c.SKEY,'XXXXX') SKEY ,c.SNAME, d.NAME,e.BNAME,f.JSNAME,g.JNAME,h.TNAME, c.HTEL1, c.HTEL2, c.HTEL3,A.YYMM,
												sum(isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
												+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0)) catotal
											FROM INS_IPMST(nolock) a
											left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
											left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
											left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE

											left outer join bonbu(nolock) e on c.scode = e.scode and c.bonbu = e.bcode
											left outer join jisa(nolock)  f on c.scode = f.scode and c.jisa = f.jscode
											left outer join jijum(nolock) g on c.scode = g.scode and c.jijum = g.jcode
											left outer join team(nolock) h  on c.scode = h.scode and c.team = h.tcode

											where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' and b.sgubun = '1' $where
										group by  c.SKEY,c.SNAME, d.NAME,e.BNAME,f.JSNAME,g.JNAME,h.TNAME, c.HTEL1, c.HTEL2, c.HTEL3,A.YYMM
								) T1
								PIVOT ( sum(catotal) FOR  NAME IN( ".$instit ." )) AS PVT) aa
						
 
) p
where rnum between ".$limit1." AND ".$limit2."
order by skey
" ;

/*
echo '<pre>';
echo $sql; 
echo '</pre>';
*/


$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}


$sql="
	SELECT isnull(c.SKEY,'XXXXX') SKEY,
		sum(isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
		+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0)) skeytot
	FROM INS_IPMST(nolock) a
						left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
						left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
						left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE
						where a.SCODE = '".$_SESSION['S_SCODE']."'  and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."'   and b.sgubun = '1'
	group by c.SKEY
";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_skeyt = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_skeyt[]	= $fet;
}

//--->�����Լ����� ����縦 Ÿ��Ʋ�� �����ϱ����� �ش���� ������ �������� ������ �ʿ��� �հ��ʵ� ��ġ�ϱ�����  ORDER BY D.NUM 
$sql ="
					SELECT  d.NAME,
							sum(isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
							+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0)) catotal
						FROM INS_IPMST(nolock) a
						left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
						left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
						left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE

						left outer join bonbu(nolock) e on c.scode = e.scode and c.bonbu = e.bcode
						left outer join jisa(nolock)  f on c.scode = f.scode and c.jisa = f.jscode
						left outer join jijum(nolock) g on c.scode = g.scode and c.jijum = g.jcode
						left outer join team(nolock) h  on c.scode = h.scode and c.team = h.tcode

						where a.SCODE =  '".$_SESSION['S_SCODE']."'   and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' and b.sgubun = '1' $where
					group by   d.NAME, D.NUM 
					ORDER BY D.NUM 
			";
$qry	= sqlsrv_query( $mscon, $sql );
$listinsTot = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listinsTot[]	= $fet;
}

$listinsTot_tot = 0; 
for($i = 0; $i <  $instit_cnt ; $i++) {
	$listinsTot_tot = $listinsTot_tot +$listinsTot[$i]['catotal']  ; 
} 


/*
echo '<pre>';
PRINT_R($listinsTot[$i]['catotal']."</br>") ;
//echo($titList[2]);
//echo $sql; 
echo '</pre>';
*/ 

// ������ �� �Ǽ�
//�˻� ������ ���ϱ� 
$sql= "
	select COUNT(*) CNT
	from(
				select aa.SKEY swonskey , aa.*, row_number()over(order by aa.BNAME,aa.JSNAME,aa.JNAME,aa.TNAME, aa.SKEY		) rnum  from 
								(SELECT * FROM (
										SELECT isnull(c.SKEY,'XXXXX') SKEY ,c.SNAME, d.NAME,e.BNAME,f.JSNAME,g.JNAME,h.TNAME, c.HTEL1, c.HTEL2, c.HTEL3,A.YYMM,
												sum(isnull(a.SAMT1,0)+isnull(a.SAMT2,0)+isnull(a.SAMT3,0)+isnull(a.SAMT4,0)+isnull(a.SAMT5,0)+isnull(a.SAMT6,0)+isnull(a.SAMT7,0)+isnull(a.SAMT8,0)
												+isnull(a.SAMT9,0)+isnull(a.SAMT10,0)+isnull(a.SAMT11,0)+isnull(a.SAMT12,0) +isnull(a.SAMT13,0)+isnull(a.HSSU,0)+isnull(a.BSU,0)) catotal
											FROM INS_IPMST(nolock) a
											left outer join INSWON(nolock) b on  a.scode = b.scode and  a.INSCODE = b.INSCODE and a.KSMAN = b.BSCODE  
											left outer join swon(nolock)  c on  b.scode = c.scode and  b.SKEY = c.SKEY
											left outer join INSSETUP(nolock)  d on  a.scode = d.scode and  a.INSCODE = d.INSCODE

											left outer join bonbu(nolock) e on c.scode = e.scode and c.bonbu = e.bcode
											left outer join jisa(nolock)  f on c.scode = f.scode and c.jisa = f.jscode
											left outer join jijum(nolock) g on c.scode = g.scode and c.jijum = g.jcode
											left outer join team(nolock) h  on c.scode = h.scode and c.team = h.tcode

											where a.SCODE =  '".$_SESSION['S_SCODE']."'  and  a.YYMM >= '".$FYYMM."'  and   a.YYMM <= '".$TYYMM."' and b.sgubun = '1' $where
										group by  c.SKEY,c.SNAME, d.NAME,e.BNAME,f.JSNAME,g.JNAME,h.TNAME, c.HTEL1, c.HTEL2, c.HTEL3,a.YYMM
								) T1
								PIVOT ( sum(catotal) FOR  NAME IN( ".$instit ." )) AS PVT) aa
					 ) P 
		  
		  " ;
/*
echo '<pre>';
 echo $sql; 
echo '</pre>';
 */ 

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 


// ������ Ŭ���� ����
// �ε�
include_once($conf['rootDir'].'/include/class/Pagination.php');

// ����
$pagination = new Pagination(array(
		'base_url' => $_SERVER['PHP_SELF']."?SDATE1=". $_REQUEST['SDATE1']."&SDATE2=". $_REQUEST['SDATE2']."&id=".$_REQUEST['id']."&page=Y",
		'per_page' => $page_row,
		'total_rows' => $totalResult['CNT'],
		'cur_page' => $page,
));

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>


<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
	<table id="sort_table" class="gridhover" style="min-width: 3200px;">
		<colgroup>
			<col width="80px">
			<col width="80px">
			<col width="100px">
			<col width="210px">


			<col width="120px">

			<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
						<col width="100px">
  			<?}?>
			<col width="auto">

		</colgroup>
		<thead>
		<tr class="rowTop">
			<th align="center">�����</th>
			<th align="center">���</th>
			<th align="center">�����</th>
			<th align="center">�Ҽ�</th>				

			<th align="center">�������հ�</th>
 
			<?for($i=1;$i<= $instit_cnt;$i++ ){?> 
				<th align="left"><?=$titList[$i] ?></th>
  			<?}?>
			<th align="center"></th>	
 
		</tr>
		</thead>			
			<tr class="summary sticky"style="top:32px">
			<th></th>
			<th></th>

			<th  class="sum01"><?= ' �� �� ' ?></th>							
			<th></th>
			<th class="sum01"><?=number_format($listinsTot_tot )?></th>							
			<? for($i = 0; $i <  $instit_cnt ; $i++) { ?> 
				<th  class="sum01"><?=number_format($listinsTot[$i]['catotal'])  ?></th>					

			<?}?>  	 
			<th></th>
			</tr>

		<tbody>
			<?if(!empty($listData)){?>
			<?$ii = 0?>
			<?foreach($listData as $key => $val){extract($val);?>
			<tr class="rowData" rol-data='<?=$swonskey?>', rol-yymm='<?=$YYMM?>'>
				<td align="center"><?=$YYMM?></td>
				<?if ($swonskey == 'XXXXX') { ?>
					<td align="left"><?='���Ī�ڵ�'?></td>
				<?}else{?>
					<td align="left"><?=$swonskey?></td>
				<?}?> 
				<td align="left"><?=$SNAME?></td>
				<?$sosok = substr($BNAME,0,4).'>'.$JSNAME.'>'.$JNAME.'>'.$TNAME   ?>
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<?$sosok = str_replace('>>','>',$sosok)?> 
				<td align="left"><?=$sosok?></td>

				<td  align="right"><?=number_format($listData_skeyt[$ii]['skeytot'])?></td>

				<?for($i = 1; $i <=  $instit_cnt ; $i++) {?>
						<td align="right"><?=number_format($listData[$ii][$titList[$i]])?></td>   <!--ũ�ν������� ȸ����÷���  -->
	 			<?}?>
				<?$ii = $ii + 1 ?>

				<td></td>
			</tr>
			<?}}else{?>
				<tr>
					<td style="color:#8C8C8C" colspan=23>�˻��� �����Ͱ� �����ϴ�</td>
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

	// page �Լ� ajax������ ����� ���� ó��
	$(".kwnlist a").click(function(){
		$('#page').val('Y');
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

		var swon  = $(".rowData").eq(idx).attr('rol-data');
		var yymm  = $(".rowData").eq(idx).attr('rol-yymm');

		var left = Math.ceil((window.screen.width - 1200)/2);
		var top = Math.ceil((window.screen.height - 1000)/2);
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu3/ga_menu3_74_list3.php?swon="+swon +"&yymm=" +yymm, "width=1200px,height=800px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();
	})

});

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>