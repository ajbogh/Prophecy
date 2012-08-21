<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('phpcassa/lib/autoload.php');
require_once('config.inc.php');

use phpcassa\Connection\ConnectionPool;
use phpcassa\ColumnFamily;
use phpcassa\SystemManager;
use phpcassa\Schema\StrategyClass;


// Create a new keyspace and column family
//$sys = new SystemManager('127.0.0.1');

// Start a connection pool, create our ColumnFamily instance
$pool = new ConnectionPool($keyspace, $hostsArr);
$users = new ColumnFamily($pool, $columnFamily);

?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.dataTables_themeroller.css" />

	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.dataTables.js"></script>
</head>
<body>

<?php


// Fetch a user record
//$user = $users->get('1'); //fetch one row by ID
$rows = $users->get_range('','',10000); //fetch all rows

//echo all rows
/*$count = 0;
echo '<pre>';
foreach($rows as $key => $columns) {
    // updates any 
	if($columns['name']=='Allan'){
		$users->insert($key, array('name' => 'John'));
	}
	echo $key."\n";
    print_r($columns);
	$count++;
	echo "\n";
}
echo '</pre>';*/

$columns;

//build columns list
foreach($rows as $key => $cols){
	foreach($cols as $colkey=>$column){
		if(!isset($columns[$colkey])) $columns[$colkey] = 1;
	}
}
echo '<table id="elements" border=1>';
echo '<thead>';
echo '<tr>';
echo '<td><input type="checkbox" onclick="checkAll(this);" /></td>';
echo '<th>ID</th>';
foreach($columns as $colkey=>$column){
	echo '<th>'.$colkey.'</th>';
}
echo '</tr>';
echo '</thead>';

echo '<tbody>';
foreach($rows as $key => $cols) {
    // updates any 
	echo '<tr>';
	echo '<td><input type="checkbox" value="'.$key.'" /></td>';
	echo '<td>'.$key.'</td>';
	foreach($columns as $colkey=>$column){
		if(isset($cols[$colkey])){
			echo '<td>'.$cols[$colkey].'</td>';
		}else{
			echo '<td></td>';
		}
	}
	/*foreach($columns as $colkey => $column){
		echo '<td>'.$column.'</td>';
	}*/
	echo '</tr>';
}
echo '</tbody>';
echo '</table>';

echo '<div style="clear:both"></div>';
echo '<br />';
echo '<div>';
echo 'With selected: ';

echo 'Edit column ';
echo '<select id="editColumn">';
foreach($columns as $colkey=>$column){
	echo '<option value="'.$colkey.'">'.$colkey.'</option>';
}

echo '</select> ';
echo 'With value <input type="text" id="editColumnValue" /> <button onclick="editColumn();">Submit</button>';
echo '</div>';

//echo one row
//$name = $user["name"];
//echo "Fetched user $name\n";

//inserts a new record
/*echo $count;
$users->insert(($count+1), array('name' => 'Jane','password'=>'test'));*/
?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#elements").dataTable({
			"aoColumnDefs": [
          			{ 
					'bSortable': false, 
					'aTargets': [0] 
				}
       			]
		});
	});

	function checkAll(elem){
		if($(elem).is(":checked")){
			$("#elements tbody tr td:first-child input[type=checkbox]").prop("checked",true);
		}else{
			$("#elements tbody tr td:first-child input[type=checkbox]").prop("checked",false);
		}
	}

	function editColumn(){
		$elems = $("#elements tbody tr td:first-child input[type=checkbox]");

		var idArr = [];
		$elems.each(function(){
			if($(this).is(":checked")){
				//add ID to idArr
				idArr.push($(this).val());
			}
		});
		$.ajax({
			url:"formhandler.php",
			dataType:"text",
			type:"POST",
			data:{
				action:"updateColumn",
				column:$("#editColumn").val(),
				value:$("#editColumnValue").val(),
				idarr:idArr
			},
			success:function(data){
				window.location.reload();
			},
			error:function(xhr){
				console.log(xhr);
			}
		});
	}
</script>
</body>
</html>
