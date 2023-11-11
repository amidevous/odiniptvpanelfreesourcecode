<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(32767);
$rPath = "./icons/";
$rURL = $_GET["url"];
$rMax = $_GET["max"];
header("Content-Type: image/png");
if ($rURL && $rMax) {
    list($rExtension) = explode(".", strtolower(pathinfo($rURL)["extension"]));
    if ($rExtension == "png") {
        $rImagePath = $rPath . md5($rURL) . "_" . $rMax . ".png";
        if (!file_exists($rImagePath)) {
            list($rWidth, $rHeight) = getimagesize($rURL);
            $rImageSize = getimagesizekeepaspectratio($rURL, $rMax, $rMax);
            if ($rImageSize["width"] && $rImageSize["height"]) {
                $rImageP = imagecreatetruecolor($rImageSize["width"], $rImageSize["height"]);
                $rImage = imagecreatefrompng($rURL);
                imagealphablending($rImageP, false);
                imagesavealpha($rImageP, true);
                imagecopyresampled($rImageP, $rImage, 0, 0, 0, 0, $rImageSize["width"], $rImageSize["height"], $rWidth, $rHeight);
                imagepng($rImageP, $rImagePath);
            }
        }
        if (file_exists($rImagePath)) {
            echo file_get_contents($rImagePath);
            exit;
        }
    }
}
$rImage = imagecreatetruecolor(1, 1);
imagesavealpha($rImage, true);
imagefill($rImage, 0, 0, imagecolorallocatealpha($rImage, 0, 0, 0, 127));
imagepng($rImage);
function getImageSizeKeepAspectRatio($imageUrl, $maxWidth, $maxHeight)
{
    $imageDimensions = getimagesize($imageUrl);
    list($imageWidth, $imageHeight) = $imageDimensions;
    $imageSize["width"] = $imageWidth;
    $imageSize["height"] = $imageHeight;
    if ($maxWidth < $imageWidth || $maxHeight < $imageHeight) {
        if ($imageHeight < $imageWidth) {
            $imageSize["height"] = floor($imageHeight / $imageWidth * $maxWidth);
            $imageSize["width"] = $maxWidth;
        } else {
            $imageSize["width"] = floor($imageWidth / $imageHeight * $maxHeight);
            $imageSize["height"] = $maxHeight;
        }
    }
    return $imageSize;
}

?>