<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");

$inscode	= $_GET['inscode'];
$kcode		= $_GET['kcode'];

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
				substring(a.snum,6,5) snum3,
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
					 when substring(a.tel,1,2) != '02' and len(a.tel) = 10 then substring(a.tel,4,3)
					 when substring(a.tel,1,2) != '02' and len(a.tel) = 11 then substring(a.tel,4,4)
					 else substring(a.tel,3,4) end tel2,
				case when substring(a.tel,1,2) = '02' and len(a.tel) = 9 then substring(a.tel,6,4)
					 when substring(a.tel,1,2) = '02' and len(a.tel) = 10 then substring(a.tel,7,4)
					 when substring(a.tel,1,2) != '02' and len(a.tel) = 10 then substring(a.tel,7,4)
					 when substring(a.tel,1,2) != '02' and len(a.tel) = 11 then substring(a.tel,8,4)
					 else substring(a.tel,8,11) end tel3,
				a.htelbit,
				a.htel,
				case when substring(a.htel,1,2) = '02' then substring(a.htel,1,2)
					 else substring(a.htel,1,3) end htel1,
				case when substring(a.htel,1,2) = '02' and len(a.htel) = 9 then substring(a.htel,3,3)
					 when substring(a.htel,1,2) = '02' and len(a.htel) = 10 then substring(a.htel,3,4)
					 when substring(a.htel,1,2) != '02' and len(a.htel) = 10 then substring(a.htel,4,3)
					 when substring(a.htel,1,2) != '02' and len(a.htel) = 11 then substring(a.htel,4,4)
					 else substring(a.htel,3,4) end htel2,
				case when substring(a.htel,1,2) = '02' and len(a.htel) = 9 then substring(a.htel,6,4)
					 when substring(a.htel,1,2) = '02' and len(a.htel) = 10 then substring(a.htel,7,4)
					 when substring(a.htel,1,2) != '02' and len(a.htel) = 10 then substring(a.htel,7,4)
					 when substring(a.htel,1,2) != '02' and len(a.htel) = 11 then substring(a.htel,8,4)
					 else substring(a.htel,8,11) end htel3,
				a.addbit,
				a.post,
				a.addr,
				a.addr_dt,
				a.bigo,
				case when isnull(a.bonbu,'') != '' then substring(b.bname,1,2) else '' end +
				case when isnull(a.bonbu,'') != '' and (isnull(a.jisa,'') != '' or isnull(a.team,'') != '')  then ' > ' else '' end +
				case when isnull(a.jisa,'') != '' then substring(c.jsname,1,4) else '' end +
				case when isnull(a.jisa,'') != '' and isnull(a.jijum,'') != '' then ' > ' else '' end +
				case when isnull(a.jijum,'') != '' then substring(d.jname,1,4) else '' end +
				case when isnull(a.jijum,'') != '' and isnull(a.team,'') != '' then ' > ' else '' end +
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
					 when substring(a.ptel,1,2) != '02' and len(a.ptel) = 10 then substring(a.ptel,4,3)
					 when substring(a.ptel,1,2) != '02' and len(a.ptel) = 11 then substring(a.ptel,4,4)
					 else substring(a.ptel,3,4) end ptel2,
				case when substring(a.ptel,1,2) = '02' and len(a.ptel) = 9 then substring(a.ptel,6,4)
					 when substring(a.ptel,1,2) = '02' and len(a.ptel) = 10 then substring(a.ptel,7,4)
					 when substring(a.ptel,1,2) != '02' and len(a.ptel) = 10 then substring(a.ptel,7,4)
					 when substring(a.ptel,1,2) != '02' and len(a.ptel) = 11 then substring(a.ptel,8,4)
					 else substring(a.ptel,8,11) end ptel3,
				a.phtelbit,
				a.phtel,
				case when substring(a.phtel,1,2) = '02' then substring(a.phtel,1,2)
					 else substring(a.phtel,1,3) end phtel1,
				case when substring(a.phtel,1,2) = '02' and len(a.phtel) = 9 then substring(a.phtel,3,3)
					 when substring(a.phtel,1,2) = '02' and len(a.phtel) = 10 then substring(a.phtel,3,4)
					 when substring(a.phtel,1,2) != '02' and len(a.phtel) = 10 then substring(a.phtel,4,3)
					 when substring(a.phtel,1,2) != '02' and len(a.phtel) = 11 then substring(a.phtel,4,4)
					 else substring(a.phtel,3,4) end phtel2,
				case when substring(a.phtel,1,2) = '02' and len(a.phtel) = 9 then substring(a.phtel,6,4)
					 when substring(a.phtel,1,2) = '02' and len(a.phtel) = 10 then substring(a.phtel,7,4)
					 when substring(a.phtel,1,2) != '02' and len(a.phtel) = 10 then substring(a.phtel,7,4)
					 when substring(a.phtel,1,2) != '02' and len(a.phtel) = 11 then substring(a.phtel,8,4)
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
				
				a.gskey,
				a.kskey,
				case when isnull(a.udate,'') = '' then convert(varchar(30),a.idate,120) else convert(varchar(30),a.udate,120) end udate,
				case when isnull(a.uswon,'') = '' then s3.sname else s4.sname end uswonnm,
				case when isnull(a.htel,'') <> '' and len(isnull(a.htel,'')) >= 10 and substring(isnull(a.htel,''),1,2) = '01'
								then 'Y' else 'N' end smsyn ,
				case when isnull(a.tdate,'99999999') > convert(varchar(8),getdate(),112) or isnull(a.tdate,'') = '' then 'N' else 'Y' end tdateyn,
				row_number()over(order by a.kdate desc, a.kcode desc) rnum
		from kwn a	
			left outer join bonbu b on a.scode = b.scode and a.bonbu = b.bcode
			left outer join jisa c on a.scode = c.scode and a.jisa = c.jscode
			left outer join jijum d on a.scode = d.scode and a.jijum = d.jcode
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


// 계약상태
$sql= "select codesub code, subnm name from common where scode = '".$_SESSION['S_SCODE']."' and code = 'COM010' and useyn = 'Y' order by num,codesub";
$qry= sqlsrv_query( $mscon, $sql );
$comData3	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $comData10[] = $fet;
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

// SMS GET데이터 암호화
$where = " and a.kcode = '".$kcode."'";
$where = Encrypt_where($where,$secret_key,$secret_iv);

?>

<!-- html영역 -->
<style>
body{background-image: none;}

.tb_type01.view{
	margin-bottom:10px;
}


div.tb_type01 th.obj {
    background: #92A2C9;
    color: #fff;
}


.top_gubun td{
	font-size:15px;
}

.top_gubun td input[type='radio']{
	width:20px;
	height:20px;
	border:1px;
}

.top_gubun td label{
	margin-left:7px;
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


/* input, select border 제거 */
.noborder .input_type  {
    border: 0px solid #b7b7b7;
}


.tb_type01.view th, .tb_type01.view td {
    padding: 4px 10px 3px 10px;
    font-size:13px;
}

.input_type input {
    font-size: 12px;
}

.grouptd{padding:0px;text-align:center;background:#f2f3f7}


.groupbox{
	padding-left: 15px;
	font-weight:700;
	font-size:14px;
	text-align:center;
}


</style>



<div class="tit_wrap mt20">
	<span class="btn_wrap">
		<a href="#" class="btn_s white" style="min-width:100px;margin-left:5px" onclick="kakaopop();">알림톡전송</a>
		<a href="#" class="btn_s white" style="min-width:100px;" onclick="smspop();">SMS전송</a>
		<a href="#" class="btn_s white" style="min-width:100px;" onclick="kwn_new();">신규</a>
		<a href="#" class="btn_s white" style="min-width:100px;" onclick="kwn_update();">저장</a>
		<a href="#" class="btn_s white" style="min-width:100px;" onclick="kwn_delete();">삭제</a>
		<a href="#" class="btn_s white" style="min-width:100px;" onclick="kwn_close();">닫기</a>
	</span>
</div>

<!-- //box_gray -->
<div class="tb_type01 view" style="overflow-y:hidden;">
	<form name="kwn_form" class="ajaxForm_kwn" method="post" action="ga_menu3_01_action.php">
	<input type="hidden" name="type" value="<?=$type?>">
	<input type="hidden" name="popbit" id="popbit" value="">	
	<input type="hidden" name="kwngogu" id="kwngogu" value="">	
		<table>
			<colgroup>
				<col width="4%">
				<col width="13%">
				<col width="35%">
				<col width="13%">
				<col width="35%">
			</colgroup>
		<tbody class="kwndata">
			<tr class="top_gubun">
				<td class="grouptd" rowspan=7 style="text-align:center;background:#e5e5e5;border-bottom:1px solid #d5d5d5"><span class="groupbox"><br>계<br>약<br>자<br>정<br>보</span></td>
				<th><em class="impor">*</em>보험구분</th>
				<td>
					<input type="radio" class="insilj" name="insilj" id="insilj1" value="1" <?if(trim($insilj)=='1') echo "checked";?>><label for="insilj1">일반 </label>&nbsp;&nbsp;&nbsp;
					<input type="radio" class="insilj" name="insilj" id="insilj2" value="2" <?if(trim($insilj)=='2') echo "checked";?>><label for="insilj2">생손보장기 </label>&nbsp;&nbsp;&nbsp;
					<input type="radio" class="insilj" name="insilj" id="insilj3" value="3" <?if(trim($insilj)=='3') echo "checked";?>><label for="insilj3">자동차 </label>&nbsp;&nbsp;&nbsp;
					<input type="radio" class="insilj" name="insilj" id="insilj4" value="9" <?if(trim($insilj)=='9') echo "checked";?>><label for="insilj4">기타</label>
				</td>
				<th>소속정보</th>
				<td class="noborder">
					<span class="input_type" style="width:60%"><input type="text" name="sosok" id="sosok" value="<?=trim($sosok)?>" readonly></span>
					<?if($gskey){?>
					<span class="input_type" style="width:35%"><input type="text" name="gskeycode" id="gskeycode" value="<?=trim($ksname).'('.trim($gskey).')'?>" readonly></span>
					<?}?>
				</td>
			</tr>
			<tr>
				<th><em class="impor">*</em>증권번호</th>
				<td><span class="input_type kwn_input" style="width:340px"><input type="text" name="kcode" id="kcode" value="<?=$kcode?>"></span></td>
				<th><em class="impor">*</em>계약자명</th>
				<td>
					<span class="input_type" style="width:285px"><input type="text" name="kname" id="kname" value="<?=trim($kname)?>"></span>
					<a href="javascript:fn_kwngo_srch('A');" class="btn_s white hide_btn">검색</a>
				</td>
			</tr>
			<tr>
				<th>고객구분</th>
				<td>
					<input type="radio" class="sbit" name="sbit" id="sbit1" value="1" <?if(trim($sbit)=='1') echo "checked";?>><label for="sbit1">개인 </label>&nbsp;&nbsp;&nbsp;
					<input type="radio" class="sbit" name="sbit" id="sbit2" value="2" <?if(trim($sbit)=='2') echo "checked";?>><label for="sbit2">사업자</label>
				</td>
				<th class="sjuno_tr">주민등록번호</th>							
				<td class="sjuno_tr">
					<span class="input_type"><input type="text" value="<?=trim($sjuno1)?>" id="sjuno1" name="sjuno1" maxlength="6" style="width:150px" oninput="NumberOnInput(this)"></span> - 
					<span class="input_type"><input type="text" value="<?=trim($sjuno2)?>" id="sjuno2" name="sjuno2" maxlength="7" style="width:162px" oninput="NumberOnInput(this)"></span>
				</td>
				<th class="snum_tr" style="display:none">사업자번호</th>
				<td class="snum_tr" style="display:none">
					<span class="input_type"><input type="text" value="<?=trim($snum1)?>" id="snum1" name="snum1" maxlength="3" style="width:76px" oninput="NumberOnInput(this)"></span> - 
					<span class="input_type"><input type="text" value="<?=trim($snum2)?>" id="snum2" name="snum2" maxlength="2" style="width:76px" oninput="NumberOnInput(this)"></span> -
					<span class="input_type"><input type="text" value="<?=trim($snum3)?>" id="snum3" name="snum3" maxlength="5" style="width:140px" oninput="NumberOnInput(this)"></span>
				</td>
				
			</tr>
			<tr>
				<th>직장명</th>
				<td><span class="input_type" style="width:340px"><input type="text" name="comnm" id="comnm" value="<?=trim($comnm)?>"></span></td>
				<th>이메일</th>
				<td>
					<span class="input_type" style="width:340px"><input type="text" name="email" id="email" value="<?=trim($email)?>"></span>
				</td>
			</tr>
			<tr>
				<th>연락처</th>
				<td>
					<span class="input_type" style="width:99px">
						<input type="text" name="tel1" id="tel1" value="<?=trim($tel1)?>" maxlength="3" title="전화번호 앞자리 입력" oninput="NumberOnInput(this)">
					</span> -
					<span class="input_type" style="width:99px">
						<input type="text" name="tel2" id="tel2" value="<?=trim($tel2)?>" maxlength="4" title="전화번호 중간자리 입력" oninput="NumberOnInput(this)">
					</span> -
					<span class="input_type" style="width:120px">
						<input type="text" name="tel3" id="tel3" value="<?=trim($tel3)?>" maxlength="4" title="전화번호 끝자리 입력" oninput="NumberOnInput(this)">
					</span> 
				</td>
				<th><em class="impor">*</em>휴대전화</th>
				<td>
					<span class="input_type" style="width:99px">
						<input type="text" name="htel1" id="htel1" value="<?=trim($htel1)?>" maxlength="3" title="전화번호 앞자리 입력" oninput="NumberOnInput(this)">
					</span> -
					<span class="input_type" style="width:99px">
						<input type="text" name="htel2" id="htel2" value="<?=trim($htel2)?>" maxlength="4" title="전화번호 중간자리 입력" oninput="NumberOnInput(this)">
					</span> -
					<span class="input_type" style="width:120px">
						<input type="text" name="htel3" id="htel3" value="<?=trim($htel3)?>" maxlength="4" title="전화번호 끝자리 입력" oninput="NumberOnInput(this)">
					</span> 
				</td>
			</tr>
			<tr>
				<th>주소</th>
				<td colspan="3">
					<span class="input_type" style="width:99px"><input type="text" name="post" id="post" value="<?=$post?>" onclick="DaumPostcode('A');" readonly></span>
					<a href="javascript:DaumPostcode('A');" class="btn_s white" style="width:60px">검색</a>								
					<span class="input_type" style="width:330px"><input type="text" name="addr" id="addr" value="<?=$addr?>" readonly></span> 
					<span class="input_type" style="width:345px"><input type="text" name="addr_dt" id="addr_dt" value="<?=trim($addr_dt)?>"></span> 
				</td>
			</tr>
			<tr>
				<th>비고</th>
				<td colspan=3>
					<textarea style="width: 840px;height: 20px" name="bigo" id="bigo"><?=trim($bigo)?></textarea>
				</td>
			</tr>
		</tbody>
		<tbody>
			<tr>
				<td class="grouptd" rowspan=11 style="text-align:center;background:#e5e5e5;border-bottom:1px solid #d5d5d5"><span class="groupbox"><br>계<br>약<br>정<br>보</span></td>
				<th><em class="impor">*</em>계약일자</th>
				<td>
					<span class="input_type date" style="width:340px"><input type="text" class="Calnew" name="kdate" id="kdate" value="<?if($kdate) echo date("Y-m-d",strtotime($kdate));?>" readonly></span> 
				</td>	
				<th>계약상태</th>
				<td>
					<select name="kstbit" id="kstbit" style="width:340px"> 
						<option value="">선택</option>
						<?foreach($comData10 as $key => $val){?>
						<option value="<?=$val['code']?>" <?if($kstbit==$val['code']) echo "selected"?>><?=$val['name']?></option>
						<?}?>
					</select>								
				</td>								
			</tr>
			<tr>
				<th>계약개시일자</th>
				<td>
					<span class="input_type date" style="width:340px"><input type="text" class="Calnew" name="fdate" id="fdate" value="<?if($fdate) echo date("Y-m-d",strtotime($fdate)); elseif(!$fdate) echo '';?>" readonly></span> 
				</td>	
				<th>계약종료일자</th>
				<td>
					<span class="input_type date" style="width:340px"><input type="text" class="Calnew" name="tdate" id="tdate" value="<?if($tdate) echo date("Y-m-d",strtotime($tdate)); elseif(!$tdate) echo '';?>" readonly></span> 
				</td>								
			</tr>
			<tr>
				<th><em class="impor">*</em>보험사</th>							
				<td class="insgubun1">
					<select name="inscode1" id="inscode1" style="width:340px" onchange="fn_inscode_chng(this.id,'C');">				
					  <option value="">선택</option>
					  <?foreach($instot as $key => $val){?>
					  <option data-seq="<?=$val['gubun']?>" value="<?=$val['code']?>" <?if($inscode==$val['code']) echo "selected"?>><?=$val['name']?></option>
					  <?}?>
					</select>								
				</td>
				<td class="insgubun2" style="display:none">
					<select name="inscode2" id="inscode2" style="width:340px" onchange="fn_inscode_chng(this.id,'C');">			
					  <option value="">선택</option>
					  <?foreach($insg2 as $key => $val){?>
					  <option data-seq="<?=$val['gubun']?>" value="<?=$val['code']?>" <?if($inscode==$val['code']) echo "selected"?>><?=$val['name']?></option>
					  <?}?>
					</select>								
				</td>							
				<th>상품</th>
				<td>
					<span class="input_type" style="width:25%"><input type="text" name="item" id="item" value="<?=trim($item)?>"></span>
					<a href="javascript:InsitemSearch();" class="btn_s white">검색</a>
					<span class="input_type itemnm textover" style="width:50%;margin-left:3px;font-size:10px;border:0px"><input type="text" name="itemnm" id="itemnm" value="<?=trim($itemnm)?>" title="<?=trim($itemnm)?>" readonly></span>							
				</td>								
			</tr>
			<tr>
				<th><em class="impor">*</em>모집사원</th>
				<td>
					<span class="input_type" style="width:25%"><input type="text" name="ksman" id="ksman" value="<?=trim($ksman)?>"></span>
					<a href="javascript:InsSwonSearch('A');" class="btn_s white">검색</a>
					<span class="ksname" style="width:60%;margin-left:5px"><?=trim($ksname)?></span>									
				</td>	
				<th>사용인</th>
				<td>
					<span class="input_type" style="width:25%"><input type="text" name="kdman" id="kdman" value="<?=trim($kdman)?>"></span>
					<a href="javascript:InsSwonSearch('B');" class="btn_s white">검색</a>
					<span class="kdname" style="width:60%;margin-left:5px"><?=trim($kdname)?></span>									
				</td>								
			</tr>
			<tr>
				<th>보험료</th>
				<td>
					<span class="input_type_number" style="width:340px"><input type="text" name="mamt" class="numberInput yb_right" value="<?=number_format($mamt)?>" style="padding-left:0px" ></span> 
				</td>	
				<th>환산월초</th>
				<td>
					<span class="input_type_number" style="width:340px"><input type="text" name="hamt" class="numberInput yb_right" value="<?=number_format($hamt)?>" style="padding-left:0px" ></span> 
				</td>							
			</tr>
			<tr>
				<th>비고</th>
				<td colspan=3>
					<textarea style="width: 840px;height: 20px" name="bigo2"><?=trim($bigo2)?></textarea>
				</td>
			</tr>
		</tbody>
		<tbody>
			<tr>
				<td class="grouptd" rowspan=6 style="text-align:center;background:#e5e5e5;border-bottom:1px solid #d5d5d5"><span class="groupbox"><br>피<br>보<br>험<br>자<br>정<br>보</span></td>
				<th>계약자와의관계</th>
				<td>
					<select name="rel" id="rel" style="width:50%">	
						<option value="">선택</option>
						<?foreach($comData9 as $key => $val){?>
						<option value="<?=$val['code']?>" <?if($rel==$val['code']) echo "selected"?>><?=$val['name']?></option>
						<?}?>
					</select>
					<input type="checkbox" class="datacopy" name="datacopy" id="datacopy"><label style="font-size:12px;padding-left:5px">계약자 본인</label>
				</td>
				<th>피보험자명</th>
				<td>
					<span class="input_type" style="width:285px"><input type="text" name="pname" id="pname" value="<?=trim($pname)?>"></span>
					<a href="javascript:fn_kwngo_srch('B');" class="btn_s white">검색</a>
				</td>
			</tr>
			<tr>
				<th>고객구분</th>
				<td>
					<input type="radio" class="psbit" name="psbit" id="psbit1" value="1" <?if(trim($psbit)=='1') echo "checked";?>><label for="psbit1">개인 </label>&nbsp;&nbsp;&nbsp;
					<input type="radio" class="psbit" name="psbit" id="psbit2" value="2" <?if(trim($psbit)=='2') echo "checked";?>><label for="psbit2">사업자</label>
				</td>
				<th class="psjuno_tr">주민등록번호</th>							
				<td class="psjuno_tr">
					<span class="input_type"><input type="text" value="<?=trim($psjuno1)?>" id="psjuno1" name="psjuno1" maxlength="6" style="width:150px" oninput="NumberOnInput(this)"></span> - 
					<span class="input_type"><input type="text" value="<?=trim($psjuno2)?>" id="psjuno2" name="psjuno2" maxlength="7" style="width:162px" oninput="NumberOnInput(this)"></span>
				</td>
				<th class="psnum_tr" style="display:none">사업자번호</th>
				<td class="psnum_tr" style="display:none">
					<span class="input_type"><input type="text" value="<?=trim($psnum1)?>" id="psnum1" name="psnum1" maxlength="3" style="width:76px" oninput="NumberOnInput(this)"></span> - 
					<span class="input_type"><input type="text" value="<?=trim($psnum2)?>" id="psnum2" name="psnum2" maxlength="2" style="width:76px" oninput="NumberOnInput(this)"></span> -
					<span class="input_type"><input type="text" value="<?=trim($psnum3)?>" id="psnum3" name="psnum3" maxlength="5" style="width:140px" oninput="NumberOnInput(this)"></span>
				</td>
				
			</tr>
			<tr>
				<th>연락처</th>
				<td>
					<span class="input_type" style="width:100px">
						<input type="text" name="ptel1" id="ptel1" value="<?=trim($ptel1)?>" maxlength="3" title="전화번호 앞자리 입력" oninput="NumberOnInput(this)">
					</span> -
					<span class="input_type" style="width:100px">
						<input type="text" name="ptel2" id="ptel2" value="<?=trim($ptel2)?>" maxlength="4" title="전화번호 중간자리 입력" oninput="NumberOnInput(this)">
					</span> -
					<span class="input_type" style="width:120px">
						<input type="text" name="ptel3" id="ptel3" value="<?=trim($ptel3)?>" maxlength="4" title="전화번호 끝자리 입력" oninput="NumberOnInput(this)">
					</span> 
				</td>
				<th>휴대전화</th>
				<td>
					<span class="input_type" style="width:100px">
						<input type="text" name="phtel1" id="phtel1" value="<?=trim($phtel1)?>" maxlength="3" title="전화번호 앞자리 입력" oninput="NumberOnInput(this)">
					</span> -
					<span class="input_type" style="width:100px">
						<input type="text" name="phtel2" id="phtel2" value="<?=trim($phtel2)?>" maxlength="4" title="전화번호 중간자리 입력" oninput="NumberOnInput(this)">
					</span> -
					<span class="input_type" style="width:120px">
						<input type="text" name="phtel3" id="phtel3" value="<?=trim($phtel3)?>" maxlength="4" title="전화번호 끝자리 입력" oninput="NumberOnInput(this)">
					</span> 
				</td>
			</tr>
			<tr>
				<th>직장명</th>
				<td><span class="input_type" style="width:340px"><input type="text" name="pcomnm" id="pcomnm" value="<?=trim($pcomnm)?>"></span></td>
				<th>비고</th>
				<td>
					<textarea style="width:340px;height: 20px" name="pbigo" id="pbigo"><?=trim($pbigo)?></textarea>
				</td>
			</tr>
		</tbody>
		<tbody class="cardata">
			<tr>
				<td class="grouptd" rowspan=14 style="text-align:center;background:#e5e5e5"><span class="groupbox"><br>차<br>량<br>정<br>보</span></td>
				<th>차량번호</th>
				<td>
					<span class="input_type" style="width:340px"><input type="text" name="carnum" id="carnum" value="<?=trim($carnum)?>"></span>
				</td>	
				<th>차대번호</th>
				<td>
					<span class="input_type" style="width:340px"><input type="text" name="carvin" id="carvin" value="<?=trim($carvin)?>"></span>
				</td>								
			</tr>
			<tr>
				<th>한정운전</th>
				<td>
					<select name="carobj" id="carobj" style="width:340px"> 
						<option value="">선택</option>
						<?foreach($conf['carobj'] as $key => $val){?>
						<option value="<?=$key?>" <?if($carobj==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>									
				</td>	
				<th>연령특약</th>
				<td>
					<select name="carty" id="carty" style="width:340px"> 
						<option value="">선택</option>
						<?foreach($conf['carty'] as $key => $val){?>
						<option value="<?=$key?>" <?if($carty==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>									
				</td>								
			</tr>
			<tr>
				<th>대인배상I</th>
				<td>
					<select name="carpay1" id="carpay1" style="width:340px"> 
						<?foreach($conf['carpay1'] as $key => $val){?>
						<option value="<?=$key?>" <?if($carpay1==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>									
				</td>	
				<th>대인배상II</th>
				<td>
					<select name="carpay2" id="carpay2" style="width:340px"> 
						<option value="">선택</option>
						<?foreach($conf['carpay2'] as $key => $val){?>
						<option value="<?=$key?>" <?if($carpay2==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>									
				</td>								
			</tr>
			<tr>
				<th>대물배상</th>
				<td>
					<select name="carbae" id="carbae" style="width:340px"> 
						<option value="">선택</option>
						<?foreach($conf['carbae'] as $key => $val){?>
						<option value="<?=$key?>" <?if($carbae==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>									
				</td>	
				<th>자기신체손해</th>
				<td>
					<select name="carbody1" id="carbody1" style="width:115px"> 
						<option value="">선택</option>
						<?foreach($conf['carbody1'] as $key => $val){?>
						<option value="<?=$key?>" <?if($carbody1==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>	
					<select name="carbody2" id="carbody2" style="width:110px"> 
						<option value="">선택</option>
						<?foreach($conf['carbody2'] as $key => $val){?>
						<option value="<?=$key?>" <?if($carbody2==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>	
					<select name="carbody3" id="carbody3" style="width:110px"> 
						<option value="">선택</option>
						<?foreach($conf['carbody3'] as $key => $val){?>
						<option value="<?=$key?>" <?if($carbody3==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>									
				</td>								
			</tr>
			<tr>
				<th>자기차량손해</th>
				<td>
					<select name="carloss" id="carloss" style="width:340px"> 
						<option value="">선택</option>
						<?foreach($conf['carloss'] as $key => $val){?>
						<option value="<?=$key?>" <?if($carloss==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>									
				</td>	
				<th style="font-size:12px">물적사고할증기준금액</th>
				<td>
					<select name="caracamt" id="caracamt" style="width:340px"> 
						<option value="">선택</option>
						<?foreach($conf['caracamt'] as $key => $val){?>
						<option value="<?=$key?>" <?if($caracamt==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>									
				</td>								
			</tr>
			<tr>
				<th>무보험차상해</th>
				<td>
					<select name="carins" id="carins" style="width:340px"> 
						<option value="">선택</option>
						<?foreach($conf['carins'] as $key => $val){?>
						<option value="<?=$key?>" <?if($carins==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>									
				</td>	
				<th>긴급출동서비스</th>
				<td>
					<select name="caremg" id="caremg" style="width:340px"> 
						<option value="">선택</option>
						<?foreach($conf['caremg'] as $key => $val){?>
						<option value="<?=$key?>" <?if($caremg==$key) echo "selected"?>><?=$val?></option>
						<?}?>
					</select>									
				</td>								
			</tr>
		</tbody>
		</table>

	</form>
</div>
<!-- // tb_type01 -->

<div class="tit_wrap" style="margin-bottom:10px;">		
	<span class="btn_wrap">
		<span style="margin-left:15px" class="font_blue"><?=$upswon_txt?> : <?=$upswon?></span>
		<span style="margin-left:15px" class="font_blue"><?=$update_txt?> : <?=$update?></span>				
	</span>
</div>


<script type="text/javascript">

//  숫자만 입력가능
function NumberOnInput(e)  {
  e.value = e.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')
}

// 신규
function kwn_new(){
	location.href='ga_menu3_01_pop.php';
}


// 삭제
function kwn_delete(){
	var type   = $("form[name='kwn_form'] input[name='type']").val();
	// 저장전에 submit 위해서 일시적으로 해제
	$('#inscode1').attr("disabled", false);
	$('#inscode2').attr("disabled", false);

	if(type == "up"){
		if(confirm("삭제하시겠습니까?")){
			document.kwn_form.type.value='del';
			$("form[name='kwn_form']").submit();
		}
	}else{
		alert("삭제할 대상이 없습니다.");
	}
}

// 저장
function kwn_update(){

	// 저장전에 submit 위해서 일시적으로 해제
	$('#inscode1').attr("disabled", false);
	$('#inscode2').attr("disabled", false);

	var kcode   = $("form[name='kwn_form'] input[name='kcode']").val();		// 증권번호
	var kname   = $("form[name='kwn_form'] input[name='kname']").val();		// 계약자명
	var sbit    = $(':radio[name="sbit"]:checked').val();					// 계약자 고객구분
	var sjuno1	= $("form[name='kwn_form'] input[name='sjuno1']").val();	// 주민번호 앞자리
	var sjuno2	= $("form[name='kwn_form'] input[name='sjuno2']").val();	// 주민번호 뒷자리
	var snum1	= $("form[name='kwn_form'] input[name='snum1']").val();		// 사업자번호 앞자리
	var snum2	= $("form[name='kwn_form'] input[name='snum2']").val();		// 사업자번호 중간
	var snum3	= $("form[name='kwn_form'] input[name='snum3']").val();		// 사업자번호 마지막

	var htel1   = $("form[name='kwn_form'] input[name='htel1']").val();		// 휴대전화 입력1
	var htel2   = $("form[name='kwn_form'] input[name='htel2']").val();		// 휴대전화 입력2
	var htel3   = $("form[name='kwn_form'] input[name='htel3']").val();		// 휴대전화 입력3

	var kdate   = $("form[name='kwn_form'] input[name='kdate']").val();		// 계약일자
	var fdate   = $("form[name='kwn_form'] input[name='fdate']").val();		// 계약일자
	var tdate   = $("form[name='kwn_form'] input[name='tdate']").val();		// 계약일자
	var insilj	= $(':radio[name="insilj"]:checked').val();					// 상품군
	var inscode1= $("form[name='kwn_form'] select[name='inscode1']").val();	// 보험사 (상품권 일반/생손보장기 선택시)
	var inscode2= $("form[name='kwn_form'] select[name='inscode2']").val();	// 보험사 (상품군 자동차 선택시)
	var ksman	= $("form[name='kwn_form'] input[name='ksman']").val();		// 모집사원
	var pname	= $("form[name='kwn_form'] input[name='pname']").val();		// 피보험자명

	var scode	= '<?=$_SESSION['S_SCODE']?>';


	if(isEmpty(kcode) == true){
		alert('증권번호를 입력해 주세요.');
		document.getElementById('kcode').focus();
	}else if(isEmpty(kname) == true){
		alert('계약자명을 입력해 주세요.');
		document.getElementById('kname').focus();
	}else if(isEmpty(kdate) == true){
		alert('계약일자를 입력해 주세요.');
		document.getElementById('kdate').focus();
	}else if((insilj == '1' || insilj == '2' || insilj == '4') && isEmpty(inscode1) == true){
		alert('보험사를 선택해 주세요 #1');
		document.getElementById('inscode1').focus();
	}else if(insilj == '3' && isEmpty(inscode2) == true){
		alert('보험사를 선택해 주세요 #2');
		document.getElementById('inscode2').focus();
	}else if(isEmpty(ksman) == true){
		alert('모집사원을 입력해 주세요.');
		document.getElementById('ksman').focus();
	}else{
		if(confirm("저장하시겠습니까?")){
			$("form[name='kwn_form']").submit();
		}
	}
	
}


// 닫기
function kwn_close(){	
	window.close();
	//opener.location.reload();
}

// sms전송팝업
function smspop(){
	var sdate1 = '';
	var sdate2 = '';

	var where = '<?=$where?>';
	var cnt = '1';
	var sms_type = 'sms_kwn_gun';

	if('<?=$type?>' != 'up'){
		alert("전송할 데이터가 없습니다.");
		return false;
	}	

	if('<?=$smsyn?>' != 'Y'){
		alert("정상적인 휴대전화번호를 기입하세요.");
		return false;
	}

	var left = Math.ceil((window.screen.width - 500)/2);
	var top = Math.ceil((window.screen.height - 400)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/sms_pop.php?sdate1="+sdate1+"&sdate2="+sdate2+"&where="+where+"&cnt="+cnt+"&sms_type="+sms_type,"smskwngo3","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

// sms전송팝업
function kakaopop(){
	var sdate1 = '';
	var sdate2 = '';

	var where = '<?=$where?>';
	var cnt = '1';
	var sms_type = 'sms_kwn_gun';

	if('<?=$type?>' != 'up'){
		alert("전송할 데이터가 없습니다.");
		return false;
	}	

	if('<?=$smsyn?>' != 'Y'){
		alert("정상적인 휴대전화번호를 기입하세요.");
		return false;
	}

	if('<?=$tdateyn?>' != 'Y'){
		alert("만기 상품만 알림톡전송이 가능합니다.");
		return false;
	}

	var left = Math.ceil((window.screen.width - 500)/2);
	var top = Math.ceil((window.screen.height - 580)/2);
	var popOpen	= 
	window.open("<?=$conf['homeDir']?>/sub/help/kakao_pop.php?sdate1="+sdate1+"&sdate2="+sdate2+"&where="+where+"&cnt="+cnt+"&sms_type="+sms_type,"kakaokwngo3","width=500px,height=580px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}


// 개인 및 사업자 변경에 따른 화면 변경처리
function fn_sbit_chng(gubun, bit){

	if(gubun == 'A'){		// 계약자
		if(bit == '2'){
			$(".sjuno_tr").css("display","none");
			$(".snum_tr").css("display","");	
		}else{
			$(".sjuno_tr").css("display","");
			$(".snum_tr").css("display","none");
		}
	}else if(gubun == 'B'){	// 피보험자
		if(bit == '2'){
			$(".psjuno_tr").css("display","none");
			$(".psnum_tr").css("display","");	
		}else{
			$(".psjuno_tr").css("display","");
			$(".psnum_tr").css("display","none");
		}
	}
}


// 월,년 개월 계산
function dateAddDel(sDate, nNum, type) {	
	var yyyy = parseInt(sDate.substr(0, 4));	
	var mm = parseInt(sDate.substr(4, 2));	
	var dd = parseInt(sDate.substr(6, 2));	
	
	if (type == "d") {		
		d = new Date(yyyy, mm-1, dd + parseInt(nNum));	
	} else if (type == "m") {		
		d = new Date(yyyy, mm-1 + parseInt(nNum), dd);	
	} else if (type == "y") {		
		d = new Date(yyyy + parseInt(nNum), mm - 1, dd);	
	}	
	
	yyyy = d.getFullYear();	
	mm = d.getMonth() + 1; mm = (mm < 10) ? '0' + mm : mm;	
	dd = d.getDate(); dd = (dd < 10) ? '0' + dd : dd;	
	
	return  yyyy + '-' +  mm  + '-' + dd;
}



// 모집사원 및 사용인 등록 팝업(A:모집사원/B:관리사원)
function InsSwonSearch(gubun){

	$("#popbit").val(gubun);
	var insilj = $("input[name='insilj']:checked").val();					// 상품군에 따른 보험사 select 가져오기
	if(insilj == '3'){
		var inscode   = $("form[name='kwn_form'] select[name='inscode2']").val();	
	}else{
		var inscode   = $("form[name='kwn_form'] select[name='inscode1']").val();	
	}

	if(inscode){
		var left = Math.ceil((window.screen.width - 800)/2);
		var top = Math.ceil((window.screen.height - 800)/2);
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_insswon_search.php?inscode="+inscode,"swonInspop","width=600px,height=800px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();
	}else{
		alert("보험사 선택 후 상품 선택해주세요!");
		return;		
	}
}

function setInsSwonValue(row,code,name,sosok){
	var gubun = document.getElementById('popbit').value;

	// 신규등록시 사용인 기준 소속정보 보여짐
	if(gubun == 'A'){
		$("#ksman").val(code);
		$('.ksname').text(name);		
	}else{
		$("#kdman").val(code);
		$('.kdname').text(name);
		$("#sosok").val(sosok);
	}
}


// 상품선택 팝업
function InsitemSearch(){

	var insilj = $("input[name='insilj']:checked").val();					// 상품군에 따른 보험사 select 가져오기
	if(insilj == '3'){
		var inscode   = $("form[name='kwn_form'] select[name='inscode2']").val();	
	}else{
		var inscode   = $("form[name='kwn_form'] select[name='inscode1']").val();	
	}

	if(inscode){

		var left = Math.ceil((window.screen.width - 900)/2);
		var top = Math.ceil((window.screen.height - 800)/2);
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_item_search.php?inscode="+inscode,"Itempop","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();
	}else{
		alert("보험사 선택 후 상품 선택해주세요!");
		return;
	}
}

// row, 신안소프트상품코드, 상품명, 상품군, 보험사상품코드
function setItemValue(row,code,name, bbit, icode){

	$("#item").val(code);
	$("#itemnm").val(name);
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
	
	// 24.01.24 관리사원은 내부사번으로 변경
	if(type == 'C'){	// onchange 이벤트 발생시에만 
		$("#item").val('');
		$("#itemnm").val('');
	}
}




// 고객검색(A:계약자 / B:피보험자)
function fn_kwngo_srch(gubun){

	$("#kwngogu").val(gubun);

	var left = Math.ceil((window.screen.width - 800)/2);
	var top = Math.ceil((window.screen.height - 800)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_kwngo_search.php?gubun="+gubun,"kwngo_ser","width=500px,height=400px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
}


function setKwngoValue(row,name,sbit,sec_data,comnm,cupnm,telbit,tel,htelbit,htel,addbit,post,addr,addrdt,emailsel,email,bigo){
	var gubun = document.getElementById('kwngogu').value;

	var sec		= sec_data.split('-');
	var telD	= tel.split('-');
	var htelD	= htel.split('-');

	if(sbit == '1'){
		fn_sbit_chng(gubun, sbit);
	}else{
		fn_sbit_chng(gubun, sbit);
	}

	if(gubun == 'A'){
		$("#kname").val(name);
		$(":radio[name$='sbit']").val([sbit]);
		if(sbit == '1'){
			$("#sjuno1").val(sec[0]);
			$("#sjuno2").val(sec[1]);
		}else{
			$("#snum1").val(sec[0]);
			$("#snum2").val(sec[1]);	
			$("#snum3").val(sec[2]);	
		}
		$("#comnm").val(comnm);
		$("#cupnm").val(cupnm);
		$("#tel1").val(telD[0]);
		$("#tel2").val(telD[1]);
		$("#tel3").val(telD[2]);
		$("#htel1").val(htelD[0]);
		$("#htel2").val(htelD[1]);
		$("#htel3").val(htelD[2]);

		$("#post").val(post);
		$("#addr").val(addr);
		$("#addrdt").val(addrdt);
		$("#email").val(email);
		$("#bigo").val(bigo);
	}else{
		$("#pname").val(name);
		$(":radio[name$='psbit']").val([sbit]);
		if(sbit == '1'){
			$("#psjuno1").val(sec[0]);
			$("#psjuno2").val(sec[1]);
		}else{
			$("#psnum1").val(sec[0]);
			$("#psnum2").val(sec[1]);	
			$("#psnum3").val(sec[2]);	
		}
		$("#pcomnm").val(comnm);
		$("#pcupnm").val(cupnm);
		$("#ptel1").val(telD[0]);
		$("#ptel2").val(telD[1]);
		$("#ptel3").val(telD[2]);
		$("#phtel1").val(htelD[0]);
		$("#phtel2").val(htelD[1]);
		$("#phtel3").val(htelD[2]);

		$("#pbigo").val(bigo);	
	}
}


function fn_insgubun_chng(val){

	if(val == '3'){	// 자동차일경우
		$('.insgubun1').css("display","none");
		$('.insgubun2').css("display","");
	}else{
		$('.insgubun1').css("display","");
		$('.insgubun2').css("display","none");		
	}
	
}

$(document).ready(function(){

	// 데이터구분 수정불가
	var type = '<?=$type?>';
	if(type == 'in'){
		$(":radio[name$='insilj']").val(["1"]);		// 상품군
		$(":radio[name$='sbit']").val(["1"]);		// 계약자 구분
		$(":radio[name$='psbit']").val(["1"]);		// 피보험자 구분
		$(".hide_btn").css("display","");			// 계약자 및 모집사원 검색 버튼 신규 등록시에만 노출
	}else{
		// 계약번호 수정 불가
		$("input[name=kcode]").attr("readonly",true);
		$(".kwn_input").css("backgroundColor","#EAEAEA");
		$(".hide_btn").css("display","none");
		// 보험사 수정불가
		$('#inscode1').attr("disabled", true);
		$("#inscode1").css("backgroundColor","#EAEAEA");
		$('#inscode2').attr("disabled", true);
		$("#inscode2").css("backgroundColor","#EAEAEA");
	}


	// 계약자 구분 변경시(개인/사업자)
	$("input[name='sbit']").change(function(){
		var sbit = $("input[name='sbit']:checked").val();

		fn_sbit_chng('A', sbit)
	});

	// 피보험자 구분 변경시(개인/사업자)
	$("input[name='psbit']").change(function(){
		var psbit = $("input[name='psbit']:checked").val();

		fn_sbit_chng('B', psbit)
	});


	// 상품군 변경시(일반/생손보장기/자동차/기타)
	$("input[name='insilj']").change(function(){
		var insilj = $("input[name='insilj']:checked").val();

		fn_insgubun_chng(insilj)
	});

	fn_insgubun_chng($(':radio[name="insilj"]:checked').val());



	// 화면 오픈 시 계약자 구분에 따른 데이터 조회
	fn_sbit_chng('A',$(':radio[name="sbit"]:checked').val());


	// 화면 오픈 시 피보험자 구분에 따른 데이터 조회
	fn_sbit_chng('B',$(':radio[name="psbit"]:checked').val());


	// 생명 및 손해보험 구분에 따른 책임보험료/환산월초/수정보험료 처리
	fn_inscode_chng('','S');


	// 피보험자 입력시 계약자 본인일 경우 체크 할 경우
	$('#datacopy').change(function(e){
		var chk = $(this).is(":checked");
		var sbit = $(':radio[name="sbit"]:checked').val();

		if(chk == true){
			$("#pname").val(document.getElementById('kname').value);		// 피보험자명
			$(":radio[name$='psbit']").val([sbit]);							// 고객구분
			fn_sbit_chng('B',sbit);
			$("#psjuno1").val(document.getElementById('sjuno1').value);		// 주민등록번호 앞
			$("#psjuno2").val(document.getElementById('sjuno2').value);		// 주민등록번호 뒤

			$("#psnum1").val(document.getElementById('snum1').value);		// 사업자번호1
			$("#psnum2").val(document.getElementById('snum2').value);		// 사업자번호2
			$("#psnum3").val(document.getElementById('snum3').value);		// 사업자번호3

			$("#pcomnm").val(document.getElementById('comnm').value);		// 직장명
			
			$("#ptel1").val(document.getElementById('tel1').value);			// 연락처1
			$("#ptel2").val(document.getElementById('tel2').value);			// 연락처2
			$("#ptel3").val(document.getElementById('tel3').value);			// 연락처3

			$("#phtel1").val(document.getElementById('htel1').value);		// 휴대전화1
			$("#phtel2").val(document.getElementById('htel2').value);		// 휴대전화2
			$("#phtel3").val(document.getElementById('htel3').value);		// 휴대전화3

		}else if(chk == false){
			$("#pname").val('');					// 피보험자명
			$(":radio[name$='psbit']").val(['1']);	// 고객구분
			$(".psjuno_tr").css("display","");
			$(".psnum_tr").css("display","none");
			$("#psjuno1").val('');				// 주민등록번호 앞
			$("#psjuno2").val('');				// 주민등록번호 뒤
			$("#psnum1").val('');				// 사업자번호1
			$("#psnum2").val('');				// 사업자번호2
			$("#psnum3").val('');				// 사업자번호3

			$("#pcomnm").val('');				// 직장명

			$("#ptel1").val('');				// 연락처1
			$("#ptel2").val('');				// 연락처2
			$("#ptel3").val('');				// 연락처3

			$("#phtel1").val('');				// 휴대전화1
			$("#phtel2").val('');				// 휴대전화2
			$("#phtel3").val('');				// 휴대전화3

			$("#ppost").val('');				// 우편번호
			$("#paddr").val('');				// 기본주소
			$("#paddr_dt").val('');				// 상세주소

			$("#pemail").val('');				// 이메일			
		}
	});


	var options = { 
		dataType:  'json',
		beforeSubmit:  showRequest_modal_kwn,  // pre-submit callback 
		success:       processJson_modal_kwn  // post-submit callback 
	}; 

	$('.ajaxForm_kwn').ajaxForm(options);

});

// pre-submit callback 
function showRequest_modal_kwn(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_kwn(data) { 
	//console.log(data);
	if(data.message){
		alert(data.message);
		//opener.location.reload();
	}

	// 성공시
	if(data.result==''){

		opener.$('.btn_search').trigger("click");	//조회버튼클릭

		if(data.rtype == 'in' || data.rtype == 'up'){
			document.kwn_form.type.value = 'up';
			// 보험사 수정불가 처리
			$('#inscode1').attr("disabled", true);
			$('#inscode2').attr("disabled", true);
		}else if(data.rtype == 'del'){
			kwn_new();
		}
		
		//opener.location.reload();
	}

}

</script>
<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>