<?php 
$dirLevel = "../";
$Mode="User";
if(isset($_GET['Mode'])){$Mode=$_GET['Mode'];} //user mode
?>
<!doctype html>
<html lang="en-au">
    <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge;chrome=1" >
        <link rel="stylesheet" href="../css/gantt.css" />
        <link rel="stylesheet" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" />
		<style type="text/css">
			body {
				font-family: Helvetica, Arial, sans-serif;
				font-size: 13px;
			}
			.contain {
				width: 1000px;
				margin: 0 auto;
			}
		</style>
        <link rel="stylesheet" href="../css/schedule.css" />
    </head>
    <body>
		<div class="contain">
			<div class="gantt"></div>
		</div>

    </body>
	<script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
	<script src="../js/jquery.fn.gantt.js"></script>
    <?php 
	$_GET['Mode']=$Mode;	
    include('get_schedule.php'); 
	?>
    <script>
    var ed = new Date();
    ed.setDate(ed.getDate()+150);

		$(function() {

			"use strict";

			$(".gantt").gantt({
				source: ganttData,
				navigate: "scroll",
				scale: "days",
				minScale: "days",
				maxScale: "days",
				itemsPerPage: 20,
                endDate: ed,
				onItemClick: function(data) {
                    parent.showOverlay(1);
					parent.document.scheddisp.id.value = data[0].id;
					parent.document.scheddisp.type.value = data[0].type;
                    parent.document.scheddisp.chassis.value = data[0].chassis;
                    parent.document.scheddisp.project.value = data[0].project;
					parent.document.scheddisp.team.value = data[0].team;
					parent.document.scheddisp.email.value = data[0].email;
                    parent.document.scheddisp.from.value = data[0].from.toLocaleDateString();
                    parent.document.scheddisp.to.value = data[0].to.toLocaleDateString();
					parent.document.scheddisp.spec_instr.value = data[0].spec_instr;
				},
				onAddClick: function(dt, rowId) {
                    if (rowId==undefined) return;
                    parent.showOverlay(3);
                    parent.document.schedadd.reset();
                    parent.document.getElementById("request-error-3").innerHTML = "&nbsp;";
                    parent.document.getElementById("request-success-3").innerHTML = "&nbsp;";
                    for (var i=0; i<parent.document.schedadd.chassis.options.length; i++)
                    {
                        if (parent.document.schedadd.chassis.options[i].value==rowId)
                        {
                            parent.document.schedadd.chassis.selectedIndex = i;
                        }
                    }
                    parent.setDate("#fromDate-3",dt);
                    parent.document.schedadd.type.focus();
				},
				onRender: function() {
                    parent.document.getElementById("schedule-panel").style.height = ($("div.gantt").height() + 50) + "px";
				}
			});

        });

    </script>
</html>
