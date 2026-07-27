<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DeveloperToolsController extends Controller
{
    public function index(): View
    {
        $ssl = $this->checkSslCertificate();

        $deploymentSteps = [
            ['title' => 'Mettre a jour le code', 'command' => 'git pull origin main', 'description' => 'Recupere les derniers changements du depot distant.'],
            ['title' => 'Installer les dependances PHP', 'command' => 'composer install --no-dev --optimize-autoloader', 'description' => 'Reinstalle les packages de production et optimise l’autoloader.'],
            ['title' => 'Executer les migrations', 'command' => 'php artisan migrate --force', 'description' => 'Applique les changements de structure de base de donnees.'],
            ['title' => 'Rebuilder les caches', 'command' => 'php artisan optimize:clear && php artisan optimize', 'description' => 'Actualise les caches de config, routes et vues.'],
            ['title' => 'Compiler les assets', 'command' => 'npm ci && npm run build', 'description' => 'Rebuild le frontend si Node est disponible sur la machine de deploiement.'],
        ];

        $logFiles = $this->discoverLogFiles();
        $latestLog = $logFiles[0] ?? null;
        $latestEntries = $latestLog ? $this->readLogEntries($latestLog['path'], null, null, 8) : [];
        $latestLevelCounts = collect($latestEntries)->groupBy('level')->map->count()->all();

        return view('admin.developer.index', compact('deploymentSteps', 'logFiles', 'latestLog', 'latestEntries', 'latestLevelCounts', 'ssl'));
    }

    public function logs(Request $request): View
    {
        $logFiles = $this->discoverLogFiles();
        $selectedFile = $request->string('file')->trim()->toString();
        $level = $request->string('level')->trim()->lower()->toString();
        $search = $request->string('search')->trim()->toString();

        $selectedLog = $this->resolveLogFile($logFiles, $selectedFile) ?? ($logFiles[0] ?? null);
        $entries = $selectedLog ? $this->readLogEntries($selectedLog['path'], $level, $search) : [];

        $groupedEntries = collect($entries)
            ->groupBy(fn (array $entry) => Carbon::parse($entry['timestamp'])->format('Y-m-d'))
            ->sortKeysDesc();

        $levelCounts = collect($entries)
            ->groupBy('level')
            ->map->count()
            ->sortDesc()
            ->all();

        return view('admin.developer.logs', compact('logFiles', 'selectedLog', 'entries', 'groupedEntries', 'levelCounts', 'level', 'search'));
    }

    private function checkSslCertificate(): array
    {
        $host    = parse_url(config('app.url'), PHP_URL_HOST) ?? request()->getHost();
        $timeout = 10;

        if (! function_exists('curl_init')) {
            return ['host' => $host, 'reachable' => false, 'error' => 'Extension cURL non disponible'];
        }

        // Step 1 — fetch the raw PEM certificate via cURL (verify_peer off just to grab the cert)
        $ch = curl_init("https://{$host}/");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => $timeout,
            CURLOPT_CONNECTTIMEOUT  => $timeout,
            CURLOPT_SSL_VERIFYPEER  => false,   // grab cert even when CA chain unknown locally
            CURLOPT_SSL_VERIFYHOST  => 0,
            CURLOPT_CERTINFO        => true,
            CURLOPT_HEADER          => false,
            CURLOPT_NOBODY          => true,
            CURLOPT_FOLLOWLOCATION  => false,
        ]);

        curl_exec($ch);
        $errno    = curl_errno($ch);
        $errstr   = curl_error($ch);
        $certInfo = curl_getinfo($ch, CURLINFO_CERTINFO);
        curl_close($ch);

        if ($errno || empty($certInfo)) {
            return [
                'host'      => $host,
                'reachable' => false,
                'error'     => $errstr ?: 'Connexion impossible (cURL #' . $errno . ')',
            ];
        }

        // Step 2 — parse dates from the first cert in the chain
        $leaf     = $certInfo[0];
        $subject  = $leaf['Subject'] ?? '';
        $issuer   = $leaf['Issuer'] ?? 'Inconnu';
        $start    = $leaf['Start date'] ?? '';
        $expire   = $leaf['Expire date'] ?? '';

        $validFrom = $start  ? strtotime($start)  : 0;
        $validTo   = $expire ? strtotime($expire) : 0;
        $now       = time();
        $daysLeft  = $validTo ? (int) ceil(($validTo - $now) / 86400) : 0;

        // Extract CN from subject string "CN=foo, O=bar, ..."
        preg_match('/CN=([^,]+)/', $subject, $cnMatch);
        $subjectCN = trim($cnMatch[1] ?? $subject);

        // Extract O from issuer string
        preg_match('/O=([^,]+)/', $issuer, $oMatch);
        $issuerO = trim($oMatch[1] ?? $issuer);

        // Step 3 — collect SANs from the leaf cert
        $sans = [];
        if (isset($leaf['Cert']) && is_string($leaf['Cert'])) {
            $parsed = @openssl_x509_parse($leaf['Cert']);
            if ($parsed && isset($parsed['extensions']['subjectAltName'])) {
                preg_match_all('/DNS:([^,\s]+)/', $parsed['extensions']['subjectAltName'], $m);
                $sans = $m[1] ?? [];
            }
        }

        $info      = null; // kept for compat below
        $validFrom = $validFrom;
        $validTo   = $validTo;

        return [
            'host'        => $host,
            'reachable'   => true,
            'valid'       => $now >= $validFrom && $now <= $validTo,
            'subject'     => $subjectCN,
            'issuer'      => $issuerO,
            'sans'        => $sans,
            'valid_from'  => $validFrom ? date('d/m/Y', $validFrom) : '—',
            'valid_to'    => $validTo   ? date('d/m/Y', $validTo)   : '—',
            'days_left'   => $daysLeft,
        ];
    }

    private function discoverLogFiles(): array
    {
        return collect(glob(storage_path('logs/*')) ?: [])
            ->filter(fn (string $path) => is_file($path))
            ->map(fn (string $path) => [
                'path' => $path,
                'name' => basename($path),
                'size' => filesize($path) ?: 0,
                'modified_at' => filemtime($path) ?: time(),
            ])
            ->sortByDesc('modified_at')
            ->values()
            ->all();
    }

    private function resolveLogFile(array $logFiles, string $name): ?array
    {
        if ($name === '') {
            return null;
        }

        foreach ($logFiles as $logFile) {
            if ($logFile['name'] === $name) {
                return $logFile;
            }
        }

        return null;
    }

    private function readLogEntries(string $path, ?string $level = null, ?string $search = null, int $limit = 250): array
    {
        if (! is_readable($path)) {
            return [];
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];
        $entries = [];
        $current = null;

        foreach ($lines as $line) {
            if (preg_match('/^\[(.*?)\]\s+([A-Za-z0-9_\-]+)\.([A-Z]+):\s?(.*)$/', $line, $matches)) {
                if ($current) {
                    $entries[] = $current;
                }

                $current = [
                    'timestamp' => $matches[1],
                    'channel' => $matches[2],
                    'level' => strtolower($matches[3]),
                    'message' => $matches[4],
                    'raw' => [$line],
                ];

                continue;
            }

            if ($current) {
                $current['raw'][] = $line;
                $current['message'] .= PHP_EOL.$line;
            }
        }

        if ($current) {
            $entries[] = $current;
        }

        $entries = array_reverse($entries);

        if ($level !== '' && $level !== null) {
            $entries = array_values(array_filter($entries, fn (array $entry) => $entry['level'] === $level));
        }

        if ($search !== '' && $search !== null) {
            $needle = mb_strtolower($search);
            $entries = array_values(array_filter($entries, function (array $entry) use ($needle) {
                $haystack = mb_strtolower(implode(PHP_EOL, $entry['raw']));

                return str_contains($haystack, $needle);
            }));
        }

        return array_slice($entries, 0, $limit);
    }
}