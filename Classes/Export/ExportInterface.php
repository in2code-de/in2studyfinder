<?php

namespace In2code\In2studyfinder\Export;

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;

interface ExportInterface
{
    /**
     * @return string the target filepath and filename of the created file
     */
    public function export(ExportConfiguration $exportConfiguration): string;
}
