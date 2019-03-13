<?php

namespace In2code\In2studyfinder\ViewHelpers\Widget;

/*                                                                        *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use In2code\In2studyfinder\ViewHelpers\Widget\Controller\PaginateController;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;

/**
 * This ViewHelper renders a Pagination of objects.
 *
 * = Examples =
 *
 * <code title="required arguments">
 * <f:widget.paginate objects="{blogs}" as="paginatedBlogs">
 * use {paginatedBlogs} as you used {blogs} before, most certainly inside
 * a <f:for> loop.
 * </f:widget.paginate>
 * </code>
 *
 * <code title="full configuration">
 * <f:widget.paginate objects="{blogs}" as="paginatedBlogs" configuration="{itemsPerPage: 5, insertAbove: 1,
 * insertBelow: 0, maximumNumberOfLinks: 10}"> use {paginatedBlogs} as you used {blogs} before, most certainly inside a
 * <f:for> loop.
 * </f:widget.paginate>
 * </code>
 *
 * = Performance characteristics =
 *
 * In the above examples, it looks like {blogs} contains all Blog objects, thus
 * you might wonder if all objects were fetched from the database.
 * However, the blogs are NOT fetched from the database until you actually use them,
 * so the paginate ViewHelper will adjust the query sent to the database and receive
 * only the small subset of objects.
 * So, there is no negative performance overhead in using the Paginate Widget.
 *
 * @api
 */
class PaginateViewHelper extends AbstractWidgetViewHelper
{
    /**
     * @var PaginateController
     */
    protected $controller;

    /**
     * @param PaginateController $controller
     */
    public function injectPaginateController(PaginateController $controller)
    {
        $this->controller = $controller;
    }


    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('objects', 'mixed', 'Object', true);
        $this->registerArgument('as', 'string', 'as', true);
        $this->registerArgument(
            'configuration',
            'array',
            'configuration',
            false,
            ['itemsPerPage' => 10, 'insertAbove' => false, 'insertBelow' => true, 'maximumNumberOfLinks' => 99]
        );
    }

    /**
     * @return string
     */
    public function render()
    {
        $objects = $this->arguments['objects'];

        if (!($objects instanceof QueryResultInterface || $objects instanceof ObjectStorage || is_array($objects))) {
            throw new \UnexpectedValueException(
                'Supplied file object type ' . get_class(
                    $objects
                ) . ' must be QueryResultInterface or ObjectStorage or be an array.', 1454510731
            );
        }

        return $this->initiateSubRequest();
    }
}
