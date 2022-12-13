# yellow-picture
Extension for use the picture tag with Datenstrom Yellow

## How to use the picture extension

* **Work only with `.jpg` at moment.**
* You have to change ImageUploadWidthMax and ImageUploadHeightMax to the double from that you need. (ImageUploadWidthMax: 2600 ImageUploadHeightMax: 2600)
* The image extension is required even if only the picture extension is used.
* Upload the image to your website as usual.
* Replace image with picture in the shortcode.
* Save your post.

## Examples

If image extension is installed and you use :

    [image pizza02.jpg "your alt text" imageclass]

You have to change image to picture:

    [picture pizza02.jpg "your alt text" imageclass]

    
This will be the result:
    
    <picture>
        <source type="image/jpeg" srcset="/media/images/retina-desktop-pizza02.jpg" media="(min-width: 576px) and (-webkit-min-device-pixel-ratio: 2), (min-width: 576px) and (min-resolution: 192dpi)">
        <source type="image/jpeg" srcset="/media/images/desktop-pizza02.jpg" media="(min-width: 576px)">
        <source type="image/jpeg" srcset="/media/images/mobile-pizza02.jpg" media="(min-width: 0px)">
        <img src="/media/images/desktop-pizza02.jpg" width="2600" height="1950" alt="your alt text" title="your alt text" class="imageclass" />
    </picture>
  
## Additional Information and my thoughts

**I am not a professional coder, everyone can see that immediately from the source code. I would be glad if someone with more knowledge would improve the code :)**

* three image files are created. `retina-desktop-pizza02.jpg`, `desktop-pizza02.jpg` and `mobile-pizza02.jpg`.
* current mobile phones all use retina displays. There is no need for small images.
* all devices larger than mobile phones most use wifi. Bandwidth is usually not a problem here.
* `576px` is the smallest breakpoint of Bootstrap. This value can be changed in `Yellow-system.ini (PictureMobileBreakpoint: 576)`


## Installation

[Download extension](https://github.com/PetersOtto/yellow-picture/archive/refs/heads/main.zip) and copy zip file into your `system/extensions` folder. Right click if you use Safari.

## Developer

PetersOtto. [Get help](https://datenstrom.se/yellow/help/)
