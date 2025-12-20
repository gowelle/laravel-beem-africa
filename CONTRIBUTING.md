# Contributing to Laravel Beem Africa

First off, thank you for considering contributing to Laravel Beem Africa! 🎉

This document provides guidelines and steps for contributing. By participating in this project, you agree to abide by our code of conduct.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Making Changes](#making-changes)
- [Pull Request Process](#pull-request-process)
- [Coding Standards](#coding-standards)
- [Testing Guidelines](#testing-guidelines)
- [Documentation](#documentation)
- [Roadmap Contributions](#roadmap-contributions)

---

## Code of Conduct

This project and everyone participating in it is governed by our commitment to providing a welcoming and inclusive environment. Please be respectful, considerate, and constructive in all interactions.

### Our Standards

- Use welcoming and inclusive language
- Be respectful of differing viewpoints
- Accept constructive criticism gracefully
- Focus on what is best for the community
- Show empathy towards other community members

---

## Getting Started

### Types of Contributions

We welcome several types of contributions:

| Type                 | Description                                     |
| -------------------- | ----------------------------------------------- |
| 🐛 **Bug Fixes**     | Fix issues in existing functionality            |
| ✨ **Features**      | Add new features from the [Roadmap](ROADMAP.md) |
| 📚 **Documentation** | Improve README, add examples, fix typos         |
| 🧪 **Tests**         | Add missing tests, improve coverage             |
| 🔧 **Refactoring**   | Code improvements without changing behavior     |
| 🌍 **Translations**  | Translate documentation                         |

### Finding Something to Work On

1. Check the [Roadmap](ROADMAP.md) for planned features
2. Look at [open issues](https://github.com/gowelle/laravel-beem-africa/issues)
3. Search for issues labeled `good first issue` or `help wanted`
4. Propose your own feature via a GitHub issue

---

## Development Setup

### Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- Git

### Installation

1. **Fork the repository** on GitHub

2. **Clone your fork**:

   ```bash
   git clone https://github.com/YOUR_USERNAME/laravel-beem-africa.git
   cd laravel-beem-africa
   ```

3. **Install dependencies**:

   ```bash
   composer install
   ```

4. **Verify setup**:
   ```bash
   composer test        # Run tests
   composer analyse     # Run static analysis
   composer format      # Check code style
   ```

### Environment Setup (for Integration Tests)

Copy the test environment file and add your Beem sandbox credentials:

```bash
cp .env.example .env.testing

# Edit .env.testing with your credentials:
BEEM_API_KEY=your_sandbox_api_key
BEEM_SECRET_KEY=your_sandbox_secret_key
BEEM_OTP_APP_ID=your_otp_app_id
```

---

## Making Changes

### Branch Naming

Use descriptive branch names:

```
feature/notification-channels
fix/sms-encoding-issue
docs/improve-moja-examples
test/add-airtime-edge-cases
refactor/simplify-http-client
```

### Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
feat(sms): add scheduled message support
fix(otp): handle empty PIN response
docs(readme): add multi-tenant example
test(moja): add WhatsApp template tests
refactor(client): extract retry logic
chore: update dependencies
```

**Commit Types**:

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `test`: Adding or updating tests
- `refactor`: Code changes that don't fix bugs or add features
- `chore`: Maintenance tasks

### Making a Change

1. **Create a branch**:

   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes**

3. **Run quality checks**:

   ```bash
   composer test      # All tests must pass
   composer analyse   # No PHPStan errors
   composer format    # Code style must be correct
   ```

4. **Commit your changes**:

   ```bash
   git add .
   git commit -m "feat(service): description of change"
   ```

5. **Push to your fork**:
   ```bash
   git push origin feature/your-feature-name
   ```

---

## Pull Request Process

### Before Submitting

Ensure your PR:

- [ ] Passes all tests (`composer test`)
- [ ] Passes static analysis (`composer analyse`)
- [ ] Follows code style (`composer format`)
- [ ] Includes tests for new functionality
- [ ] Updates documentation if needed
- [ ] Has a clear, descriptive title
- [ ] References related issues (e.g., "Fixes #123")

### PR Template

When creating a PR, include:

```markdown
## Description

Brief description of changes

## Type of Change

- [ ] Bug fix
- [ ] New feature
- [ ] Documentation update
- [ ] Refactoring
- [ ] Other (describe)

## Related Issue

Fixes #(issue number)

## Testing

Describe how you tested the changes

## Checklist

- [ ] Tests pass locally
- [ ] PHPStan passes
- [ ] Code style is correct
- [ ] Documentation updated
```

### Review Process

1. A maintainer will review your PR
2. They may request changes or ask questions
3. Make requested changes and push to the same branch
4. Once approved, a maintainer will merge your PR

---

## Coding Standards

### PHP Style

We use [Laravel Pint](https://laravel.com/docs/pint) with default Laravel rules:

```bash
# Check style
composer format -- --test

# Fix style
composer format
```

### Key Conventions

```php
// ✅ Use strict types
declare(strict_types=1);

// ✅ Use constructor property promotion
public function __construct(
    public readonly string $phone,
    public readonly string $message,
) {}

// ✅ Use named arguments for DTOs
new SmsRequest(
    sourceAddr: 'MYAPP',
    message: 'Hello',
    recipients: [...],
);

// ✅ Use enums for fixed values
enum SmsResponseCode: int
{
    case SUCCESS = 100;
    case INVALID_PHONE = 101;
}

// ✅ Return type declarations
public function send(SmsRequest $request): SmsResponse

// ✅ Null safety
public function getPhone(): ?string
```

### Static Analysis

We use PHPStan at level 9:

```bash
composer analyse
```

Fix any reported issues before submitting your PR.

---

## Testing Guidelines

### Running Tests

```bash
# Run all tests
composer test

# Run specific test file
./vendor/bin/pest tests/Unit/SmsServiceTest.php

# Run specific test
./vendor/bin/pest --filter="it sends sms successfully"

# Run with coverage
composer test-coverage

# Run integration tests (requires credentials)
./vendor/bin/pest --group=integration
```

### Writing Tests

Use [Pest PHP](https://pestphp.com/) for testing:

```php
// tests/Unit/SmsServiceTest.php

use Gowelle\BeemAfrica\DTOs\SmsRequest;
use Gowelle\BeemAfrica\DTOs\SmsRecipient;

it('sends sms successfully', function () {
    // Arrange
    $request = new SmsRequest(
        sourceAddr: 'TEST',
        message: 'Hello',
        recipients: [new SmsRecipient('REC-1', '255712345678')],
    );

    // Act
    $response = $this->smsService->send($request);

    // Assert
    expect($response->isSuccessful())->toBeTrue();
});

it('throws exception for invalid phone', function () {
    $request = new SmsRequest(
        sourceAddr: 'TEST',
        message: 'Hello',
        recipients: [new SmsRecipient('REC-1', 'invalid')],
    );

    $this->smsService->send($request);
})->throws(SmsException::class);
```

### Test Organization

```
tests/
├── Unit/           # Unit tests (no external dependencies)
│   ├── DTOs/       # DTO tests
│   ├── Enums/      # Enum tests
│   └── Services/   # Service tests with mocked HTTP
├── Feature/        # Feature tests (Laravel app context)
└── Integration/    # Integration tests (real API calls)
```

### Test Coverage Goals

- Minimum 80% coverage for new code
- Critical paths should have 100% coverage
- Include edge cases and error scenarios

---

## Documentation

### README Updates

When adding features, update the README:

1. Add to the Features section if it's a new service
2. Add usage examples in the appropriate section
3. Update the Table of Contents if adding new sections

### Inline Documentation

```php
/**
 * Send an SMS message to one or more recipients.
 *
 * @param SmsRequest $request The SMS request containing recipients and message
 * @return SmsResponse The response from Beem API
 * @throws SmsException When the API returns an error
 * @throws \InvalidArgumentException When request validation fails
 */
public function send(SmsRequest $request): SmsResponse
```

### CHANGELOG

Update `CHANGELOG.md` following [Keep a Changelog](https://keepachangelog.com/):

```markdown
## [Unreleased]

### Added

- New feature description (#issue)

### Changed

- Changed behavior description (#issue)

### Fixed

- Bug fix description (#issue)
```

---

## Roadmap Contributions

Want to work on a roadmap item? Here's how:

### 1. Claim an Item

1. Open an issue titled: "Implement: [Feature Name]"
2. Reference the roadmap section
3. Describe your implementation approach
4. Wait for maintainer approval before starting

### 2. Implementation Guidelines

**For new services**:

```
src/
├── NewService/
│   └── BeemNewServiceService.php
├── Support/
│   └── BeemNewServiceClient.php
├── DTOs/
│   ├── NewServiceRequest.php
│   └── NewServiceResponse.php
├── Enums/
│   └── NewServiceResponseCode.php
├── Exceptions/
│   └── NewServiceException.php
└── Events/
    └── NewServiceCompleted.php
```

**Required components**:

- Service class in `src/{ServiceName}/`
- HTTP client in `src/Support/`
- Request/Response DTOs
- Exception class
- Event class (if webhooks)
- Facade method
- Tests (unit + feature)
- README documentation
- CHANGELOG entry

### 3. Large Feature Process

For large features:

1. **RFC Issue**: Create a detailed proposal
2. **Discussion**: Get community feedback
3. **Approval**: Wait for maintainer approval
4. **Implementation**: Work in stages with incremental PRs
5. **Review**: Comprehensive review before merge

---

## Questions?

- **General Questions**: Open a [Discussion](https://github.com/gowelle/laravel-beem-africa/discussions)
- **Bug Reports**: Open an [Issue](https://github.com/gowelle/laravel-beem-africa/issues)
- **Direct Contact**: gowelle.john@icloud.com

---

## Recognition

Contributors are recognized in:

- README.md Credits section
- CHANGELOG.md entries
- GitHub Contributors page

Thank you for contributing to Laravel Beem Africa! 🙏

