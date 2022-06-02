<?php

namespace In2code\In2studyfinder\Controller;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * AbstractController
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class AbstractController extends ActionController implements LoggerAwareInterface
{
    use LoggerAwareTrait;
}
