<?php

namespace App\Interfaces;

interface MainRepositoryInterface
{
    public function getDashboard();

    public function getReport(string $start, string $end);
}
