<?php

namespace In2code\In2studyfinder\Export;

use In2code\In2studyfinder\Export\Configuration\ExportConfiguration;

interface ExportInterface
{
    public function export(ExportConfiguration $exportConfiguration);
}
