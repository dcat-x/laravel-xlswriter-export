<?php

declare(strict_types=1);

use Aoding9\Laravel\Xlswriter\Export\Tests\Fixtures\TestExport;

describe('BaseExport', function () {
    describe('getColumn', function () {
        it('converts column index to letter for single letters', function () {
            $export = TestExport::make([]);

            expect($export->getColumn(0))->toBe('A')
                ->and($export->getColumn(1))->toBe('B')
                ->and($export->getColumn(25))->toBe('Z');
        });

        it('converts column index to letter for double letters', function () {
            $export = TestExport::make([]);

            expect($export->getColumn(26))->toBe('AA')
                ->and($export->getColumn(27))->toBe('AB')
                ->and($export->getColumn(51))->toBe('AZ')
                ->and($export->getColumn(52))->toBe('BA');
        });

        it('converts column index to letter for triple letters', function () {
            $export = TestExport::make([]);

            expect($export->getColumn(702))->toBe('AAA');
        });

        it('caches column names', function () {
            $export = TestExport::make([]);

            $export->getColumn(0);
            $export->getColumn(0);

            expect($export->columnMap)->toHaveKey(0)
                ->and($export->columnMap[0])->toBe('A');
        });
    });

    describe('getColumnIndexByName', function () {
        it('converts letter to column index for single letters', function () {
            $export = TestExport::make([]);

            expect($export->getColumnIndexByName('A'))->toBe(0)
                ->and($export->getColumnIndexByName('B'))->toBe(1)
                ->and($export->getColumnIndexByName('Z'))->toBe(25);
        });

        it('converts letter to column index for double letters', function () {
            $export = TestExport::make([]);

            expect($export->getColumnIndexByName('AA'))->toBe(26)
                ->and($export->getColumnIndexByName('AB'))->toBe(27)
                ->and($export->getColumnIndexByName('AZ'))->toBe(51)
                ->and($export->getColumnIndexByName('BA'))->toBe(52);
        });

        it('caches column indexes', function () {
            $export = TestExport::make([]);

            $export->getColumnIndexByName('A');
            $export->getColumnIndexByName('A');

            expect($export->columnIndexMap)->toHaveKey('A')
                ->and($export->columnIndexMap['A'])->toBe(0);
        });
    });

    describe('getCellName', function () {
        it('returns correct cell name', function () {
            $export = TestExport::make([]);

            expect($export->getCellName(1, 0))->toBe('A1')
                ->and($export->getCellName(1, 1))->toBe('B1')
                ->and($export->getCellName(10, 2))->toBe('C10');
        });
    });

    describe('configuration', function () {
        it('can set max rows', function () {
            $export = TestExport::make([]);

            $export->setMax(1000);

            expect($export->max)->toBe(1000);
        });

        it('can set chunk size', function () {
            $export = TestExport::make([]);

            $export->setChunkSize(100);

            expect($export->chunkSize)->toBe(100);
        });

        it('can set debug mode', function () {
            $export = TestExport::make([]);

            $export->setDebug(true);

            expect($export->debug)->toBe(true);
        });

        it('can set font family', function () {
            $export = TestExport::make([]);

            $export->setFontFamily('Arial');

            expect($export->fontFamily)->toBe('Arial');
        });

        it('can set row heights', function () {
            $export = TestExport::make([]);

            $export->setHeaderRowHeight(60);
            $export->setTitleRowHeight(80);

            expect($export->headerRowHeight)->toBe(60)
                ->and($export->titleRowHeight)->toBe(80);
        });

        it('can toggle title usage', function () {
            $export = TestExport::make([]);

            $export->setUseTitle(true);

            expect($export->useTitle)->toBe(true);
        });

        it('can toggle freeze panes', function () {
            $export = TestExport::make([]);

            $export->useFreezePanes(true);

            expect($export->useFreezePanes)->toBe(true);
        });

        it('returns $this for method chaining', function () {
            $export = TestExport::make([]);

            $result = $export->setMax(1000)
                ->setChunkSize(100)
                ->setDebug(true)
                ->setFontFamily('Arial')
                ->useFreezePanes(true);

            expect($result)->toBeInstanceOf(TestExport::class);
        });
    });

    describe('data source', function () {
        it('initializes with array data source', function () {
            $data = [
                ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
            ];

            $export = TestExport::make($data);

            expect($export->dataSourceType)->toBe('collection')
                ->and($export->data)->toHaveCount(1);
        });

        it('initializes with collection data source', function () {
            $data = collect([
                ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
            ]);

            $export = TestExport::make($data);

            expect($export->dataSourceType)->toBe('collection')
                ->and($export->data)->toHaveCount(1);
        });

        it('initializes with null data source as other type', function () {
            $export = TestExport::make(null);

            expect($export->dataSourceType)->toBe('other');
        });
    });

    describe('header', function () {
        it('returns header array', function () {
            $export = TestExport::make([]);

            expect($export->getHeader())->toHaveCount(3)
                ->and($export->getHeader()[0]['name'])->toBe('ID');
        });

        it('calculates header length', function () {
            $export = TestExport::make([]);
            $export->setHeaderLen();

            expect($export->headerLen)->toBe(3);
        });

        it('calculates end column', function () {
            $export = TestExport::make([]);
            $export->setHeaderLen();
            $export->setEnd();

            expect($export->end)->toBe('C');
        });
    });

    describe('file properties', function () {
        it('returns filename', function () {
            $export = TestExport::make([]);

            expect($export->getFilename())->toBe('test_export');
        });

        it('returns table title', function () {
            $export = TestExport::make([]);

            expect($export->getTableTitle())->toBe('Test Export');
        });

        it('returns temp directory', function () {
            $export = TestExport::make([]);

            $tmpDir = $export->getTmpDir();

            expect($tmpDir)->toBeString()
                ->and(is_dir($tmpDir))->toBeTrue();
        });

        it('returns store file path with trailing slash', function () {
            $export = TestExport::make([]);

            expect($export->getStoreFilePath())->toEndWith('/');
        });
    });

    describe('index tracking', function () {
        it('starts index at 1', function () {
            $export = TestExport::make([]);

            expect($export->getIndex())->toBe(1);
        });

        it('starts current line at 0', function () {
            $export = TestExport::make([]);

            expect($export->currentLine)->toBe(0)
                ->and($export->getCurrentLine())->toBe(1);
        });
    });
});
