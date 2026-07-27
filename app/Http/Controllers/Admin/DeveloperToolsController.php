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
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?? request()->getHost();
        $port = 443;
        $timeout = 10;

        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer'       => true,
                'verify_peer_name'  => true,
                'SNI_enabled'       => true,
            ],
        ]);

        $client = @stream_socket_client(
            "ssl://{$host}:{$port}",
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (! $client) {
            return [
                'host'       => $host,
                'reachable'  => false,
                'error'      => $errstr ?: 'Connexion impossible',
            ];
        }

        $params = stream_context_get_params($client);
        fclose($client);

        $cert = $params['options']['ssl']['peer_certificate'] ?? null;
        if (! $cert) {
            return ['host' => $host, 'reachable' => false, 'error' => 'Certificat introuvable'];
        }

        $info    = openssl_x509_parse($cert);
        $validTo = $info['validTo_time_t'] ?? 0;
        $validFrom = $info['validFrom_time_t'] ?? 0;
        $now     = time();
        $daysLeft = (int) ceil(($validTo - $now) / 86400);
        $subject  = $info['subject']['CN'] ?? '';
        $issuer   = $info['issuer']['O'] ?? ($info['issuer']['CN'] ?? 'Inconnu');
        $sans     = [];
        if (isset($info['extensions']['subjectAltName'])) {
            preg_match_all('/DNS:([^,\s]+)/', $info['extensions']['subjectAltName'], $m);
            $sans = $m[1] ?? [];
        }

        return [
            'host'        => $host,
            'reachable'   => true,
            'valid'       => $now >= $validFrom && $now <= $validTo,
            'subject'     => $subject,
            'issuer'      => $issuer,
            'sans'        => $sans,
            'valid_from'  => date('d/m/Y', $validFrom),
            'valid_to'    => date('d/m/Y', $validTo),
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