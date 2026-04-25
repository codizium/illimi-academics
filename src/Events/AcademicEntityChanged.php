<?php

namespace Illimi\Academics\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcademicEntityChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public readonly string $entity;
    public readonly string $action;
    public readonly array $payload;

    public function __construct(string $entity, string $action, array $payload)
    {
        $this->entity = $entity;
        $this->action = $action;

        $actor = auth()->user();
        if ($actor) {
            $payload['actor_user_id'] = $payload['actor_user_id'] ?? $actor->id;
            $payload['actor_name'] = $payload['actor_name'] ?? ($actor->name ?? null);
            $payload['organization_id'] = $payload['organization_id'] ?? ($actor->organization_id ?? null);
        }

        $this->payload = $payload;
    }

    public static function fromModel(string $entity, string $action, object $model, array $previous = []): self
    {
        $payload = [
            'id' => $model->id ?? null,
            'organization_id' => $model->organization_id ?? null,
            'name' => $model->name ?? ($model->title ?? null),
            'code' => $model->code ?? ($model->slug ?? null),
            'slug' => $model->slug ?? null,
            'description' => $model->description ?? null,
            'status' => $model->status ?? null,
            'start_date' => isset($model->start_date) ? optional($model->start_date)->toIso8601String() : null,
            'end_date' => isset($model->end_date) ? optional($model->end_date)->toIso8601String() : null,
            'is_default' => isset($model->is_default) ? (bool) $model->is_default : null,
            'academic_year_id' => $model->academic_year_id ?? null,
            'terms_count' => $model->terms_count ?? null,
        ];

        if (method_exists($model, 'relationLoaded') && $model->relationLoaded('academicYear') && isset($model->academicYear)) {
            $payload['academic_year_name'] = $model->academicYear?->name;
        }

        foreach ($previous as $key => $value) {
            $payload['previous_'.$key] = $value;
        }

        return new self($entity, $action, $payload);
    }

    public function broadcastOn(): array
    {
        $organizationId = (string) ($this->payload['organization_id'] ?? 'global');

        return [new PrivateChannel("org.{$organizationId}.academics")];
    }

    public function broadcastAs(): string
    {
        return 'entity.changed';
    }

    public function broadcastWith(): array
    {
        return array_merge($this->payload, [
            'entity' => $this->entity,
            'action' => $this->action,
            'id' => $this->payload['id'] ?? null,
            'organization_id' => $this->payload['organization_id'] ?? null,
            'name' => $this->payload['name'] ?? null,
            'code' => $this->payload['code'] ?? null,
            'slug' => $this->payload['slug'] ?? null,
            'description' => $this->payload['description'] ?? null,
            'status' => $this->payload['status'] ?? null,
            'start_date' => $this->payload['start_date'] ?? null,
            'end_date' => $this->payload['end_date'] ?? null,
            'is_default' => $this->payload['is_default'] ?? null,
            'academic_year_id' => $this->payload['academic_year_id'] ?? null,
            'academic_year_name' => $this->payload['academic_year_name'] ?? null,
            'terms_count' => $this->payload['terms_count'] ?? null,
            'previous_status' => $this->payload['previous_status'] ?? null,
            'previous_start_date' => $this->payload['previous_start_date'] ?? null,
            'previous_end_date' => $this->payload['previous_end_date'] ?? null,
            'previous_is_default' => $this->payload['previous_is_default'] ?? null,
            'previous_academic_year_id' => $this->payload['previous_academic_year_id'] ?? null,
            'actor_user_id' => $this->payload['actor_user_id'] ?? null,
            'actor_name' => $this->payload['actor_name'] ?? null,
            'at' => now()->toISOString(),
        ]);
    }
}
