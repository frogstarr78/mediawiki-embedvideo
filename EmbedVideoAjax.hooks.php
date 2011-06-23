<?php

require_once "EmbedVideo.hooks.php";

abstract class EmbedVideoAjax extends EmbedVideo
{

	private static function extractAndCleanURLParams() {
		$params = $_REQUEST['asianfuse'];

		$parmas['width']  = intval(preg_replace('/[a-z]/', '', $parmas['width']));
		$parmas['height'] = intval(preg_replace('/[a-z]/', '', $parmas['height']));
		$parmas['desc']   = intval(preg_replace('/^-+/', '', $parmas['desc']));
		return $params;
	}

	private static function getEmbeddedCode() {
		$params = EmbedVideoAjax::extractAndCleanURLParams();

		return EmbedVideo::generateAlignClause($params['url'], $params['width'], $params['height'], $params['align'], $params['desc']);
	}
	public static function printEmbeddedCode() {
		print EmbedVideoAjax::getEmbeddedCode();
	}
}

?>
