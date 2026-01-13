<?php

declare(strict_types=1);

namespace Aoding9\Laravel\Xlswriter\Export\Tests\Fixtures;

use Aoding9\Laravel\Xlswriter\Export\BaseExport;

class TestExport extends BaseExport
{
    public $header = [
        ['column' => 'a', 'width' => 8, 'name' => 'ID'],
        ['column' => 'b', 'width' => 15, 'name' => 'Name'],
        ['column' => 'c', 'width' => 20, 'name' => 'Email'],
    ];

    public $fileName = 'test_export';

    public $tableTitle = 'Test Export';

    public $useTitle = false;

    public $debug = false;

    public function eachRow($row): array
    {
        return [
            $row['id'],
            $row['name'],
            $row['email'],
        ];
    }
}
