<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@revampix.com>
 * @website     http://www.revampix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Revampix_Base62LazyImages_Helper_Config extends Mage_Core_Helper_Abstract
{

    const ENABLED_XML_PATH                              = 'revampix_base62lazyimages/module/enabled';
    const USE_LOW_RESOLUTION_BASE64_IMAGES_XML_PATH     = 'revampix_base62lazyimages/module/use_low_quality_placeholders';
    const LOW_RESOLUTION_BASE64_IMAGES_QUALITY_XML_PATH = 'revampix_base62lazyimages/module/placeholders_quality';
    const NUMBER_OF_BASE64_BLOCKS_PER_REQUEST_XML_PATH  = 'revampix_base62lazyimages/module/base64_blocks_per_request';

    const IMAGE_BLOCK_CACHE_KEY_PREFIX                  = 'REVAMPIX_BASE64LAZY_IMAGE_PRODUCT_ID_';

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig(self::ENABLED_XML_PATH);
    }

//    /**
//     * @return int
//     */
//    public function shouldUseResPlaceholders()
//    {
//        return Mage::getStoreConfig(self::USE_LOW_RESOLUTION_BASE64_IMAGES_XML_PATH);
//    }

    /**
     * @return int
     */
    public function getLowResPlaceholderQuality()
    {
        return (int) Mage::getStoreConfig(self::LOW_RESOLUTION_BASE64_IMAGES_QUALITY_XML_PATH);
    }

    /**
     * @return int
     */
    public function getNumberOfBase64BlocksPerRequest()
    {
        return (int) Mage::getStoreConfig(self::NUMBER_OF_BASE64_BLOCKS_PER_REQUEST_XML_PATH);
    }

}