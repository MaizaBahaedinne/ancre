<?php

namespace App\Support;

use App\Contracts\DefinesNotificationTrigger;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TriggerRegistry
{
    /**
     * Return all known trigger definitions, merged from config and discovered events.
     *
     * @return array<int, array<string, mixed>>
     */
    public function definitions(): array
    {
        $configured = config('notification_triggers.definitions', []);
        $discovered = $this->discoverFromEvents();

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

        return array_values($byTrigger);
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
