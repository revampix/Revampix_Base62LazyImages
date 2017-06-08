<?php
/**
 * @author      Tsvetan Stoychev <t.stoychev@revampix.com>
 * @website     http://www.revampix.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

class Revampix_Base62LazyImages_Block_Picture extends Mage_Catalog_Block_Product_Abstract
{

    /** @var string */
    protected $_template = 'revampix_base62lazyimages/picture.phtml';

    /** @var array */
    private $_pictureSources = array();

    /** @var string */
    private $_base64Placeholder = '';

    /** @var string */
    private $_originalFile = '';

    protected function _construct()
    {
        $cacheKey = implode('_', array(
            Revampix_Base62LazyImages_Helper_Config::IMAGE_BLOCK_CACHE_KEY_PREFIX,
            $this->getProduct()->getId(),
            $this->getDataUriSource()
        ));

        $this->addData(array(
            'cache_lifetime' => 14400, // 14400 sec = 4h
            'cache_tags'     => array(Mage_Catalog_Model_Product::CACHE_TAG . '_' . $this->getProduct()->getId()),
            'cache_key'      => $cacheKey
        ));
    }

    /**
     * @param string $base64Placeholder
     * @return Revampix_Base62LazyImages_Block_Picture
     */
    public function setBase64Placeholder($base64Placeholder)
    {
        $this->_base64Placeholder = $base64Placeholder;
        return $this;
    }

    /**
     * @return string
     */
    public function getBase64Placeholder()
    {
        return $this->_base64Placeholder;
    }

    /**
     * @param array $pictureSources
     * @return Revampix_Base62LazyImages_Block_Picture
     */
    public function setPictureTagSources(array $pictureSources)
    {
        $this->_pictureSources = $pictureSources;
        return $this;
    }

    /**
     * @return array
     *
     * We are using <picture> tag and we can specify different versions of an image for different screen sizes.
     *
     * This can be done by having something similar to:
     *  <picture>
     *      <source srcset="mdn-logo-wide.png" media="(min-width: 600px)">
     *      <img src="mdn-logo-narrow.png" alt="MDN">
     *  </picture>
     *
     * More about the picture tag you can read here: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/picture
     */
    public function getPictureTagSources()
    {
        return $this->_pictureSources;
    }

    /**
     * @param string $originalFile
     * @return Revampix_Base62LazyImages_Block_Picture
     */
    public function setOriginalFile($originalFile)
    {
        $this->_originalFile = $originalFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalFile()
    {
        return $this->_originalFile;
    }

}