<?php
// Picture extension, https://github.com/PetersOtto/yellow-picture

class YellowPicture {
    const VERSION = "0.0.2";
    public $yellow;         // access to API
    
    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("pictureMobileBreakpoint", "576");
    }

    // Handle page content of shortcut
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;
        if ($name=="picture" && ($type=="inline" || $type=="block")) {
            if ($this->yellow->extension->isExisting("image")) {
                list($name, $alt, $style) = $this->yellow->toolbox->getTextArguments($text);
                $path = $this->yellow->lookup->findMediaDirectory("coreImageLocation");
                list($width, $height, $imageType) = getimagesize($path.$name);
                $type = $this->imageType($imageType);
                $imageUploadWidthMax = $this->yellow->system->get("imageUploadWidthMax");
                if ($type=="gif" || $type=="jpg" || $type=="png") {
                    if ($width >= $imageUploadWidthMax){
                        $path = $this->yellow->system->get("coreServerBase"). "/" .$this->yellow->lookup->findMediaDirectory("coreImageLocation");
                        $nameRetinaDesktop = "retina-desktop-" . $name;
                        $srcRetinaDesktop = $path . $nameRetinaDesktop;
                        $nameDesktop = "desktop-" . $name;
                        $srcDesktop = $path . $nameDesktop;
                        $nameMobile =  "mobile-" . $name;
                        $srcMobile = $path.$nameMobile;
                        $mobileBreakpoint = $this->yellow->system->get("pictureMobileBreakpoint");
                        $this->convertImage($name, $nameRetinaDesktop, $nameDesktop, $nameMobile, $width, $height, $type);
                        $output = "<picture>\n";
                        $output .= "<source srcset=\"" . htmlspecialchars($srcRetinaDesktop) . "\" media=\"(min-width: {$mobileBreakpoint}px) and (-webkit-min-device-pixel-ratio: 2), (min-width: {$mobileBreakpoint}px) and (min-resolution: 192dpi)\">\n";
                        $output .= "<source srcset=\"" . htmlspecialchars($srcDesktop) . "\" media=\"(min-width: {$mobileBreakpoint}px)\">\n";
                        $output .= "<source srcset=\"" . htmlspecialchars($srcMobile) . "\" media=\"(min-width: 0px)\">\n";
                        $output .= "<img src=\"" . htmlspecialchars($srcDesktop) . "\"";
                            if ($width && $height) {
                                $output .= " width=\"" . htmlspecialchars($width) . "\" height=\"" . htmlspecialchars($height) . "\"";
                            }
                            if (!is_string_empty($alt)) {
                                $output .= " alt=\"" . htmlspecialchars($alt) . "\" title=\"" . htmlspecialchars($alt) . "\"";
                            }
                            if (!is_string_empty($style)) {
                                $output .= " class=\"" . htmlspecialchars($style) . "\"";
                            }
                        $output .= " />\n";
                        $output .= "</picture>";   
                        } else {
                            $page->error(500, "Attention! Image width is to small. Minimum {$imageUploadWidthMax}px are needed");
                        }
                    } else {
                        $page->error(500, "Attention! Only .jpg, .gif and .png allowed.");
                    }
            } else {
                $page->error(500, "Attention! Picture requires 'image' extension!");
            }
        }
        return $output;
    }

    // convert image size and sharpen the image
    function convertImage($name, $nameRetinaDesktop, $nameDesktop, $nameMobile, $widthInput, $heightInput, $type) {
        $path = $this->yellow->lookup->findMediaDirectory("coreImageLocation");
        $srcOriginal = $path . $name;
        $srcRetinaDesktop =  $path . $nameRetinaDesktop;
        $srcDesktop =  $path . $nameDesktop;
        $srcMobile =  $path . $nameMobile;
        $widthOutputRetinaDesktop = $widthInput;
        $widthOutputDesktop = ($widthInput / 2); 
        $mobileBreakpoint = $this->yellow->system->get("pictureMobileBreakpoint");
        $widthOutputMobile = $mobileBreakpoint * 2;  
        $this->resizeAndSharpenImage($srcOriginal, $srcRetinaDesktop, $widthInput, $heightInput, $widthOutputRetinaDesktop, $type);
        $this->resizeAndSharpenImage($srcOriginal, $srcDesktop, $widthInput, $heightInput, $widthOutputDesktop, $type);
        $this->resizeAndSharpenImage($srcOriginal, $srcMobile, $widthInput, $heightInput, $widthOutputMobile, $type);
    }

    // sharpen the image
    public function resizeAndSharpenImage($srcOriginal, $srcChange, $widthInput, $heightInput, $widthOutput, $type) {
        $heightInput = $widthInput * ($heightInput / $widthInput);
        $heightOutput = $widthOutput * ($heightInput / $widthInput);
        if (!file_exists($srcChange)) {
            // resize, sharpen and save mobile image
            $image = $this->yellow->extension->get("image")->loadImage($srcOriginal, $type);
            $image = $this->yellow->extension->get("image")->resizeImage($image, $widthInput, $heightInput, $widthOutput, $heightOutput);
            if ($type=="jpg") {
                $image = $this->sharpenImage($image);
            }
            $this->yellow->extension->get("image")->saveImage($image, $srcChange, $type, $this->yellow->system->get("imageUploadJpgQuality"));
        }
    }

    // get type of image
    public function imageType($imageType) {
        if ($imageType == 1) {
            return "gif";
        }
        if ($imageType == 2) {
            return "jpg";
        }
        if ($imageType == 3) {
            return "png";
        }
    }

    // sharpen image
    public function sharpenImage($image) {
        $sharpen = array([0, -2, 0], [-2, 11, -2], [0, -2, 0]);
        imageconvolution($image, $sharpen, 3, 0);
        return $image;
    }
}
