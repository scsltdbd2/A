<?php
require_once __DIR__ . '/../Core/Database.php';

class Asset
{
    public static function all(): array
    {
        return Database::query("SELECT * FROM assets WHERE archived = 0 ORDER BY name");
    }

    public static function find(string $id): ?array
    {
        return Database::queryOne("SELECT * FROM assets WHERE id = ? AND archived = 0", [$id]);
    }

    public static function create(array $data): array
    {
        $id = generateAssetId();
        $now = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO assets (id, name, address, land_share_value_taka, total_size_sq_feet, actual_size_sq_feet, unit_number, status_id, lift_integration_ready, spec_bedroom, spec_drawing, spec_dining, spec_bathroom, spec_belconies, spec_kitchen, spec_living_room, spec_others, manager_name, manager_phone, engineer_name, engineer_phone, fin_type_id, fin_bank_name, fin_bank_branch, fin_routing_number, fin_account_number, fin_account_holder_name, archived, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)";
        
        Database::execute($sql, [
            $id,
            $data['name'] ?? '',
            $data['address'] ?? '',
            emptyToNull($data['land_share_value_taka'] ?? null),
            emptyToNull($data['total_size_sq_feet'] ?? null),
            emptyToNull($data['actual_size_sq_feet'] ?? null),
            $data['unit_number'] ?? '',
            $data['status_id'] ?? '',
            $data['lift_integration_ready'] ?? 'no',
            $data['spec_bedroom'] ?? null,
            $data['spec_drawing'] ?? null,
            $data['spec_dining'] ?? null,
            $data['spec_bathroom'] ?? null,
            $data['spec_belconies'] ?? null,
            $data['spec_kitchen'] ?? null,
            $data['spec_living_room'] ?? null,
            $data['spec_others'] ?? '',
            $data['manager_name'] ?? '',
            $data['manager_phone'] ?? '',
            $data['engineer_name'] ?? '',
            $data['engineer_phone'] ?? '',
            $data['fin_type_id'] ?? '',
            $data['fin_bank_name'] ?? '',
            $data['fin_bank_branch'] ?? '',
            $data['fin_routing_number'] ?? '',
            $data['fin_account_number'] ?? '',
            $data['fin_account_holder_name'] ?? '',
            $now,
            $now
        ]);
        
        return self::find($id);
    }

    public static function update(string $id, array $data): ?array
    {
        $now = date('Y-m-d H:i:s');
        
        $sql = "UPDATE assets SET 
                name = ?, address = ?, land_share_value_taka = ?, total_size_sq_feet = ?, actual_size_sq_feet = ?, 
                unit_number = ?, status_id = ?, lift_integration_ready = ?, spec_bedroom = ?, spec_drawing = ?, 
                spec_dining = ?, spec_bathroom = ?, spec_belconies = ?, spec_kitchen = ?, spec_living_room = ?, 
                spec_others = ?, manager_name = ?, manager_phone = ?, engineer_name = ?, engineer_phone = ?, 
                fin_type_id = ?, fin_bank_name = ?, fin_bank_branch = ?, fin_routing_number = ?, 
                fin_account_number = ?, fin_account_holder_name = ?, updated_at = ?
                WHERE id = ? AND archived = 0";
        
        Database::execute($sql, [
            $data['name'] ?? '',
            $data['address'] ?? '',
            emptyToNull($data['land_share_value_taka'] ?? null),
            emptyToNull($data['total_size_sq_feet'] ?? null),
            emptyToNull($data['actual_size_sq_feet'] ?? null),
            $data['unit_number'] ?? '',
            $data['status_id'] ?? '',
            $data['lift_integration_ready'] ?? 'no',
            $data['spec_bedroom'] ?? null,
            $data['spec_drawing'] ?? null,
            $data['spec_dining'] ?? null,
            $data['spec_bathroom'] ?? null,
            $data['spec_belconies'] ?? null,
            $data['spec_kitchen'] ?? null,
            $data['spec_living_room'] ?? null,
            $data['spec_others'] ?? '',
            $data['manager_name'] ?? '',
            $data['manager_phone'] ?? '',
            $data['engineer_name'] ?? '',
            $data['engineer_phone'] ?? '',
            $data['fin_type_id'] ?? '',
            $data['fin_bank_name'] ?? '',
            $data['fin_bank_branch'] ?? '',
            $data['fin_routing_number'] ?? '',
            $data['fin_account_number'] ?? '',
            $data['fin_account_holder_name'] ?? '',
            $now,
            $id
        ]);
        
        return self::find($id);
    }

    public static function archive(string $id): bool
    {
        $now = date('Y-m-d H:i:s');
        return Database::execute("UPDATE assets SET archived = 1, updated_at = ? WHERE id = ?", [$now, $id]) > 0;
    }
}