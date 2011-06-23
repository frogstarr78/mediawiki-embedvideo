<?php

require_once "EmbedVideo.hooks.php";

abstract class EmbedVideoAjax extends EmbedVideo
{

	private static function extractAndCleanURLParams() {
		$params = $_REQUEST['asianfuse'];

		$params['width']  = intval(preg_replace('/[a-z]/', '', $params['width']));
		$params['height'] = intval(preg_replace('/[a-z]/', '', $params['height']));
		$params['desc']   = intval(preg_replace('/^-+/', '', $params['desc']));
		return $params;
	}

	private static function getEmbeddedCode() {
		$params = EmbedVideoAjax::extractAndCleanURLParams();

		return EmbedVideo::generateAlignClause($params['url'], $params['width'], $params['height'], $params['align'], $params['desc']);
	}

	public static function printEmbeddedCode() {
		print EmbedVideoAjax::getEmbeddedCode();
	}

	private static function generateThumbnailClause($id, $thumbnail_url, $width, $height, $align, $desc) {
		return sprintf('
			<div id="af-video-thumbnail_-_%s$1">
				<script type="text/javascript">
					html = "<img id="button2" src="%s$2" onclick="sajax_do_call(\'%s$1\', {id: %s$1, width: %s$3, height: %s$4, align: %s$5, desc: %s$6}, document.getElementById(\'af-video-thumbnail_-_%s$1\'))" width="%s$3" height="%s$4" />";
					document.write(html);
				</script>
				<noscript>
					<a href="%s$7" target="_blank" style="border: 0px;"><img src="%s$2" width="%s$3" height="%s$4" style="border: 0px;" /></a>
				</noscript>
			</div>
		', $id, $thumbnail_url, $width, $height, $align, $desc);
	}
}

?>
