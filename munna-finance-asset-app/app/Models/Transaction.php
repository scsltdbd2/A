<?php
require_once __DIR__ . '/../Core/Database.php';

class Transaction
{
    public static function all(): array
    {
        return Database::query("SELECT * FROM transactions WHERE archived = 0 ORDER BY created_at DESC");
    }

    public static function find(string $id): ?array
    {
        return Database::queryOne("SELECT * FROM transactions WHERE id = ? AND archived = 0", [$id]);
    }

    public static function findByPerson(string $personId): array
    {
        return Database::query("SELECT * FROM transactions WHERE person_id = ? AND archived = 0 ORDER BY created_at DESC", [$personId]);
    }

    public static function create(array $data): array
    {
        $id = generateTransactionId();
        $now = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO transactions (id, person_id, person_name, type_id, amount_taka, due_taka, due_date, paid_by_id, to_method, to_person_or_name, to_account_id, from_person_or_name, from_account_id, notes, archived, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)";
        
        Database::execute($sql, [
            $id,
            $data['person_id'] ?? '',
            $data['person_name'] ?? '',
            $data['type_id'] ?? '',
            $data['amount_taka'] ?? 0,
            emptyToNull($data['due_taka'] ?? null),
            $data['due_date'] ?? '',
            $data['paid_by_id'] ?? '',
            $data['to_method'] ?? '',
            $data['to_person_or_name'] ?? '',
            $data['to_account_id'] ?? '',
            $data['from_person_or_name'] ?? '',
            $data['from_account_id'] ?? '',
            $data['notes'] ?? '',
            $now,
            $now
        ]);
        
        return self::find($id);
    }

    public static function update(string $id, array $data): ?array
    {
        $now = date('Y-m-d H:i:s');
        
        $sql = "UPDATE transactions SET 
                person_id = ?, person_name = ?, type_id = ?, amount_taka = ?, due_taka = ?, due_date = ?, 
                paid_by_id = ?, to_method = ?, to_person_or_name = ?, to_account_id = ?, 
                from_person_or_name = ?, from_account_id = ?, notes = ?, updated_at = ?
                WHERE id = ? AND archived = 0";
        
        Database::execute($sql, [
            $data['person_id'] ?? '',
            $data['person_name'] ?? '',
            $data['type_id'] ?? '',
            $data['amount_taka'] ?? 0,
            emptyToNull($data['due_taka'] ?? null),
            $data['due_date'] ?? '',
            $data['paid_by_id'] ?? '',
            $data['to_method'] ?? '',
            $data['to_person_or_name'] ?? '',
            $data['to_account_id'] ?? '',
            $data['from_person_or_name'] ?? '',
            $data['from_account_id'] ?? '',
            $data['notes'] ?? '',
            $now,
            $id
        ]);
        
        return self::find($id);
    }

    public static function archive(string $id): bool
    {
        $now = date('Y-m-d H:i:s');
        return Database::execute("UPDATE transactions SET archived = 1, updated_at = ? WHERE id = ?", [$now, $id]) > 0;
    }

    public static function recent(int $limit = 10): array
    {
        return Database::query("SELECT * FROM transactions WHERE archived = 0 ORDER BY created_at DESC LIMIT ?", [$limit]);
    }
}