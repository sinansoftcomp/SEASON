<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

$kcode	= $_GET['kcode'];

// 영업중 고객상세정보
if($_GET['kcode']){
	$type	= 'up';
	$txtnm	= '계약상세정보';

	$sql= "
		select  a.insilj,
				a.scode,
				a.kcode,
				a.kname,
				a.sbit,
				dbo.decryptkey(a.sjuno) sjuno,
				substring(dbo.decryptkey(a.sjuno),1,6) sjuno1,
				substring(dbo.decryptkey(a.sjuno),7,7) sjuno2,
				a.snum,
				substring(a.snum,1,3) snum1,
				substring(a.snum,4,2) snum2,
				substring(a.snum,5,5) snum3,
				case when a.sbit = '1' then dbo.decryptkey(a.sjuno) else a.snum end secdata,
				a.comnm,
				a.cupnm,
				a.emailsel,
				a.email,
				a.telbit,
				a.tel,
				case when substring(a.tel,1,2) = '02' then substring(a.tel,1,2)
					 else substring(a.tel,1,3) end tel1,
				case when substring(a.tel,1,2) = '02' and len(a.tel) = 9 then substring(a.tel,3,3)
					 when substring(a.tel,1,2) = '02' and len(a.tel) = 10 then substring(a.tel,3,4)
					 when substring(a.tel,1,2) != '02' and len(a.tel) = 10 then substring(a.tel,3,3)
					 else substring(a.tel,3,4) end tel2,
				case when substring(a.tel,1,2) = '02' and len(a.tel) = 9 then substring(a.tel,6,4)
					 when substring(a.tel,1,2) = '02' and len(a.tel) = 10 then substring(a.tel,7,4)
					 when substring(a.tel,1,2) != '02' and len(a.tel) = 10 then substring(a.tel,7,4)
					 else substring(a.tel,8,11) end tel3,
				a.htelbit,
				a.htel,
				case when substring(a.htel,1,2) = '02' then substring(a.htel,1,2)
					 else substring(a.htel,1,3) end htel1,
				case when substring(a.htel,1,2) = '02' and len(a.htel) = 9 then substring(a.htel,3,3)
					 when substring(a.htel,1,2) = '02' and len(a.htel) = 10 then substring(a.htel,3,4)
					 when substring(a.htel,1,2) != '02' and len(a.htel) = 10 then substring(a.htel,3,3)
					 else substring(a.htel,3,4) end htel2,
				case when substring(a.htel,1,2) = '02' and len(a.htel) = 9 then substring(a.htel,6,4)
					 when substring(a.htel,1,2) = '02' and len(a.htel) = 10 then substring(a.htel,7,4)
					 when substring(a.htel,1,2) != '02' and len(a.htel) = 10 then substring(a.htel,7,4)
					 else substring(a.htel,8,11) end htel3,
				a.addbit,
				a.post,
				a.addr,
				a.addr_dt,
				a.bigo,
				case when isnull(a.bonbu,'') != '' then b.bname else '' end + 
				case when isnull(a.bonbu,'') != '' and (isnull(a.jisa,'') != '' or isnull(a.team,'') != '')  then ' > ' else '' end +
				case when isnull(a.jisa,'') != '' then c.jsname else '' end +
				case when isnull(a.jisa,'') != '' and isnull(a.team,'') != '' then ' > ' else '' end +
				case when isnull(a.team,'') != '' then e.tname else '' end as sosok,
				a.sjik,

				a.kdate,
				a.kstbit,
				a.fdate,
				a.tdate,
				a.inscode,
				a.item,
				a.itemnm,
				a.ksman,
				s1.sname ksname,
				a.kdman,
				s2.sname kdname,
				a.mamt,
				a.hamt,
				a.samt,
				a.srate,
				a.insterm,
				a.instbit,
				a.nterm,
				a.nbit,
				a.fbit,
				a.ksbit,
				a.kday,
				a.ksgubun,
				a.bank,
				dbo.decryptkey(a.syjuno) syjuno,
				a.syj,
				a.card,
				dbo.decryptkey(a.cardnum) cardnum,
				a.cyj,
				a.vcbank,
				a.vcno,
				a.paybit,
				a.pbdate,
				a.bigo2,

				a.rel,
				a.pname,
				a.psbit,
				dbo.decryptkey(a.psjuno) sjuno,
				substring(dbo.decryptkey(a.psjuno),1,6) psjuno1,
				substring(dbo.decryptkey(a.psjuno),7,7) psjuno2,
				a.psnum,
				substring(a.psnum,1,3) psnum1,
				substring(a.psnum,4,2) psnum2,
				substring(a.psnum,5,5) psnum3,
				a.pcomnm,
				a.pcupnm,
				a.pemailsel,
				a.pemail,
				a.ptelbit,
				a.ptel,
				case when substring(a.ptel,1,2) = '02' then substring(a.ptel,1,2)
					 else substring(a.ptel,1,3) end ptel1,
				case when substring(a.ptel,1,2) = '02' and len(a.ptel) = 9 then substring(a.ptel,3,3)
					 when substring(a.ptel,1,2) = '02' and len(a.ptel) = 10 then substring(a.ptel,3,4)
					 when substring(a.ptel,1,2) != '02' and len(a.ptel) = 10 then substring(a.ptel,3,3)
					 else substring(a.ptel,3,4) end ptel2,
				case when substring(a.ptel,1,2) = '02' and len(a.ptel) = 9 then substring(a.ptel,6,4)
					 when substring(a.ptel,1,2) = '02' and len(a.ptel) = 10 then substring(a.ptel,7,4)
					 when substring(a.ptel,1,2) != '02' and len(a.ptel) = 10 then substring(a.ptel,7,4)
					 else substring(a.ptel,8,11) end ptel3,
				a.phtelbit,
				a.phtel,
				case when substring(a.phtel,1,2) = '02' then substring(a.phtel,1,2)
					 else substring(a.phtel,1,3) end phtel1,
				case when substring(a.phtel,1,2) = '02' and len(a.phtel) = 9 then substring(a.phtel,3,3)
					 when substring(a.phtel,1,2) = '02' and len(a.phtel) = 10 then substring(a.phtel,3,4)
					 when substring(a.phtel,1,2) != '02' and len(a.phtel) = 10 then substring(a.phtel,3,3)
					 else substring(a.phtel,3,4) end phtel2,
				case when substring(a.phtel,1,2) = '02' and len(a.phtel) = 9 then substring(a.phtel,6,4)
					 when substring(a.phtel,1,2) = '02' and len(a.phtel) = 10 then substring(a.phtel,7,4)
					 when substring(a.phtel,1,2) != '02' and len(a.phtel) = 10 then substring(a.phtel,7,4)
					 else substring(a.phtel,8,11) end phtel3,
				a.paddbit,
				a.ppost,
				a.paddr,
				a.paddr_dt,
				a.pbigo,

				a.carnum,
				a.carvin,
				a.carjong,
				a.caryy,
				a.carcode,
				a.carkind,
				a.cargamt,
				a.cartamt,
				a.carsub1,
				a.carsamt1,
				a.carsub2,
				a.carsamt2,
				a.carsub3,
				a.carsamt3,
				a.carsub4,
				a.carsamt4,
				a.carsub5,
				a.carsamt5,
				a.carobj,
				a.carty,
				a.carpay1,
				a.carpay2,
				a.carbae,
				a.carbody1,
				a.carbody2,
				a.carbody3,
				a.carloss,
				a.caracamt,
				a.carins,
				a.caremg,
				
				case when isnull(a.udate,'') = '' then convert(varchar(30),a.idate,120) else convert(varchar(30),a.udate,120) end udate,
				case when isnull(a.uswon,'') = '' then s3.sname else s4.sname end uswonnm,
				row_number()over(order by a.kdate desc, a.kcode desc) rnum
		from kwn a	
			left outer join bonbu b on a.scode = b.scode and a.bonbu = b.bcode
			left outer join jisa c on a.scode = c.scode and a.jisa = c.jscode
			left outer join team e on a.scode = e.scode and a.team = e.tcode
			left outer join swon s1 on s1.scode = a.scode and s1.skey = a.gskey
			left outer join swon s2 on s2.scode = a.scode and s2.skey = a.kskey
			left outer join swon s3 on s3.scode = a.scode and s3.skey = a.iswon
			left outer join swon s4 on s4.scode = a.scode and s4.skey = a.uswon
		where a.scode = '".$_SESSION['S_SCODE']."' 
		  and a.kcode = '".$_GET['kcode']."' ";

	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet	= sqlsrv_fetch_array($qry));

	$update		=	$udate;
	$upswon		=	$uswonnm;
	$upswon_txt	=	'최종수정사원';
	$update_txt	=	'최종수정일시';
}else{
	$type	= 'in';
	$txtnm	= '계약등록';

	$update		=	date("Y-m-d H:i:s");
	$upswon		=	$_SESSION['S_SNAME'];
	$upswon_txt	=	'등록사원';
	$update_txt	=	'등록일시';
}

/*echo '<pre>';
echo $sql;
echo '</pre>';*/

//검색 데이터 구하기 
$sql= "
		select 
				a.kcode,
				a.insilj,
				a.inscode,
				f.name insname,
				case when isnull(a.bonbu,'') != '' then b.bname else '' end +
				case when isnull(a.bonbu,'') != '' and (isnull(a.jisa,'') != '' or isnull(a.team,'') != '')  then ' > ' else '' end +
				case when isnull(a.jisa,'') != '' then c.jsname else '' end +
				case when isnull(a.jisa,'') != '' and isnull(a.team,'') != '' then ' > ' else '' end +
				case when isnull(a.team,'') != '' then e.tname else '' end as sosok,
				s1.sname gskey_nm,
				s2.sname kskey_nm,
				a.kname,
				case when isnull(a.htel,'') != '' then a.htel else a.tel end telno,
				a.pname,
				a.kdate,
				a.item,
				a.itemnm,
				a.mamt,
				a.hamt,
				a.kstbit,
				a.nbit,
				row_number()over(order by a.kdate desc, f.name, a.kname) rnum
		from kwn a	
			left outer join bonbu b on a.scode = b.scode and a.bonbu = b.bcode
			left outer join jisa c on a.scode = c.scode and a.jisa = c.jscode
			left outer join team e on a.scode = e.scode and a.team = e.tcode
			left outer join inssetup f on a.scode = f.scode and a.inscode = f.inscode
			left outer join swon s1 on s1.scode = a.scode and s1.skey = a.gskey
			left outer join swon s2 on s2.scode = a.scode and s2.skey = a.kskey
		where a.scode = '".$_SESSION['S_SCODE']."'
		and (dbo.DECRYPTKEY(a.sjuno) = '".$secdata."' or a.snum = '".$secdata."')
		  
		" ;
//and (dbo.DECRYPTKEY(a.sjuno) = '".$secdata."' or a.snum = '".$secdata."')
$qry	= sqlsrv_query( $mscon, $sql );
$listData = array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}


// 주소구분
$sql= "select codesub code, subnm name from common where scode = '".$_SESSION['S_SCODE']."' and code = 'COM001' and useyn = 'Y' order by num,codesub";
$qry= sqlsrv_query( $mscon, $sql );
$comData1	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $comData1[] = $fet;
}

// 이메일구분
$sql= "select codesub code, subnm name from common where scode = '".$_SESSION['S_SCODE']."' and code = 'COM002' and useyn = 'Y' order by num,codesub";
$qry= sqlsrv_query( $mscon, $sql );
$comData2	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $comData2[] = $fet;
}

// 연락처구분
$sql= "select codesub code, subnm name from common where scode = '".$_SESSION['S_SCODE']."' and code = 'COM003' and useyn = 'Y' order by num,codesub";
$qry= sqlsrv_query( $mscon, $sql );
$comData3	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $comData3[] = $fet;
}


// 상담구분
$sql= "select codesub code, subnm name from common where scode = '".$_SESSION['S_SCODE']."' and code = 'COM008' and useyn = 'Y' order by num,codesub";
$qry= sqlsrv_query( $mscon, $sql );
$comData8	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $comData8[] = $fet;
}

// 관계
$sql= "select codesub code, subnm name from common where scode = '".$_SESSION['S_SCODE']."' and code = 'COM009' and useyn = 'Y' order by num,codesub";
$qry= sqlsrv_query( $mscon, $sql );
$comData9	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $comData9[] = $fet;
}


// 전체보험사
$sql= "select inscode code, name, gubun from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by name";
$qry= sqlsrv_query( $mscon, $sql );
$instot	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $instot[] = $fet;
}


// 생명보험사
$sql= "select inscode code, name, gubun from inssetup where scode = '".$_SESSION['S_SCODE']."' and gubun = '1' and useyn = 'Y' order by name";
$qry= sqlsrv_query( $mscon, $sql );
$insg1	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insg1[] = $fet;
}


// 손해보험사
$sql= "select inscode code, name, gubun from inssetup where scode = '".$_SESSION['S_SCODE']."' and gubun = '2' and useyn = 'Y' order by name";
$qry= sqlsrv_query( $mscon, $sql );
$insg2	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insg2[] = $fet;
}


// 은행코드
$sql= "select bank code, bname name from bnk order by bank";
$qry= sqlsrv_query( $mscon, $sql );
$BnkData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $BnkData[] = $fet;
}


// 카드사
$sql= "select card code, cname name from card order by card";
$qry= sqlsrv_query( $mscon, $sql );
$CardData	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $CardData[] = $fet;
}



?>

<!-- html영역 -->
<style>
body{background-image: none;}
.container{
	margin:0px 0px 0px 10px;
	padding:10px 20px 20px 20px;
}


.tb_type01 {
    border-bottom: 2px solid #47474a;
}

.tb_type01.view{
	margin-bottom:10px;
}

/* input, select border 제거 */
.noborder .input_type, .noborder .input_type_number, textarea  {
    display: inline-block;
    height: 24px;
    background: #fff;
	font-weight:bold;
    border: 0px solid #b7b7b7;
    vertical-align: middle;
    box-sizing: border-box;
}

.noborder select {
    border: 0px solid #b7b7b7;
	color: #333;
	font-weight:bold;
	font-size: 14px;
    padding-left: 5px;
    -webkit-appearance:none; /* 크롬 화살표 없애기 */
    -moz-appearance:none; /* 파이어폭스 화살표 없애기 */
    appearance:none /* 화살표 없애기 */
}


input[type="text"]:disabled {
  background: #fff;
}

input[type="select"]:disabled {
  background: #fff;
}

input[type="radio"]:disabled {
  background: #fff;
}


.tb_type01.view th, .tb_type01.view td {
    padding: 6px 12px 5px 12px;
	font-size: 13px;
    text-align: left;
}



.noborder .input_type input {
    padding-left: 7px;
    font-size: 13px;
	font-weight:bold;
}

.noborder .input_type_number input {
    padding-left: 7px;
    font-size: 13px;
	font-weight:bold;
}

[type="radio"]:checked { background-color: #384c67; }


div.tb_type01 th.obj {
    background: #92A2C9;
    color: #fff;
}

.tb_type01 {
    border-bottom: 0px solid #47474a;
}

.tb_type02_main {
    border-bottom: 0px solid #47474a;
}


.tb_type02_main th, .tb_type02_main td {
    padding: 8px 0;
}

.tit_wrap {
    margin-bottom: 5px;
}

select {
	height:24px;
}


.datacopy{
	margin-left:20px;
}

.mgl10{
	margin-left:10px;
}


/* 첫행 고정 background: #02c836; color:#fff; */
#kwnlist thead tr th{position: sticky; top: 0; background: #f9f9f9;  z-index: 1;}


.tab_style01 li{width:100px;border-bottom:0px;}


.tit_big {margin-bottom:5px}

.tab_con_wrap{min-height:200px}


</style>

<div class="container container_bk">
	<div class="content_wrap">
		<fieldset>
			<legend>계약관리 : 계약원장</legend>

			<!--<div class="tit_wrap mt20">
				<h3 class="tit_big">원장관리</h3>
				<span class="btn_wrap">
					<a href="#" class="btn_s navy" style="min-width:80px;" onclick="btn_close();">닫기</a>
				</span>
			</div>-->

			<div class="tit_wrap mt20">
				<h3 class="tit_big">계약원장정보</h3>
				
				<div style="display:inline-block;margin-left:23.3%">
					<form name="searchFrm" method="get" action="<?$_SERVER['PHP_SELF']?>" >
						<select name="searchF1" id="searchF1" class="srch_css" onchange="fn_srch(this.value);">
							<option value="kcode" <?if($_GET['searchF1']=="a.kcode") echo "selected"?>>증권번호</option>
							<option value="kname" <?if($_GET['searchF1']=="a.kname") echo "selected"?>>계약자명</option>							
							<option value="sjuno"   <?if($_GET['searchF1']=="sjuno") echo "selected"?>>주민/사업자번호</option>
							<option value="s1"   <?if($_GET['searchF1']=="s1") echo "selected"?>>모집사원</option>
							<option value="s2"   <?if($_GET['searchF1']=="s2") echo "selected"?>>관리사원</option>
							<option value="tel"   <?if($_GET['searchF1']=="tel") echo "selected"?>>연락처</option>
						</select>
						<input type="hidden" name="skey" id="skey" value="<?=$_GET['skey']?>">
						<input type="hidden" name="kcode" id="ser_kcode" value="<?=$_GET['kcode']?>">
						<input type="text" name="searchF1Text" id="searchF1Text" class="srch_css" style="height:20px"" value="<?=$_GET['kcode']?>" onclick="enterkey()">
						<a href="#" class="btn_s navy btn_search">조회</a>
						<input type="text" name="kname" id="ser_kname" value="<?=$_GET['kname']?>" style="border: 0px solid #b7b7b7;">
					</form>
				</div>

				<span class="btn_wrap">
					<a href="#" class="btn_s navy" style="min-width:80px;" onclick="btn_close();">닫기</a>
				</span>
			</div>
	
			<div style="min-height:390px;margin-bottom:20px;border-bottom: 0px dashed #47474a;">
				<div class="won_left noborder" style="max-height:370px">

					<!-- //box_gray -->
					<div class="tb_type01 view">
						<form name="wongjang_form" class="ajaxForm_wonjang" method="post" action="">
						<input type="hidden" name="type" value="<?=$type?>">
						<input type="hidden" name="popbit" id="popbit" value="">	
						<input type="hidden" name="kwngogu" id="kwngogu" value="">	
							<table>
								<colgroup>
									<col width="18%">
									<col width="32%">
									<col width="18%">
									<col width="32%">
								</colgroup>
							<tbody class="kwndata">
								<tr class="top_gubun">
									<th>보험구분</th>
									<td>
										<select name="insilj" id="insilj" style="width:100%" disabled> 
											<?foreach($conf['insilj'] as $key => $val){?>
											<option value="<?=$key?>" <?if($insilj==$key) echo "selected"?>><?=$val?></option>
											<?}?>
										</select>	
									</td>
									<th>계약상태</th>
									<td>
										<select name="kstbit" id="kstbit" style="width:100%" disabled> 
											<option value="">미입력</option>
											<?foreach($conf['kstbit'] as $key => $val){?>
											<option value="<?=$key?>" <?if($kstbit==$key) echo "selected"?>><?=$val?></option>
											<?}?>
										</select>								
									</td>	
								</tr>
								<tr>
									<th>증권번호</th>
									<td><span class="input_type kwn_input" style="width:100%"><input type="text" name="kcode" id="kcode" value="<?=$kcode?>" disabled></span></td>
									<th>계약자명</th>
									<td>
										<span class="input_type" style="width:80%"><input type="text" name="kname" id="kname" value="<?=trim($kname)?>" disabled></span>
									</td>
								</tr>

								<tr>
									<th>계약일자</th>
									<td>
										<span class="input_type date" style="width:100%"><input type="text" class="" name="kdate" id="kdate" value="<?if($kdate) echo date("Y-m-d",strtotime($kdate)); elseif(!$kdate) echo date("Y-m-d");?>" readonly></span> 
									</td>	
									<th>수수료지급여부</th>
									<td>
										<select name="paybit" id="paybit" style="width:100%" disabled> 
											<option value="">지급여부</option>
											<?foreach($conf['pbit'] as $key => $val){?>
											<option value="<?=$key?>" <?if($paybit==$key) echo "selected"?>><?=$val?></option>
											<?}?>
										</select>								
									</td>								
								</tr>
								<tr>
									<th>계약개시일자</th>
									<td>
										<span class="input_type date" style="width:100%"><input type="text" class="" name="fdate" id="fdate" value="<?if($fdate) echo date("Y-m-d",strtotime($fdate)); elseif(!$fdate) echo '';?>" readonly></span> 
									</td>	
									<th>계약종료일자</th>
									<td>
										<span class="input_type date" style="width:100%"><input type="text" class="" name="tdate" id="tdate" value="<?if($tdate) echo date("Y-m-d",strtotime($tdate)); elseif(!$tdate) echo '';?>" readonly></span> 
									</td>								
								</tr>
								<tr>
									<th>보험사</th>							
									<td class="insgubun1">
										<select name="inscode1" id="inscode1" style="width:100%" disabled>				
										  <option value="">미입력</option>
										  <?foreach($instot as $key => $val){?>
										  <option data-seq="<?=$val['gubun']?>" value="<?=$val['code']?>" <?if($inscode==$val['code']) echo "selected"?>><?=$val['name']?></option>
										  <?}?>
										</select>								
									</td>
									<td class="insgubun2" style="display:none">
										<select name="inscode2" id="inscode2" style="width:100%" disabled>			
										  <option value="">미입력</option>
										  <?foreach($insg2 as $key => $val){?>
										  <option data-seq="<?=$val['gubun']?>" value="<?=$val['code']?>" <?if($inscode==$val['code']) echo "selected"?>><?=$val['name']?></option>
										  <?}?>
										</select>								
									</td>							
									<th>모집사원</th>
									<td>
										<span class="input_type" style="width:35%"><input type="text" name="ksman" id="ksman" value="<?=trim($ksman)?>" style="font-size:12px;" disabled></span>
										<span class="input_type ksname" style="width:55%;margin-left:10px"><input type="text" name="ksname" id="ksname" value="<?=trim($ksname)?>" disabled></span>									
									</td>								
								</tr>
								<tr>
									<th>영업당시 소속정보</th>
									<td colspan=3>
										<span class="input_type sosok" style="width:100%"><input type="text" name="sosok" id="sosok" value="<?=$sosok?>" disabled></span>
									</td>
								</tr>
								<tr>
									<th>상품</th>
									<td colspan=3>
										<span class="input_type" style="width:15%"><input type="text" name="item" id="item" value="<?=trim($item)?>" style="font-size:13px;" disabled></span>
										<span class="input_type itemnm textover" style="width:65%;margin-left:10px;border:0px"><input type="text" name="itemnm" id="itemnm" value="<?=trim($itemnm)?>" style="font-size:12px;"readonly></span>							
									</td>	
								
								</tr>
								<tr>
									<th>보험료</th>
									<td>
										<span class="input_type_number" style="width:100%"><input type="text" name="mamt" class="numberInput yb_right" value="<?=number_format($mamt)?>" style="padding-left:0px" disabled></span> 
									</td>	
									<th class="inslc1 instxt">환산월초</th>
									<td class="inslc1">
										<span class="input_type_number" style="width:100%"><input type="text" name="hamt" class="numberInput yb_right" value="<?=number_format($hamt)?>" style="padding-left:0px" disabled></span> 
									</td>
									<th class="inslc2" style="display:none">수정보험료/수정율</th>
									<td class="inslc2" style="display:none">
										<span class="input_type_number" style="width:40%"><input type="text" name="samt" class="numberInput yb_right" value="<?=number_format($samt)?>" style="padding-left:0px" disabled></span>
										<span style="margin-left:5px;display: inline-block;"> 원</span>	/
										<span class="input_type_number" style="width:40%"><input type="text" name="srate" class="numberInput yb_right" value="<?=number_format($mamt)?>" style="padding-left:0px" disabled></span> 
										<span style="margin-left:5px;display: inline-block;"> %</span>
									</td>								
								</tr>
								<tr>
									<th class="ins1">보험기간</th>
									<td class="ins1">
										<span class="input_type input_insterm" style="width:60px"><input type="text" name="insterm" id="insterm" value="<?=trim($insterm)?>" disabled></span> 
										<select name="instbit" id="instbit" style="width:20%" disabled> 
											<option value="">미입력</option>
											<?foreach($conf['instbit'] as $key => $val){?>
											<option value="<?=$key?>" <?if($instbit==$key) echo "selected"?>><?=$val?></option>
											<?}?>
										</select>									
									</td>	
									<th class="ins3">납입방법</th>
									<td class="ins3">
										<select name="nbit2" id="nbit2" style="width:20%" disabled> 
											<option value="">납입방법</option>
											<?foreach($conf['nibit_c'] as $key => $val){?>
											<option value="<?=$key?>" <?if($nbit==$key) echo "selected"?>><?=$val?></option>
											<?}?>
										</select>									
									</td>
									<th>가입경로</th>
									<td>
										<select name="fbit" id="fbit" style="width:100%" disabled> 
											<option value="">미입력</option>
											<?foreach($conf['fbit'] as $key => $val){?>
											<option value="<?=$key?>" <?if($fbit==$key) echo "selected"?>><?=$val?></option>
											<?}?>
										</select>									
									</td>								
								</tr>
								<tr class="ins2">
									<th>납입방법</th>
									<td>
										<select name="nbit1" id="nbit1" style="width:40%" disabled> 
											<option value="">미입력</option>
											<?foreach($conf['nibit_j'] as $key => $val){?>
											<option value="<?=$key?>" <?if($nbit==$key) echo "selected"?>><?=$val?></option>
											<?}?>
										</select>	
										<span style="margin:0px 10px;display: inline-block;"> / </span>	
										<span class="input_type input_insterm" style="width:25%"><input type="text" name="nterm" id="nterm" value="<?=trim($nterm)?>" disabled></span> 
										<span style="margin-left:5px;display: inline-block;"> 년납</span>										
									</td>	
									<th>수납방법/이체일자</th>
									<td>
										<select name="ksbit" id="ksbit" style="width:40%" disabled> 
											<option value="">미입력</option>
											<?foreach($conf['ksbit'] as $key => $val){?>
											<option value="<?=$key?>" <?if($ksbit==$key) echo "selected"?>><?=$val?></option>
											<?}?>
										</select> 
										<span style="margin:0px 10px;display: inline-block;"> / </span>	
										<span class="input_type" style="width:25%"><input type="text" name="kday" id="kday" value="<?=trim($kday)?>" disabled></span> 
										<span style="margin-left:5px;display: inline-block;"> 일</span>										
									</td>	
								</tr>									
							</tbody>
							</table>

						</form>
					</div><!-- // tb_type01 -->
					
				</div><!-- // con_left -->
						

				<div class="won_right" style="max-height:380px;">
					<div style="min-height:380px;border-bottom: 0px solid #47474a;">

						<!--<div class="tit_wrap mt20">
							<h3 class="tit_sub">고객별 계약내역</h3>
						</div>-->

						<div class="tb_type02 tb_type02_main" style="overflow-y:auto;max-height:380px;">
							<table id="kwnlist" class="gridhover">
								<colgroup>
									<col width="15%">
									<col width="13%">
									<col width="13%">
									<col width="10%">
									<col width="10%">
									<col width="11%">
									<col width="28%">
								</colgroup>
								<thead>
								<tr>							
									<th align="center">보험사</th>
									<th align="center">보험구분</th>	
									<th align="center">계약일자</th>
									<th align="center">계약상태</th>
									<th align="center">모집사원</th>
									<th align="center">보험료</th>
									<th align="center">상품명</th>
								</tr>
								</thead>

								<tbody>
									<?if(!empty($listData)){?>
									<?foreach($listData as $key => $val){extract($val);?>
									<tr class="rowData" rol-date='<?=$kcode?>'>								
										<td align="center"><?=$insname?></td>
										<td align="center"><?=$conf['insilj'][$insilj]?></td>
										<td align="center"><?if(trim($kdate)) echo date("Y-m-d",strtotime($kdate))?></td>
										<td align="center"><?=$conf['kstbit'][$kstbit]?></td>
										<td align="center"><?=$gskey_nm?></td>
										<td align="right" class="font_blue"><?=number_format($mamt)?></td>
										<td align="left" style="width:250px" class="textover" title="<?=$itemnm?>"><?=$itemnm?></td>				
									</tr>
									<?}}else{?>
										<tr>
											<td style="color:#8C8C8C" colspan=14>검색된 데이터가 없습니다</td>
										</tr>
									<?}?>
								</tbody>
							</table>
						</div><!-- // tb_type02 -->

					</div>

				</div><!-- // 상단 con_right -->
			
			</div><!--right/left 감싸는 div-->

			<!-- 왼쪽 하단 탭 영역 -->
			<div class="won_left" style="width:100%;min-height:500px;max-height:530px;">
				<ul id="tab" class="tab_style_main" style="margin-top:5px;border-bottom:1px solid #d5d5d5;">
					<li class="on"  data-tab="tab-1" id="tb1"><a href="#">진행사항내역</a></li>
					<li data-tab="tab-2" id="tb2"><a href="#">입금내역</a></li>
					<li data-tab="tab-3" id="tb3"><a href="#">해약<span>&#183;</span>해지관리</a></li>
					<li data-tab="tab-4" id="tb4"><a href="#">수당내역</a></li>
					<li data-tab="tab-5" id="tb5"><a href="#">모집사원</a></li>
					<li data-tab="tab-6" id="tb6"><a href="#">담보관리</a></li>
					<li data-tab="tab-7" id="tb7"><a href="#">사고관리</a></li>
				</ul>

				<!-- 진행사항내역 -->
				<div id="tab-1" class="tab_con_wrap BaseTab1 on"></div><!-- // tab-1 -->
				
				<!-- 입금내역 -->
				<div id="tab-2" class="tab_con_wrap BaseTab2"></div><!-- // tab-2 -->

				<!-- 해약/해지관리 -->
				<div id="tab-3" class="tab_con_wrap BaseTab3"></div><!-- // tab-3 -->

				<!-- 수당내역 -->
				<div id="tab-4" class="tab_con_wrap BaseTab4"></div><!-- // tab-4 -->

				<!-- 관리사원 -->
				<div id="tab-5" class="tab_con_wrap BaseTab5"></div><!-- // tab-5 -->
				
				

			</div><!-- // 하단 con_left -->

			<div class="" style="max-height:330px"></div>


			<!-- 오른쪽 하단 상담등록 영역 -->
			<!--
			<div class="con_right" style="max-height:330px">
			
				<div class="tit_wrap" style="margin-top:0px;margin-bottom:5px">
					<h3 class="tit_sub">진행사항등록</h3>
					<span class="btn_wrap">
						<a href="#" class="btn_s white" onclick="at_insert();">신규</a>
						<a href="#" class="btn_s navy"  onclick="at_update();">저장</a>
						<a href="#" class="btn_s white" onclick="at_delete();">삭제</a>
						<a href="#" class="btn_s white" onclick="modal_close();">닫기</a>
					</span>
				</div>

				<div class="tb_type01 view">
					<form name="atongha_form" class="ajaxForm_atongha" method="post" action="ga_menu2_01_action_atongha.php">
					<input type="hidden" name="type" value="<?=$type?>">
					<input type="hidden" name="kcode"  value="<?=$kcode?>">
					<input type="hidden" name="num"  value="<?=$num?>">
						<table>
							<colgroup>
								<col width="18%">
								<col width="32%">
								<col width="18%">
								<col width="32%">
							</colgroup>
						<tbody>
							<tr>
								<th>구분</th>
								<td>
									<input type="radio" class="bit updis" name="bit" id="bit1" value="1" disabled><label for="bit1">고객 </label>&nbsp;&nbsp;&nbsp;
									<input type="radio" class="bit updis" name="bit" id="bit2" value="2" checked disabled><label for="bit2">계약</label>
								</td>
								<th><em class="impor">*</em>상담구분</th>
								<td>
									<select name="gubun" id="gubun" style="width:100%">				
									  <option value="">구분</option>
									  <?foreach($comData8 as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($gubun==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>
								</td>
							</tr>
							<tr>
								<th><em class="impor">*</em>담당사원</th>
								<td colspan=3 class="span_reset">
									<span class="input_type" style="width:40%"><input type="text" name="skey" id="skey" value="<?=trim($skey)?>"></span>
									<a href="javascript:SwonSearch();" class="btn_s white">검색</a>
									<span class="sname" style="width:40%;margin-left:5px"><?=trim($sname)?></span>
								</td>
							</tr>
							<tr>
								<th><em class="impor">*</em>상담일자</th>
								<td>
									<span class="input_type" style="width:100%"><input type="text" class="Calnew" name="tondat" id="tondat" value="<?if($tondat) echo date("Y-m-d",strtotime($tondat));?>" readonly></span> 
								</td>
								<th><em class="impor">*</em>상담시간</th>
								<td>
									<span class="input_type" style="width:100%" id="input_tontim"><input type="text" name="tontim" value="<?if($type == 'up') echo $tontim;else echo date("H:i"); ?>" readonly></span> 
								</td>
							</tr>
							<tr>
								<th><em class="impor">*</em>상담내용</th>
								<td colspan="3"><textarea type="text" name="tontxt" id="tontxt" style="width:100%;height:80px"><?=$tontxt?></textarea></td>
							</tr>
						</tbody>
						</table>
					</form>
				</div>
			
			</div>--><!-- // 하단 con_right -->


		</fieldset>

	</div><!-- // content_wrap -->
</div>
<!-- // container -->




<script type="text/javascript">



// 보험구분 변경에 따른 데이터 및 화면 변경
function fn_insilj_chng(bit){
	if(bit == '1'){
		$(".insgubun1").css("display","");
		$(".insgubun2").css("display","none");
		$('.cardata').css("display","none");
		$('.ins1').css("display","");
		$('.ins2').css("display","");
		$('.ins3').css("display","none");
		$('.instxt').text('환산월초');
	}else if(bit == '2'){
		$(".insgubun1").css("display","");
		$(".insgubun2").css("display","none");
		$('.cardata').css("display","none");
		$('.ins1').css("display","");
		$('.ins2').css("display","");
		$('.ins3').css("display","none");
		$('.instxt').text('환산월초');
	}else if(bit == '3'){
		$(".insgubun1").css("display","none");
		$(".insgubun2").css("display","");	
		$('.cardata').css("display","");
		$('.ins1').css("display","none");
		$('.ins2').css("display","");
		$('.ins3').css("display","");
		$('.instxt').text('책임보험료');
	}else{
		$(".insgubun1").css("display","");
		$(".insgubun2").css("display","none");
		$('.cardata').css("display","none");
		$('.ins1').css("display","");
		$('.ins2').css("display","");
		$('.ins3').css("display","none");
		$('.instxt').text('환산월초');
	}	

}


// 개인 및 사업자 변경에 따른 화면 변경처리
function fn_sbit_chng(gubun, bit){

	if(gubun == 'A'){		// 계약자
		if(bit == '1'){
			$(".sjuno_tr").css("display","");
			$(".snum_tr").css("display","none");
		}else{
			$(".sjuno_tr").css("display","none");
			$(".snum_tr").css("display","");	
		}
	}else if(gubun == 'B'){	// 피보험자
		if(bit == '1'){
			$(".psjuno_tr").css("display","");
			$(".psnum_tr").css("display","none");
		}else{
			$(".psjuno_tr").css("display","none");
			$(".psnum_tr").css("display","");	
		}
	}
}

// 은행 및 카드 변경에 따른 입력 방식 변경
function ksgubun_chng(bit){
	if(bit == '1'){
		$(".input_bnk").css("display","");
		$(".input_card").css("display","none");	
	}else{
		$(".input_bnk").css("display","none");
		$(".input_card").css("display","");			
	}
}


// 보험사 변경처리 시 상품 및 사원내역 초기화
// type C:셀렉트박스 onchange / S:기본조회시
function fn_inscode_chng(idNm,type){

	var insilj = $("input[name='insilj']:checked").val();

	// 생명보험 및 손해보험사 구분값 가져오기
	if(idNm){								// 신규 입력 시 보험사 선택시 
		// 해당 보험사에 생명/손해 구분값 가져오기
		var data = $("#"+idNm).find("option:selected").data("seq");
	}else{									// 조회 시 선택된 보험사의 구분값 조회
		var data = $("#inscode2").find("option:selected").data("seq");
	}

	// inslc1 / inslc2
	if(insilj == '1' || insilj == '2'){
		if(data == '1'){
			$('.inslc1').css("display","");
			$('.inslc2').css("display","none");
		}else{
			$('.inslc1').css("display","none");
			$('.inslc2').css("display","");			
		}
	}

}

// 닫기
function btn_close(){	
	window.close();
	opener.location.reload();
}



function enterkey() {

	event.preventDefault();
	
	/*
	보완필요
	자동submit시 문제있고 자동submut 해제하면 로직이 불량
	if (window.event.keyCode == 13) {
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		var searchF1	= $("form[name='searchFrm'] select[name='searchF1']").val();
		var skey		= $("form[name='searchFrm'] input[name='skey']").val();	
		var searchF1Text= $("form[name='searchFrm'] input[name='searchF1Text']").val();	

		ajaxCountJSON(searchF1, skey, searchF1Text);	
    }
	*/

}


// 상단 검색조건 모집사원 및 관리사원 조회 시 사원팝업 
function fn_srch(val){

	if(val == 's1' || val == 's2'){	
		$("#searchF1Text").attr("readonly",true);
		$("#searchF1Text").css("backgroundColor","#EAEAEA");

		var left = Math.ceil((window.screen.width - 800)/2);
		var top = Math.ceil((window.screen.height - 800)/2);
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_swon_search.php","swonpop","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();
	}else{
		$("#searchF1Text").attr("readonly",false);
		$("#searchF1Text").css("backgroundColor","#fff");
	}

}


function setSwonValue(row,code,name){
	$("#skey").val(code);
	$('#searchF1Text').val(name);
}




// 상단 검색조건 모집사원 및 관리사원 조회 시 사원팝업 
function fn_srch_kwn(searchF1, skey, searchF1Text){

	var left = Math.ceil((window.screen.width - 1200)/2);
	var top = Math.ceil((window.screen.height - 800)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_kwn_search.php?searchF1="+searchF1+"&skey="+skey+"&searchF1Text="+searchF1Text,"kwnserpop","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
	

}


function setKwnValue(row,code,name){
	$("#ser_kcode").val(code);
	$("#ser_kname").val(name);
	$("form[name='searchFrm']").submit();
}



// 검색 조회결과 1건일 경우 바로조회 / 2건이상일 경우 계약팝업 오픈 
function ajaxCountJSON(searchF1, skey, searchF1Text){

	//alert(searchF1+'/'+skey+'/'+searchF1Text);

	$.ajax({
	  url: "ga_menu3_01_wonjang_ajax.php?searchF1="+searchF1+"&skey="+skey+"&searchF1Text="+searchF1Text,
	  cache : false,
	  dataType : "json",
	  method : "GET",
	  data: { ajaxType : true},
	  headers : {"charset":"euc-kr"},
	}).done(function(data) {
		//console.log(data);
		if(data.Y == 1){
			$("#ser_kcode").val(data.D);
			$("form[name='searchFrm']").submit();
		}else{
			fn_srch_kwn(searchF1, skey, searchF1Text);
			//alert('2건 이상일 경우 팝업 호출');
		}
	});
};


$(document).ready(function(){


	// 조회
	$(".btn_search").click(function(){
		$("form[name='searchFrm']").attr("method","get");
		$("form[name='searchFrm']").attr("target","");
		var searchF1	= $("form[name='searchFrm'] select[name='searchF1']").val();
		var skey		= $("form[name='searchFrm'] input[name='skey']").val();	
		var searchF1Text= $("form[name='searchFrm'] input[name='searchF1Text']").val();	

		ajaxCountJSON(searchF1, skey, searchF1Text);
		//$("form[name='searchFrm']").submit();
	}); 


	// 화면 오픈 시 계약자 구분에 따른 데이터 조회
	fn_insilj_chng($(':radio[name="insilj"]:checked').val());


	// 화면 오픈 시 계약자 구분에 따른 데이터 조회
	fn_sbit_chng('A',$(':radio[name="sbit"]:checked').val());


	// 화면 오픈 시 피보험자 구분에 따른 데이터 조회
	fn_sbit_chng('B',$(':radio[name="psbit"]:checked').val());

	// 은행 및 카드 수납정보
	ksgubun_chng($("#ksgubun").val());

	// 생명 및 손해보험 구분에 따른 책임보험료/환산월초/수정보험료 처리
	fn_inscode_chng('','S');


	// 리스트 클릭시 상세내용 조회
	$(".rowData > td").click(function(){
		var trData = $(this).parent();
		var idx    = $(".rowData").index($(trData));

		var kcode  = $(".rowData").eq(idx).attr('rol-date');

		location.href='ga_menu3_01_wonjang.php?kcode='+kcode;
	})


	// 하단 탭 디폴트 조회
	ajaxLodingTarket('ga_menu3_01_wonjang_tab1.php',$('#tab-1'),'kcode=<?=$_GET['kcode']?>');

	// 진행사항내역(Tab1)
	$("#tb1").click(function(){
		ajaxLodingTarket('ga_menu3_01_wonjang_tab1.php',$('#tab-1'),'kcode=<?=$_GET['kcode']?>');
	}); 


	// 해약/해지관리(Tab3)
	$("#tb3").click(function(){
		ajaxLodingTarket('ga_menu3_01_wonjang_tab3.php',$('#tab-3'),'kcode=<?=$_GET['kcode']?>');
	}); 

});


</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>