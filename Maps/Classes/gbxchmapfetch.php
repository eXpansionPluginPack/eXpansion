#!/usr/bin/php -q
<?php
// vim: set noexpandtab tabstop=2 softtabstop=2 shiftwidth=2:

// Simple command line driver for GBXChallMapFetcher class
// Created Sep 2012 by Xymph <tm@gamers.org>

	require_once('/home/tmn/aseco/includes/gbxdatafetcher.inc.php');

	if (!isset($argv[1]) || $argv[1] == '') {
		echo "missing filename\n";
		return;
	}
	$filename = $argv[1];
	$gbx = new GBXChallMapFetcher(true, false, true);
	try
	{
		$gbx->processFile($filename);
	}
	catch (Exception $e)
	{
		echo $e->getMessage() . "\n";
	}
	print_r($gbx);
	//file_put_contents('thumb.jpg', $gbx->thumbnail);

//	$gbxdata = file_get_contents($filename);
//	if (!$gbxdata) exit;
//	$gbx = new GBXChallMapFetcher(true, false, true);
//	try
//	{
//		$gbx->processData($gbxdata);
//	}
//	catch (Exception $e)
//	{
//		echo $e->getMessage() . "\n";
//	}
//	print_r($gbx);
//	file_put_contents('thumb.jpg', $gbx->thumbnail);
?>
