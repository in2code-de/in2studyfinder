<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

interface CourseLanguageInterface
{
    public function getLanguage(): string;

    public function setLanguage(string $language);

    public function getOptionField(): string;
}
