<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Service;

defined('_JEXEC') or die;

use RuntimeException;

final class CoreIntegrationService
{
    private const COMPONENT = 'com_decaroevents';
    private const PUBLISHED_ENTITIES = ['event', 'session', 'registration'];

    public function isAvailable(): bool
    {
        return class_exists(\Xdecaro\Core\Integration\EntityReference::class)
            && class_exists(\Xdecaro\Core\Integration\RelationReference::class);
    }

    public function createEntityReference(string $entity, int|string $id): object
    {
        $this->assertEntity($entity);
        $this->assertAvailable();
        return new \Xdecaro\Core\Integration\EntityReference(self::COMPONENT, $entity, $id);
    }

    public function createRelationReference(string $sourceEntity, int|string $sourceId, string $targetComponent, string $targetEntity, int|string $targetId, string $relationType): object
    {
        $source = $this->createEntityReference($sourceEntity, $sourceId);
        $this->assertAvailable();
        $target = new \Xdecaro\Core\Integration\EntityReference($targetComponent, $targetEntity, $targetId);
        return new \Xdecaro\Core\Integration\RelationReference($source, $target, $relationType);
    }

    private function assertAvailable(): void
    {
        if (!$this->isAvailable()) {
            throw new RuntimeException('Core by xdecaro public integration API is unavailable.');
        }
    }

    private function assertEntity(string $entity): void
    {
        if (!in_array($entity, self::PUBLISHED_ENTITIES, true)) {
            throw new RuntimeException('Unknown Events public entity type: ' . $entity);
        }
    }
}
