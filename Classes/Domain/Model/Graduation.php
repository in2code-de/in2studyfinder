<?php

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * CourseLanguage
 */
class Graduation extends AbstractEntity implements GraduationInterface
{
    const TABLE = 'tx_in2studyfinder_domain_model_graduation';

    /**
     * language
     *
     * @var string
     */
    protected $title = '';

    /**
     * Returns the graduation
     *
     * @return string graduation
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the language
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
