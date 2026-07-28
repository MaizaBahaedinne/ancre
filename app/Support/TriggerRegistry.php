<?php

namespace App\Support;

use App\Contracts\DefinesNotificationTrigger;
use App\Models\NotificationTriggerOverride;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class TriggerRegistry
{
    public function triggerFromRouteName(string $routeName): ?string
    {
        $ignoredPrefixes = (array) config('notification_triggers.route_discovery.ignore_route_name_prefixes', []);
        if (collect($ignoredPrefixes)->contains(fn (string $prefix) => Str::startsWith($routeName, $prefix))) {
            return null;
        }

        $inferred = $this->inferFromRouteName($routeName);

        return $inferred['trigger'] ?? null;
    }

    /**
     * Return all known trigger definitions, merged from config and discovered events.
     *
     * @return array<int, array<string, mixed>>
     */
    public function definitions(): array
    {
        $configured = config('notification_triggers.definitions', []);
        $discovered = array_merge(
            $this->discoverFromEvents(),
            $this->discoverFromRoutes(),
        );
        $overrides = $this->overridesFromDatabase();

        $byTrigger = [];

        foreach ($discovered as $definition) {
            $normalized = $this->normalize($definition);
            $byTrigger[$normalized['trigger']] = $normalized;
        }

        // Config overrides discovered defaults for full control.
        foreach ($configured as $definition) {
            $normalized = $this->normalize($definition);
            $byTrigger[$normalized['trigger']] = array_merge(
                $byTrigger[$normalized['trigger']] ?? [],
                $normalized
            );
        }

        // Database overrides are highest priority.
        foreach ($overrides as $definition) {
            $normalized = $this->normalize($definition);
            $current = $byTrigger[$normalized['trigger']] ?? [];

            // Only override keys explicitly set in DB.
            foreach (['name', 'description', 'module', 'is_enabled', 'receivers'] as $key) {
                if (array_key_exists($key, $definition) && $definition[$key] !== null) {
                    $current[$key] = $normalized[$key];
                }
            }

            $current['trigger'] = $normalized['trigger'];
            $byTrigger[$normalized['trigger']] = $current;
        }

        return array_values($byTrigger);
    }

    /**
     * Discover triggers from named routes to cover platform modules automatically.
     *
     * @return array<int, array<string, mixed>>
     */
    private function discoverFromRoutes(): array
    {
        if (!config('notification_triggers.route_discovery.enabled', true)) {
            return [];
        }

        $definitions = [];
        $ignoredPrefixes = (array) config('notification_triggers.route_discovery.ignore_route_name_prefixes', []);

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if (!$name) {
                continue;
            }

            if (collect($ignoredPrefixes)->contains(fn (string $prefix) => Str::startsWith($name, $prefix))) {
                continue;
            }

            $inferred = $this->inferFromRouteName($name);
            if ($inferred !== null) {
                $definitions[] = $inferred;
            }
        }

        return $definitions;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function overridesFromDatabase(): array
    {
        if (!Schema::hasTable('notification_trigger_overrides')) {
            return [];
        }

        return NotificationTriggerOverride::query()
            ->get()
            ->map(function (NotificationTriggerOverride $item): array {
                return [
                    'trigger' => $item->trigger,
                    'name' => $item->name,
                    'description' => $item->description,
                    'module' => $item->module,
                    'is_enabled' => $item->is_enabled,
                    'receivers' => $item->receivers,
                ];
            })
            ->all();
    }

    /**
     * Discover triggers from App\Events classes.
     *
     * @return array<int, array<string, mixed>>
     */
    private function discoverFromEvents(): array
    {
        $eventPath = app_path('Events');

        if (!File::exists($eventPath)) {
            return [];
        }

        $definitions = [];

        foreach (File::allFiles($eventPath) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relative = Str::replaceFirst($eventPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
            $class = 'App\\Events\\'.Str::replace(['/', '\\', '.php'], ['\\', '\\', ''], $relative);

            if (!class_exists($class)) {
                continue;
            }

            if (is_subclass_of($class, DefinesNotificationTrigger::class)) {
                /** @var class-string<DefinesNotificationTrigger> $class */
                $definition = $class::notificationTriggerDefinition();
                if (!empty($definition['trigger'])) {
                    $definitions[] = $definition;
                }
                continue;
            }

            $inferred = $this->inferFromClassName(class_basename($class));
            if ($inferred !== null) {
                $definitions[] = $inferred;
            }
        }

        return $definitions;
    }

    /**
     * Infer trigger metadata from event class naming convention.
     * Example: ParentCreated => parent.created
     */
    private function inferFromClassName(string $classBaseName): ?array
    {
        $actions = (array) config('notification_triggers.action_suffixes', []);

        foreach ($actions as $action) {
            if (!Str::endsWith($classBaseName, $action)) {
                continue;
            }

            $subject = Str::beforeLast($classBaseName, $action);
            if ($subject === '') {
                return null;
            }

            $subjectKey = Str::snake($subject);
            $actionKey = Str::lower($action);
            $trigger = $subjectKey.'.'.$actionKey;
            $module = $this->inferModule($subjectKey);

            return [
                'trigger' => $trigger,
                'name' => Str::headline($subject).' '.Str::headline($action),
                'description' => 'Auto-discovered from event '.$classBaseName,
                'module' => $module,
                'is_enabled' => false,
                'receivers' => [],
            ];
        }

        return null;
    }

    /**
     * Infer trigger metadata from route names.
     * Example: parents.store => parent.created
     */
    private function inferFromRouteName(string $routeName): ?array
    {
        $parts = explode('.', $routeName);
        if (count($parts) < 2) {
            return null;
        }

        $action = array_pop($parts);
        $actionMap = (array) config('notification_triggers.route_discovery.action_map', []);
        $mappedAction = $actionMap[$action] ?? null;
        if (!$mappedAction) {
            return null;
        }

        $contextPrefixes = (array) config('notification_triggers.route_discovery.context_prefixes', []);
        while (count($parts) > 1 && in_array($parts[0], $contextPrefixes, true)) {
            array_shift($parts);
        }

        $entityRaw = (string) ($parts[0] ?? '');
        if ($entityRaw === '') {
            return null;
        }

        $entity = Str::singular(Str::snake(str_replace('-', '_', $entityRaw)));
        $trigger = $entity.'.'.$mappedAction;

        return [
            'trigger' => $trigger,
            'name' => Str::headline($entity).' '.Str::headline(str_replace('_', ' ', $mappedAction)),
            'description' => 'Auto-discovered from route '.$routeName,
            'module' => $this->inferModule($entity),
            'is_enabled' => false,
            'receivers' => [],
        ];
    }

    /**
     * Normalize one definition and infer missing metadata.
     *
     * @param array<string, mixed> $definition
     * @return array<string, mixed>
     */
    private function normalize(array $definition): array
    {
        $trigger = (string) ($definition['trigger'] ?? '');
        if ($trigger === '') {
            return $definition;
        }

        [$entity] = array_pad(explode('.', $trigger, 2), 2, null);

        return [
            'trigger' => $trigger,
            'name' => (string) ($definition['name'] ?? Str::headline(str_replace('.', ' ', $trigger))),
            'description' => (string) ($definition['description'] ?? 'Workflow auto-generated for '.$trigger),
            'module' => (string) ($definition['module'] ?? $this->inferModule((string) $entity)),
            'is_enabled' => (bool) ($definition['is_enabled'] ?? false),
            'receivers' => is_array($definition['receivers'] ?? null) ? $definition['receivers'] : [],
        ];
    }

    private function inferModule(string $entity): string
    {
        $moduleMap = (array) config('notification_triggers.module_map', []);

        foreach ($moduleMap as $prefix => $module) {
            if (Str::startsWith($entity, (string) $prefix)) {
                return (string) $module;
            }
        }

        return 'general';
    }
}
