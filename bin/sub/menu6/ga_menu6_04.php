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


// ��ü�����
$sql= "select inscode code, name, gubun from inssetup where scode = '".$_SESSION['S_SCODE']."' and useyn = 'Y' order by name";
$qry= sqlsrv_query( $mscon, $sql );
$instot	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $instot[] = $fet;
}


// �������
$sql= "select inscode code, name, gubun from inssetup where scode = '".$_SESSION['S_SCODE']."' and gubun = '1' and useyn = 'Y' order by name";
$qry= sqlsrv_query( $mscon, $sql );
$insg1	= array();
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
  $insg1[] = $fet;
}


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
					<a class="btn_s white" style="min-width:100px;margin-left:5px" onclick="car_new();">�ű�</a>
					<a class="btn_s white" style="min-width:100px;" onclick="carest_update('C');">����</a>
					<a class="btn_s white" style="min-width:100px;" onclick="carest_delete();">����</a>
					<a class="btn_s white" style="min-width:100px;" onclick="carest_close();">�ݱ�</a>
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
									<!--<span class="input_type" style="width:250px"><input type="text" value="<?=trim($jumin)?>" id="jumin" name="jumin" maxlength="13"  oninput="NumberOnInput(this)"></span>-->
									<span class="input_type" style="width:250px"><input type="text" value="8510041659511" id="jumin" name="jumin" maxlength="13"  oninput="NumberOnInput(this)"></span>
								</td>
							</tr>
							<tr>
								<th><em class="impor">*</em>������ȣ</th>
								<td>
									<!--<span class="input_type" style="width:250px"><input type="text" name="carnumber" id="carnumber" value="<?=$carnumber?>"></span> -->
									<span class="input_type" style="width:250px"><input type="text" name="carnumber" id="carnumber" value="11��8141"></span> 
								</td>
								<th><em class="impor">*</em>����Ⱓ</th>
								<td>
									<span class="input_type date" style="width:116px"><input type="text" class="Calnew" name="idate" id="fdate" value="<?if($fdate) echo date("Y-m-d",strtotime($fdate)); elseif(!$fdate) echo '';?>" readonly></span>
									<span style="width:13px;display:inline-block;text-align:center;">~</span>
									<span class="input_type date" style="width:116px"><input type="text" class="Calnew" name="idate_to" id="tdate" value="<?if($tdate) echo date("Y-m-d",strtotime($tdate)); elseif(!$tdate) echo '';?>" readonly></span>
								</td>
								<th><em class="impor">*</em>�������</th>
								<td>
									<span class="input_type" style="width:120px"><input type="text" name="kdman" id="kdman" value="4LU910"></span>
									<a href="javascript:InsSwonSearch('B');" class="btn_s white" style="height:24px;line-height:22px;">�˻�</a>
									<span class="kdname" style="width:100px;margin-left:5px"><?=trim($kdname)?></span>
								</td>
							</tr>
						</tbody>
					</table> <!-- �Ǻ����� ���� End -->

					<!-- �������� ���� -->
					<div class="menu_group_top">
						<div class="menu_group">
							<span>�������� ����</span>
							<a class="btn_s white mgl20" onclick="yoyul_send();"><i class="fa-solid fa-calculator fa-lg mgr3"></i>�������</a>
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
									<select name="guipcarrer" id="guipcarrer" style="width:250px"> 
										<?foreach($conf['guipcarrer'] as $key => $val){?>
										<option value="<?=$key?>" <?if($guipcarrer==$key) echo "selected"?>><?=$val?></option>
										<?}?>
									</select>	
								</td>
								<th>�������� �� Ƚ��</th>
								<td>
									<select name="traffic" id="traffic" style="width:70px;height:24px;line-height:22px;">			
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
									<select name="halin" id="halin" style="width:250px">			
									  <?foreach($selcarhalin as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($halin==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
								</td>
							</tr>
							<tr>
								<th>Ư������</th>
								<td>
									<select name="special_code" id="special_code" style="width:63px;height:24px;line-height:22px;">			
									  <?foreach($selcarspecial as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($special_code==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
									<a href="javascript:;" class="btn_s white" style="height:24px;line-height:22px;" onclick="specialCode_pop('A');">�˻�</a>
									<span style="width:14px;display:inline-block;text-align:center;">/</span>
									<select name="special_code1" id="special_code1" style="width:63px;height:24px;line-height:22px;">			
									  <?foreach($selcarspecial as $key => $val){?>
									  <option value="<?=$val['code']?>" <?if($special_code1==$val['code']) echo "selected"?>><?=$val['name']?></option>
									  <?}?>
									</select>	
									<a href="javascript:;" class="btn_s white" style="height:24px;line-height:22px;" onclick="specialCode_pop('B');">�˻�</a>
								</td>
								<th>3�Ⱓ������</th>
								<td>
									<select name="ncr_code" id="ncr_code" style="width:250px"> 
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
									<select name="ss_point" id="ss_point" style="width:250px"> 
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
									<select name="ohteracc" id="ohteracc" style="width:250px"> 
										<?foreach($conf['ohteracc'] as $key => $val){?>
										<option value="<?=$key?>" <?if($ohteracc==$key) echo "selected"?>><?=$val?></option>
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
								<td colspan=3>
									<span class="input_type" style="width:250px"><input type="text" name="carname" id="carname" value="<?=$carname?>"></span> 
								</td>
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
									<span class="input_type_number" style="width:93px"><input type="text" name="totalamt" id="totalamt" class="numberInput yb_right"value="<?=number_format($totalamt)?>"></span> 
									<span>����</span>
									<span style="width:14px;display:inline-block;text-align:center;">/</span>
									<span>�Ϻ�</span>
									<span class="input_type_number" style="width:93px"><input type="text" name="carprice" id="carprice" class="numberInput yb_right"value="<?=number_format($carprice)?>"></span> 
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
									<span class="input_type" style="width:250px"><input type="text" name="jjumin" id="jjumin" value="<?=$jjumin?>" oninput="NumberOnInput(this)" placeholder="�������(YYMMDD)+����(1)"></span> 
								</td>
								<th>���������ڹ�ȣ</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="lowestjumin" id="lowestjumin" value="<?=$lowestjumin?>" oninput="NumberOnInput(this)" placeholder="�������(YYMMDD)+����(1)"></span> 
								</td>
								<th>����ڹ�ȣ</th>
								<td>
									<span class="input_type" style="width:250px"><input type="text" name="c_jumin" id="c_jumin" value="<?=$c_jumin?>" oninput="NumberOnInput(this)" placeholder="�������(YYMMDD)+����(1)"></span> 
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
									<select name="divide_num" id="divide_num" style="width:250px"> 
										<option value="">����</option>
										<?foreach($conf['divide_num'] as $key => $val){?>
										<option value="<?=$key?>" <?if($divide_num==$key) echo "selected"?>><?=$val?></option>
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
							<a class="btn_s white mgl20" onclick="carest_inssend();"><i class="fa-solid fa-arrows-rotate fa-lg mgr3"></i>��������</a>
							<a class="btn_s white" onclick=""><i class="fa-solid fa-print fa-lg mgr3"></i>�������</a>
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
									<th>�����</th>
									<th>�Ѻ����</th>
									<th>����I</th>
									<th>����II</th>
									<th>�빰���</th>
									<th>��ü����</th>
									<th>��������</th>
									<th>��������</th>
									<th>����⵿</th>
									<th>��������</th>
									<th>����������</th>
									<th>�޽���</th>
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
									<td align="left">�޽���Text</td>
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
									<td align="left">�޽���Text</td>
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
									<td align="left">�޽���Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">�Ե�</td>
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
									<td align="left">�޽���Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">�޸���</td>
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
									<td align="left">�޽���Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">�Ｚ</td>
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
									<td align="left">�޽���Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">��ȭ</td>
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
									<td align="left">�޽���Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">����</td>
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
									<td align="left">�޽���Text</td>
								</tr>
								<tr class="">
									<td><input type="checkbox"></td>
									<td align="center">�ﱹ</td>
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
									<td align="left">�޽���Text</td>
								</tr>
							</tbody>
						</table>
					</div>

				</form>
			</div><!-- // tb_type01 -->


			<!-- ������� �� -->
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


// �����˾�
function yoyul_PopOpen(){

	//var jumin		= document.getElementById("jumin");
	//alert(jumin);

	var left = Math.ceil((window.screen.width - 500)/2);
	var top = Math.ceil((window.screen.height - 400)/2);
	var popOpen	= window.open("<?=$conf['homeDir']?>/sub/menu6/ga_menu6_01_yoyul_pop.php","yoyulpop","width=500px,height=300px,top="+top+",left="+left+",status=0,toolbar=0,menubar=0,location=false,scrollbars=yes");
	popOpen.focus();
}


// �񱳰��� ����
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
	var fdate	 = $("form[name='dataform'] input[name='fdate']").val();

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

	$("#ext_bupum_txt").val(car_part);	// Ư������txt
	$("#ext_bupum").val(carext);		// Ư������ext

	// �� �� �ʱ�ȭ
	$("#add_bupum").val('');			// Ư������
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
	carseq = '240430000001';	// �ӽü���

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


function setCarlaw(code){
	$("#traffic").val(code);
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

	$.post("/bin/sub/menu6/ga_menu6_01_carage_option.php",{optVal:val}, function(data) {

		$('#carage').empty();
		$('#carage').append(data);
	});	
}


// �������� ������ ���������� ��������
function fn_carfamily_chng(val){

	$.post("/bin/sub/menu6/ga_menu6_01_carfamily_option.php",{optVal:val}, function(data) {

		$('#carfamily').empty();
		$('#carfamily').append(data);
	});	
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


$(document).ready(function(){	

	$("#div_load_image").hide();

	var val		= $("form[name='dataform'] select[name='caruse']").val();
	var val2	= $("form[name='dataform'] select[name='carfamily']").val();
	fn_carage_chng(val);
	fn_carfamily_chng(val2);

	// �ű��϶� �⺻�� ����
	if('<?=$_GET['type']?>' == 'in'){
		// ��ü���� ����Ʈ(�̰���),  ������ ����Ʈ(�Ϲ���)
		$("#dambo4").val("00");
		$("#goout").val("1");			
	}

     
	window.parent.postMessage("�ڵ������� > �񱳰������", "*");   // '*' on any domain �θ�� ������..   

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
			setCarlaw();
		}
		
		// ������ȸ
		if(data.savegubun == 'A'){
			yoyul_send();
		}
	}

}

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>