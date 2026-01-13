# Changelog

本项目所有重要更改都将记录在此文件中。

格式基于 [Keep a Changelog](https://keepachangelog.com/zh-CN/1.0.0/)，
版本号遵循 [语义化版本](https://semver.org/lang/zh-CN/)。

## [Unreleased]

## [1.0.0] - 2026-01-13

### Added

- 基于 xlswriter 扩展的高性能 Excel 导出功能
- 支持大数据集导出（百万级数据，低内存占用）
- 分块处理机制，优化内存使用
- 支持多种数据源：数组、Collection、Eloquent Builder、LazyCollection
- 灵活的表头配置（列宽、对齐方式、格式化器）
- 内置格式化器：日期、数字、百分比等
- 标题行支持（可选）
- 冻结窗格功能
- 自动列宽计算
- Swoole 协程环境支持
- 完整的生命周期钩子
- PHPStan 静态分析支持
- Laravel Pint 代码格式化
- Pest 测试框架集成
- GitHub Actions CI/CD 配置

### Requirements

- PHP 8.2+
- Laravel 12.0+
- ext-xlswriter 扩展

[Unreleased]: https://github.com/dcat-x/laravel-xlswriter-export/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/dcat-x/laravel-xlswriter-export/releases/tag/v1.0.0
