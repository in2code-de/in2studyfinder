<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\ViewHelpers\Pagination;

use Closure;
use In2code\In2studyfinder\Exception\NotPaginatableException;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Core\Pagination\PaginatorInterface;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class PaginateViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('objects', 'mixed', 'array or queryresult', true);
        $this->registerArgument('as', 'string', 'new variable name', true);
        $this->registerArgument('itemsPerPage', 'int', 'items per page', false, 10);
        $this->registerArgument('name', 'string', 'unique identification - will take "as" as fallback', false, '');
    }

    /**
     * @throws NotPaginatableException
     */
    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        if ($arguments['objects'] === null) {
            return $renderChildrenClosure();
        }
        $templateVariableContainer = $renderingContext->getVariableProvider();
        $templateVariableContainer->add($arguments['as'], [
            'pagination' => self::getPagination($arguments, $renderingContext),
            'paginator' => self::getPaginator($arguments, $renderingContext),
            'name' => self::getName($arguments),
        ]);
        $output = $renderChildrenClosure();
        $templateVariableContainer->remove($arguments['as']);
        return $output;
    }

    /**
     * @throws NotPaginatableException
     */
    protected static function getPagination(
        array $arguments,
        RenderingContextInterface $renderingContext
    ): PaginationInterface {
        $paginator = self::getPaginator($arguments, $renderingContext);
        return GeneralUtility::makeInstance(SimplePagination::class, $paginator);
    }

    /**
     * @throws NotPaginatableException
     */
    protected static function getPaginator(
        array $arguments,
        RenderingContextInterface $renderingContext
    ): PaginatorInterface {
        if (is_array($arguments['objects'])) {
            $paginatorClass = ArrayPaginator::class;
        } elseif (is_a($arguments['objects'], QueryResultInterface::class)) {
            $paginatorClass = QueryResultPaginator::class;
        } else {
            throw new NotPaginatableException('Given object is not supported for pagination', 1634132847);
        }

        return GeneralUtility::makeInstance(
            $paginatorClass,
            $arguments['objects'],
            self::getPageNumber($arguments, $renderingContext),
            (int)$arguments['itemsPerPage']
        );
    }

    protected static function getPageNumber(array $arguments, RenderingContextInterface $renderingContext): int
    {
        $request = $renderingContext->getRequest();
        if (
            !is_null($request) &&
            $request->hasArgument(self::getName($arguments)) &&
            array_key_exists('currentPage', $request->getArgument(self::getName($arguments)))
        ) {
            return (int)$request->getArgument(self::getName($arguments))['currentPage'];
        }

        return 1;
    }

    protected static function getName(array $arguments): string
    {
        return $arguments['name'] ?: $arguments['as'];
    }
}
