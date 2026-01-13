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

[English](README.en.md) | 简体中文

</div>

## 目录

- [特性](#特性)
- [环境要求](#环境要求)
- [安装](#安装)
- [快速开始](#快速开始)
- [配置选项](#配置选项)
- [高级用法](#高级用法)
- [性能基准](#性能基准)
- [API 参考](#api-参考)
- [常见问题](#常见问题)
- [故障排查](#故障排查)
- [贡献指南](#贡献指南)
- [许可证](#许可证)

## 特性

- **高性能** - 基于 C 扩展 xlswriter，导出速度快，内存占用低
- **大数据支持** - 支持 50 万+ 行数据导出，分块处理避免内存溢出
- **多数据源** - 支持 Query Builder、Collection、Array 等多种数据源
- **样式定制** - 支持单元格合并、自定义样式、冻结窗格等
- **Swoole 兼容** - 完美支持 Swoole 协程环境
- **链式调用** - 优雅的 API 设计，支持链式配置

## 环境要求

- PHP >= 8.2
- Laravel >= 12.0
- [xlswriter](https://xlswriter-docs.viest.me/) PHP 扩展

## 安装

### 1. 安装 xlswriter 扩展

在安装此包之前，需要先安装 xlswriter PHP 扩展。

<details>
<summary><strong>Linux (推荐)</strong></summary>

```bash
# 使用 PECL 安装
pecl install xlswriter

# 或者从源码编译
git clone https://github.com/viest/php-ext-xlswriter.git
cd php-ext-xlswriter
phpize
./configure
make && make install
```

添加到 `php.ini`:
```ini
extension=xlswriter.so
```
</details>

<details>
<summary><strong>macOS</strong></summary>

```bash
pecl install xlswriter
```

如果使用 Homebrew 安装的 PHP:
```bash
# 确保 pecl 可用
brew install php

pecl install xlswriter
```
</details>

<details>
<summary><strong>Windows</strong></summary>

1. 访问 [xlswriter releases](https://github.com/viest/php-ext-xlswriter/releases)
2. 下载对应 PHP 版本的 DLL 文件
3. 将 DLL 放入 PHP 的 `ext` 目录
4. 在 `php.ini` 添加: `extension=xlswriter`
</details>

<details>
<summary><strong>Docker</strong></summary>

```dockerfile
FROM php:8.2-fpm

RUN pecl install xlswriter \
    && docker-php-ext-enable xlswriter
```
</details>

安装后运行 `php -m | grep xlswriter` 或查看 `phpinfo()` 确认扩展已启用。

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
    // 从 API 或其他数据源获取数据
    return collect($this->fetchDataFromApi($page, $perPage));
}
```

### 链式配置

```php
UserExport::make($query)
    ->setMax(100000)           // 最大导出 10 万行
    ->setChunkSize(2000)       // 每块 2000 条
    ->useFreezePanes(true)     // 启用冻结窗格
    ->setFontFamily('宋体')    // 设置字体
    ->export();
```

## 配置选项

| 属性 | 类型 | 默认值 | 说明 |
|------|------|--------|------|
| `$fileName` | string | '文件名' | 导出文件名（不含扩展名） |
| `$tableTitle` | string | '表名' | 首行合并标题 |
| `$sheetName` | string | 'Sheet1' | 工作表名称 |
| `$useTitle` | bool | true | 是否显示首行标题 |
| `$useFreezePanes` | bool | false | 是否启用冻结窗格 |
| `$useGlobalStyle` | bool | true | 使用全局样式（末尾无边框） |
| `$fontFamily` | string | '微软雅黑' | 默认字体 |
| `$rowHeight` | int | 40 | 数据行高 |
| `$headerRowHeight` | int | 40 | 表头行高 |
| `$titleRowHeight` | int | 50 | 标题行高 |
| `$max` | int | 500000 | 最大导出行数 |
| `$chunkSize` | int | 5000 | 每块数据量 |
| `$debug` | bool | false | 启用调试日志 |
| `$useSwoole` | bool | false | Swoole 模式 |
| `$shouldDelete` | bool | true | 下载后删除临时文件 |

## 高级用法

### 单元格合并

#### 行级合并（在每行插入后）

```php
public function afterInsertEachRowInEachChunk($row)
{
    // 每两行合并一次 B 列
    if ($this->index % 2 === 1 && $this->getCurrentLine() < $this->completed + $this->startDataRow) {
        $range = "B{$this->getCurrentLine()}:B" . ($this->getCurrentLine() + 1);
        $nextRow = $this->getRowInChunkByIndex($this->index + 1);
        $value = $row->id . '---' . ($nextRow ? $nextRow->id : '');
        $this->excel->mergeCells($range, $value, $this->getNormalStyle());
    }
}
```

#### 静态合并（所有数据插入后）

```php
public function mergeCellsAfterInsertData()
{
    return [
        ['range' => "A1:{$this->end}1", 'value' => $this->getTableTitle(), 'formatHandle' => $this->titleStyle],
        ['range' => "A2:A3", 'value' => '序号', 'formatHandle' => $this->headerStyle],
        ['range' => "B2:B3", 'value' => 'ID', 'formatHandle' => $this->headerStyle],
        ['range' => "C2:E2", 'value' => '基本信息', 'formatHandle' => $this->headerStyle],
    ];
}
```

### 自定义单元格样式

```php
use Vtiful\Kernel\Format;

public function insertCellHandle($currentLine, $column, $data, $format, $formatHandle)
{
    // 为特定列设置高亮样式
    if ($this->getColumn($column) === 'E' && $data instanceof Carbon) {
        if ($data->isToday()) {
            $formatHandle = $this->getHighlightStyle();
        }
        $data = $data->toDateTimeString();
    }

    return $this->excel->insertText($currentLine, $column, $data, $format, $formatHandle);
}

protected function getHighlightStyle()
{
    return (new Format($this->fileHandle))
        ->background(Format::COLOR_YELLOW)
        ->fontSize(10)
        ->bold()
        ->align(Format::FORMAT_ALIGN_CENTER, Format::FORMAT_ALIGN_VERTICAL_CENTER)
        ->border(Format::BORDER_THIN)
        ->toResource();
}
```

### Swoole 支持

在 Swoole 环境中，不能使用 `exit()` 终止请求：

```php
class UserExport extends BaseExport
{
    public $useSwoole = true;

    // ...
}

// 控制器中必须 return
public function export()
{
    return UserExport::make($query)->export();
}
```

### 自定义数据源

```php
class ApiExport extends BaseExport
{
    protected string $apiUrl;

    public function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;
        parent::__construct(null); // 传入 null，使用 buildDataFromOther
    }

    public function buildDataFromOther(?int $page = null, ?int $perPage = null)
    {
        $response = Http::get($this->apiUrl, [
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return collect($response->json('data'));
    }
}
```

## 性能基准

导出 4 列数据表，使用分块查询的测试结果：

| 行数 | 分块大小 | 耗时 | 内存占用 |
|------|----------|------|----------|
| 10,000 | 2,000 | ~2s | ~15MB |
| 100,000 | 5,000 | ~10s | ~25MB |
| 500,000 | 50,000 | ~45s | ~50MB |

> 测试环境: PHP 8.2, Laravel 12, MySQL 8.0, 8GB RAM

### 性能优化建议

1. **合理设置 chunkSize** - 根据服务器内存调整，一般 2000-10000 为宜
2. **使用 Query Builder** - 避免一次性加载全部数据到内存
3. **禁用不必要的样式** - 设置 `$useGlobalStyle = false` 可略微提升性能
4. **关联数据预加载** - 使用 `with()` 预加载避免 N+1 查询

## API 参考

### 构造方法

| 方法 | 说明 |
|------|------|
| `make($dataSource, $time = null)` | 静态构造函数 |
| `__construct($dataSource, $time = null)` | 构造函数，$time 用于调试计时 |

### 导出方法

| 方法 | 说明 |
|------|------|
| `export()` | 执行导出并触发下载 |
| `store()` | 仅保存文件，不触发下载 |
| `download($filePath = null)` | 下载指定文件 |

### 配置方法

| 方法 | 说明 |
|------|------|
| `setMax(int $max)` | 设置最大导出行数 |
| `setChunkSize(int $size)` | 设置分块大小 |
| `setDebug(bool $debug)` | 启用/禁用调试模式 |
| `setFontFamily(string $font)` | 设置默认字体 |
| `setUseTitle(bool $use)` | 是否显示首行标题 |
| `useFreezePanes(bool $use)` | 启用/禁用冻结窗格 |
| `shouldDelete(bool $delete)` | 下载后是否删除文件 |
| `setSheet(string $name)` | 设置工作表名称 |

### 属性

| 属性 | 类型 | 说明 |
|------|------|------|
| `$index` | int | 当前数据行索引（从 1 开始） |
| `$currentLine` | int | 当前 Excel 行（从 0 开始） |
| `$completed` | int | 已导出的总行数 |
| `$chunkData` | Collection | 当前分块数据 |
| `$excel` | Excel | xlswriter 实例 |
| `$fileHandle` | resource | 文件句柄 |
| `$filePath` | string | 导出文件路径 |

### 辅助方法

| 方法 | 说明 |
|------|------|
| `getCurrentLine()` | 获取当前 Excel 行号（从 1 开始） |
| `getIndex()` | 获取当前数据索引 |
| `getColumn(int $index)` | 列索引转字母（0 → A） |
| `getColumnIndexByName(string $name)` | 字母转列索引（A → 0） |
| `getCellName(int $line, int $col)` | 获取单元格名称（如 A1） |
| `getRowInChunkByIndex(int $index)` | 从当前分块获取指定索引的行数据 |

### 生命周期钩子

| 方法 | 调用时机 |
|------|----------|
| `beforeInsertData()` | 数据插入前 |
| `afterInsertEachRowInEachChunk($row)` | 每行插入后 |
| `afterInsertData()` | 所有数据插入后 |
| `beforeOutput()` | 文件输出前 |
| `afterStore()` | 文件保存后 |

## 常见问题

### Q: 大数字显示为科学计数法怎么办？

**A:** 将数字转为字符串：

```php
public function eachRow($row)
{
    return [
        $row->id,
        (string) $row->phone,      // 转为字符串
        (string) $row->id_card,    // 身份证号等长数字
    ];
}
```

### Q: 如何导出带公式的单元格？

**A:** 使用 xlswriter 的 `insertFormula` 方法：

```php
public function insertCellHandle($currentLine, $column, $data, $format, $formatHandle)
{
    if ($this->getColumn($column) === 'F') {
        return $this->excel->insertFormula($currentLine, $column, '=SUM(D' . ($currentLine + 1) . ':E' . ($currentLine + 1) . ')');
    }
    return parent::insertCellHandle($currentLine, $column, $data, $format, $formatHandle);
}
```

### Q: 如何设置列宽自适应？

**A:** xlswriter 不支持真正的自适应，需要预估宽度：

```php
public $header = [
    ['column' => 'a', 'width' => 10, 'name' => 'ID'],           // 短内容
    ['column' => 'b', 'width' => 30, 'name' => '描述'],         // 长内容
    ['column' => 'c', 'width' => 20, 'name' => '创建时间'],     // 日期时间
];
```

### Q: 导出时内存不足怎么办？

**A:**
1. 减小 `$chunkSize` 值
2. 确保使用 Query Builder 而非 Collection
3. 增加 PHP 内存限制（临时方案）

```php
UserExport::make($query)
    ->setChunkSize(1000)  // 减小分块
    ->setMax(100000)      // 限制最大行数
    ->export();
```

### Q: 如何导出多个 Sheet？

**A:** 当前版本暂不支持多 Sheet，可以考虑导出多个文件后合并。

## 故障排查

### 扩展未加载

```
Class 'Vtiful\Kernel\Excel' not found
```

**解决:** 检查 xlswriter 扩展是否正确安装：
```bash
php -m | grep xlswriter
```

### 文件无法写入

```
Unable to open file for writing
```

**解决:** 检查临时目录权限：
```php
// 查看临时目录
echo sys_get_temp_dir();

// 确保目录可写
chmod 777 /tmp
```

### 内存溢出

```
Allowed memory size of xxx bytes exhausted
```

**解决:**
1. 减小 `$chunkSize`
2. 使用 Query Builder 替代 Collection
3. 临时增加内存限制：`ini_set('memory_limit', '512M')`

### Swoole 环境报错

```
exit() not allowed in Swoole
```

**解决:** 设置 `$useSwoole = true` 并在控制器中 `return` 导出结果。

## 贡献指南

欢迎贡献代码！请查看 [CONTRIBUTING.md](CONTRIBUTING.md) 了解详情。

### 本地开发

```bash
# 克隆仓库
git clone https://github.com/dcat-x/laravel-xlswriter-export.git
cd laravel-xlswriter-export

# 安装依赖
composer install

# 运行测试
composer test

# 代码格式化
composer format

# 静态分析
composer analyse

# 运行所有检查
composer check
```

## 许可证

MIT 许可证。详情请参阅 [LICENSE](LICENSE) 文件。
