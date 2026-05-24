<?php
require_once __DIR__ . '/../Core/Database.php';

class AssetShare
{
    public static function all(): array
    {
        return Database::query("SELECT * FROM asset_shares WHERE archived = 0 ORDER BY asset_id, share_number");
    }

    public static function find(string $id): ?array
    {
        return Database::queryOne("SELECT * FROM asset_shares WHERE id = ? AND archived = 0", [$id]);
    }

    public static function findByAsset(string $assetId): array
    {
        return Database::query("SELECT * FROM asset_shares WHERE asset_id = ? AND archived = 0 ORDER BY share_number", [$assetId]);
    }

    public static function create(array $data): array
    {
        $id = generateAssetShareId();
        $now = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO asset_shares (id, asset_id, share_number, share_type_id, seller_name, share_purchase_amount_taka, archived, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)";
        
        Database::execute($sql, [
            $id,
            $data['asset_id'] ?? '',
            $data['share_number'] ?? '',
            $data['share_type_id'] ?? '',
            $data['seller_name'] ?? '',
            emptyToNull($data['share_purchase_amount_taka'] ?? null),
            $now,
            $now
        ]);
        
        return self::find($id);
    }

    public static function update(string $id, array $data): ?array
    {
        $now = date('Y-m-d H:i:s');
        
        $sql = "UPDATE asset_shares SET 
                share_number = ?, share_type_id = ?, seller_name = ?, share_purchase_amount_taka = ?, updated_at = ?
                WHERE id = ? AND archived = 0";
        
        Database::execute($sql, [
            $data['share_number'] ?? '',
            $data['share_type_id'] ?? '',
            $data['seller_name'] ?? '',
            emptyToNull($data['share_purchase_amount_taka'] ?? null),
            $now,
            $id
        ]);
        
        return self::find($id);
    }

    public static function archive(string $id): bool
    {
        $now = date('Y-m-d H:i:s');
        return Database::execute("UPDATE asset_shares SET archived = 1, updated_at = ? WHERE id = ?", [$now, $id]) > 0;
    }
}