<?php
/**
 * Adult Video oEmbed
 *
 * PHP version 5.3
 *
 * @category   PHP
 * @package    WordPress
 * @subpackage avo
 * @author     Chris McCoy <chris@lod.com>
 * @license    GPLv3 http://www.gnu.org/licenses/gpl-3.0.txt
 * @version    1.0
 * @link       http://wordpress.com
 */

/**
 * Plugin Name: Adult Video oEmbed
 * Description: Autoembedding of adult video sites in WordPress
 * Plugin URI:  https://github.com/chrismccoy
 * Author:      Chris McCoy
 * Author URI:  http://github.com/chrismccoy
 * License:     GPLv3
 * Version:     1.0
 */

/*

Supported Sites:

redtube.com
tube8.com
youporn.com
pornhub.com
keezmovies.com
xhamster.com
spankwire.com
youjizz.com
mofosex.com
xvideos.com
sexbot.com
tnaflix.com
drtuber.com
hardsextube.com
submityourflicks.com
cliphunter.com
boysfood.com
yobt.com
yobt.tv
pornrabbit
freeporn.com
sunporno.com
slutload.com
vporn.com
jizzbo.com
empflix.com
moviesguy.com
porn.com
nuvid.com
madthumbs.com
bigtits.com
userporn.com
*/

! defined( 'ABSPATH' ) and die( "whassup?" );

/**
 * Plugin directory URL.
 */
if ( ! defined( 'AVO_URL' ) ) {
	define( 'AVO_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Plugin directory path.
 */
if ( ! defined( 'AVO_DIR' ) ) {
	define( 'AVO_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Include options class.
 */

require_once( AVO_DIR . 'lib/options.inc.php' );

/**
 * Video Embed Width
 */
if ( ! defined( 'AVO_WIDTH' ) ) {
	define( 'AVO_WIDTH', get_video_width() );
}

/**
 * Video Embed Height
 */
if ( ! defined( 'AVO_HEIGHT' ) ) {
	define( 'AVO_HEIGHT', get_video_height() );
}

add_action( 'plugins_loaded', array( 'AVO', 'init' ) );

/**
 * Class for autoembedding of adult video sites by URL
 *
 * Usage:
 * Paste a supported video url into a blog post or page and it will be embedded eg:
 * https://redtube.com/3183
 *
 */

class AVO {

	/*
	 * The class object.
	 *
	 */
	protected static $classobj = NULL;

	/**
	 * To load the object and get the current state.
	 */
	public static function init() {

		NULL === self::$classobj && self::$classobj = new self();

		return self::$classobj;

	}

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		// Nothing todo in admin.
		if ( is_admin() )
			return;

		add_action( 'init', array( $this, 'register_embeds' ) );
	}

	/**
	 * Register a list of embed handlers
	 *
	 */
	public function register_embeds() {

		if ( ! apply_filters( 'load_default_embeds', TRUE ) )
			return;


		/**
	 	* Red Tube (redtube.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'redtube',
			'#http://(www\.)?redtube.com/[a-zA-Z0-9]+/?$#i',
			array( $this, 'redtube_embed_handler' ),
			10
		);

		/**
	 	* Pornhub (pornhub.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'pornhub',
			'#http://(www\.)?pornhub.com/*#i',
			array( $this, 'pornhub_embed_handler' ),
			10
		);

		/**
	 	* Keez Movies (keezmovies.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'keezmovies',
			'#http://(www\.)?keezmovies.com/*#i',
			array( $this, 'keezmovies_embed_handler' ),
			10
		);

		/**
	 	* Spank Wire (spankwire.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'spankwire',
			'#http://(www\.)?spankwire.com/*#i',
			array( $this, 'spankwire_embed_handler' ),
			10
		);

		/**
	 	* xHamster (xhamster.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'xhamster',
			'#http://xhamster.com/*#i',
			array( $this, 'xhamster_embed_handler' ),
			10
		);

		/**
	 	* You Jizz (youjizz.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'youjizz',
			'#http://(www\.)?youjizz.com/*#i',
			array( $this, 'youjizz_embed_handler' ),
			10
		);

		/**
	 	* Mofo Sex (mofosex.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'mofosex',
			'#http://(www\.)?mofosex.com/*#i',
			array( $this, 'mofosex_embed_handler' ),
			10
		);

		/**
	 	* Sex Bot (sexbot.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'sexbot',
			'#http://(www\.)?sexbot.com/*#i',
			array( $this, 'sexbot_embed_handler' ),
			10
		);

		/**
	 	* Yobt (yobt.tv) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'yobt',
			'#http://(www\.)?yobt.tv/*#i',
			array( $this, 'yobt_embed_handler' ),
			10
		);

		/**
	 	* Clip Hunter (cliphunter.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'cliphunter',
			'#http://(www\.)?cliphunter.com/*#i',
			array( $this, 'cliphunter_embed_handler' ),
			10
		);

		/**
	 	* Boys Food (boysfood.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'boysfood',
			'#http://(www\.)?boysfood.com/*#i',
			array( $this, 'boysfood_embed_handler' ),
			10
		);

		/**
	 	* Tube8 (tube8.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'tube8',
			'#http://(www\.)?tube8.com/*#i',
			array( $this, 'tube8_embed_handler' ),
			10
		);

		/**
	 	* You Porn (youporn.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'youporn',
			'#http://(www\.)?youporn.com/*#i',
			array( $this, 'youporn_embed_handler' ),
			10
		);

		/**
	 	* Free Porn (freeporn.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'freeporn',
			'#http://(www\.)?freeporn.com/*#i',
			array( $this, 'freeporn_embed_handler' ),
			10
		);

		/**
	 	* Jizzbo (jizzbo.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'jizzbo',
			'#http://(www\.)?jizzbo.com/*#i',
			array( $this, 'jizzbo_embed_handler' ),
			10
		);

		/**
	 	* Slut Load (slutload.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'slutload',
			'#http://(www\.)?slutload.com/*#i',
			array( $this, 'slutload_embed_handler' ),
			10
		);

		/**
	 	* vPorn (vporn.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'vporn',
			'#http://(www\.)?vporn.com/*#i',
			array( $this, 'vporn_embed_handler' ),
			10
		);

		/**
	 	* Sun Porno (sunporno.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'sunporno',
			'#http://(www\.)?sunporno.com/*#i',
			array( $this, 'sunporno_embed_handler' ),
			10
		);

		/**
	 	* Porn Rabbit (pornrabbit.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'pornrabbit',
			'#http://(www\.)?pornrabbit.com/*#i',
			array( $this, 'pornrabbit_embed_handler' ),
			10
		);

		/**
	 	* User Porn (userporn.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'userporn',
			'#http://(www\.)?userporn.com/*#i',
			array( $this, 'userporn_embed_handler' ),
			10
		);

		/**
	 	* Tna Flix (tnaflix.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'tnaflix',
			'#http://(www\.)?tnaflix.com/*#i',
			array( $this, 'tnaflix_embed_handler' ),
			10
		);

		/**
	 	* xVideos (xvideos.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'xvideos',
			'#http://(www\.)?xvideos.com/*#i',
			array( $this, 'xvideos_embed_handler' ),
			10
		);

		/**
	 	* Movies Guy (moviesguy.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'moviesguy',
			'#http://(www\.)?moviesguy.com/*#i',
			array( $this, 'moviesguy_embed_handler' ),
			10
		);

		/**
	 	* Emp Flix (empflix.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'empflix',
			'#http://(www\.)?empflix.com/*#i',
			array( $this, 'empflix_embed_handler' ),
			10
		);

		/**
	 	* Mad Thumbs (madthumbs.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'madthumbs',
			'#http://(www\.)?madthumbs.com/*#i',
			array( $this, 'madthumbs_embed_handler' ),
			10
		);

		/**
	 	* Nuvid (nuvid.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'nuvid',
			'#http://(www\.)?nuvid.com/*#i',
			array( $this, 'nuvid_embed_handler' ),
			10
		);

		/**
	 	* Big Tits (bigtits.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'bigtits',
			'#http://(www\.)?bigtits.com/*#i',
			array( $this, 'bigtits_embed_handler' ),
			10
		);

		/**
	 	* Porn (porn.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'porn',
			'#http://(www\.)?porn.com/*#i',
			array( $this, 'porn_embed_handler' ),
			10
		);

		/**
	 	* Dr Tuber (drtuber.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'drtuber',
			'#http://(www\.)?drtuber.com/*#i',
			array( $this, 'drtuber_embed_handler' ),
			10
		);

		/**
	 	* Hard Sex Tube (hardsextube.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'hardsextube',
			'#http://(www\.)?hardsextube.com/*#i',
			array( $this, 'hardsextube_embed_handler' ),
			10
		);

		/**
	 	* Heavy R (heavy-r.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'heavy',
			'#http://(www\.)?heavy-r.com/*#i',
			array( $this, 'heavy_embed_handler' ),
			10
		);

		/**
	 	* Submit your Flicks (submityourflicks.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'submityourflicks',
			'#http://(www\.)?submityourflicks.com/*#i',
			array( $this, 'submityourflicks_embed_handler' ),
			10
		);

		/**
	 	* Yobt (yobt.com) oembed handler
	 	*
		*/
		wp_embed_register_handler(
			'yobtcom',
			'#http://(www\.)?yobt.com/*#i',
			array( $this, 'yobtcom_embed_handler' ),
			10
		);

	}

	/**
	 * Red Tube oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function redtube_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = str_replace('/','',parse_url(esc_attr($matches[0]),PHP_URL_PATH));

		$embed = sprintf(
				'<object height="'. AVO_HEIGHT . '" width="'. AVO_WIDTH . '">
					<param name="allowfullscreen" value="true">
					<param name="AllowScriptAccess" value="always">
					<param name="movie" value="http://embed.redtube.com/player/">
					<param name="FlashVars" value="id=%1$s&style=redtube&autostart=false">
					<embed src="http://embed.redtube.com/player/?id=%1$s&style=redtube" allowfullscreen="true" AllowScriptAccess="always" flashvars="autostart=false" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" height="'. AVO_HEIGHT . '" width="'. AVO_WIDTH . '" />
				</object>',
				esc_attr( $video )
		);

		return apply_filters( 'oembed_redtube', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Big Tits oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function bigtits_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$videourl = 'config=http://www.bigtits.com/videos/embed_config?id='. $video[6];

		$embed = sprintf(
				'<object id="BigTitsPlayer" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" type="application/x-shockwave-flash" data="http://www.bigtits.com/js/flowplayer/flowplayer.embed-3.2.6-dev.swf">
					<param value="true" name="allowfullscreen"/>
					<param value="always" name="allowscriptaccess"/>
					<param value="high" name="quality"/>
					<param value="#000000" name="bgcolor"/>
					<param name="movie" value="http://www.bigtits.com/js/flowplayer/flowplayer.embed-3.2.6-dev.swf" />
					<param value="'.$videourl.'" name="flashvars"/>
				</object>',

				esc_attr( $video_url )
		);

		return apply_filters( 'oembed_bigtits', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Boys Food oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function boysfood_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<iframe src="http://www.boysfood.com/embed/%1$s/" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" style="overflow: hidden" frameborder="0" scrolling="no"></iframe>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_boysfood', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Clip Hunter oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function cliphunter_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<iframe id="helpframe" src="http://www.cliphunter.com/embed/?id=%1$s" frameborder="0" scrolling="no" height="'. AVO_HEIGHT . '" width="'. AVO_WIDTH . '" frameborder="0"></iframe>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_cliphunter', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Jizzbo oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function jizzbo_embed_handler( $matches, $attr, $url, $rawattr ) {

		preg_match('!\d+!', $url, $video);

		$embed = sprintf(
				'<iframe src="http://www.jizzbo.com/videos/embed/%1$s" frameborder="0" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" scrolling="no" allowtransparency="true"></iframe>',
				esc_attr( $video[0] )
		);

		return apply_filters( 'oembed_jizzbo', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Tnaflix oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function tnaflix_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = str_replace('video','', preg_split( '!/!u', $url ));

		$embed = sprintf(
				'<iframe src="http://player.tnaflix.com/video/%1$s" frameborder="0" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '"></iframe>',
				esc_attr( $video[5] )
		);

		return apply_filters( 'oembed_tnaflix', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Tube8 oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function tube8_embed_handler( $matches, $attr, $url, $rawattr ) {

		$parts = parse_url($url);

		$video = $parts[scheme] . '://' . $parts[host] . '/embed' . $parts[path];

		$embed = sprintf(
				'<iframe src="%1$s" frameborder="0" height="'. AVO_HEIGHT . '" width="'. AVO_WIDTH . '" scrolling="no" name="t8_embed_video"></iframe>',
				esc_attr( $video )
		);

		return apply_filters( 'oembed_tube8', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Empflix oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function empflix_embed_handler( $matches, $attr, $url, $rawattr ) {

		preg_match('!\d+!', $url, $video);

		$embed = sprintf(
				'<iframe src="http://player.empflix.com/video/%1$s" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" frameborder="0"></iframe>',
				esc_attr( $video[0] )
		);

		return apply_filters( 'oembed_empflix', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Sun Porno oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function sunporno_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<iframe src="http://embeds.sunporno.com/embed/%1$s" frameborder=0 width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" scrolling="no"></iframe>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_sunporno', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Hardsextube oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function hardsextube_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<object width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '">
					<param name="movie" value="http://www.hardsextube.com/embed/%1$s/"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="AllowScriptAccess" value="always"></param>
					<param name="wmode" value="transparent"></param>
					<embed src="http://www.hardsextube.com/embed/%1$s/" type="application/x-shockwave-flash" wmode="transparent" AllowScriptAccess="always" allowFullScreen="true" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '"></embed>
				</object>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_hardsextube', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Spankwire oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function spankwire_embed_handler( $matches, $attr, $url, $rawattr ) {

		$videoid  = preg_split( '!/!u', $url );
		$video = str_replace('video','',$videoid[4]);

		$embed = sprintf(
				'<iframe src="http://www.spankwire.com/EmbedPlayer.aspx?ArticleId=%1$s" frameborder="0" height="'. AVO_HEIGHT . '" width="'. AVO_WIDTH . '" scrolling="no" name="spankwire_embed_video"></iframe>',
				esc_attr( $video )
		);

		return apply_filters( 'oembed_spankwire', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * vPorn oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function vporn_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<object width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '">
					<param name="movie" value="http://www.vporn.com/swf/player_embed.swf"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="allowscriptaccess" value="always"></param>
					<param name="flashvars" value="videoID=%1$s"></param>
					<embed src="http://www.vporn.com/swf/player_embed.swf" type="application/x-shockwave-flash" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" allowscriptaccess="always" allowfullscreen="true" flashvars="videoID=%1$s"></embed>
				</object>',
				esc_attr( $video[5] )
		);

		return apply_filters( 'oembed_vporn', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * xHamster oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function xhamster_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video  = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<iframe width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" src="http://xhamster.com/xembed.php?video=%1$s" frameborder="0" scrolling="no"></iframe>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_xhamster', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Xvideos oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function xvideos_embed_handler( $matches, $attr, $url, $rawattr ) {

		$videoid = preg_split( '!/!u', $url );
		$video = str_replace('video','',$videoid[3]);

		$embed = sprintf(
				'<iframe src="http://flashservice.xvideos.com/embedframe/%1$s" frameborder=0 width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" scrolling="no"></iframe>',
				esc_attr( $video )
		);

		return apply_filters( 'oembed_xvideos', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Userporn oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function userporn_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = str_replace('v=','',parse_url($url,PHP_URL_QUERY));

		$embed = sprintf(
				'<object height="'. AVO_HEIGHT . '" width="'. AVO_WIDTH . '">
					<param name="allowfullscreen" value="true">
					<param name="AllowScriptAccess" value="always">
					<param name="movie" value="http://userporn.com/e/%1$s">
					<embed src="http://userporn.com/e/%1$s" allowfullscreen="true" AllowScriptAccess="always" type="application/x-shockwave-flash" height="'. AVO_HEIGHT . '" width="'. AVO_WIDTH . '" />
				</object>',
				esc_attr( $video )
		);

		return apply_filters( 'oembed_userporn', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Slutload oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function slutload_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<object type="application/x-shockwave-flash" data="http://emb.slutload.com/%1$s" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '">
					<param name="AllowScriptAccess" value="always"></param>
					<param name="movie" value="http://emb.slutload.com/%1$s"></param>
					<param name="allowfullscreen" value="true"></param>
					<embed src="http://emb.slutload.com/%1$s" AllowScriptAccess="always" allowfullscreen="true" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '"></embed>
				</object>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_slutload', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Free Porn oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function freeporn_embed_handler( $matches, $attr, $url, $rawattr ) {

		preg_match('!\d+!', $url, $video);

		$embed = sprintf(
				'<iframe src="http://www.freeporn.com/embed/%1$s/" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" style="overflow: hidden" frameborder="0" scrolling="no"></iframe>',
				esc_attr( $video[0] )
		);

		return apply_filters( 'oembed_freeporn', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Heavy oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function heavy_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video  = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<iframe width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" src="http://embed.heavy-r.com/embed/%1$s/%1$s/" frameborder="0" scrolling="no"></iframe>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_heavy', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Dr Tuber oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function drtuber_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<iframe src="http://www.drtuber.com/embed/%1$s" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" frameborder="0" scrolling="no"></iframe>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_drtuber', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Keezmovies oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function keezmovies_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = str_replace('/video/','/embed/',$url);

		$embed = sprintf(
				'<iframe src="%1$s" frameborder="0" height="'. AVO_HEIGHT . '" width="'. AVO_WIDTH . '" scrolling="no" name="keezmovies_embed_video"></iframe>',
				esc_attr( $video )
		);

		return apply_filters( 'oembed_keezmovies', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Mad Thumbs oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function madthumbs_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$videourl = 'config=http://www.madthumbs.com/videos/embed_config?id='. $video[6];

		$embed = sprintf(
				'<object type="application/x-shockwave-flash" data="http://cache.tgpsitecentral.com/madthumbs/js/flowplayer/flowplayer.embed-3.2.6-dev.swf" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '">
					<param name="movie" value="http://cache.tgpsitecentral.com/madthumbs/js/flowplayer/flowplayer.embed-3.2.6-dev.swf">
					<param value="true" name="allowfullscreen">
					<param value="always" name="allowscriptaccess">
					<param value="high" name="quality">
					<param value="#000000" name="bgcolor">
					<param value="%1$s" name="flashvars">
				</object>',
				esc_attr( $videourl )
		);

		return apply_filters( 'oembed_madthumbs', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Mofosex oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function mofosex_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<iframe src="http://www.mofosex.com/embed?videoid=%1$s" frameborder="0" height="'. AVO_HEIGHT . '" width="'. AVO_WIDTH . '" scrolling="no" name="mofosex_embed_video"></iframe>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_mofosex', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Moviesguy oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function moviesguy_embed_handler( $matches, $attr, $url, $rawattr ) {

		preg_match('!\d+!', $url, $video);

		$embed = sprintf(
				'<iframe src="http://www.moviesguy.com/videos/embed/%1$s" frameborder="0" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" scrolling="no" allowtransparency="true"></iframe>',
				esc_attr( $video[0] )
		);

		return apply_filters( 'oembed_moviesguy', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Nuvid oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function nuvid_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<iframe src="http://nuvid.com/embed/%1$s" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" frameborder="0" scrolling="no"></iframe>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_nuvid', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Sexbot oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function sexbot_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<iframe src="http://www.sexbot.com/embed/%1$s" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" style="overflow: hidden" frameborder="0" scrolling="no"></iframe>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_sexbot', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Submit your flicks oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function submityourflicks_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<object type="application/x-shockwave-flash" data="http://www.submityourflicks.com/embedded/%1$s" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '">
					<param name="AllowScriptAccess" value="always"></param>
					<param name="movie" value="http://www.submityourflicks.com/embedded/%1$s"></param>
					<param name="wmode" value="transparent"></param>
					<param name="allowfullscreen" value="true"></param>
					<embed src="http://www.submityourflicks.com/embedded/%1$s" AllowScriptAccess="always" wmode="transparent" allowfullscreen="true" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '"></embed>
				</object>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_submityourflicks', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Youjizz oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function youjizz_embed_handler( $matches, $attr, $url, $rawattr ) {

		preg_match('!\d+!', $url, $video);

		$embed = sprintf(
				'<iframe src="http://www.youjizz.com/videos/embed/%1$s" frameborder="0" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" scrolling="no" allowtransparency="true"></iframe>',
				esc_attr( $video[0] )
		);

		return apply_filters( 'oembed_youjizz', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * You Porn oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function youporn_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = str_replace('/watch/','/embed/',$url);

		$embed = sprintf(
				'<iframe src="%1$s" frameborder="0" height="'. AVO_HEIGHT . '" width="'. AVO_WIDTH . '" scrolling="no" name="yp_embed_video"></iframe>',
				esc_attr( $video )
		);

		return apply_filters( 'oembed_youporn', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Porn oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function porn_embed_handler( $matches, $attr, $url, $rawattr ) {

		preg_match('!\d+!', $url, $video);

		$embed = sprintf(
				'<iframe scrolling="no" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" src="http://www.porn.com/videos/embed/%1$s" frameborder="0"></iframe>',
				esc_attr( $video[0] )
		);

		return apply_filters( 'oembed_porn', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Porn Hub oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function pornhub_embed_handler( $matches, $attr, $url, $rawattr ) {

    		$video = str_replace('viewkey=','',parse_url($url,PHP_URL_QUERY));

		$embed = sprintf(
				'<iframe src="http://www.pornhub.com/embed/%1$s" frameborder=0 height="'. AVO_HEIGHT . '" width="'. AVO_WIDTH . '" scrolling="no" name="ph_embed_video">',
				esc_attr( $video )
		);

		return apply_filters( 'oembed_pornhub', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Pornrabbit oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function pornrabbit_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<iframe src="http://www.pornrabbit.com/embed/%1$s/" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" style="overflow: hidden" frameborder="0" scrolling="no"></iframe>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_pornrabbit', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Yobt oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function yobt_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<iframe src="http://www.yobt.tv/embed/%1$s.html" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" scrolling="no" frameborder="0" allowtransparency="true" marginheight="0" marginwidth="0"></iframe>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_yobt', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Yobt oembed handler callback.
	 *
	 * @return string          The embed HTML for script.
	 */
	public function yobtcom_embed_handler( $matches, $attr, $url, $rawattr ) {

		$video = preg_split( '!/!u', $url );

		$embed = sprintf(
				'<iframe src="http://www.yobt.com/embed/%1$s.html" width="'. AVO_WIDTH . '" height="'. AVO_HEIGHT . '" scrolling="no" frameborder="0" allowtransparency="true" marginheight="0" marginwidth="0"></iframe>',
				esc_attr( $video[4] )
		);

		return apply_filters( 'oembed_yobtcom', $embed, $matches, $attr, $url, $rawattr );
	}

} // end class

