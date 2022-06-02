<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Model of tt_content
 */
class TtContent extends AbstractEntity implements TtContentInterface
{
    public const TABLE = 'tt_content';
}
