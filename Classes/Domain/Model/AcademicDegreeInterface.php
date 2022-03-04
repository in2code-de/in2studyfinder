<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

interface AcademicDegreeInterface
{
    public function getDegree(): string;

    public function setDegree(string $degree);

    public function getGraduation(): GraduationInterface;

    public function setGraduation(GraduationInterface $graduation);

    public function getOptionField(): string;
}
