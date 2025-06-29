<?php
namespace App\DTO\RecordLabel;

final class RecordLabelFilterDto
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $limit = 10
    ) {}
}