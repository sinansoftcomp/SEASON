<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$carseq = $_GET['carseq'];
$data	= $_GET['data'];

// 사원기본정보
$sql= "
	select a.scode,a.carseq,a.caruse,a.pname,
			case when len(dbo.decryptkey(a.jumin)) = 13 then substring(dbo.decryptkey(a.jumin),1,6)+'-'+substring(dbo.decryptkey(a.jumin),7,1)+'******'
										else substring(dbo.decryptkey(a.jumin),1,3)+'-'+substring(dbo.decryptkey(a.jumin),4,2)+'-'+substring(dbo.decryptkey(a.jumin),6,10) end jumin,
			a.carnumber,a.fdate,a.tdate,a.bfins,i.name bfins_nm, a.carcode,a.cargrade,a.baegicc,a.caryear,a.cardate,a.car_kind,a.carname,a.people_numcc,a.ext_bupum_txt,a.
			ext_bupum,a.add_bupum,a.carprice1,a.carprice,a.fuel,a.hi_repair,a.buy_type,a.guipcarrer,a.traffic,a.lawcodecnt,a.halin,a.special_code,a.special_code1,a.ncr_code,a.ncr_code2,a.
			ss_point,a.ss_point3,a.car_guip,a.car_own,a.careercode3,a.otheracc,a.ijumin,a.fetus,a.icnt,a.tmap_halin,a.car_own_halin,a.religionchk,a.eco_mileage,a.jjumin,a.j_name,a.lowestjumin,a.
			l_name,a.c_jumin,a.c_name,a.devide_num,a.muljuk,a.milegbn,a.milekm,a.nowkm,a.dambo2,a.dambo3,a.dambo4,a.dambo5,a.dambo6,a.goout,a.carage,a.carfamily,a.kcode,a.kdman,a.rateapi,a.
			ratedt,a.inyn,a.indt,a.upmu,a.rbit,a.selins,a.reday,a.rehour,a.bigo,a.cnum,a.agins,a.agno,a.memo,convert(varchar(8),a.idate,112) idate,a.iswon,d.name,c.sname,c.skey,b.bscode,
			c.htel1+'-'+c.htel2+'-'+c.htel3 htel , e.bigo trafficbigo , f.kind, g.amt buamt , carprice1+g.amt totalamt,add_bupum_txt,
			case when substring(a.chtel,1,2) = '02' then substring(a.chtel,1,2)
				 else substring(a.chtel,1,3) end +'-'+
			case when substring(a.chtel,1,2) = '02' and len(a.chtel) = 9 then substring(a.chtel,3,3)
				 when substring(a.chtel,1,2) = '02' and len(a.chtel) = 10 then substring(a.chtel,3,4)
				 when substring(a.chtel,1,2) != '02' and len(a.chtel) = 10 then substring(a.chtel,4,3)
				 when substring(a.chtel,1,2) != '02' and len(a.chtel) = 11 then substring(a.chtel,4,4)
				 else substring(a.chtel,3,4) end +'-'+
			case when substring(a.chtel,1,2) = '02' and len(a.chtel) = 9 then substring(a.chtel,6,4)
				 when substring(a.chtel,1,2) = '02' and len(a.chtel) = 10 then substring(a.chtel,7,4)
				 when substring(a.chtel,1,2) != '02' and len(a.chtel) = 10 then substring(a.chtel,7,4)
				 when substring(a.chtel,1,2) != '02' and len(a.chtel) = 11 then substring(a.chtel,8,4)
				 else substring(a.chtel,8,11) end chtel
	from carest a left outer join inswon b on a.scode = b.scode and a.kdman = b.bscode
					left outer join swon c on a.scode = c.scode and b.skey = c.skey
					left outer join company d on a.scode = d.scode
					left outer join carlaw e on a.traffic = e.code
					left outer join carkind f on a.car_kind = f.code
					left outer join insmaster i on a.bfins = i.code
					left outer join (select scode,carseq,sum(amt) amt from carestadd group by scode,carseq)  g on a.scode = g.scode and a.carseq=g.carseq
	where a.scode = '".$_SESSION['S_SCODE']."' and a.carseq = '".$carseq."'
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_0 = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_0[]	= $fet;
}
//asdf
$carprice1 = $listData_0[0]['carprice1'];

// 부속품 합계금액
$sql= "
	select sum(amt) addamt
	from carestadd
	where scode = '".$_SESSION['S_SCODE']."' 
	  and carseq = '".$carseq."' " ;

$qry =  sqlsrv_query($mscon, $sql);
$totalResult =  sqlsrv_fetch_array($qry); 

$addamt		= $totalResult['addamt'];
$totalamt	= (int)$carprice1 + (int)$addamt;

// 특별할인
$special_txt= "";
$comma		= "";

// 1.자녀할인
if($listData_0[0]['ijumin']){
	if($listData_0[0]['fetus'] == '1'){
		$special_txt= "자녀할인(태아)";
	}else{
		$special_txt= "자녀할인";
	}
}

// 2.티맵할인
if($listData_0[0]['tmap_halin'] >= '61'){
	if($special_txt){
		$special_txt .= ", ";
	}

	$special_txt .= "티맵할인";
}


// 3.종교단체특약
if($listData_0[0]['religionchk'] == '1'){
	if($special_txt){
		$special_txt .= ", ";
	}

	$special_txt .= "종교단체특약";
}


// 4.다수차량할인(업무용에 한함)
if($listData_0[0]['caruse'] == '2' || $listData_0[0]['caruse'] == '3'){
	if($special_txt){
		$special_txt .= ", ";
	}

	if($listData_0[0]['car_own_halin'] > 10){
		$special_txt= "다수차량할인";
	}
}


// 지정1인명
if($listData_0[0]['j_name']){
	$j_data	= $listData_0[0]['j_name'].'('.$listData_0[0]['jjumin'].')';
}

// 배우자
if($listData_0[0]['c_name']){
	$c_data	= $listData_0[0]['c_name'].'('.$listData_0[0]['c_jumin'].')';
}

// 최저연령운전자
if($listData_0[0]['l_name']){
	$l_data	= $listData_0[0]['l_name'].'('.$listData_0[0]['lowestjumin'].')';
}

// 자손 및 자상 비교
$arr_so = array("15","30","33","50","53","55","90","93","95");								// 자손
$arr_sa = array("96","91","92","94","11","97","98","99","21","83","85","31","75","51");		// 자상

$dambo4 = $listData_0[0]['dambo4'];

if(in_array($dambo4, $arr_so)){
	$dambo4_txt	= '자손';
}else if(in_array($dambo4, $arr_sa)){
	$dambo4_txt	= '자상';
}else{
	$dambo4_txt	= '자손자상';
}

$sql= "
	select SCODE,CARSEQ,RESDT,
	hd_tot,hd_man1,hd_man2,hd_mul,hd_sin,hd_mu,hd_car,hd_goout,hd_msg,hd_text,hd_txt,
	ss_tot,ss_man1,ss_man2,ss_mul,ss_sin,ss_mu,ss_car,ss_goout,ss_msg,ss_text,ss_txt,
	db_tot,db_man1,db_man2,db_mul,db_sin,db_mu,db_car,db_goout,db_msg,db_text,db_txt,
	lg_tot,lg_man1,lg_man2,lg_mul,lg_sin,lg_mu,lg_car,lg_goout,lg_msg,lg_text,lg_txt,
	dy_tot,dy_man1,dy_man2,dy_mul,dy_sin,dy_mu,dy_car,dy_goout,dy_msg,dy_text,dy_txt,
	sy_tot,sy_man1,sy_man2,sy_mul,sy_sin,sy_mu,sy_car,sy_goout,sy_msg,sy_text,sy_txt,
	sd_tot,sd_man1,sd_man2,sd_mul,sd_sin,sd_mu,sd_car,sd_goout,sd_msg,sd_text,sd_txt,
	dh_tot,dh_man1,dh_man2,dh_mul,dh_sin,dh_mu,dh_car,dh_goout,dh_msg,dh_text,dh_txt,
	gr_tot,gr_man1,gr_man2,gr_mul,gr_sin,gr_mu,gr_car,gr_goout,gr_msg,gr_text,gr_txt	
	from carestamt
	where scode = '".$_SESSION['S_SCODE']."' and carseq = '".$carseq."'
	";
$qry	= sqlsrv_query( $mscon, $sql );
$listData_1 = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData_1[]	= $fet;
}


/*
echo "<pre>";
echo $sql;
echo "</pre>";
*/
sqlsrv_free_stmt($result);
sqlsrv_close($mscon);
?>
<style>
body{background-image: none;}
.gridhover th{padding:3px 0;}


tr.colored:nth-child(even){

  background-color:#FFFFFF;

  color:#000000;

}

tr.colored:nth-child(odd){

  background-color:#000000;

  color:#FFFFFF;

}




body { !important; -webkit-print-color-adjust:exact;}
@media print{
     @page{  size:auto; margin : 0mm;  }
 }

@media print{
	.tb_type01 td{font-size:8pt;}
 }


.tb_type01 .import{
    font-size: 12px;
    font-weight:600;
}

</style>



<div class="tit_wrap ipgopop" style="padding-top:10px">

	<div class="tit_wrap" style="margin-top:20px" align="center">
		<h1>자동차 비교견적서</h1>
		
		<span class="btn_wrap" style="padding-right:29px">
			<a class="btn_s white no-print" onclick="jQuery('#print').print();" style="min-width:100px;">인쇄</a>
		</span>

	</div>
	
	<div style="padding:0px 15px;">
		<div style="border-left:1px solid #123f7d;border-right:1px solid #123f7d;border-bottom:3px solid #123f7d;">
			<div class="tb_type01 view" style="margin-top:20px;border-top:3px solid #123f7d;">
				<table>
					<colgroup>											
						<col width="9%">
						<col width="12%">
						<col width="10%">
						<col width="16%">
						<col width="11%">
						<col width="14%">
						<col width="16%">
						<col width="12%">
					</colgroup>		
					<tbody>
						<tr>
							<th>회사명</th>
							<td><?=$listData_0[0]['name']?></td>
							<th>사용인명</th>
							<td><?=$listData_0[0]['sname']?> ( <?=$listData_0[0]['bscode']?> )</td>
							<th>핸드폰번호</th>
							<td><?=$listData_0[0]['htel']?></td>
							<th>보험료산출일자</th>
							<td><?=date("Y-m-d",strtotime($listData_1[0]['RESDT']))?></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="tit_wrap" style="margin-top:15px;margin-left:5px">
				<h3 class="tit_sub">계약 내용</h3>
			</div>
			<div class="tb_type01 view" >
				<table>
					<colgroup>											
						<col width="18%">
						<col width="32%">
						<col width="18%">
						<col width="32%">
					</colgroup>
					<tbody>
						<tr>
							<th>피보험자</th>
							<td class="import"><?=$listData_0[0]['pname']?></td>
							<th>생년월일/사업자번호</th>
							<td class="import"><?=$listData_0[0]['jumin']?></td>
						</tr>
						<tr>
							<th>보험기간</th>
							<td class="import"><?=date("Y.m.d",strtotime($listData_0[0]['fdate']))?> ~ <?=date("Y.m.d",strtotime($listData_0[0]['tdate']))?></td>
							<th>지정1인명</th>
							<td><?=$j_data?></td>
						</tr>						
						<tr>
							<th>최저연령운전자</th>
							<td><?=$l_data?></td>
							<th>배우자</th>
							<td><?=$c_data?></td>
						</tr>
						<tr>
							<th>피보험자주소</th>
							<td colspan=3></td>
						</tr>
						<tr>
							<th>휴대/전화번호</th>
							<td><?=$listData_0[0]['chtel']?></td>
							<th>차종</th>
							<td><?=$listData_0[0]['kind']?></td>
						</tr>
						<tr>
							<th>차량번호</th>
							<td><?=$listData_0[0]['carnumber']?></td>
							<th>차명</th>
							<td><?=$listData_0[0]['carname']?></td>
						</tr>
						<tr>
							<th>차명코드</th>
							<td><?=$listData_0[0]['carcode']?></td>
							<th>차량총가액</th>
							<td><?=number_format($totalamt)?> 만원</td>
						</tr>
						<tr>
							<th>특별요율</th>
							<td><?=$listData_0[0]['ext_bupum_txt']?></td>
							<th>추가부속</th>
							<td><?=$listData_0[0]['add_bupum_txt']?></td>
						</tr>
						<tr>
							<th>전보험사</th>
							<td><?=$listData_0[0]['bfins_nm']?></td>
							<th>기타할인</th>
							<td><?=$special_txt?></td>
						</tr>
						<tr>
							<th>가입경력</th>
							<td><?=$conf['guipcarrer'][$listData_0[0]['guipcarrer']]?></td>
							<th>할인할증</th>
							<td><?=$listData_0[0]['halin']?></td>
						</tr>
						<tr>
							<th>법규위반</th>
							<td><?=$listData_0[0]['trafficbigo']?> ( <?=$listData_0[0]['traffic']?> )</td>
							<th>특별할증</th>
							<td><?=$conf['ncr_code'][$listData_0[0]['ncr_code']]?></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="tit_wrap" style="margin-top:15px;margin-left:5px">
				<h3 class="tit_sub">담보 내용</h3>
			</div>
			<div class="tb_type01 view">
				<table>
					<colgroup>											
						<col width="12%">
						<col width="12%">
						<col width="12%">
						<col width="12%">
						<col width="12%">
						<col width="12%">
						<col width="12%">
						<col width="auto">
					</colgroup>
					<thead>
						<tr>
							<th align="center">대인I</th>
							<th align="center">대인II</th>
							<th align="center">대물배상</th>
							<th align="center">자손/자상</th>
							<th align="center">무보험차</th>
							<th align="center">자차손해</th>
							<th align="center">긴급출동</th>
							<th align="center">물적할증기준</th>
						</tr>
					</thead>			
					<tbody>
						<tr style="height:33px;">
							<td align="center">의무가입</td>
							<td align="center"><?=$conf['dambo2'][$listData_0[0]['dambo2']]?></td>
							<td align="center"><?=$conf['dambo3'][$listData_0[0]['dambo3']]?></td>
							<td align="center"><?=$conf['dambo4'][$listData_0[0]['dambo4']]?></td>
							<td align="center"><?=$conf['dambo5'][$listData_0[0]['dambo5']]?></td>
							<td align="center"><?=$conf['dambo6'][$listData_0[0]['dambo6']]?></td>
							<td align="center"><?=$conf['goout1'][$listData_0[0]['goout']]?></td>
							<td align="center"><?=$conf['muljuk'][$listData_0[0]['muljuk']]?></td>
						</tr>
					</tbody>
				</table>
			</div>
		
			<div class="tit_wrap" style="margin-top:10px;margin-left:5px">
				<h3 class="tit_sub">보험사별 비교</h3>
			</div>

			<div class="tb_type01 view">
				<table>
					<colgroup>											
						<col width="8%">
						<col width="9.5%">
						<col width="8%">
						<col width="8%">
						<col width="9.5%">
						<col width="8.2%">
						<col width="8.2%">
						<col width="9.5%">
						<col width="9.5%">
						<col width="11%">
						<col width="auto">
					</colgroup>	
					<thead>
						<tr>
							<th align="center">보험사</th>
							<th align="center">총보험료</th>
							<th align="center">대인I</th>
							<th align="center">대인II</th>
							<th align="center">대물배상</th>
							<th align="center"><?=$dambo4_txt?></th>
							<th align="center">무보험</th>
							<th align="center">자차손해</th>
							<th align="center">긴급출동</th>
							<th align="center">운전자한정</th>
							<th align="center">연령한정</th>
						</tr>
					</thead>
					<tbody>
						<tr style="height:33px" bgcolor="#D9E5FF" id="SY">
							<td align="center">흥국</td>
							<td align="right" class="font_red"><?=number_format($listData_1[0]['sy_tot'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sy_man1'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sy_man2'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sy_mul'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sy_sin'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sy_mu'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sy_car'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sy_goout'])?></td>
							<td align="left"><?=$listData_1[0]['sy_txt']?></td>
							<td align="left"><?=$listData_1[0]['sy_text']?></td>
						</tr>
						<tr style="height:33px;" id="SS">
							<td align="center">삼성</td>
							<td align="right" class="font_red"><?=number_format($listData_1[0]['ss_tot'])?></td>
							<td align="right"><?=number_format($listData_1[0]['ss_man1'])?></td>
							<td align="right"><?=number_format($listData_1[0]['ss_man2'])?></td>
							<td align="right"><?=number_format($listData_1[0]['ss_mul'])?></td>
							<td align="right"><?=number_format($listData_1[0]['ss_sin'])?></td>
							<td align="right"><?=number_format($listData_1[0]['ss_mu'])?></td>
							<td align="right"><?=number_format($listData_1[0]['ss_car'])?></td>
							<td align="right"><?=number_format($listData_1[0]['ss_goout'])?></td>
							<td align="left"><?=$listData_1[0]['ss_txt']?></td>
							<td align="left"><?=$listData_1[0]['ss_text']?></td>
						</tr>
						<tr style="height:33px" id="GR">
							<td align="center">MG</td>
							<td align="right" class="font_red"><?=number_format($listData_1[0]['gr_tot'])?></td>
							<td align="right"><?=number_format($listData_1[0]['gr_man1'])?></td>
							<td align="right"><?=number_format($listData_1[0]['gr_man2'])?></td>
							<td align="right"><?=number_format($listData_1[0]['gr_mul'])?></td>
							<td align="right"><?=number_format($listData_1[0]['gr_sin'])?></td>
							<td align="right"><?=number_format($listData_1[0]['gr_mu'])?></td>
							<td align="right"><?=number_format($listData_1[0]['gr_car'])?></td>
							<td align="right"><?=number_format($listData_1[0]['gr_goout'])?></td>
							<td align="left"><?=$listData_1[0]['gr_txt']?></td>
							<td align="left"><?=$listData_1[0]['gr_text']?></td>
						</tr>
						<tr style="height:33px" id="DH">
							<td align="center">롯데</td>
							<td align="right" class="font_red"><?=number_format($listData_1[0]['dh_tot'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dh_man1'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dh_man2'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dh_mul'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dh_sin'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dh_mu'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dh_car'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dh_goout'])?></td>
							<td align="left"><?=$listData_1[0]['dh_txt']?></td>
							<td align="left"><?=$listData_1[0]['dh_text']?></td>
						</tr>
						<tr style="height:33px" id="HD">
							<td align="center">현대</td>
							<td align="right" class="font_red"><?=number_format($listData_1[0]['hd_tot'])?></td>
							<td align="right"><?=number_format($listData_1[0]['hd_man1'])?></td>
							<td align="right"><?=number_format($listData_1[0]['hd_man2'])?></td>
							<td align="right"><?=number_format($listData_1[0]['hd_mul'])?></td>
							<td align="right"><?=number_format($listData_1[0]['hd_sin'])?></td>
							<td align="right"><?=number_format($listData_1[0]['hd_mu'])?></td>
							<td align="right"><?=number_format($listData_1[0]['hd_car'])?></td>
							<td align="right"><?=number_format($listData_1[0]['hd_goout'])?></td>
							<td align="left"><?=$listData_1[0]['hd_txt']?></td>
							<td align="left"><?=$listData_1[0]['hd_text']?></td>
						</tr>
						<tr style="height:33px" id="DY">
							<td align="center">메리츠</td>
							<td align="right" class="font_red"><?=number_format($listData_1[0]['dy_tot'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dy_man1'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dy_man2'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dy_mul'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dy_sin'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dy_mu'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dy_car'])?></td>
							<td align="right"><?=number_format($listData_1[0]['dy_goout'])?></td>
							<td align="left"><?=$listData_1[0]['dy_txt']?></td>
							<td align="left"><?=$listData_1[0]['dy_text']?></td>
						</tr>
						<tr style="height:33px" id="DB">
							<td align="center">DB</td>
							<td align="right" class="font_red"><?=number_format($listData_1[0]['db_tot'])?></td>
							<td align="right"><?=number_format($listData_1[0]['db_man1'])?></td>
							<td align="right"><?=number_format($listData_1[0]['db_man2'])?></td>
							<td align="right"><?=number_format($listData_1[0]['db_mul'])?></td>
							<td align="right"><?=number_format($listData_1[0]['db_sin'])?></td>
							<td align="right"><?=number_format($listData_1[0]['db_mu'])?></td>
							<td align="right"><?=number_format($listData_1[0]['db_car'])?></td>
							<td align="right"><?=number_format($listData_1[0]['db_goout'])?></td>
							<td align="left"><?=$listData_1[0]['db_txt']?></td>
							<td align="left"><?=$listData_1[0]['db_text']?></td>
						</tr>
						<tr style="height:33px" id="SD">
							<td align="center">한화</td>
							<td align="right" class="font_red"><?=number_format($listData_1[0]['sd_tot'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sd_man1'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sd_man2'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sd_mul'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sd_sin'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sd_mu'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sd_car'])?></td>
							<td align="right"><?=number_format($listData_1[0]['sd_goout'])?></td>
							<td align="left"><?=$listData_1[0]['sd_txt']?></td>
							<td align="left"><?=$listData_1[0]['sd_text']?></td>
						</tr>
						<tr style="height:33px" id="LG">
							<td align="center">KB</td>
							<td align="right" class="font_red"><?=number_format($listData_1[0]['lg_tot'])?></td>
							<td align="right"><?=number_format($listData_1[0]['lg_man1'])?></td>
							<td align="right"><?=number_format($listData_1[0]['lg_man2'])?></td>
							<td align="right"><?=number_format($listData_1[0]['lg_mul'])?></td>
							<td align="right"><?=number_format($listData_1[0]['lg_sin'])?></td>
							<td align="right"><?=number_format($listData_1[0]['lg_mu'])?></td>
							<td align="right"><?=number_format($listData_1[0]['lg_car'])?></td>
							<td align="right"><?=number_format($listData_1[0]['lg_goout'])?></td>
							<td align="left"><?=$listData_1[0]['lg_txt']?></td>
							<td align="left"><?=$listData_1[0]['lg_text']?></td>
						</tr>
					</tbody>
				</table>			
			</div>
		</div>

		<div class="tit_wrap" style="margin:10px 0 10px 5px" align="left">
			<h4>* 자차손해 자기 부담금은 자차 수리비의 20%가 기준입니다.</h4>
			<h4>* 단, 최저 부담금은 물적할증기준의 10%이며, 최대부담금은 50만원 한도입니다.</h4>
			<h4>* 보험회사별 인수지침이나 기타조건에 따라 보험료가 상이하거나 인수불가 할수도 있습니다.</h4>
			<h4 class="font_red">* 이문서는 대외비이며, 문서 유출시 법적인 책임이 유출자에게 있습니다.</h4>
		</div>		
	</div>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="<?=$conf['homeDir']?>/common/js/jQuery.print.js"></script>

<script type="text/javascript">

$(document).ready(function(){

	// 출력 보험사 배열값에 따른 display 
	var arrdata = '<?=$data?>';

	// 모든 tr 태그를 선택
	const trElements = document.querySelectorAll('tr');

	// 각 tr 태그의 id 값을 확인하고 배열에 존재하는 경우만 표시
	trElements.forEach(tr => {
		if (arrdata.includes(tr.id)) {
			tr.style.display = ''; // 표시
		} else {
			tr.style.display = 'none'; // 숨기기
		}
	});

});	

</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>