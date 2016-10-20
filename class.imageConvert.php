<?php
//=========================
// PHP image converter 
// require php 5+, gd image
// Coder: Jay Hsu
// Date: 2016/10/20
//=========================

class PHP_img_converter{

	//image crop and resize param
	public $filename = "";
	public $width = 400;
	public $height = 400;
	public $bg_color = '255,255,255';
	public $method = "";
	public $format = "";
	public $options = "";

	//image watermark param
	public $watermark_state = false;
	public $watermark_source = "";
	public $watermark_width = 100;
	public $watermark_height = 100;
	public $watermark_repeat = true;
	public $watermark_align_horizontal = 'right';
	public $watermark_align_vertical = 'bottom';

	//textmark param
	public $textmark_state = false;
	public $textmark_content = "Textmark";
	public $textmark_width = 200;
	public $textmark_height = 50;
	public $textmark_repeat = false;
	public $textmark_align_horizontal = 'right';
	public $textmark_align_vertical = 'bottom';
	public $textmark_fontsize = 20;
	public $textmark_angle = 0;
	public $textmark_color = 'random,random,random';

	//run before class start
	public function __construct() {
	}

	//run after class end
	public function __destruct() {   
    }

	public function generate(){
		$filename = $this->filename;
		$width = $this->width;
		$height = $this->height;
		$method = $this->method;
		$format = $this->format;
		$options = $this->options;
		$bg_color = $this->bg_color;

		//if no image found just return message and exit
		if (empty($filename)){
			echo 'Please Provide image file!';
			exit();
		}

		// Get new dimensions
		$image = imagecreatefromstring(file_get_contents($filename));
		imagealphablending($image, true);
		$width_orig = imagesx($image);
		$height_orig = imagesy($image);
		$x = 0;
		$y = 0;
		$x_orig = 0;
		$y_orig = 0;

		//set method
		switch ($method) {
			case 'ratio_scale':
				//scale the image with give ratio and ratio
				$ratio_orig = $width_orig/$height_orig;

				//recalculate position
				switch ($options) {
					case 'center':
						if ($height_orig > $width_orig){
							$x_extra = ($width*($height_orig/$height)-$width_orig)/2;
							$x_orig = $x_extra*-1;
							$width_orig = $width_orig+($x_extra*2);
						}else{
							$y_extra = ($height*($width_orig/$width)-$height_orig)/2;
							$y_orig = $y_extra*-1;
							$height_orig = $height_orig+($y_extra*2);
						}
					break;
					case 'fixed_size':
						if ($height_orig > $width_orig){
							$x_extra = ($width*($height_orig/$height)-$width_orig)/2;
							$width_orig = $width_orig+($x_extra*2);
						}else{
							$y_extra = ($height*($width_orig/$width)-$height_orig)/2;
							$height_orig = $height_orig+($y_extra*2);
						}
					break;
					default:
						//recalculate convert width and height
						if ($width/$height > $ratio_orig) {
						   $width = $height*$ratio_orig;
						} else {
						   $height = $width/$ratio_orig;
						}
					break;
				}
				
			break;
			case 'ratio_crop':
				//scale and crop the image with give size and ratio
				//recalculate position
				switch ($options) {
					case 'center':
						if ($height_orig > $width_orig){
							$y_orig = ($height_orig-($width_orig*($height/$width)))/2;
							//var_dump($y_orig);die();
						}else{
							$x_orig = ($width_orig-($height_orig*($width/$height)))/2;
							//var_dump("X:"$x_orig." W:".$width_orig." NW:".($height_orig*($width/$height)));die();
						}
					break;
				}

				//recalculate original width and height
				if ($height_orig > $width_orig){
					$height_orig = $width_orig*($height/$width);
				}else{
					$width_orig = $height_orig*($width/$height);
				}

			break;
			case 'crop':
				//scale and crop the image with give size

				//recalculate position
				switch ($options) {
					case 'center':
						$y_orig = ($height_orig-$height)/2;
						$x_orig = ($width_orig-$width)/2;
					break;
				}

				$width_orig = $width;
				$height_orig = $height;
				
			break;
			default:
				//scale the image with give size

			break;
		}
			
		// Resample Output
		//set format
		$image_p = imagecreatetruecolor($width, $height);
		$bg = imagecolorallocate($image_p, $this->getColor($bg_color,'R'), $this->getColor($bg_color,'G'), $this->getColor($bg_color,'B'));
		$bg_alpha = imagecolorallocatealpha($image_p, 0, 0, 0, 127);

		switch ($format) {
			case 'gif':
				imagecolortransparent($image_p, $bg);
			    imagealphablending($image_p, false);
			    imagesavealpha($image_p, true);
				imagecopyresampled($image_p, $image, $x, $y, $x_orig, $y_orig, $width, $height, $width_orig, $height_orig);
				imagefill($image_p, 0, 0, $bg_alpha);
				imagesavealpha($image_p, TRUE);
				$image_p = $this->waterMark($image_p); //apply watermark
				$image_p = $this->textMark($image_p); //apply textmark
				header('Content-Type: image/gif');
			    imagegif($image_p, null, 9);
			break;
			case 'png':
				imagecolortransparent($image_p, $bg);
			    imagealphablending($image_p, false);
			    imagesavealpha($image_p, true);
				imagecopyresampled($image_p, $image, $x, $y, $x_orig, $y_orig, $width, $height, $width_orig, $height_orig);
				imagefill($image_p, 0, 0, $bg_alpha);
				imagesavealpha($image_p, TRUE);
				$image_p = $this->waterMark($image_p); //apply watermark
				$image_p = $this->textMark($image_p); //apply textmark
				header('Content-Type: image/png');
			    imagepng($image_p, null);
			break;
			case 'jpg':
			default:
				imagecopyresampled($image_p, $image, $x, $y, $x_orig, $y_orig, $width, $height, $width_orig, $height_orig);
				imagefill($image_p, 0, 0, $bg);
				$image_p = $this->waterMark($image_p); //apply watermark
				$image_p = $this->textMark($image_p); //apply textmark
				header('Content-Type: image/jpeg');
				imagejpeg($image_p, null, 100);
			break;
		}

		imagedestroy($image);
	}

	//watermark function
	public function waterMark($image_p){

		//global param
		$watermark_state = $this->watermark_state;
		$watermark_source = $this->watermark_source;
		$watermark_width = $this->watermark_width;
		$watermark_height = $this->watermark_height;
		$watermark_repeat = $this->watermark_repeat;
		$watermark_align_horizontal = $this->watermark_align_horizontal;
		$watermark_align_vertical = $this->watermark_align_vertical;
		$updateImage = $image_p;
		$pos = array();
		$x = 0;
		$y = 0;
		$align_x = 0;
		$align_y = 0;
		$width = imagesx($image_p);
		$height = imagesy($image_p);

		//Get water mark image
		if ($watermark_state){

			//if watermark source is none just return without process
			if (empty($watermark_source)){
				return $updateImage;
			}

			//check repeat
			if($watermark_repeat) {
				$repeat_x = $width/$watermark_width;
				$repeat_y = $height/$watermark_height;
			}else{
				$repeat_x = 1;
				$repeat_y = 1;
			}

			//check alignment (horizontal)
			switch($watermark_align_horizontal){
				case 'left':
					$align_x = 0;
				break;
				case 'center':
					$align_x = ($width - $watermark_width) / 2;
					$repeat_x = $repeat_x / 2;
				break;
				case 'right':
				default:
					$align_x = $width - $watermark_width;
				break;
			}

			//check alignment (vertical)
			switch($watermark_align_vertical){
				case 'top':
					$align_y = 0;
				break;
				case 'center':
					$align_y = ($height - $watermark_height) / 2;
					$repeat_y = $repeat_y / 2;
				break;
				case 'bottom':
				default:
					$align_y = $height - $watermark_height;
				break;
			}

			//generate watermark position
			for ($i = 0; $i < $repeat_x;$i++) {
				$x = abs($align_x - ($i*$watermark_width));
				for ($j = 0; $j < $repeat_y; $j++) {
					$y = abs($align_y - ($j*$watermark_height));
					array_push($pos,array(
						'x'=>$x,
						'y'=>$y
					));
				}
			}

			//if repeat is using and align is set to center, print the other half watermark
			if ($watermark_repeat == true) {
				//get half from horizontal center
				if ($watermark_align_horizontal == 'center') {
					for ($i = 0; $i < $repeat_x;$i++) {
						$x = abs($align_x + ($i*$watermark_width));
						for ($j = 0; $j < $repeat_y; $j++) {
							$y = abs(0 + ($j*$watermark_height));
							array_push($pos,array(
								'x'=>$x,
								'y'=>$y
							));
						}
					}
				}
				//get half from vertical center
				if ($watermark_align_vertical == 'center') {
					for ($i = 0; $i < $repeat_x;$i++) {
						$x = abs(0 + ($i*$watermark_width));
						for ($j = 0; $j < $repeat_y; $j++) {
							$y = abs($align_y + ($j*$watermark_height));
							array_push($pos,array(
								'x'=>$x,
								'y'=>$y
							));
						}
					}
				}
				//get half from horizontal and vertical center
				if ($watermark_align_horizontal == 'center' && $watermark_align_vertical == 'center') {
					for ($i = 0; $i < $repeat_x;$i++) {
						$x = abs($align_x + ($i*$watermark_width));
						for ($j = 0; $j < $repeat_y; $j++) {
							$y = abs($align_y + ($j*$watermark_height));
							array_push($pos,array(
								'x'=>$x,
								'y'=>$y
							));
						}
					}
				}

			}
			
			//var_dump(json_encode($pos));die();

			//generate watermark from position array
			for ($i = 0; $i < count($pos);$i++) {
				$image_p = $updateImage;
				$mark = imagecreatefromstring(file_get_contents($watermark_source));
				$orig_width = imagesx($mark);
				$orig_height = imagesy($mark);
				$bg = imagecolorallocate($image_p, 255, 255, 0);
				imagecolortransparent($image_p, $bg);
				imagealphablending($image_p, true);
				imagesavealpha($image_p, true);
				imagecopyresampled($image_p, $mark, $pos[$i]['x'], $pos[$i]['y'], 0, 0, $watermark_width, $watermark_height, $orig_width, $orig_height);
				imagedestroy($mark);
				$updateImage = $image_p;
			}
		}
		return $updateImage;
	}

	//textmark function
	public function textMark($image_p){

		//global param
		$textmark_state = $this->textmark_state;
		$textmark_content = $this->textmark_content;
		$textmark_width = $this->textmark_width;
		$textmark_height = $this->textmark_height;
		$textmark_repeat = $this->textmark_repeat;
		$textmark_align_horizontal = $this->textmark_align_horizontal;
		$textmark_align_vertical = $this->textmark_align_vertical;
		$textmark_fontsize = $this->textmark_fontsize;
		$textmark_angle = $this->textmark_angle;
		$textmark_color = $this->textmark_color;
		$updateImage = $image_p;
		$pos = array();
		$x = 0;
		$y = 0;
		$align_x = 0;
		$align_y = 0;
		$width = imagesx($image_p);
		$height = imagesy($image_p);

		//Get text mark
		if ($textmark_state){

			//check repeat
			if($textmark_repeat) {
				$repeat_x = $width/$textmark_width;
				$repeat_y = $height/$textmark_height;
			}else{
				$repeat_x = 1;
				$repeat_y = 1;
			}

			//check alignment (horizontal)
			switch($textmark_align_horizontal){
				case 'left':
					$align_x = 0;
				break;
				case 'center':
					$align_x = ($width - $textmark_width) / 2;
					$repeat_x = $repeat_x / 2;
				break;
				case 'right':
				default:
					$align_x = $width - $textmark_width;
				break;
			}

			//check alignment (vertical)
			switch($textmark_align_vertical){
				case 'top':
					$align_y = 0;
				break;
				case 'center':
					$align_y = ($height - $textmark_height) / 2;
					$repeat_y = $repeat_y / 2;
				break;
				case 'bottom':
				default:
					$align_y = $height - $textmark_height;
				break;
			}

			//generate watermark position
			for ($i = 0; $i < $repeat_x;$i++) {
				$x = abs($align_x - ($i*$textmark_width));
				for ($j = 0; $j < $repeat_y; $j++) {
					$y = abs($align_y - ($j*$textmark_height));
					array_push($pos,array(
						'x'=>$x,
						'y'=>$y
					));
				}
			}

			//if repeat is using and align is set to center, print the other half watermark
			if ($textmark_repeat == true) {
				//get half from horizontal center
				if ($textmark_align_horizontal == 'center') {
					for ($i = 0; $i < $repeat_x;$i++) {
						$x = abs($align_x + ($i*$textmark_width));
						for ($j = 0; $j < $repeat_y; $j++) {
							$y = abs(0 + ($j*$textmark_height));
							array_push($pos,array(
								'x'=>$x,
								'y'=>$y
							));
						}
					}
				}
				//get half from vertical center
				if ($textmark_align_vertical == 'center') {
					for ($i = 0; $i < $repeat_x;$i++) {
						$x = abs(0 + ($i*$textmark_width));
						for ($j = 0; $j < $repeat_y; $j++) {
							$y = abs($align_y + ($j*$textmark_height));
							array_push($pos,array(
								'x'=>$x,
								'y'=>$y
							));
						}
					}
				}
				//get half from horizontal and vertical center
				if ($textmark_align_horizontal == 'center' && $textmark_align_vertical == 'center') {
					for ($i = 0; $i < $repeat_x;$i++) {
						$x = abs($align_x + ($i*$textmark_width));
						for ($j = 0; $j < $repeat_y; $j++) {
							$y = abs($align_y + ($j*$textmark_height));
							array_push($pos,array(
								'x'=>$x,
								'y'=>$y
							));
						}
					}
				}

			}
			
			//var_dump(json_encode($pos));die();

			//generate watermark from position array
			for ($i = 0; $i < count($pos);$i++) {
				$image_p = $updateImage;
				$font = 'font.ttf';
				$mark = imagecreatetruecolor($textmark_width, $textmark_height);
				$bg = imagecolorallocate($image_p, 0, 0, 0);
				$color = imagecolorallocate($mark, $this->getColor($textmark_color,'R'), $this->getColor($textmark_color,'G'), $this->getColor($textmark_color,'B'));
				$trans_colour = imagecolorallocatealpha($mark, 0, 0, 0, 127);
    			imagefill($mark, 0, 0, $trans_colour);
				imagettftext($mark, $textmark_fontsize, $textmark_angle, ($textmark_fontsize*2), ($textmark_fontsize*2), $color, $font, $textmark_content);
				imagecolortransparent($image_p, $bg);
				imagealphablending($image_p, true);
				imagesavealpha($image_p, true);
				imagecopyresampled($image_p, $mark, $pos[$i]['x'], $pos[$i]['y'], 0, 0, $textmark_width, $textmark_height, $textmark_width, $textmark_height);
				imagedestroy($mark);
				$updateImage = $image_p;
			}
		}
		return $updateImage;
	}

	//getColor
	public function getColor($color,$return) {
		$arry = explode(",",$color);
		$R = ($arry[0] == 'random') ? rand(0,255) : $arry[0] ;
		$G = ($arry[1] == 'random') ? rand(0,255) : $arry[1] ;
		$B = ($arry[2] == 'random') ? rand(0,255) : $arry[2] ;

		switch($return){
			case 'R':
				return $R;
			break;
			case 'G':
				return $G;
			break;
			case 'B':
				return $B;
			break;
		}
	}
	
}
?>