<style>
	th,td{font-family:'Malgun Gothic','���� ���', font-size:12px;font-weight:600;}
</style>

<table id="sort_table" class="gridhover" >
		<colgroup>
			<col width="150px">
			<col width="100px">
			<col width="80px">

		</colgroup>
		<thead>
			<tr class="rowTop">
				<th align="center" style="font-family:'Malgun Gothic','���� ���', font-size:12px">���ǹ�ȣ</th>
				<th align="center">�����</th>
				<th align="center">���豸��</th>	
				
			</tr>
		</thead>

		<tbody>


			<?if(!empty($listData)){?>
			<?foreach($listData as $key => $val){extract($val);
			
			?>

			<tr class="rowData" rol-date='<?=$kcode?>' rol-date2='<?=$inscode?>'>
				<td>ȫ�浿</td>
				<td>50,000</td>
				<td>ABCDEFabcdef</td>


			</tr>
			<?}}else{?>
				<tr>
					<td style="color:#8C8C8C" colspan=3>�˻��� �����Ͱ� �����ϴ�</td>
				</tr>
			<?}?>
		</tbody>
	</table>