<?php
/*
Plugin Name: Live2D WordPress Plugin
*/

class Live2D{
	function __construct(){
		//Init path
		$opt = get_option('live2d_options');
		if(!$opt){
			$opt['moc_path'] = "assets/Koharu/Koharu.moc3";
			$opt['tex1_path'] = "assets/Koharu/Koharu_01.png";
			$opt['tex2_path'] = "assets/Koharu/Koharu_02.png";
			$opt['tex3_path'] = "assets/Koharu/Koharu_03.png";
			$opt['mot1_path'] = "assets/Koharu/Koharu_01.motion3.json";
			$opt['mot2_path'] = "assets/Koharu/Koharu_02.motion3.json";
			$opt['mot3_path'] = "assets/Koharu/Koharu_03.motion3.json";
			$opt['phy_path'] = "assets/Koharu/Koharu.physics3.json";
			$opt['attach_tag'] = ".entry-header";
			$opt['pos_x'] = "200";
			$opt['pos_y'] = "250";
			$opt['scale'] = "400";
			update_option('live2d_options', $opt);
			$opt = array();
		}

		//Load javascript
		if(!strstr($_SERVER["REQUEST_URI"], 'wp-admin')){
			wp_enqueue_script('pixi', 'https://cdnjs.cloudflare.com/ajax/libs/pixi.js/4.6.1/pixi.min.js', '', '1.0', false);
			//By including below library in your project you agree to http://live2d.com/eula/live2d-proprietary-software-license-agreement_en.html
			wp_enqueue_script('live2dcubismcore', 'https://s3-ap-northeast-1.amazonaws.com/cubism3.live2d.com/sdk/js_eap/live2dcubismcore.min.js', '', '1.0', false);
			wp_enqueue_script('live2dcubismframework', get_stylesheet_directory_uri() . '/js/live2dcubismframework.js', '', '1.0', false);
			wp_enqueue_script('live2dcubismpixi', get_stylesheet_directory_uri() . '/js/live2dcubismpixi.js', '', '1.0', false);
			wp_enqueue_script('pixiKoharu', get_stylesheet_directory_uri() . '/js/pixiKoharu.js', '', '1.0', false);
		}
		add_action('admin_menu', array($this, 'add_pages'));

		//Set options
		$opt = get_option('live2d_options');
		$path = get_stylesheet_directory() . "/";
		print("<script>");
		print("var theme_path = '" . get_stylesheet_directory_uri() . "/';\n");
		if(isset($opt['moc_path']) && !empty($opt['moc_path']) && file_exists($path . $opt['moc_path'])){ print("var moc_path = '" . $opt['moc_path'] . "';\n"); }
		if(isset($opt['tex1_path']) && !empty($opt['tex1_path']) && file_exists($path . $opt['tex1_path'])){ print("var tex1_path = '" . $opt['tex1_path'] . "';\n"); }
		if(isset($opt['tex2_path']) && !empty($opt['tex2_path']) && file_exists($path . $opt['tex2_path'])){ print("var tex2_path = '" . $opt['tex2_path'] . "';\n"); }
		if(isset($opt['tex3_path']) && !empty($opt['tex3_path']) && file_exists($path . $opt['tex3_path'])){ print("var tex3_path = '" . $opt['tex3_path'] . "';\n"); }
		if(isset($opt['mot1_path']) && !empty($opt['mot1_path']) && file_exists($path . $opt['mot1_path'])){ print("var mot1_path = '" . $opt['mot1_path'] . "';\n"); }
		if(isset($opt['mot2_path']) && !empty($opt['mot2_path']) && file_exists($path . $opt['mot2_path'])){ print("var mot2_path = '" . $opt['mot2_path'] . "';\n"); }
		if(isset($opt['mot3_path']) && !empty($opt['mot3_path']) && file_exists($path . $opt['mot3_path'])){ print("var mot3_path = '" . $opt['mot3_path'] . "';\n"); }
		if(isset($opt['phy_path']) && !empty($opt['phy_path']) && file_exists($path . $opt['phy_path'])){ print("var phy_path = '" . $opt['phy_path'] . "';\n"); }
		print("var attach_tag = '" . ((isset($opt['attach_tag']) && !empty($opt['attach_tag'])) ? $opt['attach_tag'] : ".entry-header") . "';\n"); 
		print("var pos_x = " . ((isset($opt['pos_x']) && !empty($opt['pos_x'])) ? $opt['pos_x'] : "0") . ";\n"); 
		print("var pos_y = " . ((isset($opt['pos_y']) && !empty($opt['pos_y'])) ? $opt['pos_y'] : "0") . ";\n"); 
		print("var scale = " . ((isset($opt['scale']) && !empty($opt['scale'])) ? $opt['scale'] : "100") . ";\n"); 
		print("</script>");
	}
	function add_pages(){
		add_menu_page('Live2D Settings', 'Live2D Settings', 'level_8', __FILE__, array($this, 'option_page'), '', 26);
	}
	function option_page(){
		if(isset($_POST['live2d_options'])){
			check_admin_referer('live2d_action', 'live2d_nonce_filed');
			$opt = $_POST['live2d_options'];
			update_option('live2d_options', $opt);
?>
			<div class="updated fade"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
<?php
		}
?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br /></div>
			<h2>Live2D Settings</h2>
			<form action="" method="post">
<?php
			wp_nonce_field('live2d_action', 'live2d_nonce_filed');
			$opt = get_option('live2d_options');
			$moc_path = isset($opt['moc_path']) ? $opt['moc_path']: null;    //assets/Koharu/Koharu.moc3
			$tex1_path = isset($opt['tex1_path']) ? $opt['tex1_path']: null;
			$tex2_path = isset($opt['tex2_path']) ? $opt['tex2_path']: null;
			$tex3_path = isset($opt['tex3_path']) ? $opt['tex3_path']: null;
			$mot1_path = isset($opt['mot1_path']) ? $opt['mot1_path']: null;
			$mot2_path = isset($opt['mot2_path']) ? $opt['mot2_path']: null;
			$mot3_path = isset($opt['mot3_path']) ? $opt['mot3_path']: null;
			$phy_path = isset($opt['phy_path']) ? $opt['phy_path']: null;
			$attach_tag = isset($opt['attach_tag']) ? $opt['attach_tag']: null;
			$pos_x = isset($opt['pos_x']) ? $opt['pos_x']: null;
			$pos_y = isset($opt['pos_y']) ? $opt['pos_y']: null;
			$scale = isset($opt['scale']) ? $opt['scale']: null;
?> 
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="moc_path">Model path (Required)</label></th>
						<td>
							<input name="live2d_options[moc_path]" type="text" id="moc_path" value="<?php  echo $moc_path ?>" class="regular-text" /><br />
							e.g. assets/Koharu/Koharu.moc3
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="tex1_path">Texture path 1 (Required)</label></th>
						<td>
							<input name="live2d_options[tex1_path]" type="text" id="tex1_path" value="<?php  echo $tex1_path ?>" class="regular-text" /><br />
							e.g. assets/Koharu/Koharu_01.png
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="tex2_path">Texture path 2</label></th>
						<td>
							<input name="live2d_options[tex2_path]" type="text" id="tex2_path" value="<?php  echo $tex2_path ?>" class="regular-text" /><br />
							e.g. assets/Koharu/Koharu_02.png
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="tex3_path">Texture path 3</label></th>
						<td>
							<input name="live2d_options[tex3_path]" type="text" id="tex3_path" value="<?php  echo $tex3_path ?>" class="regular-text" /><br />
							e.g. assets/Koharu/Koharu_03.png
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="mot1_path">Motion path 1</label></th>
						<td>
							<input name="live2d_options[mot1_path]" type="text" id="mot1_path" value="<?php  echo $mot1_path ?>" class="regular-text" /><br />
							e.g. assets/Koharu/Koharu_01.motion3.json
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="mot2_path">Motion path 2</label></th>
						<td>
							<input name="live2d_options[mot2_path]" type="text" id="mot2_path" value="<?php  echo $mot2_path ?>" class="regular-text" /><br />
							e.g. assets/Koharu/Koharu_02.motion3.json
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="mot3_path">Motion path 3</label></th>
						<td>
							<input name="live2d_options[mot3_path]" type="text" id="mot3_path" value="<?php  echo $mot3_path ?>" class="regular-text" /><br />
							e.g. assets/Koharu/Koharu_03.motion3.json
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="phy_path">Physics path</label></th>
						<td>
							<input name="live2d_options[phy_path]" type="text" id="phy_path" value="<?php  echo $phy_path ?>" class="regular-text" /><br />
							e.g. assets/Koharu/Koharu.physics3.json
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="attach_tag">Attach tag (Required)</label></th>
						<td>
							<input name="live2d_options[attach_tag]" type="text" id="attach_tag" value="<?php  echo $attach_tag ?>" class="regular-text" /><br />
							e.g. .entry-header (CSS Selector)
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="pos_x">Possition x (Required)</label></th>
						<td>
							<input name="live2d_options[pos_x]" type="text" id="pos_x" value="<?php  echo $pos_x ?>" class="regular-text" /><br />
							e.g. 200
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="pos_y">Possition y (Required)</label></th>
						<td>
							<input name="live2d_options[pos_y]" type="text" id="pos_y" value="<?php  echo $pos_y ?>" class="regular-text" /><br />
							e.g. 250
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="scale">Scale (Required)</label></th>
						<td>
							<input name="live2d_options[scale]" type="text" id="scale" value="<?php  echo $scale ?>" class="regular-text" /><br />
							e.g. 400
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
			</form>
		</div>
<?php
	}
}
$live2d = new Live2D;

