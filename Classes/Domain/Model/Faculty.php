<?php

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Faculty
 */
class Faculty extends AbstractEntity implements FacultyInterface
{
    const TABLE = 'tx_in2studyfinder_domain_model_faculty';

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

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
     * Returns the option Field
     *
     * @return string title
     */
    public function getOptionField()
    {
        return $this->getTitle();
    }
}
