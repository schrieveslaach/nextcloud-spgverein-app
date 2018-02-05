<?php

namespace OCA\SPGVerein\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class Section implements IIconSection {

    /** @var IL10N */
    private $l;

    /** @var IURLGenerator */
    private $url;

    /**
     * @param IURLGenerator $url
     * @param IL10N $l
     */
    public function __construct(IURLGenerator $url, IL10N $l) {
        $this->url = $url;
        $this->l = $l;
    }

    /**
     * returns the ID of the section. It is supposed to be a lower case string,
     * e.g. 'ldap'
     *
     * @returns string
     */
    public function getID() {
        return 'spgverein';
    }

    /**
     * returns the translated name as it should be displayed, e.g. 'LDAP / AD
     * integration'. Use the L10N service to translate it.
     *
     * @return string
     */
    public function getName() {
        return $this->l->t('SPG Verein');
    }

    /**
     * @return int whether the form should be rather on the top or bottom of
     * the settings navigation. The sections are arranged in ascending order of
     * the priority values. It is required to return a value between 0 and 99.
     *
     * E.g.: 70
     */
    public function getPriority() {
        return 25;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon() {
        return $this->url->imagePath('spgverein', 'app_gray.svg');
    }

}
