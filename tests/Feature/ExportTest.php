<?php

declare(strict_types=1);

use Aoding9\Laravel\Xlswriter\Export\Tests\Fixtures\TestExport;

describe('Export Feature', function () {
    beforeEach(function () {
        $this->testData = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['id' => 3, 'name' => 'Bob Wilson', 'email' => 'bob@example.com'],
        ];
    });

    afterEach(function () {
        // Clean up any generated files
        $tmpDir = sys_get_temp_dir();
        $files = glob($tmpDir.'/test_export*.xlsx');
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    });

    describe('store', function () {
        it('creates an xlsx file', function () {
            $export = TestExport::make($this->testData);
            $export->store();

            expect(file_exists($export->filePath))->toBeTrue()
                ->and($export->filePath)->toEndWith('.xlsx');
        });

        it('stores file in temp directory', function () {
            $export = TestExport::make($this->testData);
            $export->store();

            $tmpDir = $export->getTmpDir();

            expect($export->filePath)->toStartWith($tmpDir);
        });
    });

    describe('buildData', function () {
        it('builds data from collection with pagination', function () {
            $export = TestExport::make($this->testData);

            $page1 = $export->buildData(1, 2);
            $page2 = $export->buildData(2, 2);

            expect($page1)->toHaveCount(2)
                ->and($page2)->toHaveCount(1);
        });

        it('returns all data on first page when chunk size exceeds data', function () {
            $export = TestExport::make($this->testData);

            $result = $export->buildData(1, 100);

            expect($result)->toHaveCount(3);
        });
    });

    describe('eachRow', function () {
        it('maps data correctly', function () {
            $export = TestExport::make($this->testData);

            $row = ['id' => 1, 'name' => 'Test', 'email' => 'test@example.com'];
            $result = $export->eachRow($row);

            expect($result)->toBe([1, 'Test', 'test@example.com']);
        });
    });

    describe('chunk processing', function () {
        it('tracks completed count', function () {
            $export = TestExport::make($this->testData);
            $export->setChunkSize(2);
            $export->store();

            expect($export->completed)->toBe(3);
        });
    });

    describe('header data', function () {
        it('sets header data without title', function () {
            $export = TestExport::make($this->testData);
            $export->setUseTitle(false);
            $export->setHeaderData();

            expect($export->headerData)->toHaveCount(1)
                ->and($export->headerData->first())->toBe(['ID', 'Name', 'Email']);
        });

        it('sets header data with title', function () {
            $export = TestExport::make($this->testData);
            $export->setUseTitle(true);
            $export->setHeaderData();

            expect($export->headerData)->toHaveCount(2)
                ->and($export->headerData->first())->toBe(['Test Export']);
        });
    });

    describe('swoole mode', function () {
        it('can enable swoole mode', function () {
            $export = TestExport::make($this->testData);
            $export->useSwoole = true;

            expect($export->useSwoole())->toBeTrue();
        });

        it('defaults to non-swoole mode', function () {
            $export = TestExport::make($this->testData);

            expect($export->useSwoole())->toBeFalse();
        });
    });

    describe('delete after download', function () {
        it('can configure delete after download', function () {
            $export = TestExport::make($this->testData);

            $export->shouldDelete(false);

            expect($export->shouldDelete)->toBeFalse();
        });

        it('defaults to delete after download', function () {
            $export = TestExport::make($this->testData);

            expect($export->shouldDelete)->toBeTrue();
        });
    });

    describe('merge cells after insert', function () {
        it('returns empty array when title is disabled', function () {
            $export = TestExport::make($this->testData);
            $export->setUseTitle(false);
            $export->setHeaderLen();
            $export->setEnd();

            $result = $export->mergeCellsAfterInsertData();

            expect($result)->toBe([]);
        });

        it('returns title merge config when title is enabled', function () {
            $export = TestExport::make($this->testData);
            $export->setUseTitle(true);
            $export->setHeaderLen();
            $export->setEnd();
            $export->setFileHandle();
            $export->setTitleStyle();

            $result = $export->mergeCellsAfterInsertData();

            expect($result)->toHaveCount(1)
                ->and($result[0]['range'])->toBe('A1:C1')
                ->and($result[0]['value'])->toBe('Test Export');
        });
    });
});
