<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");

/*
	���Ѱ���
	bin/include/source/auch_chk.php
*/
$pageTemp	= explode("/",$_SERVER['PHP_SELF']);
$auth = auth_Ser($_SESSION['S_MASTER'], $pageTemp[count($pageTemp)-1], $_SESSION['S_SKEY'], $mscon);
if($auth != "Y"){
	sqlsrv_close($mscon);
	alert('�ش� �޴��� ���� ������ �����ϴ�. �����ڿ��� ���� �ٶ��ϴ�.');
	exit;
}

//var_dump( $_SESSION );


if(isset($_GET['carseq'])){
	$type	= 'up';
	$carseq	= $_GET['carseq'];
}else{
	$type	= 'in';
	$carseq	= '';
}


	$sql= "select 
				a.scode,
				a.carseq,
				a.caruse,
				a.pname,
				convert(varchar(20), dbo.decryptkey(a.jumin)) jumin,
				case when substring(a.chtel,1,2) = '02' then substring(a.chtel,1,2)
					 else substring(a.chtel,1,3) end chtel1,
				case when substring(a.chtel,1,2) = '02' and len(a.chtel) = 9 then substring(a.chtel,3,3)
					 when substring(a.chtel,1,2) = '02' and len(a.chtel) = 10 then substring(a.chtel,3,4)
					 when substring(a.chtel,1,2) != '02' and len(a.chtel) = 10 then substring(a.chtel,4,3)
					 when substring(a.chtel,1,2) != '02' and len(a.chtel) = 11 then substring(a.chtel,4,4)
					 else substring(a.chtel,3,4) end chtel2,
				case when substring(a.chtel,1,2) = '02' and len(a.chtel) = 9 then substring(a.chtel,6,4)
					 when substring(a.chtel,1,2) = '02' and len(a.chtel) = 10 then substring(a.chtel,7,4)
					 when substring(a.chtel,1,2) != '02' and len(a.chtel) = 10 then substring(a.chtel,7,4)
					 when substring(a.chtel,1,2) != '02' and len(a.chtel) = 11 then substring(a.chtel,8,4)
					 else substring(a.chtel,8,11) end chtel3,
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
				a.add_bupum_txt,
				a.add_bupum_amt,
				isnull(a.carprice1,0) carprice1,
				isnull(a.carprice,0) carprice,
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
				a.iswon,
				case when isnull(a.chtel,'') <> '' and len(isnull(a.chtel,'')) >= 10 and substring(isnull(a.chtel,''),1,2) = '01'
								then 'Y' else 'N' end smsyn 
	from carest a
	where a.scode = '".$_SESSION['S_SCODE']."' and a.carseq = '".$carseq."' ";

	//$qry	= sqlsrv_query( $mscon, $sql );
	//extract($fet	= sqlsrv_fetch_array($qry));

	$qry	= sqlsrv_query( $mscon, $sql );
	$fet = sqlsrv_fetch_array($qry, SQLSRV_FETCH_ASSOC);
	if ($fet === false) {
		die(print_r(sqlsrv_errors(), true));
	}
	// �迭���� ������ ����
	if (is_array($fet)) {
		extract($fet);
	}

	// �μ�ǰ �հ�ݾ�
	$sql= "
		select sum(amt) addamt
		from carestadd
		where scode = '".$_SESSION['S_SCODE']."' 
		  and carseq = '".$carseq."' " ;

	$qry =  sqlsrv_query($mscon, $sql);
	$totalResult =  sqlsrv_fetch_array($qry); 

	if(isset($carprice1)){
		$carprice1 = $carprice1;
	}else{
		$carprice1 = 0;
	}

	$addamt		= $totalResult['addamt'];
	$totalamt	= (int)$carprice1 + (int)$addamt;


	// �񱳰������� ������ ȸ���� SMS�� �������ֱ⶧���� üũ
	$sql= "
		select count(*) smscnt
		from carestamt
		where scode = '".$_SESSION['S_SCODE']."' 
		  and carseq = '".$carseq."' " ;

	$qry =  sqlsrv_query($mscon, $sql);
	$totalResult_sms =  sqlsrv_fetch_array($qry); 
	$smscnt	= (int)$totalResult_sms['smscnt'];





// ���غ����
$sql= "select code, name, gubun from insmaster where gubun = '2' order by name";
$qry= sqlsrv_query( $mscon, $sql );
$insg2	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insg2[] = $fet;
}



// ��������
$sql= "select code, kind name from carkind order by code";
$qry= sqlsrv_query( $mscon, $sql );
$selcarkind	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $selcarkind[] = $fet;
}


// ��������
$sql= "select code, code name from carhalin order by num, code";
$qry= sqlsrv_query( $mscon, $sql );
$selcarhalin	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $selcarhalin[] = $fet;
}


// Ư������
$sql= "select code, code name from carspecial order by groupnum, innum";
$qry= sqlsrv_query( $mscon, $sql );
$selcarspecial	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $selcarspecial[] = $fet;
}


// ��������
$sql= "select code, code name from carlaw order by code desc";
$qry= sqlsrv_query( $mscon, $sql );
$selcarlaw	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $selcarlaw[] = $fet;
}


// ��������
$sql= "select code, bigo name from carage order by code";
$qry= sqlsrv_query( $mscon, $sql );
$selcarage	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $selcarage[] = $fet;
}


// ����������
$sql= "select code, bigo name from carfamily order by code";
$qry= sqlsrv_query( $mscon, $sql );
$selcarfamily	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $selcarfamily[] = $fet;
}

// SMS GET������ ��ȣȭ
$where = " and a.scode = '".$_SESSION['S_SCODE']."' and a.carseq = '".$carseq."' ";
$where = Encrypt_where($where,$secret_key,$secret_iv);

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);

?>

<!-- html���� -->
<style>
body{background-image: none;}

#if{width: 0px;height: 0px;border: 0px;}


</style>
					
<div class="container">
	<div class="content_wrap">
		<fieldset>
			<div class="tit_wrap mt20">
				<span class="btn_wrap">
					<a class="btn_s white" style="min-width:100px;margin-left:5px;display:none" id="kakaobtn" onclick="kakaopop();" >�˸�������</a>
					<a class="btn_s white" style="min-width:100px;display:none" id="smsbtn" onclick="smspop();" >SMS����</a>
					<a class="btn_s white" style="min-width:100px;" onclick="car_new();">�ű�</a>
					<a class="btn_s white" style="min-width:100px;" onclick="carest_update('C');">����</a>
					<a class="btn_s white" style="min-width:100px;" onclick="carest_delete();">����</a>
				</span>
			</div>

			<div class="tb_type01 view">
				<form name="dataform" class="ajaxForm_carest" method="post" action="ga_menu6_01_action.php">
				<input type="hidden" name="type" value="<?=$type?>">
				<input type="hidden" name="savegubun" id="savegubun" value="">
				<input type="hidden" name="carseq" id="carseq" value="<?=$carseq?>">
				<input type="hidden" name='agent' value='samsungkw2' placeholder="agent">	
				<input type="hidden" id="ret_url" name="ret_url" value="www.gaplus.net/bin/sub/menu6/testret_url.php">
					<!-- �Ǻ����� ���� -->
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
								<th><em class="impor">*</em>��������</th>
								<td>
									<select name="caruse" id="caruse" style="width:250px" onchange="fn_caruse_chng(this.value);"> 
										<?foreach($conf['caruse'] as $key => $val){?>
										<option value="<?=$key?>" <?if($caruse==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>		
								</td>
								<th><em class="impor">*</em>�Ǻ�����</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="pname" id="pname" value="<?=$pname?>" ></span> 
								</td>
								<th><em class="impor">*</em>�Ǻ����ڹ�ȣ</th>							
								<td class="sjuno_tr" >
									<span class="input_type" style="width:250px"><input type="text" value="<?=trim($jumin)?>" id="jumin" name="jumin" maxlength="13"  oninput="NumberOnInput(this)"></span>
									<!--<span class="input_type" style="width:250px"><input type="text" value="8510041659511" id="jumin" name="jumin" maxlength="13"  oninput="NumberOnInput(this)"></span>-->
								</td>
							</tr>
							<tr>
								<th><em class="impor">*</em>������ȣ</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="carnumber" id="carnumber" value="<?=$carnumber?>"></span> 
									<!--<span class="input_type" style="width:250px"><input type="text" name="carnumber" id="carnumber" value="11��8141"></span> -->
								</td>
								<th><em class="impor">*</em>����Ⱓ</th>
								<td>
									<span class="input_type date" style="width:116px"><input type="text" class="Calnew" name="idate" id="fdate" value="<?if($fdate) echo date("Y-m-d",strtotime($fdate)); elseif(!$fdate) echo '';?>" readonly></span>
									<span style="width:13px;display:inline-block;text-align:center;">~</span>
									<span class="input_type date" style="width:116px"><input type="text" class="Calnew" name="idate_to" id="tdate" value="<?if($tdate) echo date("Y-m-d",strtotime($tdate)); elseif(!$tdate) echo '';?>" readonly></span>
								</td>
								<th><em class="impor">*</em>�������</th>
								<td>
									<span class="input_type" style="width:120px"><input type="text" name="kdman" id="kdman" value=""></span>
									<a href="javascript:InsSwonSearch('B');" class="btn_s white" style="height:24px;line-height:22px;">�˻�</a>
									<span class="kdname" style="width:100px;margin-left:5px"><?=trim($kdname)?></span>
								</td>
							</tr>
							<tr>
								<th>(��)�����</th>
								<td>
									<select name="bfins" id="bfins" style="width:250px">				
									  <option value="">����</option>
									  <?foreach($insg2 as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($bfins==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
								</td>
								<th>�޴���ȭ</th>
								<td>
									<span class="input_type" style="width:62px">
										<input type="text" name="chtel1" id="chtel1" value="<?=trim($chtel1)?>" maxlength="3" title="��ȭ��ȣ ���ڸ� �Է�" oninput="NumberOnInput(this)">
									</span> -
									<span class="input_type" style="width:85px">
										<input type="text" name="chtel2" id="chtel2" value="<?=trim($chtel2)?>" maxlength="4" title="��ȭ��ȣ �߰��ڸ� �Է�" oninput="NumberOnInput(this)">
									</span> -
									<span class="input_type" style="width:85px">
										<input type="text" name="chtel3" id="chtel3" value="<?=trim($chtel3)?>" maxlength="4" title="��ȭ��ȣ ���ڸ� �Է�" oninput="NumberOnInput(this)">
									</span> 
								</td>
								<th></th><td></td>
							</tr>
						</tbody>
					</table> <!-- �Ǻ����� ���� End -->

					<!-- �������� ���� -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>�������� ����</span>
							<a class="btn_s white mgl20" onclick="carest_update('A');"><i class="fa-solid fa-calculator fa-lg mgr3"></i>�������</a>
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
								<th>���԰��</th>
								<td>
									<select name="guipcarrer" id="guipcarrer" style="width:250px;background:#f3f7ff" > 
										<?foreach($conf['guipcarrer'] as $key => $val){?>
										<option value="<?=$key?>" <?if($guipcarrer==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
								</td>
								<th>�������� �� Ƚ��</th>
								<td>
									<select name="traffic" id="traffic" style="width:70px;height:24px;line-height:22px;background:#f3f7ff">			
									  <?foreach($selcarlaw as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($traffic==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>
									<a href="javascript:;" class="btn_s white" style="height:24px;line-height:22px;" onclick="traffic_pop();">�˻�</a>
									<span style="width:13px;display:inline-block;text-align:center;">/</span>
									<select name="lawcodecnt" id="lawcodecnt" style="width:109px"> 
										<?foreach($conf['lawcodecnt'] as $key => $val){?>
										<option value="<?=$key?>" <?if($lawcodecnt==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('a',400,150);"></i>
								</td>
								<th>��������</th>
								<td>
									<select name="halin" id="halin" style="width:250px;background:#f3f7ff">			
									  <?foreach($selcarhalin as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($halin==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
								</td>
							</tr>
							<tr>
								<th>Ư������</th>
								<td>
									<select name="special_code" id="special_code" style="width:63px;height:24px;line-height:22px;background:#f3f7ff">			
									  <?foreach($selcarspecial as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($special_code==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
									<a href="javascript:;" class="btn_s white" style="height:24px;line-height:22px;" onclick="specialCode_pop('A');">�˻�</a>
									<span style="width:14px;display:inline-block;text-align:center;">/</span>
									<select name="special_code1" id="special_code1" style="width:63px;height:24px;line-height:22px;background:#f3f7ff">			
									  <?foreach($selcarspecial as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($special_code1==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
									<a href="javascript:;" class="btn_s white" style="height:24px;line-height:22px;" onclick="specialCode_pop('B');">�˻�</a>
								</td>
								<th>3�Ⱓ������</th>
								<td>
									<select name="ncr_code" id="ncr_code" style="width:250px;background:#f3f7ff"> 
										<?foreach($conf['ncr_code'] as $key => $val){?>
										<option value="<?=$key?>" <?if($ncr_code==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('b',600,220);"></i>
								</td>
								<th>3�Ⱓ������2(�Ｚ)</th>
								<td>
									<select name="ncr_code2" id="ncr_code2" style="width:250px"> 
										<option value="">����</option>
										<?foreach($conf['ncr_code2'] as $key => $val){?>
										<option value="<?=$key?>" <?if($ncr_code2==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('c',600,250);"></i>
								</td>
							</tr>
							<tr>
								<th>1�Ⱓ�������</th>
								<td>
									<select name="ss_point" id="ss_point" style="width:250px;background:#f3f7ff"> 
										<?foreach($conf['ss_point'] as $key => $val){?>
										<option value="<?=$key?>" <?if($ss_point==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('d',450,200);"></i>
								</td>
								<th>3�Ⱓ�������</th>
								<td>
									<select name="ss_point3" id="ss_point3" style="width:250px"> 
										<?foreach($conf['ss_point'] as $key => $val){?>
										<option value="<?=$key?>" <?if($ss_point3==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('e',420,150);"></i>
								</td>
								<th>�������԰��</th>
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
								<th>�׿� ��������</th>
								<td>
									<select name="car_own" id="car_own" style="width:250px"> 
										<?foreach($conf['car_own'] as $key => $val){?>
										<option value="<?=$key?>" <?if($car_own==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('g',530,220);"></i>
								</td>
								<th>����3�Ⱑ�԰��</th>
								<td>
									<select name="careercode3" id="careercode3" style="width:250px"> 
										<?foreach($conf['careercode3'] as $key => $val){?>
										<option value="<?=$key?>" <?if($careercode3==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('h',550,250);"></i>
								</td>
								<th>�׿� �����</th>
								<td>
									<select name="otheracc" id="otheracc" style="width:250px"> 
										<?foreach($conf['otheracc'] as $key => $val){?>
										<option value="<?=$key?>" <?if($otheracc==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('i',510,150);"></i>
								</td>
							</tr>
						</tbody>
					</table> <!-- ���� End -->


					<!-- �������� ���� -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>�������� ����</span>
							<a class="btn_s white mgl20" onclick="selectcar_pop('A');"><i class="fa-solid fa-magnifying-glass fa-lg mgr3"></i>�¿���</a>
							<a class="btn_s white" onclick="selectcar_pop('B');"><i class="fa-solid fa-magnifying-glass fa-lg mgr3"></i>����/ȭ����</a>
							<a class="btn_s white" onclick="selectcar_pop('D');"><i class="fa-solid fa-magnifying-glass fa-lg mgr3"></i>�ӽ��ڵ弱��</a>
							<a class="btn_s white" onclick="selectcar_pop('C');"><i class="fa-solid fa-magnifying-glass fa-lg mgr3"></i>������</a>
							<a class="btn_s white" onclick="Carcode_ser();"><i class="fa-solid fa-magnifying-glass fa-lg mgr3"></i>�����ڵ�˻�</a>
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
								<th>�����ڵ�</th>
								<td>
									<span class="input_type" style="width:75px;"><input type="text" name="carcode" id="carcode" value="<?=$carcode?>"></span>
									<span class="input_type" style="width:53px;margin-left:20px;"><input type="text" name="cargrade" id="cargrade" value="<?=$cargrade?>"></span> 
									<span>���</span>
									<span class="input_type car_visible" style="width:54px;margin-left:20px;"><input type="text" name="baegicc" id="baegicc" value="<?=$baegicc?>"></span> 
									<span class="car_visible">cc</span>
									<span class="input_type car_visible2" style="width:54px;margin-left:20px;display:none;"><input type="text" name="people_numcc" id="people_numcc" value="<?=$people_numcc?>"></span> 
									<span class="car_visible2" style="display:none;" id="people_numcc_txt"></span>
								</td>
								<th>����</th>
								<td>
									<span class="input_type" style="width:250px;"><input type="text" name="caryear" id="caryear" value="<?=$caryear?>"></span> 
								</td>
								<th>���������</th>
								<td>
									<span class="input_type date" style="width:250px"><input type="text" class="Calnew" name="cardate" id="cardate" value="<?if($cardate) echo date("Y-m-d",strtotime($cardate)); elseif(!$cardate) echo '';?>" readonly></span> 
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('j',500,160);"></i>
								</td>
							</tr>
							<tr>
								<th>����</th>
								<td>
									<select name="car_kind" id="car_kind" style="width:250px">			
									  <?foreach($selcarkind as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($car_kind==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
								</td>
								<th>����</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="carname" id="carname" value="<?=$carname?>"></span> 
								</td>
								<th></th>
								<td></td>
							</tr>
							<tr>
								<th>Ư������</th>
								<td colspan=5>
									<span class="input_type" style="width:854px">
										<input type="text" name="ext_bupum_txt" id="ext_bupum_txt" value="<?=$ext_bupum_txt?>">																
									</span> 
									<input type="hidden" name="ext_bupum" id="ext_bupum" value="<?=$ext_bupum?>">				
									<a href="javascript:;" class="btn_s white" onclick="car_busok('A')" style="height:24px;line-height:22px;">�˻�</a>
								</td>
							</tr>
							<tr>
								<th>�߰��μ�ǰ</th>
								<td colspan=5>
									<span class="input_type" style="width:854px">
										<input type="text" name="add_bupum_txt" id="add_bupum_txt" value="<?=$add_bupum_txt?>">									
									</span> 
									<input type="hidden" name="add_bupum" id="add_bupum" value="<?=$add_bupum?>">
									<input type="hidden" name="add_bupum_amt" id="add_bupum_amt" value="<?=$add_bupum_amt?>">	
									<a href="javascript:;" class="btn_s white" onclick="car_busok('B')" style="height:24px;line-height:22px;">�˻�</a>
								</td>
							</tr>
							<tr>
								<th>��������</th>
								<td>
									<span class="input_type_number" style="width:250px"><input type="text" name="carprice1" id="carprice1" class="numberInput yb_right"value="<?=number_format($carprice1)?>"></span> 
									<span>����</span>
								</td>
								<th>�μ�ǰ����</th>
								<td>
									<span class="input_type_number" style="width:250px"><input type="text" name="addamt" id="addamt" class="numberInput yb_right"value="<?=number_format($addamt)?>"></span> 
									<span>����</span>
								</td>
								<th>�����հ�</th>
								<td>
									<span class="input_type_number" style="width:93px"><input type="text" name="totalamt" id="totalamt" class="numberInput yb_right"value="<?=number_format((int)$totalamt)?>"></span> 
									<span>����</span>
									<span style="width:14px;display:inline-block;text-align:center;">/</span>
									<span>�Ϻ�</span>
									<span class="input_type_number" style="width:93px"><input type="text" name="carprice" id="carprice" class="numberInput yb_right"value="<?=number_format((int)$carprice)?>"></span> 
									<span>����</span>
								</td>
							</tr>
							<tr>
								<th>��������</th>
								<td>
									<select name="fuel" id="fuel" style="width:250px"> 
										<option value="">����</option>
										<?foreach($conf['fuel'] as $key => $val){?>
										<option value="<?=$key?>" <?if($fuel==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('k',450,150);"></i>
								</td>
								<th>��������</th>
								<td>
									<select name="hi_repair" id="hi_repair" style="width:250px"> 
										<?foreach($conf['hi_repair'] as $key => $val){?>
										<option value="<?=$key?>" <?if($hi_repair==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('l',450,150);"></i>
								</td>
								<th>��������</th>
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
					</table> <!-- �������� ���� End -->

					<!-- ��ŸƯ�� ���� -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>��Ÿ ����</span>
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
								<th>�ڳ�����Ư��</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="ijumin" id="ijumin" value="<?=$ijumin?>" placeholder="�ڳ�������(YYYYMMDD)" oninput="NumberOnInput(this)" maxlength=8></span>
									<input type="checkbox" class="fetus" name="fetus" id="fetus" value="1" <?if(trim($fetus)=='1') echo "checked";?> style="margin-left:20px;">
									<label for="fetus" style="font-size:12px;padding-left:3px">�¾�</label>
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('n',800,630);"></i>
								</td>
								<th>�ڳ��</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="icnt" id="icnt" value="<?=$icnt?>" placeholder="�ڳ��" oninput="NumberOnInput(this)"></span> 
								</td>
								<th>Ƽ�ʿ�������</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="tmap_halin" id="tmap_halin" value="<?=$tmap_halin?>" oninput="NumberOnInput(this)" placeholder="61~100(���ڸ��Է�)" maxlength=3></span> 
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('o',600,350);"></i>
								</td>
							</tr>
							<tr>
								<th>����Ÿ�������</th>
								<td>
									<select name="eco_mileage" id="eco_mileage" style="width:250px"> 
										<option value="">����</option>
										<?foreach($conf['eco_mileage'] as $key => $val){?>
										<option value="<?=$key?>" <?if($eco_mileage==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('p',850,370);"></i>
								</td>
								<th>�ټ���������Ư��</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="car_own_halin" id="car_own_halin" value="<?=$car_own_halin?>" oninput="NumberOnInput(this)" placeholder="�������"></span> 
									<i class="fa-regular fa-circle-question fa-lg question_aw" onclick="doummalPopOpen('q',500,370);"></i>
								</td>
								<th>������üƯ��</th>
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
								<th>����1�ι�ȣ</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="jjumin" id="jjumin" value="<?=$jjumin?>" oninput="NumberOnInput(this)" placeholder="�������(YYMMDD)+����(1)" maxlength=7></span> 
								</td>
								<th>���������ڹ�ȣ</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="lowestjumin" id="lowestjumin" value="<?=$lowestjumin?>" oninput="NumberOnInput(this)" placeholder="�������(YYMMDD)+����(1)" maxlength=7></span> 
								</td>
								<th>����ڹ�ȣ</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="c_jumin" id="c_jumin" value="<?=$c_jumin?>" oninput="NumberOnInput(this)" placeholder="�������(YYMMDD)+����(1)" maxlength=7></span> 
								</td>
							</tr>
							<tr>
								<th>����1�θ�</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="j_name" id="j_name" value="<?=$j_name?>"></span> 
								</td>
								<th>���������ڸ�</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="l_name" id="l_name" value="<?=$l_name?>"></span> 
								</td>
								<th>����ڸ�</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="c_name" id="c_name" value="<?=$c_name?>"></span> 
								</td>
							</tr>
						</tbody>
					</table> <!-- ��Ÿ���� ���� End -->

					<!-- �㺸���� ���� -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>�㺸���� ����</span>
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
								<th>��������</th>
								<td>
									<select name="carage" id="carage" style="width:250px"> 
										<?foreach($selcarage as $key => $val){?>
										<option value="<?=$val['code']?>" <?if($carage==$val['code']) echo "selected"?>><?=$val['name']?></option>
										<?}?>
									</select>
								</td>
								<th>����������</th>
								<td>
									<select name="carfamily" id="carfamily" style="width:250px"> 
										<?foreach($selcarfamily as $key => $val){?>
										<option value="<?=$val['code']?>" <?if($carfamily==$val['code']) echo "selected"?>><?=$val['name']?></option>
										<?}?>
									</select>
								</td>
								<th>����II</th>
								<td>
									<select name="dambo2" id="dambo2" style="width:250px"> 
										<?foreach($conf['dambo2'] as $key => $val){?>
										<option value="<?=$key?>" <?if($dambo2==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
							</tr>
							<tr>
								<th>�빰���</th>
								<td>
									<select name="dambo3" id="dambo3" style="width:250px"> 
										<?foreach($conf['dambo3'] as $key => $val){?>
										<option value="<?=$key?>" <?if($dambo3==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>��ü����</th>
								<td>
									<select name="dambo4" id="dambo4" style="width:250px"> 
										<option value="">����</option>
										<?foreach($conf['dambo4'] as $key => $val){?>
										<option value="<?=$key?>" <?if($dambo4==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>��������</th>
								<td>
									<select name="dambo5" id="dambo5" style="width:250px"> 
										<?foreach($conf['dambo5'] as $key => $val){?>
										<option value="<?=$key?>" <?if($dambo5==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
							</tr>
							<tr>
								<th>��������</th>
								<td>
									<select name="dambo6" id="dambo6" style="width:250px"> 
										<?foreach($conf['dambo6'] as $key => $val){?>
										<option value="<?=$key?>" <?if($dambo6==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>����⵿</th>
								<td>
									<select name="goout1" id="goout" style="width:250px"> 
										<?foreach($conf['goout1'] as $key => $val){?>
										<option value="<?=$key?>" <?if($goout==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>��������</th>
								<td>
									<select name="muljuk" id="muljuk" style="width:250px"> 
										<option value="">����</option>
										<?foreach($conf['muljuk'] as $key => $val){?>
										<option value="<?=$key?>" <?if($muljuk==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
							</tr>
							<tr>
								<th>���ϸ���</th>
								<td>
									<select name="MileGbn" id="MileGbn" style="width:250px"> 
										<option value="">����</option>
										<?foreach($conf['milegbn'] as $key => $val){?>
										<option value="<?=$key?>" <?if($milegbn==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>��������</th>
								<td>
									<select name="MileKm" id="MileKm" style="width:250px"> 
										<option value="">����</option>
										<?foreach($conf['milekm'] as $key => $val){?>
										<option value="<?=$key?>" <?if($milekm==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>��������</th>
								<td>
									<span class="input_type_number" style="width:250px"><input type="text" name="nowkm" id="nowkm" class="numberInput yb_right"value="<?=number_format($nowkm)?>"></span> 
								</td>
							</tr>
							<tr>
								<th>���Թ��</th>
								<td>
									<select name="devide_num" id="devide_num" style="width:250px"> 
										<option value="">����</option>
										<?foreach($conf['devide_num'] as $key => $val){?>
										<option value="<?=$key?>" <?if($devide_num==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th></th><td></td>
								<th></th><td></td>
							</tr>
						</tbody>
					</table> <!-- �㺸���� ���� End -->

					<!-- ������ ���� -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>������ ����</span>
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
								<th>�������</th>
								<td>
									<select name="rbit" id="rbit" style="width:250px"> 
										<?foreach($conf['rbit'] as $key => $val){?>
										<option value="<?=$key?>" <?if($rbit==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
								</td>
								<th>����</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="upmu" id="upmu" value="<?=$upmu?>"></span> 
								</td>
								<th></th><td></td>
							</tr>
							<tr>
								<th>�����ȣ</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="cnum" id="cnum" value="<?=$cnum?>"></span> 
								</td>
								<th>���㿹��</th>
								<td>
									<span class="input_type" style="width:105px"><input type="text" name="reday" id="reday" value="<?=$reday?>"></span> 
									<span style="width:14px;display:inline-block;text-align:center;margin-right:21px">��</span>
									<select name="rehour" id="rehour" style="width:105px"> 
										<option value="">����</option>
										<?foreach($conf['rehour'] as $key => $val){?>
										<option value="<?=$key?>" <?if($rehour==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
									<span style="width:14px;display:inline-block;text-align:center;">��</span>
								</td>
								<th>����ȸ��</th>
								<td>
									<select name="selins" id="selins" style="width:250px">			
									  <option value="">����</option>
									  <?foreach($insg2 as $key => $val){?>
									  <option data-seq="<?=$val['gubun']?>" value="<?=$val['code']?>" <?if($selins==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
								</td>
							</tr>
							<tr>
								<th>��������</th>
								<td>
									<select name="inyn" id="inyn" style="width:250px"> 
										<option value="">����</option>
										<?foreach($conf['inyn'] as $key => $val){?>
										<option value="<?=$key?>" <?if($inyn==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>
								</td>
								<th>��������</th>
								<td>
									<span class="input_type date" style="width:250px"><input type="text" class="Calnew" name="indt" id="indt" value="<?if($indt) echo date("Y-m-d",strtotime($indt)); elseif(!$indt) echo '';?>" readonly></span> 
								</td>
								<th>���ǹ�ȣ</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="agno" id="agno" value="<?=$agno?>"></span> 
								</td>
							</tr>
							<tr>
								<th>��Ÿ</th>
								<td colspan=5>
									<span class="input_type" style="width:854px"><input type="text" name="bigo" id="bigo" value="<?=$bigo?>"></span> 
								</td>
							</tr>
						</tbody>
					</table> <!-- ��Ÿ���� ���� End -->


					<!-- �������� ���� -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>����� ���</span>
							<a class="btn_s white mgl20" onclick="carest_update('B');"><i class="fa-solid fa-arrows-rotate fa-lg mgr3"></i>��������</a>
							<a class="btn_s white" onclick="print_caramt();"><i class="fa-solid fa-print fa-lg mgr3"></i>�������</a>
						</div>
					</div>
					<div id="caramtins"></div>

				</form>
			</div><!-- // tb_type01 -->

			<iframe name="form_iframe" id="if" style="height:0;width:0;border:0;border:none;visibility:hidden;"></iframe>

			<!-- ������� �� -->
			<form method="post" name="yoyulform" target="form_iframe" action="//www.ibss-b.co.kr/car/gas_rate.php">
				<input type="hidden" id="form_ret_url" name="ret_url" value="">
				<input type='hidden' id='form_agent' name='agent' value='samsungkw2'/> 
				<input type='hidden' id='company' name='company' value='hd'/> 
				<input type='hidden' id='user_code' name='user_code' value='4LU910'/> 

				<input type='hidden' id='form_jumin' name='jumin' value=''/> 
				<input type='hidden' id='form_carnumber' name='carnumber' value=''/> 
				<input type='hidden' id='form_caruse' name='caruse' value=''/> 
			</form>


			<!-- �񱳰��� ���� �� -->
			<form method="post" name="caramtform" target="form_iframe" action="https://www.ibss-b.co.kr/car/gas_car.php">
				<input type="hidden" id="car_ret_url" name="ret_url" value="">
				<input type='hidden' id='car_agent' name='agent' value='samsungkw2'/> 
				
				<input type='hidden' id='car_caruse'	name='caruse' value=''/> 
				<input type='hidden' id='car_jumin'		name='jumin' value=''/> 
				<input type='hidden' id='car_idate'		name='idate' value=''/> 
				<input type='hidden' id='car_idate_to'	name='idate_to' value=''/> 
				<input type='hidden' id='car_carcode'	name='carcode' value=''/> 
				<input type='hidden' id='car_cargrade'	name='cargrade' value=''/> 
				<input type='hidden' id='car_baegicc'	name='baegicc' value=''/> 
				<input type='hidden' id='car_caryear'	name='caryear' value=''/> 
				<input type='hidden' id='car_car_kind'	name='car_kind' value=''/> 
				<input type='hidden' id='car_people_numcc' name='people_numcc' value=''/> 
				<input type='hidden' id='car_ext_bupum'	name='ext_bupum' value=''/> 
				<input type='hidden' id='car_add_bupum'	name='add_bupum' value=''/> 
				<input type='hidden' id='car_add_bupumprice' name='add_bupumprice' value=''/> 
				<input type='hidden' id='car_carprice1'	name='carprice1' value=''/> 
				<input type='hidden' id='car_carprice'	name='carprice' value=''/> 
				<input type='hidden' id='car_carname'	name='carname' value=''/> 

				<input type='hidden' id='car_guipcarrer'name='guipcarrer' value=''/> 
				<input type='hidden' id='car_traffic'	name='traffic' value=''/> 
				<input type='hidden' id='car_halin'		name='halin' value=''/> 
				<input type='hidden' id='car_special_code' name='special_code' value=''/> 
				<input type='hidden' id='car_special_code1'name='special_code1' value=''/> 
				<input type='hidden' id='car_ncr_code'	name='ncr_code' value=''/> 
				<input type='hidden' id='car_ncr_code2' name='ncr_code2' value=''/> 
				<input type='hidden' id='car_ss_point'	name='ss_point' value=''/> 
				<input type='hidden' id='car_ss_point3' name='ss_point3' value=''/> 
				<input type='hidden' id='car_car_guip'	name='car_guip' value=''/> 
				<input type='hidden' id='car_car_own'	name='car_own' value=''/> 
				<input type='hidden' id='car_buy_type'	name='buy_type' value=''/> 

				<input type='hidden' id='car_eco_mileage'	name='eco_mileage' value=''/> 
				<input type='hidden' id='car_tmap_halin'	name='tmap_halin' value=''/> 
				<input type='hidden' id='car_car_own_halin'	name='car_own_halin' value=''/> 
				<input type='hidden' id='car_careercode3'	name='careercode3' value=''/> 
				<input type='hidden' id='car_lawcodecnt'	name='lawcodecnt' value=''/> 
				<input type='hidden' id='car_lowestJumin'	name='lowestJumin' value=''/> 
				<input type='hidden' id='car_jjumin'	name='jjumin' value=''/> 
				<input type='hidden' id='car_c_jumin'	name='c_jumin' value=''/> 
				<input type='hidden' id='car_ijumin'	name='ijumin' value=''/> 
				<input type='hidden' id='car_fetus'		name='fetus' value=''/> 
				<input type='hidden' id='car_child_cnt'	name='child_cnt' value=''/> 

				<input type='hidden' id='car_divide_num'name='divide_num' value=''/> 
				<input type='hidden' id='car_dambo2'	name='dambo2' value=''/> 
				<input type='hidden' id='car_dambo3'	name='dambo3' value=''/> 
				<input type='hidden' id='car_dambo4'	name='dambo4' value=''/> 
				<input type='hidden' id='car_dambo5'	name='dambo5' value=''/> 
				<input type='hidden' id='car_dambo6'	name='dambo6' value=''/> 
				<input type='hidden' id='car_goout1'	name='goout1' value=''/> 
				<input type='hidden' id='car_carfamily'	name='carfamily' value=''/> 
				<input type='hidden' id='car_carage'	name='carage' value=''/> 
				<input type='hidden' id='car_muljuk'	name='muljuk' value=''/> 
				<input type='hidden' id='car_MileGbn'	name='MileGbn' value=''/> 
				<input type='hidden' id='car_MileKm'	name='MileKm' value=''/> 
				<input type='hidden' id='car_fuel'		name='fuel' value=''/> 
				<input type='hidden' id='car_hi_repair'	name='hi_repair' value=''/> 
				<input type='hidden' id='car_religionchk'	name='religionchk' value=''/> 
				<input type='hidden' id='car_otheracc'	name='otheracc' value=''/> 
				<input type='hidden' id='car_j_kind'	name='j_kind' value=''/> 
				<input type='hidden' id='car_cardate'	name='cardate' value=''/> 
			</form>


		</fieldset>
	</div><!-- // content_wrap -->
</div>

<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">

// ���� ����Ű �����ڵ� �˻�
$("#caryear").on("keyup", function(key){
	if(key.keyCode == 13){
		Carcode_ser();
	}
});

// ��������� ���ý� �˻��� ���� ������ �����ڵ� �˻�
$("#cardate").on("change", function(key){
	var carname	= $("#carname").val();
	
	if(isEmpty(carname) == true){
		Carcode_ser();
	}
});

// �������� �ʱ�ȭ
function car_Reset(){
	$("#ext_bupum_txt").val('');		// Ư������txt
	$("#ext_bupum").val('');			// Ư������ext


	//$("#carcode").val('');			// �����ڵ�
	$("#cargrade").val('');				// �������
	$("#baegicc").val('');				// ��ⷮ
	$("#carname").val('');				// ����
	$("#car_kind").val('');				// ����
	
	$("#carprice1").val(0);				// ��������
	$("#totalamt").val(0);				// �����հ�
	$("#fuel").val('');					// ��������
	$("#hi_repair").val('');			// ��������
	$("#people_numcc").val('');			// ž���ο�&��


	// �� �� �ʱ�ȭ
	$("#add_bupum").val('');			// �߰��μ�arr
	$("#add_bupum_amt").val('');		// �߰��μӱݾ�arr
	$("#add_bupum_txt").val('');		// �߰��μ��ؽ�Ʈ
	$("#addamt").val(0);				// �μ�ǰ����	
}

// �����ڵ� �˻�
function Carcode_ser(){

	var carcode	= $("#carcode").val();
	var caryear	= $("#caryear").val();
	var cardate	= $("#cardate").val();

	if(isEmpty(carcode) == true){
		alert('�����ڵ带 �Է��� �ּ���.');
		document.getElementById('carcode').focus();
		return;
	}else if(isEmpty(caryear) == true){
		alert('���������� �Է��� �ּ���.');
		document.getElementById('caryear').focus();
		return;
	}else if(isEmpty(cardate) == true){
		alert('����������� �Է��� �ּ���.');
		document.getElementById('cardate').focus();
		return;
	}

	// �ʱ�ȭ
	car_Reset();

	$.ajaxSetup({ cache : false, dataType : "json", data: { ajaxType : true}, contentType:'application/x-www-form-urlencoded; charset=euc-kr'});	
	$.post("/bin/sub/menu6/ga_menu6_01_cardata_select.php",{carcode:carcode, caryear:caryear, cardate:cardate}, function(data) {

		console.log(data);

		var car_part	= data.car_part;
		var gubun		= data.gubun;
		var	sport		= data.sport;

		if(data.fracc == 'Y'){
			car_part = car_part + ',�����浹����';
		}

		if(data.lineout == 'Y'){
			car_part = car_part + ',������Ż����';
		}

		if(data.connectcar == 'Y'){
			car_part = car_part + ',Ŀ��Ƽ��ī';
		}


		// �ӽ������϶� Ư������ ����
		if(gubun != 'D'){
			var carext = ext_chng(car_part,sport);
		}

		var ext_bupum_i		= $("#ext_bupum").val();
		var ext_bupum_txt_i	= $("#ext_bupum_txt").val();

		if(ext_bupum_i){
			carext		= ext_bupum_i + '-' + carext;
			car_part	= ext_bupum_txt_i + ',' + car_part;
		}

		$("#ext_bupum_txt").val(car_part);	// Ư������txt
		$("#ext_bupum").val(carext);		// Ư������ext


		$("#carcode").val(data.car_code);		// �����ڵ�
		$("#cargrade").val(data.car_grade);		// �������
		$("#baegicc").val(data.bae_gi);			// ��ⷮ
		$("#carname").val(data.car_sub);		// ����
		$("#car_kind").val(data.hyoung_sik);	// ����
		
		$("#carprice1").val(comma(data.amt));	// ��������
		$("#totalamt").val(comma(data.amt));	// �����հ�
		$("#fuel").val(data.fuel);				// ��������
		$("#hi_repair").val(data.hi_repair);	// ��������
		$("#people_numcc").val(data.people_num);// ž���ο�&��


		// �� �� �ʱ�ȭ
		$("#add_bupum").val('');			// �߰��μ�arr
		$("#add_bupum_amt").val('');		// �߰��μӱݾ�arr
		$("#add_bupum_txt").val('');		// �߰��μ��ؽ�Ʈ
		$("#addamt").val(0);				// �μ�ǰ����

		if(gubun == "A" || gubun == "C" || gubun == "D"){
			$(".car_visible").css("display","");
			$(".car_visible2").css("display","none");
			
		}else if(gubun == "B"){
			$(".car_visible").css("display","none");
			$(".car_visible2").css("display","");
		}

	});		
}


// �񱳰��� ���� ��ȸ
function yoyul_send(){	

	var scode		= '<?=$_SESSION['S_SCODE']?>';
	var pname		= $("#pname").val();
	var jumin		= $("#jumin").val();
	var carnumber	= $("#carnumber").val();
	var caruse		= $("#caruse").val();
	var carseq		= $("#carseq").val();
	var returl		= 'www.gaplus.net:452/bin/sub/menu6/ga_menu6_01_ret_url_yoyul.php?scode='+scode+'&carseq='+carseq;

	if(isEmpty(pname) == true){
		alert('�Ǻ����ڸ��� �Է��� �ּ���.');
		document.getElementById('pname').focus();
	}else if(isEmpty(jumin) == true){
		alert('�Ǻ����ڹ�ȣ�� �Է��� �ּ���.');
		document.getElementById('jumin').focus();
	}else if((caruse == '1' || caruse == '2') && jumin.length != 13){
		alert('���ο��� ��� �Ǻ����� ��ȣ�� 13�ڸ� �Է����ֽñ� �ٶ��ϴ�.');
		document.getElementById('jumin').focus();
	}else if(caruse == '3'  && jumin.length != 10){
		alert('������ ��� �Ǻ����� ��ȣ�� 10�ڸ� ����ڹ�ȣ�Դϴ�.');
		document.getElementById('jumin').focus();
	}else if(isEmpty(carnumber) == true){
		alert('������ȣ�� �Է��� �ּ���.');
		document.getElementById('carnumber').focus();
	}else{
		
		$("#div_load_image").show();
		
		$("#form_jumin").val(jumin);
		$("#form_carnumber").val(carnumber);
		$("#form_caruse").val(caruse);
		$("#form_ret_url").val(returl);

		$("form[name='yoyulform']").submit();
		
	}
}



// �޽����� �����ϴ� �̺�Ʈ ������
window.addEventListener('message', function(event) {
	console.log(event);

	const expectedOrigin = 'https://gaplus.net:452';
	const eventOrigin = event.origin.replace('www.', '');

	// �̺�Ʈ�� ��ó�� Ȯ��(�������������� origin ���� ���� ��ġ Ȯ��)
	if (eventOrigin !== expectedOrigin) {
		// ��ó�� ����� ���� �ƴϸ� �޽����� �����մϴ�.
		return;
	}

	// ���� �����͸� ó���մϴ�.
	const message = event.data;
	if (typeof message === 'object' && message.functionName === 'fn_yoyul_end') {			// �������
		fn_yoyul_end(message.data);
	}else if (typeof message === 'object' && message.functionName === 'fn_caramt_end') {	// ��������
		fn_caramt_end(message.data)
	}
});


// �������ó�� �� ���������� ���
function fn_yoyul_end(arr_data){
	
	$("#div_load_image").hide();

	//console.log(arr_data);

	//const arr = JSON.parse(arr_data);
	const arr = arr_data;

	//console.log(arr);

	var guipcarrer	=	arr.guipcarrer;
	var traffic		=	arr.traffic;
	var halin		=	arr.halin;
	var special_code=	arr.special_code;
	var special_code1=	arr.special_code1;
	var ss_point	=	arr.ss_point;
	var ncr_code	=	arr.ncr_code;
	var ext_bupum	=	arr.ext_bupum;
	var ext_bupum_txt=	arr.ext_bupum_txt;
	var fdate		=	arr.fdate;
	var tdate		=	arr.tdate;
	var idateyn		=	arr.idateyn;

	fdate	=	fdate.substr(0,4)+'-'+fdate.substr(4,2)+'-'+fdate.substr(6,2);
	tdate	=	tdate.substr(0,4)+'-'+tdate.substr(4,2)+'-'+tdate.substr(6,2);

	var ext_bupum_i		= $("#ext_bupum").val();
	var ext_bupum_txt_i	= $("#ext_bupum_txt").val();

	if(ext_bupum_i){
		ext_bupum		= ext_bupum_i + '-' + ext_bupum;
		ext_bupum_txt	= ext_bupum_txt_i + ',' + ext_bupum_txt;
	}


	$("#guipcarrer").val(guipcarrer);
	$("#traffic").val(traffic);
	$("#halin").val(halin);
	$("#special_code").val(special_code);
	$("#special_code1").val(special_code1);
	$("#ncr_code").val(ncr_code);
	$("#ss_point").val(ss_point);
	$("#ext_bupum").val(ext_bupum);
	$("#ext_bupum_txt").val(ext_bupum_txt);
	$("#fdate").val(fdate);
	$("#tdate").val(tdate);

	if(idateyn == 'Y'){
		alert('����Ⱓ�� ���� �����Ǿ����ϴ�.');
	}
	
}

// ����� ���� �� ����Ʈ ��ȸ
function fn_caramt_end(carseq){
	$("#div_load_image").hide();

	ajaxLodingTarket('ga_menu6_01_caramt_list.php',$('#caramtins'),'&carseq='+carseq);
}


// �񱳰��� �ݾ� ����
function caramt_send(){

	var scode		= '<?=$_SESSION['S_SCODE']?>';
	var carseq		= $("#carseq").val();
	var returl		= 'www.gaplus.net:452/bin/sub/menu6/ga_menu6_01_ret_url_caramt.php?scode='+scode+'&carseq='+carseq;

	var pname		= $("#pname").val();
	var carnumber	= $("#carnumber").val();

	var caruse		= $("#caruse").val();	
	var jumin		= $("#jumin").val();
	var juminfull	= jumin;
	// �����ϰ�� 7�ڸ�
	if(caruse == '1'){
		jumin = jumin.substr(0,7);
	}
	var idate		= $("#fdate").val();
	var idate_to	= $("#tdate").val();
	idate	= idate.replace(/-/gi, "");
	idate_to= idate_to.replace(/-/gi, "");

	var carcode		= $("#carcode").val();
	var cargrade	= $("#cargrade").val();
	var baegicc		= $("#baegicc").val();
	var caryear		= $("#caryear").val();
	var car_kind	= $("#car_kind").val();
	var people_numcc	= $("#people_numcc").val();
	var ext_bupum	= $("#ext_bupum").val();
	var add_bupum	= $("#add_bupum").val();
	var add_bupumprice	= uncomma($("#addamt").val());
	var carprice1	= uncomma($("#carprice1").val());
	var carprice	= uncomma($("#carprice").val());
	var carname		= $("#carname").val();

	var guipcarrer	= $("#guipcarrer").val();
	var traffic		= $("#traffic").val();
	var halin		= $("#halin").val();
	var special_code	= $("#special_code").val();
	var special_code1	= $("#special_code1").val();
	var ncr_code	= $("#ncr_code").val();
	var ncr_code2	= $("#ncr_code2").val();
	var ss_point	= $("#ss_point").val();
	var ss_point3	= $("#ss_point3").val();
	var car_guip	= $("#car_guip").val();
	var car_own		= $("#car_own").val();
	var buy_type	= $("#buy_type").val();

	var eco_mileage	= $("#eco_mileage").val();
	var tmap_halin	= $("#tmap_halin").val();
	if(!tmap_halin){
		tmap_halin = 0;
	}
	var car_own_halin	= $("#car_own_halin").val();
	var careercode3	= $("#careercode3").val();
	var lawcodecnt	= $("#lawcodecnt").val();
	var lowestJumin	= $("#lowestJumin").val();
	var jjumin		= $("#jjumin").val();
	var c_jumin		= $("#c_jumin").val();
	var ijumin		= $("#ijumin").val();
	if($('input:checkbox[id="fetus"]').is(":checked") == true){
		var fetus		= "1";
	}
	var child_cnt	= $("#icnt").val();

	var divide_num	= $("#divide_num").val();
	var dambo2		= $("#dambo2").val();
	var dambo3		= $("#dambo3").val();
	var dambo4		= $("#dambo4").val();
	var dambo5		= $("#dambo5").val();
	var dambo6		= $("#dambo6").val();
	var goout1		= $("#goout").val();
	var carfamily	= $("#carfamily").val();
	var carage		= $("#carage").val();
	var muljuk		= $("#muljuk").val();
	var MileGbn		= $("#MileGbn").val();
	var MileKm		= $("#MileKm").val();
	var fuel		= $("#fuel").val();
	var hi_repair	= $("#hi_repair").val();
	var religionchk	= $("#religionchk").val();
	var otheracc	= $("#otheracc").val();
	var j_kind		= $("#j_kind").val();
	var cardate		= $("#cardate").val();
	cardate	= cardate.replace(/-/gi, "");

	if(isEmpty(pname) == true){
		alert('�Ǻ����ڸ��� �Է��� �ּ���.');
		document.getElementById('pname').focus();
	}else if(isEmpty(jumin) == true){
		alert('�Ǻ����ڹ�ȣ�� �Է��� �ּ���.');
		document.getElementById('jumin').focus();
	}else if(isEmpty(carnumber) == true){
		alert('������ȣ�� �Է��� �ּ���.');
		document.getElementById('carnumber').focus();
	}else if(isEmpty(carcode) == true){
		alert('�����ڵ带 �Է��� �ּ���.');
		document.getElementById('carcode').focus();
	}else if(carcode.length != 5){
		alert('�����ڵ�� 5�ڸ��� �ԷµǾ���մϴ�.');
		document.getElementById('carcode').focus();
	}else if(isEmpty(caryear) == true){
		alert('������ �Է��� �ּ���.');
		document.getElementById('caryear').focus();
	}else if(isEmpty(cardate) == true){
		alert('����������� �Է��� �ּ���.');
		document.getElementById('cardate').focus();
	}else if(isEmpty(car_kind) == true){
		alert('������ ������ �ּ���.');
		document.getElementById('car_kind').focus();
	}else if(isEmpty(carname) == true){
		alert('������ �Է��� �ּ���.');
		document.getElementById('carname').focus();
	}else if(carprice1 < 1){
		alert('���������� �Է��� �ּ���.');
		document.getElementById('carprice1').focus();
	}else if(totalamt < 1){
		alert('�����հ�ݾ��� �Է��� �ּ���.');
		document.getElementById('totalamt').focus();
	}else if(carfamily == '20' && c_jumin.length != 7){
		alert('�κ������� ��� ����ڹ�ȣ�� �ʼ��Դϴ�.');
		document.getElementById('c_jumin').focus();
	}else if(carfamily == '14' && lowestjumin.length != 7){
		alert('�ڳ������� ��� ���������ڹ�ȣ�� �ʼ��Դϴ�.');
		document.getElementById('lowestjumin').focus();
	}else{

		// �ε���
		$("#div_load_image").show();

		$("#car_caruse").val(caruse);
		$("#car_jumin").val(jumin);
		$("#car_idate").val(idate);
		$("#car_idate_to").val(idate_to);
		$("#car_carcode").val(carcode);
		$("#car_cargrade").val(cargrade);
		$("#car_baegicc").val(baegicc);
		$("#car_caryear").val(caryear);
		$("#car_car_kind").val(car_kind);
		$("#car_people_numcc").val(people_numcc);
		$("#car_ext_bupum").val(ext_bupum);
		$("#car_add_bupum").val(add_bupum);
		$("#car_add_bupumprice").val(add_bupumprice);
		$("#car_carprice1").val(carprice1);
		$("#car_carprice").val(carprice);
		$("#car_carname").val(carname);

		$("#car_guipcarrer").val(guipcarrer);
		$("#car_traffic").val(traffic);
		$("#car_halin").val(halin);
		$("#car_special_code").val(special_code);
		$("#car_special_code1").val(special_code1);
		$("#car_ncr_code").val(ncr_code);
		$("#car_ncr_code2").val(ncr_code2);
		$("#car_ss_point").val(ss_point);
		$("#car_ss_point3").val(ss_point3);
		$("#car_car_guip").val(car_guip);
		$("#car_car_own").val(car_own);
		$("#car_buy_type").val(buy_type);

		$("#car_eco_mileage").val(eco_mileage);
		$("#car_tmap_halin").val(tmap_halin);
		$("#car_car_own_halin").val(car_own_halin);
		$("#car_careercode3").val(careercode3);
		$("#car_lawcodecnt").val(lawcodecnt);
		$("#car_lowestJumin").val(lowestJumin);
		$("#car_jjumin").val(jjumin);
		$("#car_c_jumin").val(c_jumin);
		$("#car_ijumin").val(ijumin);
		$("#car_fetus").val(fetus);
		$("#car_child_cnt").val(child_cnt);

		$("#car_divide_num").val(divide_num);
		$("#car_dambo2").val(dambo2);
		$("#car_dambo3").val(dambo3);
		$("#car_dambo4").val(dambo4);
		$("#car_dambo5").val(dambo5);
		$("#car_dambo6").val(dambo6);
		$("#car_goout1").val(goout1);
		$("#car_carfamily").val(carfamily);
		$("#car_carage").val(carage);
		$("#car_muljuk").val(muljuk);
		$("#car_MileGbn").val(MileGbn);
		$("#car_MileKm").val(MileKm);
		$("#car_fuel").val(fuel);
		$("#car_hi_repair").val(hi_repair);
		$("#car_religionchk").val(religionchk);
		$("#car_otheracc").val(otheracc);
		$("#car_j_kind").val(j_kind);
		$("#car_cardate").val(cardate);
		$("#car_ret_url").val(returl);
		
		$("form[name='caramtform']").submit();
	}
}


//  ���ڸ� �Է°���
function NumberOnInput(e)  {
  e.value = e.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')
}


// ��������
function selectcar_pop(gubun){

	/* 
		gubun : A-�¿�, B:����/ȭ��, C:������, D:�ӽ�
		(1)���������� ���ο��� ��� �������� �� �¿��� �˾�����
		(2)���������� ������(����) ��� �������� �� ȭ��/������ �˾�����
		(3)���������� ������(����) ��� �������� �� �¿��� & ȭ��/������ ������ �� ����ǵ���
	*/

	var caruse   = $("form[name='dataform'] select[name='caruse']").val();
	var fdate	 = $("form[name='dataform'] input[name='idate']").val();

	if(gubun == 'A' && caruse == '2'){
		alert('������(����)�� �¿����� ��ȸ�Ͻ� �� �����ϴ�');
		return
	}else if(gubun == 'B' && caruse == '1'){
		alert('���ο��� ����/ȭ������ ��ȸ�Ͻ� �� �����ϴ�');
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


// �������� �� ������ �Է�
function setCarValue(gubun, car_code, car_grade, bae_gi, caryeartxt, cardate, hyoung_sik, car_sub, amt, fuel, hi_repair, car_part, people_num, sport){

	// gubun : A-�¿�, B:����/ȭ��, C:������, D:�ӽ�

	if(cardate){
		var yy = cardate.substr(0,4);
		var mm = cardate.substr(4,2);
		var dd = cardate.substr(6,2);

		var date = yy+'-'+mm+'-'+dd;
	}

	// �ӽ������϶� Ư������ ����
	if(gubun != 'D'){
		var carext = ext_chng(car_part,sport);
	}

	$("#carcode").val(car_code);		// �����ڵ�
	$("#cargrade").val(car_grade);		// �������
	$("#baegicc").val(bae_gi);			// ��ⷮ
	$("#caryear").val(caryeartxt);		// ����
	$("#cardate").val(date);			// ���������
	$("#carname").val(car_sub);			// ����
	$("#car_kind").val(hyoung_sik);		// ����
	
	$("#carprice1").val(comma(amt));	// ��������
	$("#totalamt").val(comma(amt));		// �����հ�
	$("#fuel").val(fuel);				// ��������
	$("#hi_repair").val(hi_repair);		// ��������

	var ext_bupum_i		= $("#ext_bupum").val();
	var ext_bupum_txt_i	= $("#ext_bupum_txt").val();

	if(ext_bupum_i){
		carext		= ext_bupum_i + '-' + carext;
		car_part	= ext_bupum_txt_i + ',' + car_part;
	}

	$("#ext_bupum_txt").val(car_part);	// Ư������txt
	$("#ext_bupum").val(carext);		// Ư������ext

	// �� �� �ʱ�ȭ
	$("#add_bupum").val('');			// �߰��μ�arr
	$("#add_bupum_amt").val('');		// �߰��μӱݾ�arr
	$("#add_bupum_txt").val('');		// �߰��μ��ؽ�Ʈ
	$("#addamt").val(0);				// �μ�ǰ����

	$("#people_numcc").val(people_num);	// ž���ο�&��

	if(gubun == "A" || gubun == "C" || gubun == "D"){
		$(".car_visible").css("display","");
		$(".car_visible2").css("display","none");
		
	}else if(gubun == "B"){
		$(".car_visible").css("display","none");
		$(".car_visible2").css("display","");
	}

}


// ���� Ư������ �� �μ�ǰ �˾�
function car_busok(gubun){

	var extdata	 = $("form[name='dataform'] input[name='ext_bupum']").val();
	var adddata	 = $("form[name='dataform'] input[name='add_bupum']").val();
	var carseq	 = $("form[name='dataform'] input[name='carseq']").val();
	//carseq = '240430000001';	// �ӽü���

	// gubun(A:Ư������ / B:�μ�ǰ)
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


// �μ�ǰ �˾������� ��������
function setCarBusok(gubun, data, text, amt, toaladdamt){
	if(gubun == 'A'){	// Ư������
		$("#ext_bupum").val(data);
		$("#ext_bupum_txt").val(text);
	}else{				// �߰��μ�ǰ
		$("#add_bupum").val(data);
		$("#add_bupum_txt").val(text);	
		$("#add_bupum_amt").val(amt);			// �μ�ǰ�հ�(�迭)
		$("#addamt").val(comma(toaladdamt));	// �μ�ǰ����

		var totalamt = $("#totalamt").val();
		totalamt = parseInt(uncomma(totalamt)) + parseInt(toaladdamt);
		$("#totalamt").val(comma(totalamt));	// �����հ�
	}
}


// �������� �˾�
function traffic_pop(){
	var left = Math.ceil((window.screen.width - 500)/2);
	var top = Math.ceil((window.screen.height - 800)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/car_traffic_search.php" ,"carlaw","width=500px,height=700px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();	
}



// �������� �˾�
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


// �űԺ񱳰���
function car_new(){
	location.href='ga_menu6_01.php?type=in';
}


// ����
function doummalPopOpen(index,openwidth,openheight){

	var left = Math.ceil((window.screen.width - openwidth)/2);
	var top = Math.ceil((window.screen.height - (openheight+100))/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/doummal/doummal_"+index+".php","","width="+openwidth+"px,height="+openheight+"px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}


// �������� ������ �������� ��������
function fn_caruse_chng(val){

	fn_carage_chng(val);
	fn_carfamily_chng(val);
}


// �������� ������ �������� ��������
function fn_carage_chng(val){

	$.ajaxSetup({ async:false });	
	$.post("/bin/sub/menu6/ga_menu6_01_carage_option.php",{optVal:val}, function(data) {

		$('#carage').empty();
		$('#carage').append(data);
	});	

	if('<?=$type?>' == 'up'){
		$("#carage").val('<?=$carage?>');
	}
}


// �������� ������ ���������� ��������
function fn_carfamily_chng(val){

	$.ajaxSetup({ async:false });	
	$.post("/bin/sub/menu6/ga_menu6_01_carfamily_option.php",{optVal:val}, function(data) {

		$('#carfamily').empty();
		$('#carfamily').append(data);
	});	

	if('<?=$type?>' == 'up'){
		$("#carfamily").val('<?=$carfamily?>');
	}
}


// ���������� ���
$("#fdate").change( function() {
	ajax_idateto("fdate="+$("#fdate").val());
});

// ���������� ��� (+1��)
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


// �񱳰��� ����
/*
	gubun => A �� ��� ���������� ��ȸ���� ����������
	gubun => B �� ��� �񱳰��� ���� ��ȸ���� ����������
	gubun => C �� ��� �񱳰��� �������� ����
*/
function carest_update(gubun){

	document.dataform.savegubun.value = gubun;

	if(gubun == 'A' || gubun == 'B'){
		$("form[name='dataform']").submit();
				
	}else{
		if(confirm("�����Ͻðڽ��ϱ�?")){
			$("form[name='dataform']").submit();
		}	
	}
}


// �񱳰��� ����
function carest_delete(){
	var type   = $("form[name='dataform'] input[name='type']").val();

	if(type == "up"){
		if(confirm("�����Ͻðڽ��ϱ�?")){
			document.dataform.type.value='del';
			$("form[name='dataform']").submit();
		}
	}else{
		alert("������ ����� �����ϴ�.");
	}
}



// ������� �� ����� ��� �˾�(A:�������/B:�������)
function InsSwonSearch(){
	
	var left = Math.ceil((window.screen.width - 800)/2);
	var top = Math.ceil((window.screen.height - 800)/2);
	if('<?=$_GET["jisa"]?>'=='Y'){
		var popOpen	= window.open("<?=$conf['homeDir']?>/subjisa/help/ga_insswon_search.php?inscode=00018","swonInspop","width=600px,height=800px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	}else{
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/ga_insswon_search.php?inscode=00018","swonInspop","width=600px,height=800px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	}
	popOpen.focus();
}

function setInsSwonValue(row,code,name,sosok){
	// �űԵ�Ͻ� ����� ���� �Ҽ����� ������
	$("#kdman").val(code);
	$('.kdname').text(name);
	
}



// �������
function print_caramt(){

	// ����� üũ�ڽ� ���õȰ� �迭���
	const checkboxes = document.querySelectorAll('.inscheck:checked');
	const arr_ins = Array.from(checkboxes).map(checkbox => checkbox.value);
	//console.log(arr_ins.length);

	if(arr_ins.length == 0){
		alert("1�� �̻��� ����縦 �����ϼž��մϴ�.");
		return;
	}

	var data = JSON.stringify(arr_ins);

	var carseq	= $("#carseq").val();

	if(!carseq){
		alert('����� ���� �� ����� �����մϴ�.')
	}else{
		var left = Math.ceil((window.screen.width - 1000)/2);
		var top = Math.ceil((window.screen.height - 900)/2);
		var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu6/ga_menu6_01_print.php?carseq="+carseq+"&data="+data ,"printcar","width=1000px,height=800px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
		popOpen.focus();	
	}
}


function smspop(){
	var sdate1 = '';
	var sdate2 = '';
	var where = '<?=$where?>';
	var cnt = '1';
	var sms_type = 'sms_car_gun';

	if('<?=$type?>' != 'up'){
		alert("������ �����Ͱ� �����ϴ�.");
		return false;
	}	
	if('<?=$smsyn?>' != 'Y'){
		alert("�������� �޴���ȭ��ȣ�� �����ϼ���.");
		return false;
	}
	if('<?=$smscnt?>' < 1){
		alert("�������� ���� �� SMS�� ������ �� �ֽ��ϴ�.");
		return false;
	}

	var left = Math.ceil((window.screen.width - 750)/2);
	var top = Math.ceil((window.screen.height - 450)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/sms_pop.php?sdate1="+sdate1+"&sdate2="+sdate2+"&where="+where+"&cnt="+cnt+"&sms_type="+sms_type,"smskwngo3","width=750px,height=450px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

function kakaopop(){
	var sdate1 = '';
	var sdate2 = '';
	var where = '<?=$where?>';
	var cnt = '1';
	var sms_type = 'sms_car_gun';

	if('<?=$type?>' != 'up'){
		alert("������ �����Ͱ� �����ϴ�.");
		return false;
	}	
	if('<?=$smsyn?>' != 'Y'){
		alert("�������� �޴���ȭ��ȣ�� �����ϼ���.");
		return false;
	}
	if('<?=$smscnt?>' < 1){
		alert("�������� ���� �� SMS�� ������ �� �ֽ��ϴ�.");
		return false;
	}

	var left = Math.ceil((window.screen.width - 500)/2);
	var top = Math.ceil((window.screen.height - 580)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/help/kakao_pop.php?sdate1="+sdate1+"&sdate2="+sdate2+"&where="+where+"&cnt="+cnt+"&sms_type="+sms_type,"kakaokwngo3","width=500px,height=580px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}

$(document).ready(function(){	

	$("#div_load_image").hide();

	ajaxLodingTarket('ga_menu6_01_caramt_list.php',$('#caramtins'),'&carseq=<?=$carseq?>');

	var val		= $("form[name='dataform'] select[name='caruse']").val();
	fn_carage_chng(val);
	fn_carfamily_chng(val);

	// �ű��϶� �⺻�� ����
	if('<?=$type?>' == 'in'){
		// ����Ʈ�� ����
		$("#dambo4").val("00");	//��ü����
		$("#goout").val("1");	// ����⵿
		$("#muljuk").val("4");	// ��������
		$("#dambo2").val("2");	// �㺸(����II)
		$("#dambo3").val("90");	// �㺸(�빰���)
		$("#dambo4").val("30");	// �㺸(��ü����)
		$("#dambo5").val("1");	// �㺸(��������)
		$("#dambo6").val("94");	// �㺸(��������)
		$("#carfamily").val("30");	// �㺸(����������)
		
	}else{
		// ��ȸ�� �񱳰������⸮��Ʈ open
		$("#caramt").css("display","");
		//$("#carage").val('<?=$carage?>');
		//$("#carfamily").val('<?=$carfamily?>');

		// ��ȸ�� SMS��ư Ȱ��ȭ
		$("#smsbtn").css("display","");
		$("#kakaobtn").css("display","");
	}

     
	//window.parent.postMessage("�ڵ������� > �񱳰������", "*");   // '*' on any domain �θ�� ������..   

	//ajaxLodingTarket('ga_menu6_01_yoyul_form.php',$('.yoyulBody'),'&val='+val);


});

// �񱳰��� ������� ����
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
		if(data.savegubun == 'C'){	// �������
			alert(data.message);
		}
	}

	// ������
	if(data.result==''){

		if(data.rtype == 'in'){
			document.dataform.type.value = 'up';
			document.dataform.carseq.value = data.carseq;
		}else if(data.rtype == 'up'){
			document.dataform.type.value = 'up';

		}else if(data.rtype == 'del'){		
			alert(data.message);
			car_new();
		}
		
		// ������ȸ
		if(data.savegubun == 'A'){
			yoyul_send();
		}

		// �񱳰�������
		if(data.savegubun == 'B'){
			$("#caramt").css("display","");
			caramt_send();
		}
	}

}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>