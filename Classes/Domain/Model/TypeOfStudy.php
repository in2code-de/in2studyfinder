<?php

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * TypeOfStudy
 */
class TypeOfStudy extends AbstractEntity implements TypeOfStudyInterface
{
    const TABLE = 'tx_in2studyfinder_domain_model_typeofstudy';

    /**
     * type
     *
     * @var string
     */
    protected $type = '';

    /**
     * Returns the type
     *
     * @return string type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the option Field
     *
     * @return string type
     */
    public function getOptionField()
    {
        return $this->getType();
    }
}
