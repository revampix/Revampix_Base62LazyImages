<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@revampix.com>
 * @website     http://www.revampix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Revampix_Base62LazyImages_Helper_Data extends Mage_Core_Helper_Abstract
{

    /** @var int */
    private $_base64BlocksPerRequest;

    const SPACER_BASE64 = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    /** @var Mage_Catalog_Helper_Image */
    private $_imageHelper;

    /** @var Revampix_Base62LazyImages_Helper_Config */
    private $_configHelper;

    public function __construct()
    {
        $this->_imageHelper  = Mage::helper('catalog/image');
        $this->_configHelper = Mage::helper('revampix_base62lazyimages/config');

        $this->_base64BlocksPerRequest = $this->_configHelper->getNumberOfBase64BlocksPerRequest();
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_configHelper->isEnabled();
    }

    /**
     * @todo: Maybe I need to add and option for retina display dimensions
     *
     * @param Mage_Catalog_Model_Product $_product
     * @param int|string $originalResizeDimensions
     * @param int|string $base64PlaceholderDimensions
     * @param array $pictureSourceConfig
     *
     * @return string
     */
    public function getBase62LazyImages(Mage_Catalog_Model_Product $_product, $originalResizeDimensions, $base64PlaceholderDimensions, array $pictureSourceConfig = array())
    {
        $cacheData = Mage::app()->loadCache(Revampix_Base62LazyImages_Helper_Config::IMAGE_BLOCK_CACHE_KEY_PREFIX . $_product->getId());

        //In case we have already resized and cached the Image block we just return cached result and do nothing else
        if ($cacheData) {
            $this->_base64BlocksPerRequest--;
            return $cacheData;
        }

        $dataUriSource = 'default';

        /**
         * In this if/else block we prepare the low quality base64 version
         * and if we reach a limit of resizes per request then we serve a transparent spacer
         */
        if ($this->_base64BlocksPerRequest > 0) {
            $url = $this->_imageHelper->init($_product, 'small_image')
                ->keepFrame(false)
                ->setQuality($this->_configHelper->getLowResPlaceholderQuality())
                ->resize($base64PlaceholderDimensions)
                ->__toString();

            $fileName = $this->_getFilePathFromUrl($url);

            $dataUriBase64 = $this->_getDataUriFromFile($fileName);

            $dataUriSource = 'original_image';
            $this->_base64BlocksPerRequest--;
        } else {
            //In case we reached the maximum allowed resizes per HTTP request we serve transparent gif file
            $dataUriSource = 'base64spacer';
            $dataUriBase64 = self::SPACER_BASE64;
        }

        $originalSrc = $this->_imageHelper->init($_product, 'small_image')
            ->keepFrame(false)
            ->resize($originalResizeDimensions)
            ->__toString();

        /** @var Revampix_Base62LazyImages_Block_Picture $pictureBlock */
        $pictureBlock = Mage::app()
            ->getLayout()
            ->createBlock('revampix_base62lazyimages/picture', '', array('product' => $_product, 'data_uri_source' => $dataUriSource));

        $pictureBlock->setBase64Placeholder($dataUriBase64)
            ->setOriginalFile($originalSrc)
            ->setPictureTagSources($this->_generatePictureSources($_product, $pictureSourceConfig));

        return $pictureBlock->toHtml();
    }

    /**
     * @param string $url
     * @return string
     */
    protected function _getFilePathFromUrl($url)
    {
        $mediaUrl  = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $mediaPath = Mage::getBaseDir('media') . DS;

        return str_ireplace($mediaUrl, $mediaPath, $url);
    }

    /**
     * Creates data URI string of an image file for image embedding.
     *
     * Taken from Luca's comment: https://css-tricks.com/snippets/php/create-data-uris/
     *
     * @param  string $file
     * @return string
     */
    protected function _getDataUriFromFile($file)
    {
        $contents = file_get_contents($file);
        $base64 = base64_encode($contents);
        $imageType = exif_imagetype($file);
        $mime = image_type_to_mime_type($imageType);

        return "data:$mime;base64,$base64";
    }

    /**
     * @param Mage_Catalog_Model_Product $_product
     * @param array $pictureSourceConfig
     * @return array
     */
    protected function _generatePictureSources(Mage_Catalog_Model_Product $_product ,array $pictureSourceConfig)
    {
        $pictureSources = array();

        foreach ($pictureSourceConfig as $sourceConfig)
        {
            $pictureSource = array();

            $pictureSource['set'] = $this->_imageHelper->init($_product, 'small_image')
                ->keepFrame(false)
                ->resize($sourceConfig['resize'])
                ->__toString();

            $pictureSource['media'] = $sourceConfig['media'];

            $pictureSources[] = $pictureSource;
        }

        return $pictureSources;
    }

}