<?
include($_SERVER['DOCUMENT_ROOT']."/bin/include/source/head.php");
?>

<!DOCTYPE html>

          <html>
          <head>
          <title>Drag and Drop Table Columns</title>
<style>
	table {
	  font-size: 11px;
	  border-collapse:collapse; 
	  border:1px solid
	}
	table td, table th {
	  border:1px solid;
	  padding: 3px;
	}
	.dragging {
	  background:#eee; 
	  color:#000
	}
	.hovering {
	  background:#ccc; 
	  color:#555
	}
	.tableHead{
		background:#aaa;
		cursor:pointer
	}
</style>    

<script type="text/javascript" src="<?=$conf['homeDir']?>/js/colResizable-1.6.min.js"></script>
<script type="text/javascript">

$(function(){
	$(".JCLRgrips").remove(); 
	$('#tableOne').colResizable({
		liveDrag:true,
		gripInnerHtml:"<div class='fa-solid fa-location-arrow fa-2xs font_blue'></div>", 
		postbackSafe:true, partialRefresh:true,
		gripInnerHtml:"<div class='grip'></div>",
		draggingClass:"dragging",
		minWidth:50,				// 최소너비(px)
		resizeMode:'overflow'		// 전체 width 고정하려고 할 경우 제외, 단 리사이즈 할때마다 높이 깨짐
	});
});
          window.onload = function() {         
              var head = document.getElementsByTagName("th");
              for (i=0; i<head.length; i++) {
                  head[i].onselectstart = function() { return false }         
                  head[i].onmousedown = mousedown;
                  head[i].onmouseover = mouseover;
                  head[i].onmouseout = mouseout;
                  head[i].onmouseup   = mouseup;
				  head[i].className = "tableHead";
              }    
          }
        

          var dragTD = null;
          function mousedown(ev){
              dragTD = this;
	          addClass(this, "dragging");   
          }

          function mouseover(ev){
              if (dragTD === null) { return;}
              addClass(this, "hovering");
          }

          function mouseout(ev){
              if (dragTD === null) { return;}
              removeClass(this, "hovering");
          }

          function mouseup(ev){
			var srcInx = dragTD.cellIndex;
			var tarInx = this.cellIndex;
			var table = document.getElementById("tableOne");
			var rows = table.rows;
			for (var x=0; x<rows.length; x++) {
				tds = rows[x].cells;
				rows[x].insertBefore(tds[srcInx], tds[tarInx])
			}
			dragTD = null;
          }
			function addClass(src, classname) {
				if (src.className.indexOf(classname) === -1 ) {
					src.className += " " + classname;
				}
			}
			function removeClass(src, classname) {
				src.className = src.className.replace(" " + classname, "");
			}
          </script>
        
          </head>

          <body>
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
          </body>
          </html>

<?
include($_SERVER['DOCUMENT_ROOT'].$conf['homeDir']."/include/source/bottom.php");
?>