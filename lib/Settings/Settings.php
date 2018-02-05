<?php

namespace OCA\SPGVerein\Settings;

use OCA\User_LDAP\Configuration;
use OCA\User_LDAP\Helper;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IL10N;
use OCP\Settings\ISettings;

class Settings implements ISettings {

    /** @var IL10N */
    private $l;

    /**
     * @param IL10N $l
     */
    public function __construct(IL10N $l) {
        $this->l = $l;
    }

    /**
     * @return TemplateResponse
     */
    public function getForm() {
        $helper = new Helper(\OC::$server->getConfig());
        $prefixes = $helper->getServerConfigurationPrefixes();
        $hosts = $helper->getServerConfigurationHosts();

        $parameters['serverConfigurationPrefixes'] = $prefixes;
        $parameters['serverConfigurationHosts'] = $hosts;

        // assign default values
        $config = new Configuration('', false);
        $defaults = $config->getDefaults();
        foreach ($defaults as $key => $default) {
            $parameters[$key . '_default'] = $default;
        }

        return new TemplateResponse('spgverein', 'admin', $parameters);
    }

    /**
     * @return string the section ID, e.g. 'sharing'
     */
    public function getSection() {
        return 'spgverein';
    }

    /**
     * @return int whether the form should be rather on the top or bottom of
     * the admin section. The forms are arranged in ascending order of the
     * priority values. It is required to return a value between 0 and 100.
     *
     * E.g.: 70
     */
    public function getPriority() {
        return 5;
    }

}
