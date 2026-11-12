<?php

namespace Tests\Unit;

use App\Services\CustomerModJobService;
use PHPUnit\Framework\TestCase;

class CustomerModJobServiceTest extends TestCase
{
    private const BASE = 'mod_jobs_famiglia_comp';

    private function moduleSchema(): array
    {
        return [[
            'id' => 8,
            'dynamic' => '1',
            'schema' => json_encode([[
                '$formkit' => 'group',
                'name' => self::BASE,
                'children' => [
                    ['$formkit' => 'text', 'name' => 'mod_jobs_famiglia_comp_nome'],
                    ['$formkit' => 'text', 'name' => 'mod_jobs_famiglia_comp_cf'],
                ],
            ]]),
        ]];
    }

    public function test_double_suffix_orphan_moved_to_first_free_slot(): void
    {
        $values = [
            self::BASE => ['mod_jobs_famiglia_comp_nome' => 'A'],
            self::BASE . '_2_1' => ['mod_jobs_famiglia_comp_nome' => 'B'],
            'other' => 'x',
        ];

        [$repaired, $changes] = CustomerModJobService::repairDynamicGroups($values, [self::BASE]);

        $this->assertSame([
            'other' => 'x',
            self::BASE => ['mod_jobs_famiglia_comp_nome' => 'A'],
            self::BASE . '_1' => ['mod_jobs_famiglia_comp_nome' => 'B'],
        ], $repaired);
        $this->assertSame([self::BASE, self::BASE . '_2_1'], $changes[0]['before']);
        $this->assertSame([self::BASE, self::BASE . '_1'], $changes[0]['after']);
    }

    public function test_identical_orphan_dropped_as_duplicate(): void
    {
        $values = [
            self::BASE => ['n' => 'A'],
            self::BASE . '_1' => ['n' => 'B', 'cf' => 'X'],
            self::BASE . '_2_1' => ['cf' => 'X', 'n' => 'B'],
        ];

        [$repaired, $changes] = CustomerModJobService::repairDynamicGroups($values, [self::BASE]);

        $this->assertSame([self::BASE, self::BASE . '_1'], array_keys($repaired));
        $this->assertSame(['mod_jobs_famiglia_comp_2_1 (duplicato di mod_jobs_famiglia_comp_1)'], $changes[0]['dropped']);
    }

    public function test_different_orphan_kept_and_ordered_by_numeric_tuple(): void
    {
        $values = [
            self::BASE . '_3' => ['n' => 'D'],
            self::BASE . '_2_1' => ['n' => 'C'],
            self::BASE => ['n' => 'A'],
            self::BASE . '_1' => ['n' => 'B'],
        ];

        [$repaired] = CustomerModJobService::repairDynamicGroups($values, [self::BASE]);

        $this->assertSame([
            self::BASE => ['n' => 'A'],
            self::BASE . '_1' => ['n' => 'B'],
            self::BASE . '_2' => ['n' => 'C'],
            self::BASE . '_3' => ['n' => 'D'],
        ], $repaired);
    }

    public function test_strict_compare_does_not_merge_numeric_like_strings(): void
    {
        $values = [
            self::BASE => ['tel' => '0444'],
            self::BASE . '_2_1' => ['tel' => '444'],
        ];

        [$repaired] = CustomerModJobService::repairDynamicGroups($values, [self::BASE]);

        $this->assertCount(2, $repaired);
    }

    public function test_empty_entries_dropped(): void
    {
        $values = [
            self::BASE => [],
            self::BASE . '_2_1' => ['n' => 'B'],
        ];

        [$repaired, $changes] = CustomerModJobService::repairDynamicGroups($values, [self::BASE]);

        $this->assertSame([self::BASE => ['n' => 'B']], $repaired);
        $this->assertSame(['mod_jobs_famiglia_comp (vuoto)'], $changes[0]['dropped']);
    }

    public function test_canonical_family_untouched(): void
    {
        $values = [
            self::BASE => ['n' => 'A'],
            self::BASE . '_1' => ['n' => 'B'],
        ];

        [$repaired, $changes] = CustomerModJobService::repairDynamicGroups($values, [self::BASE]);

        $this->assertSame($values, $repaired);
        $this->assertSame([], $changes);
    }

    public function test_non_whitelisted_multi_suffix_key_untouched(): void
    {
        $values = [
            'foo' => ['n' => 'A'],
            'foo_2_1' => ['n' => 'B'],
        ];

        [$repaired, $changes] = CustomerModJobService::repairDynamicGroups($values, [self::BASE]);

        $this->assertSame($values, $repaired);
        $this->assertSame([], $changes);
    }

    public function test_hydrate_edit_exposes_orphan_in_canonical_slot(): void
    {
        $persisted = [
            self::BASE => ['n' => 'A'],
            self::BASE . '_2_1' => ['n' => 'B'],
        ];

        $hydrated = CustomerModJobService::hydrateEdit($this->moduleSchema(), $persisted);

        $this->assertSame(['n' => 'B'], $hydrated['values'][self::BASE . '_1']);
        $this->assertArrayNotHasKey(self::BASE . '_2_1', $hydrated['values']);

        $names = array_column(json_decode($hydrated['schema'][0]['schema'], true), 'name');
        $this->assertSame([self::BASE, self::BASE . '_1'], $names);
    }

    public function test_merge_module_values_removes_legacy_multi_suffix_key(): void
    {
        $existing = [
            self::BASE => ['n' => 'A'],
            self::BASE . '_2_1' => ['n' => 'B'],
            'other_module_field' => 'keep',
        ];
        $schema = $this->moduleSchema();
        $schema[0]['schema'] = json_encode([
            ['$formkit' => 'group', 'name' => self::BASE],
            ['$formkit' => 'group', 'name' => self::BASE . '_1'],
        ]);
        $posted = [
            self::BASE => ['n' => 'A'],
            self::BASE . '_1' => ['n' => 'B'],
        ];

        $final = CustomerModJobService::mergeModuleValues($existing, $schema, $posted);

        $this->assertSame(['n' => 'B'], $final[self::BASE . '_1']);
        $this->assertArrayNotHasKey(self::BASE . '_2_1', $final);
        $this->assertSame('keep', $final['other_module_field']);
    }
}
