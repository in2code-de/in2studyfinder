<?php

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * CourseLanguage
 */
class CourseLanguage extends AbstractEntity implements CourseLanguageInterface
{
    const TABLE = 'tx_in2studyfinder_domain_model_courselanguage';

    /**
     * language
     *
     * @var string
     */
    protected $language = '';

    /**
     * Returns the language
     *
     * @return string language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the language
     *
     * @param string $language
     * @return void
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Returns the option Field
     *
     * @return string title
     */
    public function getOptionField()
    {
        return $this->getLanguage();
    }
}
