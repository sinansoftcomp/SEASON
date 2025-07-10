<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

//var_dump( $_SESSION );




if($_GET['carseq']){
	$type	= 'up';
	$carseq	= $_GET['carseq'];

	$sql= "select 
				a.scode,
				a.carseq,
				a.caruse,
				a.pname,
				a.jumin,
				a.carnumber,
				a.fdate,
				a.tdate,
				a.bfins,
				a.carcode,
				a.cargrade,
				a.baegicc,
				a.caryear,
				a.cardate,
				a.car_kind,
				a.carname,
				a.people_numcc,
				a.ext_bupum_txt,
				a.ext_bupum,
				a.add_bupum,
				a.carprice1,
				a.carprice,
				a.fuel,
				a.hi_repair,
				a.buy_type,
				a.guipcarrer,
				a.traffic,
				a.lawcodecnt,
				a.halin,
				a.special_code,
				a.special_code1,
				a.ncr_code,
				a.ncr_code2,
				a.ss_point,
				a.ss_point3,
				a.car_guip,
				a.car_own,
				a.careercode3,
				a.otheracc,
				a.ijumin,
				a.fetus,
				a.icnt,
				a.tmap_halin,
				a.car_own_halin,
				a.religionchk,
				a.eco_mileage,
				a.jjumin,
				a.j_name,
				a.lowestjumin,
				a.l_name,
				a.c_jumin,
				a.c_name,
				a.devide_num,
				a.muljuk,
				a.milegbn,
				a.milekm,
				a.nowkm,
				a.dambo2,
				a.dambo3,
				a.dambo4,
				a.dambo5,
				a.dambo6,
				a.goout,
				a.carage,
				a.carfamily,
				a.kcode,
				a.kdman,
				a.rateapi,
				a.ratedt,
				a.inyn,
				a.indt,
				a.upmu,
				a.rbit,
				a.selins,
				a.reday,
				a.rehour,
				a.bigo,
				a.cnum,
				a.agins,
				a.agno,
				a.memo,
				a.idate,
				a.iswon
	from carest a
	where scode = '".$_SESSION['S_SCODE']."' and carseq = '".$carseq."' ";

	$qry	= sqlsrv_query( $mscon, $sql );
	extract($fet	= sqlsrv_fetch_array($qry));

}else{
	$type	= 'in';
	$carseq	= '';
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
$sql= "select code, name, gubun from insmaster where gubun = '2' order by name";
$qry= sqlsrv_query( $mscon, $sql );
$insg2	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insg2[] = $fet;
}



// 차량종류
$sql= "select code, kind name from carkind order by code";
$qry= sqlsrv_query( $mscon, $sql );
$selcarkind	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $selcarkind[] = $fet;
}


// 할인할증
$sql= "select code, code name from carhalin order by num, code";
$qry= sqlsrv_query( $mscon, $sql );
$selcarhalin	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $selcarhalin[] = $fet;
}


// 특별할증
$sql= "select code, code name from carspecial order by groupnum, innum";
$qry= sqlsrv_query( $mscon, $sql );
$selcarspecial	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $selcarspecial[] = $fet;
}


// 법규위반
$sql= "select code, code name from carlaw order by code desc";
$qry= sqlsrv_query( $mscon, $sql );
$selcarlaw	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $selcarlaw[] = $fet;
}


// 연령한정
$sql= "select code, bigo name from carage order by code";
$qry= sqlsrv_query( $mscon, $sql );
$selcarage	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $selcarage[] = $fet;
}


// 운전자한정
$sql= "select code, bigo name from carfamily order by code";
$qry= sqlsrv_query( $mscon, $sql );
$selcarfamily	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $selcarfamily[] = $fet;
}

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html영역 -->
<style>
body{background-image: none;}

#if{width: 0px;height: 0px;border: 0px;}

</style>
					
<div class="container">
	<div class="content_wrap">
		<fieldset>
			<div class="tit_wrap mt20">
				<span class="btn_wrap">
					<a class="btn_s white" style="min-width:100px;margin-left:5px" onclick="car_new();">신규</a>
					<a class="btn_s white" style="min-width:100px;" onclick="carest_update('C');">저장</a>
					<a class="btn_s white" style="min-width:100px;" onclick="carest_delete();">삭제</a>
					<a class="btn_s white" style="min-width:100px;" onclick="carest_close();">닫기</a>
				</span>
			</div>

			<div class="tb_type01 view">
				<form name="dataform" class="ajaxForm_carest" method="post" action="ga_menu6_01_action.php">
				<input type="hidden" name="type" value="<?=$type?>">
				<input type="hidden" name="savegubun" id="savegubun" value="">
				<input type="hidden" name="carseq" id="carseq" value="<?=$carseq?>">
				<input type="hidden" name='agent' value='samsungkw2' placeholder="agent">	
				<input type="hidden" id="ret_url" name="ret_url" value="www.gaplus.net/bin/sub/menu6/testret_url.php">
					<!-- 피보험자 정보 -->
					<table>
						<colgroup>
							<col width="120px">
							<col width="300px">
							<col width="120px">
							<col width="300px">
							<col width="120px">
							<col width="300px">
						</colgroup>
						<tbody>						
							<tr>
								<th><em class="impor">*</em>보험종목</th>
								<td>
									<select name="caruse" id="caruse" style="width:250px" onchange="fn_caruse_chng(this.value);"> 
										<?foreach($conf['caruse'] as $key => $val){?>
										<option value="<?=$key?>" <?if($caruse==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>		
								</td>
								<th><em class="impor">*</em>피보험자</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="pname" id="pname" value="<?=$pname?>" ></span> 
								</td>
								<th><em class="impor">*</em>피보험자번호</th>							
								<td class="sjuno_tr" >
									<!--<span class="input_type" style="width:250px"><input type="text" value="<?=trim($jumin)?>" id="jumin" name="jumin" maxlength="13"  oninput="NumberOnInput(this)"></span>-->
									<span class="input_type" style="width:250px"><input type="text" value="8510041659511" id="jumin" name="jumin" maxlength="13"  oninput="NumberOnInput(this)"></span>
								</td>
							</tr>
							<tr>
								<th><em class="impor">*</em>차량번호</th>
								<td>
									<!--<span class="input_type" style="width:250px"><input type="text" name="carnumber" id="carnumber" value="<?=$carnumber?>"></span> -->
									<span class="input_type" style="width:250px"><input type="text" name="carnumber" id="carnumber" value="11라8141"></span> 
								</td>
								<th><em class="impor">*</em>보험기간</th>
								<td>
									<span class="input_type date" style="width:116px"><input type="text" class="Calnew" name="idate" id="fdate" value="<?if($fdate) echo date("Y-m-d",strtotime($fdate)); elseif(!$fdate) echo '';?>" readonly></span>
									<span style="width:13px;display:inline-block;text-align:center;">~</span>
									<span class="input_type date" style="width:116px"><input type="text" class="Calnew" name="idate_to" id="tdate" value="<?if($tdate) echo date("Y-m-d",strtotime($tdate)); elseif(!$tdate) echo '';?>" readonly></span>
								</td>
								<th><em class="impor">*</em>담담사용인</th>
								<td>
									<span class="input_type" style="width:120px"><input type="text" name="kdman" id="kdman" value="4LU910"></span>
									<a href="javascript:InsSwonSearch('B');" class="btn_s white" style="height:24px;line-height:22px;">검색</a>
									<span class="kdname" style="width:100px;margin-left:5px"><?=trim($kdname)?></span>
								</td>
							</tr>
						</tbody>
					</table> <!-- 피보험자 정보 End -->

					<!-- 요율관련 정보 -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>요율관련 사항</span>
							<a class="btn_s white mgl20" onclick="yoyul_send();"><i class="fa-solid fa-calculator fa-lg mgr3"></i>요율계산</a>
						</div>
					</div>
					<table>
						<colgroup>
							<col width="120px">
							<col width="300px">
							<col width="120px">
							<col width="300px">
							<col width="120px">
							<col width="300px">
						</colgroup>
						<tbody>						
							<tr>
								<th>가입경력</th>
								<td>
									<select name="guipcarrer" id="guipcarrer" style="width:250px"> 
										<?foreach($conf['guipcarrer'] as $key => $val){?>
										<option value="<?=$key?>" <?if($guipcarrer==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
								</td>
								<th>법규위반 및 횟수</th>
								<td>
									<select name="traffic" id="traffic" style="width:70px;height:24px;line-height:22px;">			
									  <?foreach($selcarlaw as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($traffic==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>
									<a href="javascript:;" class="btn_s white" style="height:24px;line-height:22px;" onclick="traffic_pop();">검색</a>
									<span style="width:13px;display:inline-block;text-align:center;">/</span>
									<select name="lawcodecnt" id="lawcodecnt" style="width:109px"> 
										<?foreach($conf['lawcodecnt'] as $key => $val){?>
										<option value="<?=$key?>" <?if($lawcodecnt==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('a',400,150);"></i>
								</td>
								<th>할인할증</th>
								<td>
									<select name="halin" id="halin" style="width:250px">			
									  <?foreach($selcarhalin as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($halin==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
								</td>
							</tr>
							<tr>
								<th>특별할증</th>
								<td>
									<select name="special_code" id="special_code" style="width:63px;height:24px;line-height:22px;">			
									  <?foreach($selcarspecial as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($special_code==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
									<a href="javascript:;" class="btn_s white" style="height:24px;line-height:22px;" onclick="specialCode_pop('A');">검색</a>
									<span style="width:14px;display:inline-block;text-align:center;">/</span>
									<select name="special_code1" id="special_code1" style="width:63px;height:24px;line-height:22px;">			
									  <?foreach($selcarspecial as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($special_code1==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
									<a href="javascript:;" class="btn_s white" style="height:24px;line-height:22px;" onclick="specialCode_pop('B');">검색</a>
								</td>
								<th>3년간사고요율</th>
								<td>
									<select name="ncr_code" id="ncr_code" style="width:250px"> 
										<?foreach($conf['ncr_code'] as $key => $val){?>
										<option value="<?=$key?>" <?if($ncr_code==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('b',600,220);"></i>
								</td>
								<th>3년간사고요율2(삼성)</th>
								<td>
									<select name="ncr_code2" id="ncr_code2" style="width:250px"> 
										<option value="">선택</option>
										<?foreach($conf['ncr_code2'] as $key => $val){?>
										<option value="<?=$key?>" <?if($ncr_code2==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('c',600,250);"></i>
								</td>
							</tr>
							<tr>
								<th>1년간사고점수</th>
								<td>
									<select name="ss_point" id="ss_point" style="width:250px"> 
										<?foreach($conf['ss_point'] as $key => $val){?>
										<option value="<?=$key?>" <?if($ss_point==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('d',450,200);"></i>
								</td>
								<th>3년간사고점수</th>
								<td>
									<select name="ss_point3" id="ss_point3" style="width:250px"> 
										<?foreach($conf['ss_point'] as $key => $val){?>
										<option value="<?=$key?>" <?if($ss_point3==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('e',420,150);"></i>
								</td>
								<th>차량가입경력</th>
								<td>
									<select name="car_guip" id="car_guip" style="width:250px"> 
										<?foreach($conf['car_guip'] as $key => $val){?>
										<option value="<?=$key?>" <?if($car_guip==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('f',550,210);"></i>
								</td>
							</tr>
							<tr>
								<th>그외 보유차량</th>
								<td>
									<select name="car_own" id="car_own" style="width:250px"> 
										<?foreach($conf['car_own'] as $key => $val){?>
										<option value="<?=$key?>" <?if($car_own==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('g',530,220);"></i>
								</td>
								<th>직전3년가입경력</th>
								<td>
									<select name="careercode3" id="careercode3" style="width:250px"> 
										<?foreach($conf['careercode3'] as $key => $val){?>
										<option value="<?=$key?>" <?if($careercode3==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('h',550,250);"></i>
								</td>
								<th>그외 사고여부</th>
								<td>
									<select name="ohteracc" id="ohteracc" style="width:250px"> 
										<?foreach($conf['ohteracc'] as $key => $val){?>
										<option value="<?=$key?>" <?if($ohteracc==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('i',510,150);"></i>
								</td>
							</tr>
						</tbody>
					</table> <!-- 요율 End -->


					<!-- 차량관련 정보 -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>차량관련 사항</span>
							<a class="btn_s white mgl20" onclick="selectcar_pop('A');"><i class="fa-solid fa-magnifying-glass fa-lg mgr3"></i>승용차</a>
							<a class="btn_s white" onclick="selectcar_pop('B');"><i class="fa-solid fa-magnifying-glass fa-lg mgr3"></i>승합/화물차</a>
							<a class="btn_s white" onclick="selectcar_pop('D');"><i class="fa-solid fa-magnifying-glass fa-lg mgr3"></i>임시코드선택</a>
							<a class="btn_s white" onclick="selectcar_pop('C');"><i class="fa-solid fa-magnifying-glass fa-lg mgr3"></i>외제차</a>
						</div>
					</div>
					<table>
						<colgroup>
							<col width="120px">
							<col width="300px">
							<col width="120px">
							<col width="300px">
							<col width="120px">
							<col width="300px">
						</colgroup>
						<tbody>						
							<tr>
								<th>차명코드</th>
								<td>
									<span class="input_type" style="width:75px;"><input type="text" name="carcode" id="carcode" value="<?=$carcode?>"></span>
									<span class="input_type" style="width:53px;margin-left:20px;"><input type="text" name="cargrade" id="cargrade" value="<?=$cargrade?>"></span> 
									<span>등급</span>
									<span class="input_type car_visible" style="width:54px;margin-left:20px;"><input type="text" name="baegicc" id="baegicc" value="<?=$baegicc?>"></span> 
									<span class="car_visible">cc</span>
									<span class="input_type car_visible2" style="width:54px;margin-left:20px;display:none;"><input type="text" name="people_numcc" id="people_numcc" value="<?=$people_numcc?>"></span> 
									<span class="car_visible2" style="display:none;" id="people_numcc_txt"></span>
								</td>
								<th>연식</th>
								<td>
									<span class="input_type" style="width:250px;"><input type="text" name="caryear" id="caryear" value="<?=$caryear?>"></span> 
								</td>
								<th>차량등록일</th>
								<td>
									<span class="input_type date" style="width:250px"><input type="text" class="Calnew" name="cardate" id="cardate" value="<?if($cardate) echo date("Y-m-d",strtotime($cardate)); elseif(!$cardate) echo '';?>" readonly></span> 
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('j',500,160);"></i>
								</td>
							</tr>
							<tr>
								<th>차종</th>
								<td>
									<select name="car_kind" id="car_kind" style="width:250px">			
									  <?foreach($selcarkind as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($car_kind==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
								</td>
								<th>차명</th>
								<td colspan=3>
									<span class="input_type" style="width:250px"><input type="text" name="carname" id="carname" value="<?=$carname?>"></span> 
								</td>
							</tr>
							<tr>
								<th>특별요율</th>
								<td colspan=5>
									<span class="input_type" style="width:854px">
										<input type="text" name="ext_bupum_txt" id="ext_bupum_txt" value="<?=$ext_bupum_txt?>">																
									</span> 
									<input type="hidden" name="ext_bupum" id="ext_bupum" value="<?=$ext_bupum?>">				
									<a href="javascript:;" class="btn_s white" onclick="car_busok('A')" style="height:24px;line-height:22px;">검색</a>
								</td>
							</tr>
							<tr>
								<th>추가부속품</th>
								<td colspan=5>
									<span class="input_type" style="width:854px">
										<input type="text" name="add_bupum_txt" id="add_bupum_txt" value="<?=$add_bupum_txt?>">									
									</span> 
									<input type="hidden" name="add_bupum" id="add_bupum" value="<?=$add_bupum?>">
									<input type="hidden" name="add_bupum_amt" id="add_bupum_amt" value="<?=$add_bupum_amt?>">	
									<a href="javascript:;" class="btn_s white" onclick="car_busok('B')" style="height:24px;line-height:22px;">검색</a>
								</td>
							</tr>
							<tr>
								<th>차량가액</th>
								<td>
									<span class="input_type_number" style="width:250px"><input type="text" name="carprice1" id="carprice1" class="numberInput yb_right"value="<?=number_format($carprice1)?>"></span> 
									<span>만원</span>
								</td>
								<th>부속품가액</th>
								<td>
									<span class="input_type_number" style="width:250px"><input type="text" name="addamt" id="addamt" class="numberInput yb_right"value="<?=number_format($addamt)?>"></span> 
									<span>만원</span>
								</td>
								<th>차량합계</th>
								<td>
									<span class="input_type_number" style="width:93px"><input type="text" name="totalamt" id="totalamt" class="numberInput yb_right"value="<?=number_format($totalamt)?>"></span> 
									<span>만원</span>
									<span style="width:14px;display:inline-block;text-align:center;">/</span>
									<span>일부</span>
									<span class="input_type_number" style="width:93px"><input type="text" name="carprice" id="carprice" class="numberInput yb_right"value="<?=number_format($carprice)?>"></span> 
									<span>만원</span>
								</td>
							</tr>
							<tr>
								<th>연료형태</th>
								<td>
									<select name="fuel" id="fuel" style="width:250px"> 
										<option value="">선택</option>
										<?foreach($conf['fuel'] as $key => $val){?>
										<option value="<?=$key?>" <?if($fuel==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('k',450,150);"></i>
								</td>
								<th>고가수리비</th>
								<td>
									<select name="hi_repair" id="hi_repair" style="width:250px"> 
										<?foreach($conf['hi_repair'] as $key => $val){?>
										<option value="<?=$key?>" <?if($hi_repair==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('l',450,150);"></i>
								</td>
								<th>구입형태</th>
								<td>
									<select name="buy_type" id="buy_type" style="width:250px"> 
										<?foreach($conf['buy_type'] as $key => $val){?>
										<option value="<?=$key?>" <?if($buy_type==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('m',450,200);"></i>
								</td>
							</tr>
						</tbody>
					</table> <!-- 차량관련 정보 End -->

					<!-- 기타특약 정보 -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>기타 사항</span>
						</div>
					</div>
					<table>
						<colgroup>
							<col width="120px">
							<col width="300px">
							<col width="120px">
							<col width="300px">
							<col width="120px">
							<col width="300px">
						</colgroup>
						<tbody>						
							<tr>
								<th>자녀할인특약</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="ijumin" id="ijumin" value="<?=$ijumin?>" placeholder="자녀생년월일(YYYYMMDD)" oninput="NumberOnInput(this)" maxlength=8></span>
									<input type="checkbox" class="fetus" name="fetus" id="fetus" value="1" <?if(trim($fetus)=='1') echo "checked";?> style="margin-left:20px;">
									<label for="fetus" style="font-size:12px;padding-left:3px">태아</label>
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('n',800,630);"></i>
								</td>
								<th>자녀수</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="icnt" id="icnt" value="<?=$icnt?>" placeholder="자녀수" oninput="NumberOnInput(this)"></span> 
								</td>
								<th>티맵운전할인</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="tmap_halin" id="tmap_halin" value="<?=$tmap_halin?>" oninput="NumberOnInput(this)" placeholder="61~100(숫자만입력)" maxlength=3></span> 
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('o',600,350);"></i>
								</td>
							</tr>
							<tr>
								<th>주행거리선할인</th>
								<td>
									<select name="eco_mileage" id="eco_mileage" style="width:250px"> 
										<option value="">선택</option>
										<?foreach($conf['eco_mileage'] as $key => $val){?>
										<option value="<?=$key?>" <?if($eco_mileage==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('p',850,370);"></i>
								</td>
								<th>다수차량할인특약</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="car_own_halin" id="car_own_halin" value="<?=$car_own_halin?>" oninput="NumberOnInput(this)" placeholder="보유대수"></span> 
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('q',500,370);"></i>
								</td>
								<th>종교단체특약</th>
								<td>
									<select name="religionchk" id="religionchk" style="width:250px"> 
										<?foreach($conf['religionchk'] as $key => $val){?>
										<option value="<?=$key?>" <?if($religionchk==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('r',450,150);"></i>
								</td>
							</tr>
							<tr>
								<th>지정1인번호</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="jjumin" id="jjumin" value="<?=$jjumin?>" oninput="NumberOnInput(this)" placeholder="생년월일(YYMMDD)+성별(1)"></span> 
								</td>
								<th>최저운전자번호</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="lowestjumin" id="lowestjumin" value="<?=$lowestjumin?>" oninput="NumberOnInput(this)" placeholder="생년월일(YYMMDD)+성별(1)"></span> 
								</td>
								<th>배우자번호</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="c_jumin" id="c_jumin" value="<?=$c_jumin?>" oninput="NumberOnInput(this)" placeholder="생년월일(YYMMDD)+성별(1)"></span> 
								</td>
							</tr>
							<tr>
								<th>지정1인명</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="j_name" id="j_name" value="<?=$j_name?>"></span> 
								</td>
								<th>최저운전자명</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="l_name" id="l_name" value="<?=$l_name?>"></span> 
								</td>
								<th>배우자명</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="c_name" id="c_name" value="<?=$c_name?>"></span> 
								</td>
							</tr>
						</tbody>
					</table> <!-- 기타사항 정보 End -->

					<!-- 담보관련 정보 -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>담보관련 사항</span>
						</div>
					</div>
					<table>
						<colgroup>
							<col width="120px">
							<col width="300px">
							<col width="120px">
							<col width="300px">
							<col width="120px">
							<col width="300px">
						</colgroup>
						<tbody>						
							<tr>
								<th>연령한정</th>
								<td>
									<select name="carage" id="carage" style="width:250px"> 
										<?foreach($selcarage as $key => $val){?>
										<option value="<?=$val['code']?>" <?if($carage==$val['code']) echo "selected"?>><?=$val['name']?></option>
										<?}?>
									</select>
								</td>
								<th>운전자한정</th>
								<td>
									<select name="carfamily" id="carfamily" style="width:250px"> 
										<?foreach($selcarfamily as $key => $val){?>
										<option value="<?=$val['code']?>" <?if($carfamily==$val['code']) echo "selected"?>><?=$val['name']?></option>
										<?}?>
									</select>
								</td>
								<th>대인II</th>
								<td>
									<select name="dambo2" id="dambo2" style="width:250px"> 
										<?foreach($conf['dambo2'] as $key => $val){?>
										<option value="<?=$key?>" <?if($dambo2==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
							</tr>
							<tr>
								<th>대물배상</th>
								<td>
									<select name="dambo3" id="dambo3" style="width:250px"> 
										<?foreach($conf['dambo3'] as $key => $val){?>
										<option value="<?=$key?>" <?if($dambo3==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>신체상해</th>
								<td>
									<select name="dambo4" id="dambo4" style="width:250px"> 
										<option value="">선택</option>
										<?foreach($conf['dambo4'] as $key => $val){?>
										<option value="<?=$key?>" <?if($dambo4==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>무보험차</th>
								<td>
									<select name="dambo5" id="dambo5" style="width:250px"> 
										<?foreach($conf['dambo5'] as $key => $val){?>
										<option value="<?=$key?>" <?if($dambo5==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
							</tr>
							<tr>
								<th>자차손해</th>
								<td>
									<select name="dambo6" id="dambo6" style="width:250px"> 
										<?foreach($conf['dambo6'] as $key => $val){?>
										<option value="<?=$key?>" <?if($dambo6==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>긴급출동</th>
								<td>
									<select name="goout1" id="goout" style="width:250px"> 
										<?foreach($conf['goout1'] as $key => $val){?>
										<option value="<?=$key?>" <?if($goout==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>물적할증</th>
								<td>
									<select name="muljuk" id="muljuk" style="width:250px"> 
										<option value="">선택</option>
										<?foreach($conf['muljuk'] as $key => $val){?>
										<option value="<?=$key?>" <?if($muljuk==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
							</tr>
							<tr>
								<th>마일리지</th>
								<td>
									<select name="MileGbn" id="MileGbn" style="width:250px"> 
										<option value="">선택</option>
										<?foreach($conf['milegbn'] as $key => $val){?>
										<option value="<?=$key?>" <?if($milegbn==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>연간주행</th>
								<td>
									<select name="MileKm" id="MileKm" style="width:250px"> 
										<option value="">선택</option>
										<?foreach($conf['milekm'] as $key => $val){?>
										<option value="<?=$key?>" <?if($milekm==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>현재주행</th>
								<td>
									<span class="input_type_number" style="width:250px"><input type="text" name="nowkm" id="nowkm" class="numberInput yb_right"value="<?=number_format($nowkm)?>"></span> 
								</td>
							</tr>
							<tr>
								<th>납입방법</th>
								<td>
									<select name="divide_num" id="divide_num" style="width:250px"> 
										<option value="">선택</option>
										<?foreach($conf['divide_num'] as $key => $val){?>
										<option value="<?=$key?>" <?if($divide_num==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th></th><td></td>
								<th></th><td></td>
							</tr>
						</tbody>
					</table> <!-- 담보사항 정보 End -->

					<!-- 상담관련 정보 -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>상담관련 사항</span>
						</div>
					</div>
					<table>
						<colgroup>
							<col width="120px">
							<col width="300px">
							<col width="120px">
							<col width="300px">
							<col width="120px">
							<col width="300px">
						</colgroup>
						<tbody>						
							<tr>
								<th>설계상태</th>
								<td>
									<select name="rbit" id="rbit" style="width:250px"> 
										<?foreach($conf['rbit'] as $key => $val){?>
										<option value="<?=$key?>" <?if($rbit==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
								</td>
								<th>업무</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="upmu" id="upmu" value="<?=$upmu?>"></span> 
								</td>
								<th></th><td></td>
							</tr>
							<tr>
								<th>설계번호</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="cnum" id="cnum" value="<?=$cnum?>"></span> 
								</td>
								<th>재상담예약</th>
								<td>
									<span class="input_type" style="width:105px"><input type="text" name="reday" id="reday" value="<?=$reday?>"></span> 
									<span style="width:14px;display:inline-block;text-align:center;margin-right:21px">일</span>
									<select name="rehour" id="rehour" style="width:105px"> 
										<option value="">선택</option>
										<?foreach($conf['rehour'] as $key => $val){?>
										<option value="<?=$key?>" <?if($rehour==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
									<span style="width:14px;display:inline-block;text-align:center;">시</span>
								</td>
								<th>선택회사</th>
								<td>
									<select name="selins" id="selins" style="width:250px">			
									  <option value="">선택</option>
									  <?foreach($insg2 as $key => $val){?>
									  <option data-seq="<?=$val['gubun']?>" value="<?=$val['code']?>" <?if($selins==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
								</td>
							</tr>
							<tr>
								<th>사전동의</th>
								<td>
									<select name="inyn" id="inyn" style="width:250px"> 
										<option value="">선택</option>
										<?foreach($conf['inyn'] as $key => $val){?>
										<option value="<?=$key?>" <?if($inyn==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>동의일자</th>
								<td>
									<span class="input_type date" style="width:250px"><input type="text" class="Calnew" name="indt" id="indt" value="<?if($indt) echo date("Y-m-d",strtotime($indt)); elseif(!$indt) echo '';?>" readonly></span> 
								</td>
								<th>동의번호</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="agno" id="agno" value="<?=$agno?>"></span> 
								</td>
							</tr>
							<tr>
								<th>기타</th>
								<td colspan=5>
									<span class="input_type" style="width:854px"><input type="text" name="bigo" id="bigo" value="<?=$bigo?>"></span> 
								</td>
							</tr>
						</tbody>
					</table> <!-- 기타사항 정보 End -->


					<!-- 보험료산출 정보 -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>보험료 결과</span>
							<a class="btn_s white mgl20" onclick="carest_inssend();"><i class="fa-solid fa-arrows-rotate fa-lg mgr3"></i>보험료산출</a>
							<a class="btn_s white" onclick=""><i class="fa-solid fa-print fa-lg mgr3"></i>견적출력</a>
						</div>
					</div>
					<div class="tb_type01 view" style="margin-bottom:20px;">
						<table>
							<colgroup>
								<col width="20px">
								<col width="80px">
								<col width="80px">
								<col width="80px">
								<col width="80px">
								<col width="80px">
								<col width="80px">
								<col width="80px">
								<col width="80px">
								<col width="80px">
								<col width="80px">
								<col width="80px">
								<col width="auto">
							</colgroup>

							<tbody style="display:none">
								<tr>
									<th><input type="checkbox"></th>
									<th>보험사</th>
									<th>총보험료</th>
									<th>대인I</th>
									<th>대인II</th>
									<th>대물배상</th>
									<th>신체상해</th>
									<th>무보험차</th>
									<th>자차손해</th>
									<th>긴급출동</th>
									<th>연령한정</th>
									<th>운전자한정</th>
									<th>메시지</th>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">DB</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="left">메시지Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">KB</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="left">메시지Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">MG</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="left">메시지Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">롯데</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="left">메시지Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">메리츠</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="left">메시지Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">삼성</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="left">메시지Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">한화</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="left">메시지Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">현대</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="left">메시지Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">흥국</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="right">1,111</td>
									<td align="left">메시지Text</td>
								</tr>
							</tbody>
						</table>
					</div>

				</form>
			</div><!-- // tb_type01 -->


			<!-- 요율계산 폼 -->
			<form method="post" name="yoyulform" action="https://www.ibss-b.co.kr/car/gas_rate.php" target="param">
				<input type="hidden" id="form_ret_url" name="ret_url" value="">
				<input type='hidden' id='form_agent' name='agent' value='samsungkw2'/> 
				<input type='hidden' id='company' name='company' value='hd'/> 
				<input type='hidden' id='user_code' name='user_code' value='4LU910'/> 

				<input type='hidden' id='form_jumin' name='jumin' value=''/> 
				<input type='hidden' id='form_carnumber' name='carnumber' value=''/> 
				<input type='hidden' id='form_caruse' name='caruse' value=''/> 
			</form>


			<iframe id="if" name="param"></iframe>


		</fieldset>
	</div><!-- // content_wrap -->
</div>

<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">


// 요율팝업
function yoyul_PopOpen(){

	//var jumin		= document.getElementById("jumin");
	//alert(jumin);

	var left = Math.ceil((window.screen.width - 500)/2);
	var top = Math.ceil((window.screen.height - 400)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu6/ga_menu6_01_yoyul_pop.php","yoyulpop","width=500px,height=300px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}


// 비교견적 전송
function yoyul_send(){

	//carest_update();

	$("#div_load_image").show();

	var jumin		= $("#jumin").val();
	var carnumber	= $("#carnumber").val();
	var caruse		= $("#caruse").val();
	var carseq		= $("#carseq").val();
	var returl		= 'www.gaplus.net/bin/sub/menu6/ga_menu6_01_ret_url_yoyul.php?carseq='+carseq;


	$("#form_jumin").val(jumin);
	$("#form_carnumber").val(carnumber);
	$("#form_caruse").val(caruse);
	$("#form_ret_url").val(returl);

	$("form[name='yoyulform']").submit();
}


//  숫자만 입력가능
function NumberOnInput(e)  {
  e.value = e.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')
}


// 차량선택
function selectcar_pop(gubun){

	/* 
		gubun : A-승용, B:승합/화물, C:외제차, D:임시
		(1)보험종목이 개인용일 경우 차량선택 시 승용차 팝업으로
		(2)보험종목이 업무용(개인) 경우 차량선택 시 화물/승합차 팝업으로
		(3)보험종목이 업무용(법인) 경우 차량선택 시 승용차 & 화물/승합차 선택이 후 진행되도록
	*/

	var caruse   = $("form[name='dataform'] select[name='caruse']").val();
	var fdate	 = $("form[name='dataform'] input[name='fdate']").val();

	if(gubun == 'A' && caruse == '2'){
		alert('업무용(개인)은 승용차를 조회하실 수 없습니다');
		return
	}else if(gubun == 'B' && caruse == '1'){
		alert('개인용은 승합/화물차를 조회하실 수 없습니다');
		return
	}

	var popurl = "";
	if(gubun == 'A'){
		popurl =  "<?=$conf['homeDir']?>/sub/help/carsel_basic_1th.php?fdate="+fdate;
	}else if(gubun == 'B'){
		popurl =  "<?=$conf['homeDir']?>/sub/help/carsel_cargo_1th.php?fdate="+fdate;
	}else if(gubun == 'C'){
		popurl =  "<?=$conf['homeDir']?>/sub/help/carsel_foreign.php?fdate="+fdate;
	}else{
		popurl =  "<?=$conf['homeDir']?>/sub/help/carsel_imsi.php?fdate="+fdate;
	}

	var left = Math.ceil((window.screen.width - 700)/2);
	var top = Math.ceil((window.screen.height - 500)/2);
	var popOpen	= window.open( popurl ,"carsel","width=700px,height=300px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}


// 차량선택 후 데이터 입력
function setCarValue(gubun, car_code, car_grade, bae_gi, caryeartxt, cardate, hyoung_sik, car_sub, amt, fuel, hi_repair, car_part, people_num, sport){

	// gubun : A-승용, B:승합/화물, C:외제차, D:임시

	if(cardate){
		var yy = cardate.substr(0,4);
		var mm = cardate.substr(4,2);
		var dd = cardate.substr(6,2);

		var date = yy+'-'+mm+'-'+dd;
	}

	// 임시차명일땐 특별요율 수동
	if(gubun != 'D'){
		var carext = ext_chng(car_part,sport);
	}

	$("#carcode").val(car_code);		// 차명코드
	$("#cargrade").val(car_grade);		// 차량등급
	$("#baegicc").val(bae_gi);			// 배기량
	$("#caryear").val(caryeartxt);		// 연식
	$("#cardate").val(date);			// 차량등록일
	$("#carname").val(car_sub);			// 차명
	$("#car_kind").val(hyoung_sik);		// 차종
	
	$("#carprice1").val(comma(amt));	// 차량가액
	$("#totalamt").val(comma(amt));		// 차량합계
	$("#fuel").val(fuel);				// 연료형태
	$("#hi_repair").val(hi_repair);		// 고가수리비

	$("#ext_bupum_txt").val(car_part);	// 특별요율txt
	$("#ext_bupum").val(carext);		// 특별요율ext

	// 그 외 초기화
	$("#add_bupum").val('');			// 특별요율
	$("#addamt").val(0);				// 부속품가액

	$("#people_numcc").val(people_num);	// 탑승인원&톤

	if(gubun == "A" || gubun == "C" || gubun == "D"){
		$(".car_visible").css("display","");
		$(".car_visible2").css("display","none");
		
	}else if(gubun == "B"){
		$(".car_visible").css("display","none");
		$(".car_visible2").css("display","");
	}

}


// 차량 특별요율 및 부속품 팝업
function car_busok(gubun){

	var extdata	 = $("form[name='dataform'] input[name='ext_bupum']").val();
	var adddata	 = $("form[name='dataform'] input[name='add_bupum']").val();
	var carseq	 = $("form[name='dataform'] input[name='carseq']").val();
	carseq = '240430000001';	// 임시세팅

	// gubun(A:특별요율 / B:부속품)
	var popurl = "";
	if(gubun == 'A'){
		popurl =  "<?=$conf['homeDir']?>/sub/help/carsel_busok_ext.php?data="+extdata;
	}else{
		popurl =  "<?=$conf['homeDir']?>/sub/help/carsel_busok_add.php?data="+adddata+"&carseq="+carseq;
	}

	var left = Math.ceil((window.screen.width - 600)/2);
	var top = Math.ceil((window.screen.height - 900)/2);
	var popOpen	= window.open( popurl ,"carbusok","width=600px,height=700px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}


// 부속품 팝업데이터 가져오기
function setCarBusok(gubun, data, text, amt, toaladdamt){
	if(gubun == 'A'){	// 특별요율
		$("#ext_bupum").val(data);
		$("#ext_bupum_txt").val(text);
	}else{				// 추가부속품
		$("#add_bupum").val(data);
		$("#add_bupum_txt").val(text);	
		$("#add_bupum_amt").val(amt);			// 부속품합계(배열)
		$("#addamt").val(comma(toaladdamt));	// 부속품가액

		var totalamt = $("#totalamt").val();
		totalamt = parseInt(uncomma(totalamt)) + parseInt(toaladdamt);
		$("#totalamt").val(comma(totalamt));	// 차량합계
	}
}


// 법규위반 팝업
function traffic_pop(){
	var left = Math.ceil((window.screen.width - 500)/2);
	var top = Math.ceil((window.screen.height - 800)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/car_traffic_search.php" ,"carlaw","width=500px,height=700px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();	
}


function setCarlaw(code){
	$("#traffic").val(code);
}


// 법규위반 팝업
function specialCode_pop(gubun){
	var left = Math.ceil((window.screen.width - 700)/2);
	var top = Math.ceil((window.screen.height - 800)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/car_specialcode_search.php" ,"carsepcial","width=700px,height=700px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();	
}


function setCarSpecial(code){
	$("#special_code").val(code);
	$("#special_code1").val(code);
}


// 신규비교견적
function car_new(){

	location.href='ga_menu6_01.php?type=in';
}


// 도움말
function doummalPopOpen(index,openwidth,openheight){

	var left = Math.ceil((window.screen.width - openwidth)/2);
	var top = Math.ceil((window.screen.height - (openheight+100))/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/doummal/doummal_"+index+".php","","width="+openwidth+"px,height="+openheight+"px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}


// 보험종목 수정시 연령한정 동적변경
function fn_caruse_chng(val){

	fn_carage_chng(val);
	fn_carfamily_chng(val);
}


// 보험종목 수정시 연령한정 동적변경
function fn_carage_chng(val){

	$.post("/bin/sub/menu6/ga_menu6_01_carage_option.php",{optVal:val}, function(data) {

		$('#carage').empty();
		$('#carage').append(data);
	});	
}


// 보험종목 수정시 운전자한정 동적변경
function fn_carfamily_chng(val){

	$.post("/bin/sub/menu6/ga_menu6_01_carfamily_option.php",{optVal:val}, function(data) {

		$('#carfamily').empty();
		$('#carfamily').append(data);
	});	
}


// 보험종료일 계산
$("#fdate").change( function() {
	ajax_idateto("fdate="+$("#fdate").val());
});

// 보험종료일 계산 (+1년)
function ajax_idateto(etcData){

	$.ajax({
	  url: "ga_menu6_01_ajax_idateto.php?"+etcData,
	  cache : false,
	  dataType : "json",
	  method : "GET",
	  data: { ajaxType : true},
	  headers : {"charset":"euc-kr"},
	}).done(function(data) {
		$("#tdate").val(data.Y);
	});
};


// 비교견적 저장
/*
	gubun => A 일 경우 요율데이터 조회전에 데이터저장
	gubun => B 일 경우 비교견적 산출 조회전에 데이터저장
	gubun => C 일 경우 비교견적 고객데이터 저장
*/
function carest_update(gubun){

	document.dataform.savegubun.value = gubun;

	if(gubun == 'A' || gubun == 'B'){
		$("form[name='dataform']").submit();
				
	}else{
		if(confirm("저장하시겠습니까?")){
			$("form[name='dataform']").submit();
		}	
	}
}


$(document).ready(function(){	

	$("#div_load_image").hide();

	var val		= $("form[name='dataform'] select[name='caruse']").val();
	var val2	= $("form[name='dataform'] select[name='carfamily']").val();
	fn_carage_chng(val);
	fn_carfamily_chng(val2);

	// 신규일때 기본값 세팅
	if('<?=$_GET['type']?>' == 'in'){
		// 신체상해 디폴트(미가입),  긴급출등 디폴트(일반형)
		$("#dambo4").val("00");
		$("#goout").val("1");			
	}

     
	window.parent.postMessage("자동차견적 > 비교견적등록", "*");   // '*' on any domain 부모로 보내기..   

	//ajaxLodingTarket('ga_menu6_01_yoyul_form.php',$('.yoyulBody'),'&val='+val);


});

// 비교견적 계약정보 저장
var options = { 
	dataType:  'json',
	beforeSubmit:  showRequest_modal_carest,  // pre-submit callback 
	success:       processJson_modal_carest  // post-submit callback 
}; 

$('.ajaxForm_carest').ajaxForm(options);
	

// pre-submit callback 
function showRequest_modal_carest(formData, jqForm, options) { 
	var queryString = $.param(formData); 
	return true; 
} 
 
// post-submit callback 
function processJson_modal_carest(data) { 
	//console.log(data);
	if(data.message){
		if(data.savegubun == 'C'){	// 고객저장시
			alert(data.message);
		}
	}

	// 성공시
	if(data.result==''){

		if(data.rtype == 'in'){
			document.dataform.type.value = 'up';
			document.dataform.carseq.value = data.carseq;
		}else if(data.rtype == 'up'){
			document.dataform.type.value = 'up';

		}else if(data.rtype == 'del'){
			setCarlaw();
		}
		
		// 요율조회
		if(data.savegubun == 'A'){
			yoyul_send();
		}
	}

}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>