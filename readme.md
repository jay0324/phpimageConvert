Purpose: Convert image to any size with watermark
-------------
Usage:
-------------
#1. Include the class to php
```
include("class.imageConvert.php");
```

#2. Define class
```
$image1 = new PHP_img_converter;
```

#3. Define source image
```
$image1->filename = 'img/test.jpg';
```

#4. Return it to page
```
echo $image1->generate();
```

options:
-------------
```
//define image source (string)
$image1->filename = "";

//define image width (int)
$image1->width = 400;

//define image height (int)
$image1->height = 400;

//define image bg color (string)(R,G,B)
$image1->bg_color = '255,255,255';

//define generate method (string)
$image1->method = "";
- ratio_scale 	(scale image to define size with source original ratio)
- ratio_crop 	(crop image to define size with source original ratio)
- crop 			(crop image to define size without source original ratio)
- default		(scale image to define size without source original ratio)

//output format (string)
$image1->format = "";
- gif
- png
- jpg

//scale and crop options (string)
$image1->options = "";
- center		(scale or crop image from center)
- fixed_size	(scale image by original ratio to fit define width or height)(ratio_scale only)

//active watermark (boolean)
$image1->watermark_state = false;

//watermark image source (string)
$image1->watermark_source = "";

//watermark image width (int)
$image1->watermark_width = 100;

//watermark image height (int)
$image1->watermark_height = 100;

//watermark image repeat (boolean)
$image1->watermark_repeat = true;

//watermark image horizontal alignment (string)
$image1->watermark_align_horizontal = '';
- right		(align to right and repeat from right) (default)
- center	(align to center and repeat from center)
- left		(align to left and repeat from left)

//watermark image vertical alignment (string)
$image1->watermark_align_vertical = '';
- top		(align to top and repeat from top)
- center	(align to center and repeat from center)
- bottom	(align to bottom and repeat from bottom) (default)

//active textmark (boolean)
$image1->textmark_state = false;

//textmark content (string)
$image1->textmark_content = "Textmark";

//textmark width (int)
$image1->textmark_width = 200;

//textmark height (int)
$image1->textmark_height = 50;

//textmark repeat (boolean)
$image1->textmark_repeat = false;

//watermark image horizontal alignment (string)
$image1->textmark_align_horizontal = '';
- right		(align to right and repeat from right) (default)
- center	(align to center and repeat from center)
- left		(align to left and repeat from left)

//watermark image vertical alignment (string)
$image1->textmark_align_vertical = '';
- top		(align to top and repeat from top)
- center	(align to center and repeat from center)
- bottom	(align to bottom and repeat from bottom) (default)

//textmark font size (int)
$image1->textmark_fontsize = 20;

//textmark rotation (int)
$image1->textmark_angle = 0;

//textmark color (string)
$image1->textmark_color = 'random,random,random';
- 'R,G,B' (default: 'random' with generate any value)
```