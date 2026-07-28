<?php

namespace App\Http\Middleware;

use App\Services\NotificationService;
use App\Support\TriggerRegistry;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class DispatchWorkflowNotificationFromRoute
{
    /**
     * Dispatch workflow notifications based on route naming conventions.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$this->shouldDispatch($request, $response)) {
            return $response;
        }

        $route = $request->route();
        $routeName = $route?->getName();
        if (!is_string($routeName) || $routeName === '') {
            return $response;
        }

        $trigger = app(TriggerRegistry::class)->triggerFromRouteName($routeName);
        if (!$trigger) {
            return $response;
        }

        $skipTriggers = (array) config('notification_triggers.runtime.skip_triggers', []);
        if (in_array($trigger, $skipTriggers, true)) {
            return $response;
        }

        NotificationService::dispatch($trigger, $this->buildPayload($request, $trigger, $routeName));

        return $response;
    }

    private function shouldDispatch(Request $request, Response $response): bool
    {
        if (!config('notification_triggers.runtime.auto_dispatch_from_routes', true)) {
            return false;
        }

        if (!$request->user()) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        $allowedMethods = array_map('strtoupper', (array) config('notification_triggers.runtime.dispatch_methods', ['POST', 'PUT', 'PATCH', 'DELETE']));

        if (!in_array(strtoupper($request->method()), $allowedMethods, true)) {
            return false;
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(Request $request, string $trigger, string $routeName): array
    {
        [$entity, $action] = array_pad(explode('.', $trigger, 2), 2, null);
        $entity = (string) ($entity ?? 'item');
        $action = (string) ($action ?? 'updated');

        $entityLabel = Str::headline(str_replace('_', ' ', $entity));
        $actionLabel = Str::headline(str_replace('_', ' ', $action));

        $metadata = [
            'trigger' => $trigger,
            'route_name' => $routeName,
            'request_method' => strtoupper($request->method()),
            'action_url' => $request->fullUrl(),
            'entity' => $entity,
            'action' => $action,
            'created_by_id' => $request->user()?->id,
            'created_by_name' => $request->user()?->name,
            'created_by_email' => $request->user()?->email,
        ];

        foreach ($request->route()?->parametersWithoutNulls() ?? [] as $key => $value) {
            if (is_scalar($value)) {
                $metadata[(string) $key] = $value;

                if (Str::endsWith((string) $key, '_id')) {
                    $metadata[$entity.'_id'] = $value;
                }

                continue;
            }

            if (is_object($value)) {
                if (method_exists($value, 'getKey')) {
                    $metadata[(string) $key.'_id'] = $value->getKey();
                    $metadata[$entity.'_id'] = $value->getKey();
                }

                foreach (['name', 'nom', 'title', 'prenom', 'email'] as $field) {
                    if (isset($value->{$field}) && is_scalar($value->{$field})) {
                        $metadata[(string) $key.'_'.$field] = (string) $value->{$field};
                    }
                }

                if (isset($value->name) && is_scalar($value->name)) {
                    $metadata[$entity.'_name'] = (string) $value->name;
                } elseif (isset($value->nom) && is_scalar($value->nom)) {
                    $metadata[$entity.'_name'] = (string) $value->nom;
                }
            }
        }

        $subject = $entityLabel.' '.$actionLabel;
        $description = ($request->user()?->name ?? 'Systeme').' a effectue '.mb_strtolower($actionLabel).' sur '.$entityLabel.'.';

        return [
            'subject' => $subject,
            'description' => $description,
            'metadata' => $metadata,
        ];
    }
}
