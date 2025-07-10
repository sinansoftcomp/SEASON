<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>3D Scatter Plot with Plotly.js</title>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <style>
        html, body {
            margin: 0;
            height: 100%;
            overflow: hidden;
        }
        #plotly-graph {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div id="plotly-graph"></div>

    <!-- PHP���� �����͸� �����ɴϴ�. -->
    <?php
    // SQL Server ���� ���� ����
    include($_SERVER['DOCUMENT_ROOT']."/bin/include/config.php");
    include($_SERVER['DOCUMENT_ROOT']."/bin/include/dbConn.php");


	$FYYMM   = substr($_REQUEST['sdate1'],0,4).substr($_REQUEST['sdate1'],5,2).'';
	$TYYMM  =  substr($_REQUEST['sdate2'],0,4).substr($_REQUEST['sdate2'],5,2).'99';   //-->�Ѵ޸� ����.
	$pbit =  $_REQUEST['pbit'] ;   //-->

	if ($pbit == '11') {

			$where = "";

			// ������ Ʈ�� ���ý� �Ҽ�����(swon ��Ī : s2 - kdman(����α���)) 
			if($_REQUEST['id']){
				
				$Ngubun = substr($_REQUEST['id'],0,2);

				if($Ngubun == 'N1'){
					$bonbu = substr($_REQUEST['id'],2,10);
					$where  .= " where  c.BONBU = '".$bonbu."' " ;
				}else if($Ngubun == 'N2'){
					$jisa = substr($_REQUEST['id'],2,10);
					$where  .= " where c.JISA = '".$jisa."' " ;
				}else if($Ngubun == 'N3'){
					$jijum = substr($_REQUEST['id'],2,10);
					$where  .= " where c.JIJUM = '".$jijum."' " ;
				}else if($Ngubun == 'N4'){
					$team = substr($_REQUEST['id'],2,10);
					$where  .= " where c.TEAM = '".$team."' " ;
				}else if($Ngubun == 'N5'){
					$ksman = substr($_REQUEST['id'],2,10);
					$where  .= " where c.skey = '".$ksman."' " ;
				}
			}

			// ������ ��ȸ ����

			$tit = '���� FC�� �����м� 3D������ (����̻� ���� �̸� �Ķ�) ';
			$xtit = '����';
			$ytit = 'FC';
			$ztit = '����';

			$sql = "
				select  
 					SUBSTRING(ISNULL(f.JSNAME, ''), 1, 2) + SUBSTRING(ISNULL(g.JNAME, ''), 1, 4) AS dis1,
					ISNULL(c.SNAME, '') AS dis2,
					ISNULL(a.IPMST4, 0) AS Value
				from 
						(SELECT SCODE, SKEY ,sum(IMST1) IPMST1,sum(IMST2) IPMST2,sum(IMST3) IPMST3,sum(IMST4) IPMST4 ,SUM(SU1) SU1,SUM(SU2) SU2,SUM(SU3) SU3,SUM(SU4) SU4,   sum(IMST4) -  SUM(SU4) CATOT , SUM(SUNAB) SUNAB  ,
										 sum(KWN_CNT1) KWN_CNT1,sum(KWN_AMT1) KWN_AMT1,sum(KWN_CNT2) KWN_CNT2,sum(KWN_AMT2) KWN_AMT2 ,sum(KWN_CNT3) KWN_CNT3,sum(KWN_AMT3) KWN_AMT3 ,sum(KWN_CNT4) KWN_CNT4,sum(KWN_AMT4) KWN_AMT4  
							FROM  mistot 
							where scode =  '".$_SESSION['S_SCODE']."'     and  YYMM >= '".$FYYMM."'  and   YYMM <= '".$TYYMM."'
							GROUP BY scode, skey   ) a
							left outer join swon(nolock) c on a.scode = c.scode and a.skey = c.skey
							left outer join bonbu(nolock) e on c.scode = e.scode and c.bonbu = e.bcode
							left outer join jisa(nolock)  f on c.scode = f.scode and c.jisa = f.jscode
							left outer join jijum(nolock) g on c.scode = g.scode and c.jijum = g.jcode
							left outer join team(nolock) h  on c.scode = h.scode and c.team = h.tcode	
							left outer join common(nolock) i  on a.scode = i.scode and i.CODE = 'COM006' and  c.POS = i.CODESUB	
				$where 
			";
	}

	if ($pbit == '12') {
			$where = "";
			// ������ Ʈ�� ���ý� �Ҽ�����(swon ��Ī : s2 - kdman(����α���)) 
			if($_REQUEST['id']){
				
				$Ngubun = substr($_REQUEST['id'],0,2);

				if($Ngubun == 'N1'){
					$bonbu = substr($_REQUEST['id'],2,10);
					$where  .= " and  c.BONBU = '".$bonbu."' " ;
				}else if($Ngubun == 'N2'){
					$jisa = substr($_REQUEST['id'],2,10);
					$where  .= " and c.JISA = '".$jisa."' " ;
				}else if($Ngubun == 'N3'){
					$jijum = substr($_REQUEST['id'],2,10);
					$where  .= " and c.JIJUM = '".$jijum."' " ;
				}else if($Ngubun == 'N4'){
					$team = substr($_REQUEST['id'],2,10);
					$where  .= " and c.TEAM = '".$team."' " ;
				}else if($Ngubun == 'N5'){
					$ksman = substr($_REQUEST['id'],2,10);
					$where  .= " and c.skey = '".$ksman."' " ;
				}
			}

			// ������ ��ȸ ����

			$tit = '���� �����纰 �����м� 3D������ (����̻� ���� �̸� �Ķ�) ';
			$xtit = '����';
			$ytit = '������';
			$ztit = '����';

			$sql = "
				SELECT 
					SUBSTRING(ISNULL(f.JSNAME, ''), 1, 2) + SUBSTRING(ISNULL(g.JNAME, ''), 1, 4) AS dis1,
					SUBSTRING(ISNULL(j.NAME, ''), 1, 2) AS dis2,
					ISNULL(a.IPMST4, 0) AS Value
				FROM (
					SELECT 
						a.SCODE, 
						ISNULL(c.BONBU, '') AS BONBU,
						ISNULL(c.JISA, '') AS JISA,
						ISNULL(c.JIJUM, '') AS JIJUM,
						ISNULL(c.TEAM, '') AS TEAM,
						ISNULL(a.INSCODE, '') AS INSCODE,
						SUM(IMST4) AS IPMST4
					FROM mistot a
					LEFT OUTER JOIN swon c ON a.scode = c.scode AND a.skey = c.skey
					WHERE a.scode =  '".$_SESSION['S_SCODE']."'     and  YYMM >= '".$FYYMM."'   and   YYMM <= '".$TYYMM."'  $where
					GROUP BY 
						a.scode, 
						ISNULL(c.BONBU, ''), 
						ISNULL(c.JISA, ''), 
						ISNULL(c.JIJUM, ''), 
						ISNULL(c.TEAM, ''), 
						ISNULL(a.INSCODE, '')
				) a
				LEFT OUTER JOIN bonbu e ON a.scode = e.scode AND a.bonbu = e.bcode
				LEFT OUTER JOIN jisa f ON a.scode = f.scode AND a.jisa = f.jscode
				LEFT OUTER JOIN jijum g ON a.scode = g.scode AND a.jijum = g.jcode
				LEFT OUTER JOIN team h ON a.scode = h.scode AND a.team = h.tcode
				LEFT OUTER JOIN inssetup j ON a.scode = j.scode AND a.INSCODE = j.INSCODE
			";
	}


	if ($pbit == '13') {
			$where = "";

			// ������ Ʈ�� ���ý� �Ҽ�����(swon ��Ī : s2 - kdman(����α���)) 
			if($_REQUEST['id']){
				
				$Ngubun = substr($_REQUEST['id'],0,2);

				if($Ngubun == 'N1'){
					$bonbu = substr($_REQUEST['id'],2,10);
					$where  .= " and c.BONBU = '".$bonbu."' " ;
				}else if($Ngubun == 'N2'){
					$jisa = substr($_REQUEST['id'],2,10);
					$where  .= " and c.JISA = '".$jisa."' " ;
				}else if($Ngubun == 'N3'){
					$jijum = substr($_REQUEST['id'],2,10);
					$where  .= " and c.JIJUM = '".$jijum."' " ;
				}else if($Ngubun == 'N4'){
					$team = substr($_REQUEST['id'],2,10);
					$where  .= " and c.TEAM = '".$team."' " ;
				}else if($Ngubun == 'N5'){
					$ksman = substr($_REQUEST['id'],2,10);
					$where  .= " and c.skey = '".$ksman."' " ;
				}
			}

			// ������ ��ȸ ����

			$tit = '������ �����м� 3D������ (����̻� ���� �̸� �Ķ�) ';
			$xtit = '����';
			$ytit = '���';
			$ztit = '����';

			$sql = "
					select     
							SUBSTRING(ISNULL(f.JSNAME, ''), 1, 2) + SUBSTRING(ISNULL(g.JNAME, ''), 1, 4) AS dis1,
							' ' AS dis2,
							ISNULL(a.IPMST4, 0) AS Value
					from   	
							(SELECT a.SCODE, isnull(c.BONBU,'') BONBU ,isnull(c.JISA,'') JISA ,isnull(c.JIJUM,'') JIJUM ,isnull(c.TEAM,'') TEAM  , 
										sum(IMST1) IPMST1,sum(IMST2) IPMST2,sum(IMST3) IPMST3,sum(IMST4) IPMST4 ,SUM(SU1) SU1,SUM(SU2) SU2,SUM(SU3) SU3,SUM(SU4) SU4,   sum(IMST4) -  SUM(SU4) CATOT , SUM(SUNAB) SUNAB  ,
										sum(KWN_CNT1) KWN_CNT1,sum(KWN_AMT1) KWN_AMT1,sum(KWN_CNT2) KWN_CNT2,sum(KWN_AMT2) KWN_AMT2 ,sum(KWN_CNT3) KWN_CNT3,sum(KWN_AMT3) KWN_AMT3 ,sum(KWN_CNT4) KWN_CNT4,sum(KWN_AMT4) KWN_AMT4   
							FROM  mistot a  left outer join swon(nolock) c on a.scode = c.scode and a.skey = c.skey
							where a.scode =   '".$_SESSION['S_SCODE']."'     and  YYMM >= '".$FYYMM."'   and   YYMM <= '".$TYYMM."'  $where
							group by   a.scode,isnull(c.BONBU,''),isnull(c.JISA,''),isnull(c.JIJUM,''),isnull(c.TEAM,'')   ) a 

							left outer join bonbu(nolock) e on a.scode = e.scode and a.bonbu = e.bcode
							left outer join jisa(nolock)  f on a.scode = f.scode and a.jisa = f.jscode
							left outer join jijum(nolock) g on a.scode = g.scode and a.jijum = g.jcode
							left outer join team(nolock) h  on a.scode = h.scode and a.team = h.tcode	 			

			";
	}

 /*
echo '<pre>';
echo $sql; 
echo '</pre>';
 */ 

    $stmt = sqlsrv_query($mscon, $sql);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true)); // ���� ���� ���� �� ���� ���
    }

    // ��ȸ�� �����͸� �迭�� ����
    $data = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $row['dis1'] = iconv("EUC-KR", "UTF-8", $row['dis1']);
        $row['dis2'] = iconv("EUC-KR", "UTF-8", $row['dis2']);
        $row['Value'] = iconv("EUC-KR", "UTF-8", $row['Value']);
        $data[] = $row;
    }

    // JSON �������� ������ ���
    echo '<script>';
    echo 'var data = ' . json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';';
    echo '</script>';

    // ���� ����
    sqlsrv_free_stmt($stmt); // SQL Server ���� �ڿ� ����
    sqlsrv_close($mscon); // SQL Server ���� ����
    ?>

    <script>
        // �����Ͱ� ����� �ε�Ǿ����� Ȯ���մϴ�.
        console.log(data);

        // ������ ó��
        var organizations = [];
        var insuranceCompanies = [];
        var values = [];
        var texts = [];

        for (var i = 0; i < data.length; i++) {
            var item = data[i];
            organizations.push(item.dis1); // ���� �̸� ����
            insuranceCompanies.push(item.dis2); // ����� �̸� ����
            values.push(Number(item.Value)); // ���� �� ����, ���ڷ� ��ȯ
            // 3�ڸ����� ��ǥ�� �߰��Ͽ� ���� �� ǥ��
            var formattedValue = Number(item.Value).toLocaleString();
            texts.push(item.dis1 + '( ' + item.dis2 + ') ' + formattedValue); // �ؽ�Ʈ ���� ����
        }

        // ���� ���� ��� ���
        var meanValue = values.reduce(function(sum, value) {
            return sum + value;
        }, 0) / values.length;

        // ���� ���� ���� ���� �� ũ�� ���� �Լ�
		function getColorAndSize(value) {
			var color, size;
			if (value >= meanValue) {
				// ������ ��� �̻��� ��� ������ �� ũ�� ����
				var redIntensity = Math.min(255, Math.round((value - meanValue) * 5.1)); // ������ ���� ���
				color = `rgba(255, 0, 0, ${redIntensity / 255})`;
				size = Math.min(10, Math.max(20, (value - meanValue) * 0.015  + 0.5)); // ũ�� ��� (1/4�� ���)
			} else {
				// ������ ��� �̸��� ��� �Ķ��� �� ũ�� ����
				var blueIntensity = Math.min(255, Math.round((meanValue - value) * 5.1)); // �Ķ��� ���� ���
				color = `rgba(0, 0, 255, ${blueIntensity / 255})`;
				size = Math.min(10, Math.max(20, (meanValue - value) * 0.015  + 0.5)); // ũ�� ��� (1/4�� ���)
			}
			return { color: color, size: size };
		}

		// �� ���� ���� ���� ���� �� ũ�� �迭 ����
		var markerColors = values.map(value => getColorAndSize(value).color);
		var markerSizes = values.map(value => getColorAndSize(value).size);
        // Plotly.js�� ����Ͽ� 3D ������ ����
        var trace = {
            x: organizations, // X�� ������ (����)
            y: insuranceCompanies, // Y�� ������ (�����)
            z: values, // Z�� ������ (����)
            mode: 'markers+text', // ��Ŀ�� �ؽ�Ʈ ���
            marker: {
                size: markerSizes, // ��Ŀ ũ�� ����
                color: markerColors, // ��Ŀ ���� ����
                opacity: 1, // ��Ŀ ���� ����
                line: {
                    color: 'black', // ��Ŀ �׵θ� ����
                    width: 0.5 // ��Ŀ �׵θ� �β�
                }
            },
            text: texts, // �ؽ�Ʈ ������
            textposition: 'top center', // �ؽ�Ʈ ��ġ ����
            type: 'scatter3d' // 3D ������ Ÿ�� ����
        };

        // ���̾ƿ� ����
        var layout = {
     //       title: '������ ����纰 ����', // �׷��� ����
           title: '<?=$tit?>', // �׷��� ����
            scene: {
                xaxis: {title: '<?=$xtit?>'}, // X�� ����
                yaxis: {title: '<?=$ytit?>'}, // Y�� ����
                zaxis: {title:  '<?=$ztit?>'} // Z�� ����
            }
        };

        // Plotly�� ����Ͽ� �׷����� �����ϰ� ǥ��
        Plotly.newPlot('plotly-graph', [trace], layout);

        // â ũ�� ���� �� �׷��� ũ�� ����
        window.onresize = function() {
            Plotly.Plots.resize(document.getElementById('plotly-graph'));
        };
    </script>
</body>
</html>
