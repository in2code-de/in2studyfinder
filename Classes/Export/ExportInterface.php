<?php

namespace In2code\In2studyfinder\Export;

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;

interface ExportInterface
{
    public function getFileType();
    public function export(array $exportRecords, ExportConfiguration $exportConfiguration);
}
