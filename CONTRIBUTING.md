# 贡献指南

感谢你考虑为 Laravel Xlswriter Export 做出贡献！

## 行为准则

请尊重所有参与者，保持友好和专业的交流氛围。

## 如何贡献

### 报告 Bug

1. 确保该 Bug 尚未被报告，搜索 [Issues](https://github.com/dcat-x/laravel-xlswriter-export/issues)
2. 如果找不到相关 Issue，[创建一个新的](https://github.com/dcat-x/laravel-xlswriter-export/issues/new)
3. 请包含以下信息：
   - 清晰的标题和描述
   - 复现步骤
   - 预期行为与实际行为
   - PHP、Laravel、xlswriter 扩展版本
   - 相关代码片段或错误日志

### 功能建议

1. 搜索现有 Issues 确保该功能尚未被提议
2. 创建 Issue 描述你的想法
3. 说明该功能的使用场景和价值

### 提交代码

1. Fork 本仓库
2. 创建功能分支：`git checkout -b feature/amazing-feature`
3. 编写代码并添加测试
4. 确保所有检查通过：`composer check`
5. 提交更改：`git commit -m 'Add amazing feature'`
6. 推送分支：`git push origin feature/amazing-feature`
7. 创建 Pull Request

## 开发环境设置

### 前置要求

- PHP 8.2+
- xlswriter 扩展
- Composer

### 安装步骤

```bash
# 克隆仓库
git clone https://github.com/dcat-x/laravel-xlswriter-export.git
cd laravel-xlswriter-export

# 安装依赖
composer install
```

## 开发规范

### 代码风格

本项目使用 [Laravel Pint](https://laravel.com/docs/pint) 进行代码格式化：

```bash
# 格式化代码
composer format

# 检查格式（不修改）
composer format:check
```

### 静态分析

使用 [PHPStan](https://phpstan.org/) 进行静态分析：

```bash
composer analyse
```

### 测试

使用 [Pest](https://pestphp.com/) 进行测试：

```bash
# 运行测试
composer test

# 运行测试并生成覆盖率报告
composer test:coverage
```

### 完整检查

提交前请运行完整检查：

```bash
composer check
```

这将依次执行：格式检查、静态分析、测试。

## 提交信息规范

请使用清晰、描述性的提交信息：

- `feat: 添加新功能描述`
- `fix: 修复问题描述`
- `docs: 更新文档描述`
- `test: 添加或修改测试`
- `refactor: 重构代码描述`
- `chore: 其他杂项更改`

## Pull Request 流程

1. 确保 PR 描述清晰说明了更改内容
2. 关联相关 Issue（如有）
3. 确保所有 CI 检查通过
4. 等待代码审查
5. 根据反馈进行修改

## 版本发布

版本发布由维护者负责，遵循 [语义化版本](https://semver.org/lang/zh-CN/)：

- **主版本**：不兼容的 API 更改
- **次版本**：向后兼容的功能新增
- **修订版本**：向后兼容的 Bug 修复

## 许可证

通过贡献代码，你同意你的贡献将在 [MIT 许可证](LICENSE) 下发布。

## 问题？

如有任何问题，请通过 [Issues](https://github.com/dcat-x/laravel-xlswriter-export/issues) 联系我们。
