<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('phpcassa/lib/autoload.php');
require_once('config.inc.php');

use phpcassa\Connection\ConnectionPool;
use phpcassa\ColumnFamily;
use phpcassa\SystemManager;
use phpcassa\Schema\StrategyClass;

if(isset($_POST['action'])){
	try{
		$_POST['action']($_POST);
	}catch(Exception $e){
	}
}


function updateColumn($args){
	global $keyspace;
	global $hostsArr;
	global $columnFamily;
	
	$ks = (isset($args["keyspace"])?$args["keyspace"]:$keyspace);
	$hosts = (isset($args["hostsArr"])?$args["hostsArr"]:$hostsArr);
	$cf = (isset($args["columnFamily"])?$args["columnFamily"]:$columnFamily);

	$pool = new ConnectionPool($ks, $hosts);
	$colFam = new ColumnFamily($pool, $cf);

	//$args['column']
	//$args['value']
	//$args['idarr'] - an array of IDs to modify

	foreach($args['idarr'] as $id){
		$colFam->insert($id, array($args['column']=>$args['value']));
	}
}

function getData($args){
	global $keyspace;
	global $hostsArr;
	global $columnFamily;
	
	$ks = (isset($args["keyspace"])?$args["keyspace"]:$keyspace);
	$hosts = (isset($args["hostsArr"])?$args["hostsArr"]:$hostsArr);
	$cf = (isset($args["columnFamily"])?$args["columnFamily"]:$columnFamily);
	$start = (isset($args["start"])?$args["start"]:'');
	$end = (isset($args["end"])?$args["end"]:'');
	$count = (isset($args["count"])?$args["count"]:'');
	
	$pool = new ConnectionPool($ks, $hosts);
	$users = new ColumnFamily($pool, $cf);
	
	$rows = $users->get_range($start,$end,$count); //fetch all rows
	
	echo json_encode($rows);
}

function getRow($args){
	global $keyspace;
	global $hostsArr;
	global $columnFamily;
	
	$ks = (isset($args["keyspace"])?$args["keyspace"]:$keyspace);
	$hosts = (isset($args["hostsArr"])?$args["hostsArr"]:$hostsArr);
	$cf = (isset($args["columnFamily"])?$args["columnFamily"]:$columnFamily);
	
	$user = $users->get('1'); //fetch one row by ID
	
}

?>
