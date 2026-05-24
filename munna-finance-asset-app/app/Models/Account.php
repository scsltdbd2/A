<?php
require_once __DIR__ . '/../Core/Database.php';

class Account
{
    public static function all(): array
    {
        return Database::query("SELECT * FROM accounts WHERE archived = 0 ORDER BY owner_type, owner_id");
    }

    public static function find(string $id): ?array
    {
        return Database::queryOne("SELECT * FROM accounts WHERE id = ? AND archived = 0", [$id]);
    }

    public static function findByOwner(string $ownerType, string $ownerId): array
    {
        return Database::query("SELECT * FROM accounts WHERE owner_type = ? AND owner_id = ? AND archived = 0", [$ownerType, $ownerId]);
    }

    public static function create(array $data): array
    {
        $id = generateAccountId();
        $now = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO accounts (id, owner_type, owner_id, kind, channel_name_id, account_type_id, bank_name, branch, account_number, account_holder_name, routing_number, account, archived, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)";
        
        Database::execute($sql, [
            $id,
            $data['owner_type'] ?? '',
            $data['owner_id'] ?? '',
            $data['kind'] ?? '',
            $data['channel_name_id'] ?? '',
            $data['account_type_id'] ?? '',
            $data['bank_name'] ?? '',
            $data['branch'] ?? '',
            $data['account_number'] ?? '',
            $data['account_holder_name'] ?? '',
            $data['routing_number'] ?? '',
            $data['account'] ?? '',
            $now,
            $now
        ]);
        
        return self::find($id);
    }

    public static function update(string $id, array $data): ?array
    {
        $now = date('Y-m-d H:i:s');
        
        $sql = "UPDATE accounts SET 
                kind = ?, channel_name_id = ?, account_type_id = ?, bank_name = ?, branch = ?, 
                account_number = ?, account_holder_name = ?, routing_number = ?, account = ?, updated_at = ?
                WHERE id = ? AND archived = 0";
        
        Database::execute($sql, [
            $data['kind'] ?? '',
            $data['channel_name_id'] ?? '',
            $data['account_type_id'] ?? '',
            $data['bank_name'] ?? '',
            $data['branch'] ?? '',
            $data['account_number'] ?? '',
            $data['account_holder_name'] ?? '',
            $data['routing_number'] ?? '',
            $data['account'] ?? '',
            $now,
            $id
        ]);
        
        return self::find($id);
    }

    public static function archive(string $id): bool
    {
        $now = date('Y-m-d H:i:s');
        return Database::execute("UPDATE accounts SET archived = 1, updated_at = ? WHERE id = ?", [$now, $id]) > 0;
    }
}