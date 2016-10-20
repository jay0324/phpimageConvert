<?php
include("class.imageConvert.php");
$image1 = new PHP_img_converter;
$image1->filename = 'img/test.jpg';
$image1->watermark_state = true;
$image1->watermark_source = 'img/mark24.png';

echo $image1->generate();
?>