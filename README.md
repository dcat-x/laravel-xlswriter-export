<div align="center">

# Laravel Xlswriter Export

<p>
    <a href="https://github.com/dcat-x/laravel-xlswriter-export/actions"><img src="https://github.com/dcat-x/laravel-xlswriter-export/actions/workflows/tests.yml/badge.svg" alt="Tests"></a>
    <a href="https://packagist.org/packages/dcat-x/laravel-xlswriter-export"><img src="https://poser.pugx.org/dcat-x/laravel-xlswriter-export/v/stable" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/dcat-x/laravel-xlswriter-export"><img src="https://img.shields.io/packagist/dt/dcat-x/laravel-xlswriter-export.svg" alt="Total Downloads"></a>
    <a href="https://www.php.net/"><img src="https://img.shields.io/badge/php-8.2+-59a9f8.svg" alt="PHP Version"></a>
    <a href="https://laravel.com/"><img src="https://img.shields.io/badge/laravel-12+-59a9f8.svg" alt="Laravel Version"></a>
    <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License"></a>
</p>

**基于 [xlswriter](https://xlswriter-docs.viest.me/) 扩展的 Laravel 高性能 Excel 导出工具**

</div>

## 特性

- 高性能、低内存占用
- 支持大数据量导出（50万+ 行）
- 分块数据处理
- 单元格合并与自定义样式
- 冻结窗格支持
- 多数据源支持（Query Builder、Collection、Array）
- Swoole 兼容

## 环境要求

- PHP >= 8.2
- Laravel >= 12.0
- [xlswriter](https://xlswriter-docs.viest.me/) PHP 扩展

## 安装

### 1. 安装 xlswriter 扩展

在安装此包之前，需要先安装 xlswriter PHP 扩展。

**Windows:** 从官网下载对应 PHP 版本的 DLL 文件。

**Linux/macOS:**

```bash
pecl install xlswriter
```

安装后在 `phpinfo()` 中确认扩展已启用。

### 2. 安装扩展包

```bash
composer require dcat-x/laravel-xlswriter-export
```

## 快速开始

### 基础导出

创建一个继承 `BaseExport` 的导出类：

```php
<?php

namespace App\Exports;

use Aoding9\Laravel\Xlswriter\Export\BaseExport;

class UserExport extends BaseExport
{
    public $header = [
        ['column' => 'a', 'width' => 8, 'name' => 'ID'],
        ['column' => 'b', 'width' => 15, 'name' => '姓名'],
        ['column' => 'c', 'width' => 10, 'name' => '性别'],
        ['column' => 'd', 'width' => 20, 'name' => '创建时间'],
    ];

    public $fileName = '用户导出';
    public $tableTitle = '用户导出表';

    public function eachRow($row)
    {
        return [
            $row->id,
            $row->name,
            $row->gender,
            $row->created_at->toDateTimeString(),
        ];
    }
}
```

在控制器中使用：

```php
use App\Exports\UserExport;
use App\Models\User;

public function export()
{
    $query = User::query();

    return UserExport::make($query)->export();
}
```

### 从 Collection/Array 导出

```php
$data = [
    ['id' => 1, 'name' => '张三', 'created_at' => now()->toDateString()],
    ['id' => 2, 'name' => '李四', 'created_at' => now()->toDateString()],
];

UserExport::make($data)->export();
```

或者重写 `buildData()` 方法实现分块处理：

```php
public function buildData(?int $page = null, ?int $perPage = null)
{
    return collect([
        ['id' => 1, 'name' => '张三'],
        ['id' => 2, 'name' => '李四'],
    ]);
}
```

## 配置选项

| 属性 | 类型 | 默认值 | 说明 |
|------|------|--------|------|
| `$fileName` | string | 'export' | 导出文件名 |
| `$tableTitle` | string | '' | 首行标题 |
| `$useTitle` | bool | true | 是否显示标题行 |
| `$useFreezePanes` | bool | false | 是否启用冻结窗格 |
| `$useGlobalStyle` | bool | true | 是否使用全局样式 |
| `$fontFamily` | string | '微软雅黑' | 默认字体 |
| `$rowHeight` | int | 40 | 数据行高 |
| `$headerRowHeight` | int | 40 | 表头行高 |
| `$titleRowHeight` | int | 50 | 标题行高 |
| `$max` | int | 500000 | 最大导出行数 |
| `$chunkSize` | int | 5000 | 每块数据量 |
| `$debug` | bool | false | 是否启用调试模式 |

## 高级用法

### 单元格合并

重写 `afterInsertEachRowInEachChunk()` 实现行级合并：

```php
public function afterInsertEachRowInEachChunk($row)
{
    if ($this->index % 2 === 1) {
        $range = "B{$this->getCurrentLine()}:B" . ($this->getCurrentLine() + 1);
        $this->excel->mergeCells($range, $row->id, $this->getCustomStyle());
    }
}
```

重写 `mergeCellsAfterInsertData()` 实现静态合并：

```php
public function mergeCellsAfterInsertData()
{
    return [
        ['range' => "A1:{$this->end}1", 'value' => $this->getTableTitle(), 'formatHandle' => $this->titleStyle],
        ['range' => "A2:A3", 'value' => 'ID', 'formatHandle' => $this->headerStyle],
    ];
}
```

### 自定义单元格样式

重写 `insertCellHandle()` 实现单元格级样式：

```php
use Vtiful\Kernel\Format;

public function insertCellHandle($currentLine, $column, $data, $format, $formatHandle)
{
    if ($this->getColumn($column) === 'E' && $data instanceof Carbon) {
        $formatHandle = $this->getHighlightStyle();
        $data = $data->toDateTimeString();
    }

    return $this->excel->insertText($currentLine, $column, $data, $format, $formatHandle);
}

public function getHighlightStyle()
{
    return (new Format($this->fileHandle))
        ->background(Format::COLOR_YELLOW)
        ->fontSize(10)
        ->bold()
        ->align(Format::FORMAT_ALIGN_CENTER, Format::FORMAT_ALIGN_VERTICAL_CENTER)
        ->toResource();
}
```

### Swoole 支持

在导出类中启用 Swoole 模式：

```php
public $useSwoole = true;
```

然后在控制器中返回导出结果：

```php
return UserExport::make($query)->export();
```

## 性能基准

导出 4 列数据表，使用分块查询的测试结果：

| 行数 | 分块大小 | 耗时 | 内存占用 |
|------|----------|------|----------|
| 10,000 | 2,000 | ~2s | ~15MB |
| 500,000 | 50,000 | ~45s | ~50MB |

## API 参考

### 主要方法

| 方法 | 说明 |
|------|------|
| `make($dataSource, $time)` | 静态构造函数 |
| `export()` | 执行导出并下载 |
| `store()` | 保存到文件但不下载 |
| `download($filePath)` | 下载已存在的文件 |
| `setMax(int $max)` | 设置最大行数 |
| `setChunkSize(int $size)` | 设置分块大小 |
| `setDebug(bool $debug)` | 启用/禁用调试 |
| `useFreezePanes(bool $use)` | 启用/禁用冻结 |
| `shouldDelete(bool $delete)` | 下载后是否删除文件 |

### 主要属性

| 属性 | 说明 |
|------|------|
| `$index` | 当前行索引（从 1 开始） |
| `$currentLine` | 当前行号（从 0 开始） |
| `$completed` | 已导出的总行数 |
| `$chunkData` | 当前分块数据 |
| `$excel` | xlswriter Excel 实例 |

### 辅助方法

| 方法 | 说明 |
|------|------|
| `getCurrentLine()` | 获取 Excel 行号（从 1 开始） |
| `getIndex()` | 获取当前数据索引 |
| `getColumn(int $index)` | 列索引转字母 |
| `getColumnIndexByName(string $name)` | 字母转列索引 |
| `getCellName(int $line, int $col)` | 获取单元格名称（如 "A1"） |
| `getRowInChunkByIndex(int $index)` | 根据索引获取行数据 |

## 贡献

欢迎贡献代码！请随时提交 Pull Request。

## 许可证

MIT 许可证。详情请参阅 [LICENSE](LICENSE) 文件。
