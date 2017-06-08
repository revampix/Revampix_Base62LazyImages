# Revampix_Base62LazyImages
Magento 1 module that allows us to show important images faster and lazy load the rest of the images. The module generates base64 version of the important images and embed them with data URI.

You can read about the story of the module at my blog: <a href="https://www.revampix.com/2017/06/08/instant-render-of-important-images-and-lazy-load-of-the-rest/">https://www.revampix.com/2017/06/08/instant-render-of-important-images-and-lazy-load-of-the-rest</a>

You also can configure how many images to be embeded in base64 and in what quality they be generated.

You can configure the module if in Magento admin you go to: System -> Configuration -> Base64 LazyImages:

<img style="border: 1px solid #ccc" src="https://www.revampix.com/wp-content/uploads/2017/06/Base64-lazy-images-admin-config-2.png" />

You also have to use part of extension code inside your template files. You may follow this code sample but keept in mind that you may need to change some values that are appropriate for your theme:

```
<a href="<?php echo $_product->getProductUrl() ?>" title="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" class="product-image">
    <?php
        // Code we need to inject in templates in order to use
        /** @var Revampix_Base62LazyImages_Helper_Data $lazyLoadHelper */
        $lazyLoadHelper = Mage::helper('revampix_base62lazyimages');

        if ($lazyLoadHelper->isEnabled()) :
            $pictureSourcesConfig = array(
                array(
                    'resize' => '136',
                    'media' => '(max-width: 360px)',
                ),
                array(
                    'resize' => '180',
                    'media' => '(max-width: 640px)',
                )
            );

            echo $lazyLoadHelper->getBase62LazyImages($_product, 280, 220, $pictureSourcesConfig);

        // If the module Revampix_Base62LazyImages is is disabled we fallback to default RWD IMG tag
        else :
    ?>
    <?php $_imgSize = 210; ?>
    <img id="product-collection-image-<?php echo $_product->getId(); ?>"
         src="<?php echo $this->helper('catalog/image')->init($_product, 'small_image')->resize($_imgSize); ?>"
         alt="<?php echo $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true) ?>" />
    <?php endif; ?>
</a>
```
