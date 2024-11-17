<?php
/*
    #####################################
            Resize_image v1.0
              by Asif Agaria
    #####################################

*/

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to resize an image from a URL
function resizeImage($url, $width, $height, $quality) {
    // Get the image extension
    $imageInfo = getimagesize($url);
    if (!$imageInfo) {
        die("Invalid image URL.");
    }

    $mime = $imageInfo['mime'];
    
    // Create a new image from the given URL based on its type
    switch ($mime) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($url);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($url);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($url);
            break;
        default:
            die("Unsupported image type.");
    }

    // Get original dimensions
    list($originalWidth, $originalHeight) = $imageInfo;

    // Create a new true color image
    $thumb = imagecreatetruecolor($width, $height);

    // Resize the image
    imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

    // Set the content type header
    header('Content-Type: ' . $mime);
    
    // Output the resized image
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($thumb, null, $quality); // Set quality for JPEG
            break;
        case 'image/png':
            imagepng($thumb, null, 9 - ($quality / 10)); // Adjust compression level for PNG
            break;
        case 'image/gif':
            imagegif($thumb);
            break;
    }

    // Free up memory
    imagedestroy($sourceImage);
    imagedestroy($thumb);
}

// Get URL, width, height, and quality from query parameters
if (isset($_GET['url']) && isset($_GET['width']) && isset($_GET['height']) && isset($_GET['quality'])) {
    $url = $_GET['url'];
    $width = (int)$_GET['width'];
    $height = (int)$_GET['height'];
    $quality = (int)$_GET['quality'];

    // Ensure quality is between 0 and 100
    $quality = max(0, min(100, $quality));

    resizeImage($url, $width, $height, $quality);
} else {
    echo "Please provide image URL, width, height, and quality as query parameters.";
}
?>