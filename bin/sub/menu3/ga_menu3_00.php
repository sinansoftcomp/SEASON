<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");
?>

<style>
table {
    font-size: 11px;
    border-collapse:collapse; 
    border:1px solid
}
table td, table th {
    border:1px solid;
    padding: 3px 5px 3px 5px;
}

.tableHead{
    background:#aaa; 
    cursor:pointer
}
 
.draggedDiv {
    position:absolute; 
    background:#eee;
}

.dragging {
    background:#eee; 
    color:#000
}

.hovering {
    background:#ccc; 
    color:#555
}
</style>    
<script src='https://code.jquery.com/jquery-3.2.1.slim.js'
        integrity='sha256-tA8y0XqiwnpwmOIl3SGAcFl2RvxHjA8qp0+1uCGmRmg=' crossorigin='anonymous'></script>
<script>
// JQuery
$(function() {
    var heads = $("#tableOne th");
    
    $.each(heads, function(inx, row ) {
        var head = $(row);
        head.bind( "selectstart", function() { return false });
        head.bind( "mousedown", mousedown);
        head.bind( "mouseover", mouseover);
        head.bind( "mouseout", mouseout);
        head.bind( "mouseup", mouseup);

        head.addClass("tableHead");
    });
    $(document.documentElement).bind( "mouseup", documentMouseup);
    $(document.documentElement).bind( "mousemove", documentMouseMove);
});

function documentMouseup(ev){
    if (!dragTD) { return;}
    
    $(dragTD).removeClass("dragging");
    dragTD = null;    
    draggedDiv.remove();
    draggedDiv = null;
}

function documentMouseMove(ev){
    if (!draggedDiv) { return;}
    
    draggedDiv.css({top: ev.pageY + 5 + "px", left: ev.pageX + 10 + "px"});
}

var dragTD = null, draggedDiv=null;
function mousedown(ev){
    dragTD = this;
    $(this).addClass("dragging");
    
    draggedDiv = $("<div>");
    draggedDiv.addClass("draggedDiv");
    draggedDiv.css({top: ev.pageY + 5 + "px", left:ev.pageX + 10 + "px"});
    $(document.documentElement).append(draggedDiv);
    
    var dragTable = $("<table>");
    draggedDiv.append(dragTable);

    var srcInx = dragTD.cellIndex;
    var rows = $("#tableOne tr"); 
    
    for (var x=0; x<rows.length; x++) {
        var tr = rows[x].cloneNode(false);
        dragTable.append(tr);
        var tds = rows[x].cells[srcInx].cloneNode(true);
        tr.appendChild(tds);
    }    
}

function mouseover(ev){
    if (dragTD === null) { return;}
    $(this).addClass("hovering");
}

function mouseout(ev){
    if (dragTD === null) { return;}
    $(this).removeClass("hovering");
}

function mouseup(ev){
    $(this).removeClass("hovering");
    $(dragTD).removeClass("dragging");
    draggedDiv.remove();
    draggedDiv = null;
    
    var srcInx = dragTD.cellIndex;
    var tarInx = this.cellIndex;
    var rows = $("#tableOne tr"); 
    
    for (var x=0; x<rows.length; x++) {
        tds = rows[x].cells;
        rows[x].insertBefore(tds[srcInx], tds[tarInx])
    }
    
    dragTD = null;
}

</script>

<button id="mybutton">AAA</button>
<div class="tb_type01 kwndatalist div_grid" style="overflow-y:auto;">	
	<table id="tableOne" class="gridhover" style="min-width: 3000px;">
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
			<col width="90px">

			<col width="80px">
			<col width="80px">

			<col width="100px">
			<col width="90px">
			<col width="120px">
			<col width="auto">
		</colgroup>
		<thead>
			<tr class="rowTop">
				<th align="center">증권번호</th>
				<th align="center">보험사</th>
				<th align="center">보험구분</th>	
				
				<th align="center">소속</th>
				<th align="center">모집사원</th>
				<th align="center">사용인</th>

				<th align="center">계약자</th>
				<th align="center">피보험자</th>
				
				<th align="center">보험료</th>
				<th align="center">수정보험료</th>		
				<th align="center">상품</th>

				<th align="center">계약일자</th>
				<th align="center">계약개시일자 ~ 종료일자</th>

				<th align="center">납입회차</th>
				<th align="center">최종납입일</th>
				<th align="center">계약상태</th>
				<th align="center">수납상태</th>
				<th align="center">수수료계약상태</th>

				<th align="center">모집사원코드</th>
				<th align="center">사용인코드</th>

				<th align="center">납입방법</th>
				<th align="center">납입기간</th>
				<th align="center">전화번호</th>		
				<th align="center">주소</th>
			</tr>
		</thead>

		<tbody>



			<tr class="rowData" rol-date='<?=$kcode?>' rol-date2='<?=$inscode?>'>
				<td align="left" style=""><?=$kcode?></td>
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

		</tbody>
	</table>
</div><!-- // tb_type01 -->

<!--
<table id="tableOne">
    <thead>
        <tr>
            <th>Column 1</th>
            <th>Column 2</th>
            <th>Column 3</th>
            <th>Column 4</th>
            <th>Column 5</th>
            <th>Column 6</th>
            <th>Column 7</th>
            <th>Column 8</th>
            <th>Column 9</th>
            <th>Column 10</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>data 1</td>
            <td>data 2</td>
            <td>data 3</td>
            <td>data 4</td>
            <td>data 5</td>
            <td>data 6</td>
            <td>data 7</td>
            <td>data 8</td>
            <td>data 9</td>
            <td>data 10</td>
        </tr>
        <tr>
            <td>data 1</td>
            <td>data 2</td>
            <td>data 3</td>
            <td>data 4</td>
            <td>data 5</td>
            <td>data 6</td>
            <td>data 7</td>
            <td>data 8</td>
            <td>data 9</td>
            <td>data 10</td>
        </tr>
    </tbody>
</table>
-->

<script>
$('#tableOne').on('click',function(e) {
  console.log("User left clicked!"); // do select code
  if (e.button == 0 && e.detail == 2) {
    console.log("User left doubleclicked!"); // do action code
  }
});
</script>