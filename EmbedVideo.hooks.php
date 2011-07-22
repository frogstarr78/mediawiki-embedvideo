<?php
/**
 * Wrapper class for encapsulating EmbedVideo related parser methods
 */
abstract class EmbedVideo
{
    protected static $initialized = false;

    /**
     * Sets up parser functions.
     */
    public static function setup()
    {
        # Setup parser hooks. ev is the primary hook, evp is supported for
        # legacy purposes
        global $wgVersion;
        $prefix = version_compare($wgVersion, '1.7', '<') ? '#' : '';
        EmbedVideo::addMagicWord($prefix, "ev", "EmbedVideo::parserFunction_ev");
        EmbedVideo::addMagicWord($prefix, "evp", "EmbedVideo::parserFunction_evp");
        return true;
    }

    private static function addMagicWord($prefix, $word, $function)
    {
        global $wgParser;
        $wgParser->setFunctionHook($prefix . $word, $function);
    }

    /**
     * Adds magic words for parser functions.
     * @param Array $magicWords
     * @param $langCode
     * @return Boolean Always true
     */
    public static function parserFunctionMagic(&$magicWords, $langCode='en')
    {
        $magicWords['evp'] = array(0, 'evp');
        $magicWords['ev']  = array(0, 'ev');
        return true;
    }

    /**
     * Embeds video of the chosen service, legacy support for 'evp' version of
     * the tag
     * @param Parser $parser Instance of running Parser.
     * @param String $service Which online service has the video.
     * @param String $id Identifier of the chosen service
     * @param String $width Width of video (optional)
     * @return String Encoded representation of input params (to be processed later)
     */
    public static function parserFunction_evp($parser, $service = null, $id = null, $desc = null,
        $align = null, $width = null)
    {
        return EmbedVideo::parserFunction_ev($parser, $service, $id, $width, $align, $desc);
    }

    /**
     * Embeds video of the chosen service
     * @param Parser $parser Instance of running Parser.
     * @param String $service Which online service has the video.
     * @param String $id Identifier of the chosen service
     * @param String $width Width of video (optional)
     * @param String $desc description to show (optional, unused)
     * @param String $align alignment of the video (optional, unused)
     * @return String Encoded representation of input params (to be processed later)
     */
    public static function parserFunction_ev($parser, $service = null, $id = null, $width = null,
        $align = null, $desc = null)
    {
        global $wgScriptPath;

        # Initialize things once
        if (!EmbedVideo::$initialized) {
            EmbedVideo::VerifyWidthMinAndMax();
            # Add system messages
            wfLoadExtensionMessages('embedvideo');
            $parser->disableCache();
            EmbedVideo::$initialized = true;
        }

        # Get the name of the host
        if ($service === null || $id === null)
            return EmbedVideo::errMissingParams($service, $id);

        $service = trim($service);
        $id = trim($id);
		$desc = $parser->recursiveTagParse($desc);

        $entry = EmbedVideo::getServiceEntry($service);
        if (!$entry)
            return EmbedVideo::errBadService($service);

        if (!EmbedVideo::sanitizeWidth($entry, $width))
            return EmbedVideo::errBadWidth($width);
        $height = EmbedVideo::getHeight($entry, $width);

        $hasalign = ($align !== null);
        if ($hasalign)
            $desc = EmbedVideo::getDescriptionMarkup($desc);

        # If the service has an ID pattern specified, verify the id number
        if (!EmbedVideo::verifyID($entry, $id))
            return EmbedVideo::errBadID($service, $id);

        # if the service has it's own custom extern declaration, use that instead
        if (array_key_exists ('extern', $entry) && ($clause = $entry['extern']) != NULL) {
            $clause = wfMsgReplaceArgs($clause, array($wgScriptPath, $id, $width, $height));
            if ($hasalign)
                $clause = EmbedVideo::generateAlignExternClause($clause, $align, $desc, $width, $height);
            return array($clause, 'noparse' => true, 'isHTML' => true);
        }

        # Build URL and output embedded flash object
        $url = wfMsgReplaceArgs($entry['url'], array($id, $width, $height));
        $clause = "";
        if ($hasalign)
            $clause = EmbedVideo::generateAlignClause($url, $width, $height, $align, $desc);
        else
            $clause = EmbedVideo::generateNormalClause($url, $width, $height);
        return array($clause, 'noparse' => true, 'isHTML' => true);
    }

	private static function html_id($rand = null) {
		if($rand == null){
			$rand = rand();
		}
		$id = "af-media-embed_{$rand}";
		return array($rand, $id);
	}

    # Generate the HTML necessary to embed the video with the given alignment
    # and text description
    protected static function generateAlignClause($url, $width, $height, $align, $desc)
    {

		$rand = rand();
		$clause = EmbedVideo::generateNormalClause($url, $width, $height, $rand);
		return EmbedVideo::generateAlignExternClause($clause, $align, $desc, $width, $height, $rand);
    }

    # Return the HTML necessary to embed the video normally.
    private static function generateNormalClause($url, $width, $height, $rand = null)
    {
		list($rand, $id) = EmbedVideo::html_id($rand);
		$clause = "<div id=\"af-media-restore_{$rand}\" style=\"text-align: right; display: none; cursor: pointer; height: 17px; width: 17px; border:0px;\" onclick=\"_asianfuse2.video_panels['{$rand}'].minimize();\">" . 
			"		<img src=\"/images/restore.png\" style=\"width: 16px; height: 16px; border: 0px; background: transparent; -ms-filter: 'progid:DXImageTransform.Microsoft.gradient(startColorstr=#00FFFFFF,endColorstr=#00FFFFFF)'; /* IE8 */ filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#00FFFFFF,endColorstr=#00FFFFFF);/* IE6 & 7 */ zoom: 1;\" />" . 
			"</div>" .
			"<object width=\"{$width}\" height=\"{$height}\" onmousedown=\"_asianfuse2.video_panels['{$rand}'].maximize();\">" . 
            "	<param name=\"movie\" value=\"{$url}\"></param>" .
            "	<param name=\"wmode\" value=\"transparent\"></param>" .
            "	<embed src=\"{$url}\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"{$width}\" height=\"{$height}\" id=\"af-media-embed_{$rand}\"></embed>" .
			"</object>" .
			'<script type="text/javascript">' .
			"	_asianfuse2.add('{$rand}', _asianfuse2);" . 
			"	_asianfuse2.video_panels['{$rand}'].container.width({$width});" .
			'</script>';
        return $clause;
    }

    # The HTML necessary to embed the video with a custom embedding clause,
    # specified align and description text
    private static function generateAlignExternClause($clause, $align, $desc, $width, $height, $rand = null)
    {
		list($rand, $id) = EmbedVideo::html_id($rand);
        $clause = "<div class=\"thumb t{$align}\">" .
			" <div style=\"\" class=\"thumbinner\" id=\"af-media-embed-container_{$rand}\" onmousedown=\"_asianfuse2.video_panels['{$rand}'].maximize();\">" .
					$clause .
            "		<div class=\"thumbcaption\">" .
						$desc .
			"		</div>" .
			"	</div>" . 
			"</div>";
        return $clause;
    }

    # Get the entry for the specified service, by name
    private static function getServiceEntry($service)
    {
        # Get the entry in the list of services
        global $wgEmbedVideoServiceList;
        return $wgEmbedVideoServiceList[$service];
    }

    # Get the width. If there is no width specified, try to find a default
    # width value for the service. If that isn't set, default to 425.
    # If a width value is provided, verify that it is numerical and that it
    # falls between the specified min and max size values. Return true if
    # the width is suitable, false otherwise.
    private static function sanitizeWidth($entry, &$width)
    {
        global $wgEmbedVideoMinWidth, $wgEmbedVideoMaxWidth;
        if ($width === null || $width == '*' || $width == '') {
            if (isset($entry['default_width']))
                $width = $entry['default_width'];
            else
                $width = 425;
            return true;
        }
        if (!is_numeric($width))
            return false;
        return $width >= $wgEmbedVideoMinWidth && $width <= $wgEmbedVideoMaxWidth;
    }

    # Calculate the height from the given width. The default ratio is 450/350,
    # but that may be overridden for some sites.
    private static function getHeight($entry, $width)
    {
        $ratio = 425 / 350;
        if (isset($entry['default_ratio']))
            $ratio = $entry['default_ratio'];
        return round($width / $ratio);
    }

    # If we have a textual description, get the markup necessary to display
    # it on the page.
    private static function getDescriptionMarkup($desc)
    {
        if ($desc !== null)
            return "<div class=\"thumbcaption\">$desc</div>";
        return "";
    }

    # Verify the id number of the video, if a pattern is provided.
    private static function verifyID($entry, $id)
    {
        $idhtml = htmlspecialchars($id);
        //$idpattern = (isset($entry['id_pattern']) ? $entry['id_pattern'] : '%[^A-Za-z0-9_\\-]%');
        //if ($idhtml == null || preg_match($idpattern, $idhtml)) {
        return ($idhtml != null);
    }

    # Get an error message for the case where the ID value is bad
    private static function errBadID($service, $id)
    {
        $idhtml = htmlspecialchars($id);
        $msg = wfMsgForContent('embedvideo-bad-id', $idhtml, @htmlspecialchars($service));
        return '<div class="errorbox">' . $msg . '</div>';
    }

    # Get an error message if the width is bad
    private static function errBadWidth($width)
    {
        $msg = wfMsgForContent('embedvideo-illegal-width', @htmlspecialchars($width));
        return '<div class="errorbox">' . $msg . '</div>';
    }

    # Get an error message if there are missing parameters
    private static function errMissingParams($service, $id)
    {
        return '<div class="errorbox">' . wfMsg('embedvideo-missing-params') . '</div>';
    }

    # Get an error message if the service name is bad
    private static function errBadService($service)
    {
        $msg = wfMsg('embedvideo-unrecognized-service', @htmlspecialchars($service));
        return '<div class="errorbox">' . $msg . '</div>';
    }

    # Verify that the min and max values for width are sane.
    private static function VerifyWidthMinAndMax()
    {
        global $wgEmbedVideoMinWidth, $wgEmbedVideoMaxWidth;
        if (!is_numeric($wgEmbedVideoMinWidth) || $wgEmbedVideoMinWidth < 100)
            $wgEmbedVideoMinWidth = 100;
        if (!is_numeric($wgEmbedVideoMaxWidth) || $wgEmbedVideoMaxWidth > 1024)
            $wgEmbedVideoMaxWidth = 1024;
    }
}
