# Laravel Beem Africa - Roadmap

> **Version**: 1.8.0 (Current)  
> **Last Updated**: December 2024  
> **Status**: Active Development

This document outlines the strategic roadmap for the `laravel-beem-africa` package. It covers planned features, improvements, and long-term goals organized by priority and timeline.

---

## 📊 Current State Summary

### ✅ Implemented Services (v1.8.0)

| Service               | Status      | Features                                            |
| --------------------- | ----------- | --------------------------------------------------- |
| **Payment Checkout**  | ✅ Complete | Redirect, Iframe, Webhooks, Transaction Storage     |
| **OTP**               | ✅ Complete | Request, Verify, 18 Error Codes                     |
| **SMS**               | ✅ Complete | Send, Bulk, Scheduled, Templates, Sender Names, DLR |
| **Airtime**           | ✅ Complete | Transfer, Balance, Status, Callbacks                |
| **Disbursements**     | ✅ Complete | Transfer, Scheduled, 14 Error Codes                 |
| **Collections**       | ✅ Complete | Balance, Webhooks                                   |
| **USSD Hub**          | ✅ Complete | Sessions, Balance, Menu Responses                   |
| **Contacts**          | ✅ Complete | AddressBooks, Contacts CRUD, Pagination             |
| **Moja**              | ✅ Complete | 6 Message Types, 4 Channels, Templates, Webhooks    |
| **International SMS** | ✅ Complete | Send, Binary, Multi-recipient, DLR                  |

### 📈 Package Metrics

- **Tests**: 384+ tests, 1053+ assertions
- **PHP Support**: 8.2, 8.3, 8.4
- **Laravel Support**: 11.x, 12.x
- **Static Analysis**: PHPStan Level 9

---

## 🚀 Roadmap

### Phase 1: Developer Experience (Q1 2025)

#### 1.1 Laravel Notifications Integration

**Priority**: 🔴 High  
**Target**: v2.0.0

Create notification channels for seamless Laravel integration:

```php
// Future API
class OrderShipped extends Notification
{
    public function via($notifiable): array
    {
        return ['beem-sms', 'beem-whatsapp'];
    }

    public function toBeemSms($notifiable): BeemSmsMessage
    {
        return (new BeemSmsMessage)
            ->content('Your order has shipped!')
            ->from('MYAPP');
    }

    public function toBeemWhatsApp($notifiable): BeemMojaMessage
    {
        return (new BeemMojaMessage)
            ->template('order_shipped')
            ->params(['order_id' => $this->order->id]);
    }
}
```

**Tasks**:

- [ ] Create `BeemSmsChannel` notification channel
- [ ] Create `BeemMojaChannel` (WhatsApp) notification channel
- [ ] Create message builder classes (`BeemSmsMessage`, `BeemMojaMessage`)
- [ ] Add `routeNotificationForBeemSms()` trait for User model
- [ ] Support template variables substitution
- [ ] Documentation and examples

---

#### 1.2 Queue Support & Async Operations

**Priority**: 🔴 High  
**Target**: v2.0.0

Enable queueing for all outbound operations:

```php
// Future API
Beem::sms()->queue()->send($request);
Beem::moja()->onQueue('whatsapp')->sendMessage($request);

// Batch operations
Beem::sms()->batch([
    new SmsRequest(...),
    new SmsRequest(...),
])->dispatch();
```

**Tasks**:

- [ ] Create `BeemJob` base class
- [ ] Implement `SendSmsJob`, `SendMojaMessageJob`, `TransferAirtimeJob`
- [ ] Add `queue()` fluent method to services
- [ ] Add batch processing support
- [ ] Implement retry policies with exponential backoff
- [ ] Add job middleware for rate limiting
- [ ] Fire events on job completion/failure

---

#### 1.3 Retry & Circuit Breaker Patterns

**Priority**: 🟡 Medium  
**Target**: v2.0.0

Improve resilience with automatic retries and circuit breakers:

```php
// config/beem-africa.php
'retry' => [
    'enabled' => true,
    'max_attempts' => 3,
    'delay_ms' => 1000,
    'multiplier' => 2,
    'retryable_codes' => [503, 504, 429],
],

'circuit_breaker' => [
    'enabled' => true,
    'failure_threshold' => 5,
    'recovery_timeout' => 60,
],
```

**Tasks**:

- [ ] Add configurable retry logic to HTTP clients
- [ ] Implement circuit breaker pattern
- [ ] Add health check endpoint (`Beem::health()`)
- [ ] Create `BeemServiceUnavailableException`
- [ ] Add telemetry/metrics for failed requests

---

#### 1.4 Rate Limiting

**Priority**: 🟡 Medium  
**Target**: v2.1.0

Prevent API rate limit violations:

```php
// Future API
Beem::sms()->withRateLimit(100, 'per_minute')->send($request);

// Or global config
'rate_limits' => [
    'sms' => ['requests' => 100, 'per' => 'minute'],
    'moja' => ['requests' => 30, 'per' => 'minute'],
],
```

**Tasks**:

- [ ] Implement rate limiter using Laravel's `RateLimiter`
- [ ] Add per-service rate limit configuration
- [ ] Queue overflow requests automatically
- [ ] Add `RateLimitExceededException`
- [ ] Dashboard/logging for rate limit hits

---

### Phase 2: Enhanced Features (Q2 2025)

#### 2.1 Message Templates Engine

**Priority**: 🟡 Medium  
**Target**: v2.1.0

Local template management with variable substitution:

```php
// Future API
Beem::sms()->template('welcome')
    ->to('255712345678')
    ->with(['name' => 'John', 'code' => '1234'])
    ->send();

// templates/sms/welcome.txt
Hello {{ name }}, your verification code is {{ code }}
```

**Tasks**:

- [ ] Create `BeemTemplateManager`
- [ ] Support Blade-like variable syntax
- [ ] Add template validation (character limits, encoding)
- [ ] Support SMS message splitting for long messages
- [ ] Add template caching

---

#### 2.2 Webhook Verification Enhancements

**Priority**: 🟡 Medium  
**Target**: v2.1.0

Advanced webhook security and handling:

```php
// Future API
// Automatic webhook signature verification (HMAC)
'webhook' => [
    'verify_signature' => true,
    'signature_header' => 'X-Beem-Signature',
    'signature_algorithm' => 'sha256',
    'tolerance' => 300, // seconds
],
```

**Tasks**:

- [ ] Add HMAC signature verification
- [ ] Implement webhook replay protection (timestamp tolerance)
- [ ] Add webhook event logging/auditing
- [ ] Create webhook simulation tool for testing
- [ ] Add idempotency key support

---

#### 2.3 Sandbox/Testing Mode

**Priority**: 🟡 Medium  
**Target**: v2.1.0

Built-in testing mode without hitting real APIs:

```php
// Future API
// In tests
Beem::fake();

Beem::sms()->send($request);

Beem::assertSent(SmsRequest::class, function ($request) {
    return $request->message === 'Hello World';
});

Beem::assertSentCount(1);
Beem::assertNothingSent();
```

**Tasks**:

- [ ] Create `BeemFake` facade for testing
- [ ] Implement assertion methods
- [ ] Add request/response recording
- [ ] Create factory classes for DTOs
- [ ] Document testing patterns

---

#### 2.4 Multi-Tenancy Support

**Priority**: 🟢 Low  
**Target**: v2.2.0

Support for SaaS applications with multiple Beem accounts:

```php
// Future API
Beem::forTenant($tenant)->sms()->send($request);

// Or via middleware
Route::middleware('beem.tenant')->group(function () {
    // Uses tenant's Beem credentials
});
```

**Tasks**:

- [ ] Add dynamic credential resolution
- [ ] Support per-tenant configuration
- [ ] Add tenant context to webhooks
- [ ] Database-driven credential storage option

---

### Phase 3: Monitoring & Analytics (Q2-Q3 2025)

#### 3.1 Logging & Observability

**Priority**: 🔴 High  
**Target**: v2.0.0

Comprehensive logging for debugging and auditing:

```php
// config/beem-africa.php
'logging' => [
    'enabled' => true,
    'channel' => 'beem',
    'level' => 'info',
    'include_payload' => env('BEEM_LOG_PAYLOAD', false),
    'mask_sensitive' => true, // Mask phone numbers, API keys
],
```

**Tasks**:

- [ ] Add configurable logging to all HTTP clients
- [ ] Implement sensitive data masking
- [ ] Create dedicated log channel
- [ ] Add request/response correlation IDs
- [ ] Support structured logging (JSON format)

---

#### 3.2 Metrics & Dashboard

**Priority**: 🟢 Low  
**Target**: v2.3.0

Track usage, costs, and performance:

```php
// Future API
$stats = Beem::stats()->forPeriod('last_30_days');

$stats->smsSent;           // 1,234
$stats->smsDelivered;      // 1,180
$stats->deliveryRate;      // 95.6%
$stats->totalCost;         // 45,000 TZS
```

**Tasks**:

- [ ] Create `beem_metrics` database table
- [ ] Track all outbound operations
- [ ] Calculate delivery rates, costs
- [ ] Optional Pulse/Horizon integration
- [ ] Export to Prometheus/Grafana (optional)

---

#### 3.3 Cost Tracking & Budget Alerts

**Priority**: 🟢 Low  
**Target**: v2.3.0

Monitor spending and set budget limits:

```php
// config/beem-africa.php
'budget' => [
    'monthly_limit' => 500000, // TZS
    'alert_threshold' => 80, // Percent
    'alert_channels' => ['mail', 'slack'],
],

// Future API
Beem::budget()->remaining(); // 125,000 TZS
Beem::budget()->usedPercent(); // 75%
```

**Tasks**:

- [ ] Track costs per operation type
- [ ] Implement budget alerts
- [ ] Add cost estimation before sending
- [ ] Support multiple currencies
- [ ] Daily/weekly usage reports

---

### Phase 4: Additional Services (Q3-Q4 2025)

#### 4.1 Voice/Call Service

**Priority**: 🟢 Low  
**Target**: v3.0.0

If Beem adds voice API:

```php
// Future API (speculative)
Beem::voice()->call(
    to: '255712345678',
    message: 'Your order has shipped',
    voice: 'en-female'
);
```

**Tasks**:

- [ ] Monitor Beem API for voice features
- [ ] Implement when available
- [ ] Support IVR menus if available

---

#### 4.2 URL Shortener Service

**Priority**: 🟢 Low  
**Target**: v2.4.0

If Beem adds URL shortening:

```php
// Future API (speculative)
$shortUrl = Beem::shorten('https://myapp.com/orders/123');
// Returns: https://beem.link/abc123
```

---

#### 4.3 Email Service

**Priority**: 🟢 Low  
**Target**: v3.0.0

If Beem adds email API:

```php
// Future API (speculative)
Beem::email()->send(
    to: 'user@example.com',
    subject: 'Order Confirmation',
    html: view('emails.order', $data)
);
```

---

### Phase 5: Documentation & Community (Ongoing)

#### 5.1 Documentation Site

**Priority**: 🔴 High  
**Target**: Q1 2025

Create a dedicated documentation website:

**Tasks**:

- [ ] Set up VitePress/Docusaurus documentation site
- [ ] Write comprehensive API reference
- [ ] Create step-by-step tutorials
- [ ] Add code examples for all services
- [ ] Create video tutorials
- [ ] Add architecture diagrams
- [ ] Multi-language support (Swahili, French)

---

#### 5.2 Example Applications

**Priority**: 🟡 Medium  
**Target**: Q2 2025

Build reference implementations:

**Tasks**:

- [ ] **E-commerce**: Order notifications, OTP checkout
- [ ] **SaaS**: Multi-tenant messaging platform
- [ ] **Support Bot**: WhatsApp customer service
- [ ] **USSD Menu**: Mobile money integration
- [ ] Publish as separate repositories

---

#### 5.3 Community Building

**Priority**: 🟡 Medium  
**Target**: Ongoing

**Tasks**:

- [ ] Create CONTRIBUTING.md with guidelines
- [ ] Add issue templates (bug, feature, question)
- [ ] Set up GitHub Discussions
- [ ] Create Discord/Slack community
- [ ] Write blog posts about use cases
- [ ] Present at Laravel conferences
- [ ] Partner with Beem for official recognition

---

### Phase 6: Quality & Maintenance (Ongoing)

#### 6.1 Testing Improvements

**Priority**: 🔴 High  
**Target**: Ongoing

**Tasks**:

- [ ] Achieve 100% code coverage
- [ ] Add contract tests for API compatibility
- [ ] Implement mutation testing (Infection PHP)
- [ ] Add performance/load tests
- [ ] Automated dependency updates (Dependabot)

---

#### 6.2 Static Analysis & Type Safety

**Priority**: 🟡 Medium  
**Target**: v2.0.0

**Tasks**:

- [ ] Upgrade to PHPStan Level 9 (complete ✅)
- [ ] Add PHPDoc generics where applicable
- [ ] Consider PHP native type declarations where missing
- [ ] Add `@template` annotations for better IDE support

---

#### 6.3 Performance Optimization

**Priority**: 🟢 Low  
**Target**: v2.2.0

**Tasks**:

- [ ] Profile HTTP client performance
- [ ] Add connection pooling option
- [ ] Optimize DTO serialization
- [ ] Benchmark against alternatives
- [ ] Add caching for frequently accessed data (balance, templates)

---

## 📅 Release Schedule

| Version    | Target Date | Focus Area                             |
| ---------- | ----------- | -------------------------------------- |
| **v2.0.0** | Q1 2025     | Notifications, Queues, Logging         |
| **v2.1.0** | Q2 2025     | Templates, Testing Mode, Rate Limiting |
| **v2.2.0** | Q2 2025     | Multi-tenancy, Performance             |
| **v2.3.0** | Q3 2025     | Metrics, Dashboard, Budget Alerts      |
| **v2.4.0** | Q4 2025     | Additional services as Beem releases   |
| **v3.0.0** | 2026        | Major architectural improvements       |

---

## 🎯 Version 2.0.0 Milestone Checklist

The next major release will include:

- [ ] Laravel Notification channels (SMS, WhatsApp)
- [ ] Queue support for all outbound operations
- [ ] Automatic retries with exponential backoff
- [ ] Circuit breaker pattern
- [ ] Comprehensive logging with masking
- [ ] Testing fake/mock support
- [ ] Documentation website (initial version)
- [ ] CONTRIBUTING.md and issue templates
- [ ] Breaking change: Minimum PHP 8.2 (already met)
- [ ] Breaking change: Minimum Laravel 11 (already met)

---

## 🤝 Contributing

We welcome contributions! Here's how you can help:

1. **Feature Development**: Pick an item from this roadmap
2. **Bug Fixes**: Check GitHub issues
3. **Documentation**: Improve README or create tutorials
4. **Testing**: Add tests for edge cases
5. **Translations**: Help translate docs to other languages

### Priority Legend

- 🔴 **High**: Critical for next major release
- 🟡 **Medium**: Important but not blocking
- 🟢 **Low**: Nice to have, future consideration

---

## 📝 Feedback

Have suggestions for the roadmap?

- Open a [GitHub Discussion](https://github.com/gowelle/laravel-beem-africa/discussions)
- Create a [Feature Request](https://github.com/gowelle/laravel-beem-africa/issues/new?template=feature_request.md)
- Email: gowelle.john@icloud.com

---

_This roadmap is a living document and will be updated as priorities shift and new opportunities arise._

