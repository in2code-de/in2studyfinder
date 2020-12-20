<?php

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * StartOfStudy
 */
class StartOfStudy extends AbstractEntity implements StartOfStudyInterface
{
    const TABLE = 'tx_in2studyfinder_domain_model_startofstudy';

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * startDate
     *
     * @var string
     */
    protected $startDate = '';

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
     * Returns the startDate
     *
     * @return string $startDate
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Sets the startDate
     *
     * @param string $startDate
     * @return void
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * Returns the option Field
     *
     * @return string title
     */
    public function getOptionField()
    {
        return $this->getTitle();
    }
}
