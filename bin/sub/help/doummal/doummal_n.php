<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");
?>

<style>
body{background-image: none;}

</style>

<div class="container container_bk">
	<div class="content_wrap">

			<div class="menu_group_top" style="border-bottom:0px solid;">
				<div class="menu_group">
					<span><i class="fa-solid fa-comment font_topcolor mgr3"></i>�ڳ�����Ư��</span>
				</div>
			</div>
			<div class="tb_type01 view">
				<p class="mt10">

				* ���� ���� ���� : ���ο� �Ǵ� DB�պ� ������ ��������(4��ȭ��,��ȭ��,3������,�����)<br><br>
				* ���� �����纰 �󼼳���<br>
				</p>
			</div>
		<!-- //box_gray -->
			<div class="tb_type01 view">
				<table>
					<colgroup>
						<col width="23%">
						<col width="24%">
						<col width="auto">
					</colgroup>
					<thead>
						<th align="center">������</th>
						<th align="center">��������������</th>
						<th align="center">���δ��</th>
					</thead>

					<tbody>
						<tr>
							<td align="center">����(���ο�)</td>
							<td align="center">���Ѿ���</td>
							<td align="left">�����ϱ���, <b>�� 6������ �ڳ�</b>�� �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� ��<br>
								<span class="font_red">** 2024�� 3�� 16�� ���� �ڳ� ��(���� 1��� 2���̻󱸺�)�� ���� ����</span>
							</td>
						</tr>
						<tr>
							<td align="center">�Ｚ(���ο�)</td>
							<td align="center">���Ѿ���(��,�ڳ����� ���ԺҰ�)</td>
							<td align="left">�����ϱ���, <b>�� 6������ �ڳడ</b> �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� ��<br>
								<span class="font_red">** ������ 2024�� 4�� 11�� ����,</span><br>
								<span class="font_red">&nbsp&nbsp&nbsp&nbsp&nbsp<b>�� 15������ �ڳ�</b>�� �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� �� ���԰���</span><br>
								<span class="font_red">** 2024�� 4�� 11�� ���� �ڳ� ��(2��� 3���̻�)�� ���� �߰� ����(���ڳ��߰�����)</span>
							</td>
						</tr>
						<tr>
							<td align="center">DB(���ο�)</td>
							<td align="center">���Ѿ���(��, ����1������ ���ԺҰ�)</td>
							<td align="left">�����ϱ���, <b>�� 6������ �ڳ�</b>�� �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� ��<br>
								<span class="font_red">** ������ 2023�� 10�� 16�� ����,</span><br>
								<span class="font_red">&nbsp&nbsp&nbsp&nbsp&nbsp<b>�� 11������ �ڳ�</b>�� �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� �� ���԰���</span>
							</td>
						</tr>
						<tr>
							<td align="center">DB<br>(�����밳�μ���-��������)</td>
							<td align="center">1������, �κ�����</td>
							<td align="left">�����ϱ���, <b>�� 6������ �ڳ�</b>�� �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� ��</td>
						</tr>
						<tr>
							<td rowspan=2 align="center">��ȭ(���ο�)</td>
							<td align="center">1������, �κ�����</td>
							<td align="left">�����ϱ���, <b>�� 8������ �ڳ�</b>�� �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� ��<br>
								<span class="font_red">** ������ 2023�� 11�� 1�� ����,</span><br>
								<span class="font_red">&nbsp&nbsp&nbsp&nbsp&nbsp<b>�� 10������ �ڳ�</b>�� �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� �� ���԰���</span>									
							</td>
						</tr>
						<tr>
							<td align="center">1������, �κ����� - �̿�</td>
							<td align="left"><span class="font_red">������ 2023�� 11�� 1�� ����, <b>�� 1������ �ڳ�</b>�� �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� ��</span></td>
						</tr>
						<tr>
							<td rowspan=2 align="center">KB(���ο�)</td>
							<td align="center">1������, �κ�����</td>
							<td align="left"> �����ϱ���, <b>�� 9������ �ڳ�</b>�� �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� ��</td>
						</tr>
						<tr>
							<td align="center">1������, �κ����� - �̿�</td>
							<td align="left"> �����ϱ���, <b>�� 3������ �ڳ�</b>�� �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� ��</td>
						</tr>
						<tr>
							<td align="center">�޸���,�Ե�(���ο�)</td>
							<td align="center">1������, �κ�����</td>
							<td align="left">�����ϱ���, <b>�� 7������ �ڳ�</b>�� �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� ��</td>
						</tr>
						<tr>
							<td align="center">�ﱹ(���ο�)</td>
							<td align="center">1������, �κ�����</td>
							<td align="left">�����ϱ���, <b>�� 12������ �ڳ�</b>�� �ִ� �� �Ǵ� <b>�¾�</b>�� �ִ� ��</td>
						</tr>
					</tbody>
				</table>
				<p class="mt10">

				* DB�պ��� ���, ������ü���, ������ ���� ������ΰ� ��Ȯ���� ���� ��� (��: ��꿹������ ����Ⱓ������ ��ĥ ��/��)<br>
				&nbsp&nbsp&nbsp�¾Ʒ� �з���. �� �ڼ��� ������ �� ������ ���꿡�� Ȯ�� �ٶ�.<br>
				</p>
			</div>
			<!-- // tb_type01 -->
	</div>

</div>

<!-- // popup_wrap -->

 </body>
</html>

<script type="text/javascript">

	
$(document).ready(function(){
	

});


</script>


<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>
