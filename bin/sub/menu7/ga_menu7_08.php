<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");


$sql = "";		

$qry	= sqlsrv_query( $mscon, $sql );
while( $fet = sqlsrv_fetch_array( $qry, SQLSRV_FETCH_ASSOC) ) {
	$listData[]	= $fet;
}

sqlsrv_free_stmt($result);
sqlsrv_close($mscon);


?>

 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css"/>

<!-- html¿µ¿ª -->
<style>
body{background-image: none;}


</style>

<div class="container">
	<div class="content_wrap">
		<fieldset>

				

		</fieldset>
	</div><!-- // content_wrap -->
</div>
<!-- // container -->
<!-- // wrap -->
<script type="text/javascript">



$(document).ready(function(){	


});

</script>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>