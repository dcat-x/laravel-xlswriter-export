<?php

declare(strict_types=1);

arch('source files use strict types')
    ->expect('Aoding9\Laravel\Xlswriter\Export')
    ->not->toUse(['die', 'dd', 'dump', 'ray', 'var_dump', 'print_r']);

arch('test files use strict types')
    ->expect('Aoding9\Laravel\Xlswriter\Export\Tests')
    ->toUseStrictTypes();
