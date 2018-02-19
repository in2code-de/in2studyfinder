<?php

namespace In2code\In2studyfinder\Export\ExportTypes;

use In2code\In2studyfinder\Export\AbstractExport;
use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;
use In2code\In2studyfinder\Export\ExportInterface;

class XlsExport extends AbstractExport implements ExportInterface
{
    /**
     * @var string
     */
    protected $fileName = 'export.xls';

    public function export(ExportConfiguration $exportConfiguration)
    {
    }
}
