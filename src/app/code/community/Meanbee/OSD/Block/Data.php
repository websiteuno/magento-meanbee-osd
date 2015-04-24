<?php

/**
 * Class Meanbee_OSD_Block_Data
 *
 * @method $this setStructuredData($data)
 * @method array getStructuredData()
 */
class Meanbee_OSD_Block_Data extends Mage_Core_Block_Abstract
{

    /** @var Meanbee_OSD_Helper_Config $config */
    protected $config;

    protected function _construct()
    {
        parent::_construct();

        $this->config = Mage::helper("meanbee_osd/config");

        $this->buildStructuredData();
    }

    /**
     * Check if the block is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config->isEnabled();
    }

    /**
     * Get the structured data array as a JSON string.
     *
     * @return string
     */
    public function getStructuredDataAsJson()
    {
        return Zend_Json::encode($this->getStructuredData());
    }

    /**
     * Build the structured data array, replacing any existing
     * structured data set on the block.
     *
     * @return $this
     */
    protected function buildStructuredData()
    {
        $data = array(
            "@context" => "http://schema.org",
            "@type"    => "Organization",
            "url"      => $this->getConfig()->getOrganisationUrl()
        );

        if ($logo = $this->getConfig()->getOrganisationLogoUrl()) {
            $data["logo"] = $logo;
        }

        if ($contacts = $this->getContactsList()) {
            $data["contactPoint"] = $contacts;
        }

        Mage::dispatchEvent("meanbee_osd_after_build_structured_data", array("data" => $data));

        $this->setStructuredData($data);

        return $this;
    }

    /**
     * Get the structured data for organisation contacts.
     *
     * @return array
     */
    protected function getContactsList()
    {
        $contacts = array();

        if ($customer_support = $this->getConfig()->getCustomerSupportContact()) {
            $contacts[] = array(
                "@type"       => "ContactPoint",
                "contactType" => "customer support",
                "telephone"   => $customer_support
            );
        }

        if ($sales = $this->getConfig()->getSalesContact()) {
            $contacts[] = array(
                "@type"       => "ContactPoint",
                "contactType" => "sales",
                "telephone"   => $sales
            );
        }

        return $contacts;
    }

    protected function _toHtml()
    {
        if ($this->isEnabled() && $data = $this->getStructuredDataAsJson()) {
            return sprintf('<script type="application/ld+json">%s</script>', $data);
        }

        return "";
    }

    /**
     * Get the config helper.
     *
     * @return Meanbee_OSD_Helper_Config
     */
    protected function getConfig()
    {
        return Mage::helper("meanbee_osd/config");
    }

}
