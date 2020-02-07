<?php

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * StudyCourse
 */
class GlobalData extends AbstractEntity implements GlobalDataInterface
{
    const TABLE = 'tx_in2studyfinder_domain_model_globaldata';

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * @var bool
     */
    protected $defaultPreset = false;

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return boolean
     */
    public function isDefaultPreset()
    {
        return $this->defaultPreset;
    }

    /**
     * @param boolean $defaultPreset
     */
    public function setDefaultPreset($defaultPreset)
    {
        $this->defaultPreset = $defaultPreset;
    }
}
