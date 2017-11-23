<?php

namespace In2code\In2studyfinder\DataProvider;

interface ExportProviderInterface
{

    public function export(array $exportRecords, ExportConfiguration $exportConfiguration);
}
