<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class GlobalDataRepository extends AbstractRepository
{
    public function findDefaultPreset(): ?object
    {
        return $this->getDefaultQuery()->execute()->getFirst();
    }

    public function countDefaultPreset(): int
    {
        return $this->getDefaultQuery()->execute()->count();
    }

    protected function getDefaultQuery(): QueryInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->matching(
            $query->logicalAnd(
                $query->equals('default_preset', true),
                $query->equals('deleted', 0)
            )
        );
        return $query;
    }
}
