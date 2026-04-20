# save-page-now-2

PHP 8.2+ client library for the **Save Page Now 2 (SPN2) API** provided by the [Internet Archive Wayback Machine](https://web.archive.org/save).

## Documentation officielle

- [Google Doc (Vangelis Banos, Internet Archive)](https://docs.google.com/document/d/1Nsv52MvSjbLb2PCpHlat0gkzw0EvtSgpKHu4mk0MnrA/)  ← référence la plus complète
- [Gist miroir lisible](https://gist.github.com/regstuff/82e690db2f1d91ba59f6681c1abad6cf)

---

## Installation

```bash
composer require inwebo/save-page-now-2
```

Obtenez vos clés S3 sur <https://archive.org/account/s3.php>.

---

## Utilisation rapide

```php
use Inwebo\SavePageNow2\Auth\S3Credentials;
use Inwebo\SavePageNow2\Capture\CaptureOptionsBuilder;
use Inwebo\SavePageNow2\Response\JobStatus;
use Inwebo\SavePageNow2\SavePageNow2Client;
use Symfony\Component\HttpClient\HttpClient;

$client = new SavePageNow2Client(
    HttpClient::create(),
    new S3Credentials('my-access-key', 'my-secret'),
);

// 1. Soumettre une URL
$options = (new CaptureOptionsBuilder())
    ->withSkipFirstArchive()   // plus rapide
    ->withJsBehaviorTimeout(0) // page sans JS
    ->build();

$job = $client->capture('https://example.com/', $options);
echo "Job démarré : {$job->jobId}\n";

// 2. Polling jusqu'à la fin
do {
    sleep(5);
    $status = $client->getStatus($job->jobId);
} while ($status->getStatus() === JobStatus::Pending);

// 3. Résultat
if ($status->getStatus() === JobStatus::Success) {
    echo "✅ Archivé : {$status->getWaybackUrl()}\n";
} else {
    echo "❌ Erreur : {$status->getMessage()} ({$status->getStatusExt()})\n";
}
```

---

## Architecture

```
src/
├── Auth/
│   ├── AuthInterface.php          Interface générique d'authentification
│   ├── S3Credentials.php          Authorization: LOW key:secret  (recommandé)
│   └── CookieCredentials.php      logged-in-user + logged-in-sig (fallback)
│
├── Capture/
│   ├── CaptureOptions.php         Value object readonly — tous les paramètres POST
│   └── CaptureOptionsBuilder.php  Fluent builder immutable
│
├── Response/
│   ├── JobStatus.php              Enum: Pending | Success | Error
│   ├── CaptureJobResponse.php     Réponse du POST /save  {url, job_id}
│   ├── UserStatusResponse.php     GET /save/status/user  {available, processing}
│   ├── SystemStatusResponse.php   GET /save/status/system {status}
│   └── Status/
│       ├── StatusResponseInterface.php
│       ├── StatusResponseFactory.php   Dispatch json → bonne implémentation
│       ├── PendingStatusResponse.php
│       ├── SuccessStatusResponse.php   + getWaybackUrl()
│       └── ErrorStatusResponse.php     + getStatusExt(), getMessage()
│
├── Exception/
│   ├── SavePageNowException.php       Base
│   ├── ApiException.php               Réponse inattendue / malformée
│   ├── AuthenticationException.php    HTTP 401 / error:unauthorized
│   ├── UserSessionLimitException.php  error:user-session-limit
│   └── NetworkException.php           Erreur de transport Symfony
│
├── SavePageNow2Interface.php      Contrat public du client
└── SavePageNow2Client.php         Implémentation Symfony HttpClient
```

---

## API complète

### `capture(string $url, ?CaptureOptions $options = null): CaptureJobResponse`

Soumet une URL pour archivage. Retourne immédiatement un `job_id`.

### `getStatus(string $jobId): StatusResponseInterface`

Retourne `PendingStatusResponse`, `SuccessStatusResponse` ou `ErrorStatusResponse`.

### `getStatuses(array $jobIds): array<string, StatusResponseInterface>`

Statuts de plusieurs jobs en une seule requête.

### `getOutlinksStatus(string $parentJobId): array<string, StatusResponseInterface>`

Statuts de tous les outlinks d'un job parent (nécessite `capture_outlinks=1`).

### `getUserStatus(): UserStatusResponse`

Sessions actives et disponibles pour l'utilisateur authentifié.

### `getSystemStatus(): SystemStatusResponse`

Santé globale du service SPN2.

---

## Options de capture

| Builder method                          | Paramètre API             | Description |
|-----------------------------------------|---------------------------|-------------|
| `withCaptureAll()`                      | `capture_all=1`           | Capture aussi les pages 4xx/5xx |
| `withCaptureOutlinks()`                 | `capture_outlinks=1`      | Archive automatiquement les outlinks |
| `withCaptureScreenshot()`               | `capture_screenshot=1`    | Capture un screenshot PNG pleine page |
| `withDelayWbAvailability()`             | `delay_wb_availability=1` | Disponible dans ~12 h (réduit la charge) |
| `withForceGet()`                        | `force_get=1`             | Force un GET simple (pas de headless) |
| `withSkipFirstArchive()`                | `skip_first_archive=1`    | Skip le check "premier archivage" (plus rapide) |
| `withIfNotArchivedWithin(string)`       | `if_not_archived_within`  | N'archive que si plus ancien que `"3d 5h"` |
| `withOutlinksAvailability()`            | `outlinks_availability=1` | Retourne le timestamp du dernier snapshot de chaque outlink |
| `withEmailResult()`                     | `email_result=1`          | Envoie un rapport par email |
| `withJsBehaviorTimeout(int $s)`         | `js_behavior_timeout=N`   | Temps JS après chargement (0–30 s) |
| `withCaptureCookie(string)`             | `capture_cookie`          | Cookie HTTP supplémentaire pour la cible |
| `withTargetCredentials(string, string)` | `target_username/password`| Credentials pour les formulaires de la cible |

---

## Gestion des erreurs

```php
use Inwebo\SavePageNow2\Exception\AuthenticationException;
use Inwebo\SavePageNow2\Exception\UserSessionLimitException;
use Inwebo\SavePageNow2\Exception\NetworkException;
use Inwebo\SavePageNow2\Exception\ApiException;

try {
    $job = $client->capture('https://example.com/');
} catch (AuthenticationException $e) {
    // Clés S3 invalides ou expirées
} catch (UserSessionLimitException $e) {
    // 12 captures simultanées atteintes (authentifié) / 6 (anonyme)
} catch (NetworkException $e) {
    // Problème de transport (timeout, DNS…)
} catch (ApiException $e) {
    // Réponse inattendue de l'API
}
```

Les codes d'erreur `status_ext` (ex: `error:not-found`, `error:too-many-daily-captures`) sont
disponibles sur `ErrorStatusResponse::getStatusExt()`.

---

## Tests

Avec PHPUnit installé via Composer :

```bash
composer install
./vendor/bin/phpunit
```

Sans Composer (runner autonome inclus dans le repo) :

```bash
php run-tests.php
```

```
Save Page Now 2 — Test Suite
========================================
..........................................................................................
58 tests, 58 passed, 0 failed
```

---

## Licence

GPL-3.0-or-later
