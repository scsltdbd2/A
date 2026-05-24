<?php
require_once __DIR__ . '/../Core/Database.php';

class Person
{
    public static function all(): array
    {
        return Database::query("SELECT * FROM persons WHERE archived = 0 ORDER BY name");
    }

    public static function find(string $id): ?array
    {
        return Database::queryOne("SELECT * FROM persons WHERE id = ? AND archived = 0", [$id]);
    }

    public static function create(array $data): array
    {
        $id = generatePersonId();
        $now = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO persons (id, name, photo, phone, email, relationship_type_id, address, employee_salary_taka, starting_balance_taka, archived, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)";
        
        Database::execute($sql, [
            $id,
            $data['name'] ?? '',
            $data['photo'] ?? '',
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['relationship_type_id'] ?? '',
            $data['address'] ?? '',
            emptyToNull($data['employee_salary_taka'] ?? null),
            emptyToNull($data['starting_balance_taka'] ?? null),
            $now,
            $now
        ]);
        
        return self::find($id);
    }

    public static function update(string $id, array $data): ?array
    {
        $now = date('Y-m-d H:i:s');
        
        $sql = "UPDATE persons SET 
                name = ?, photo = ?, phone = ?, email = ?, relationship_type_id = ?, 
                address = ?, employee_salary_taka = ?, starting_balance_taka = ?, updated_at = ?
                WHERE id = ? AND archived = 0";
        
        Database::execute($sql, [
            $data['name'] ?? '',
            $data['photo'] ?? '',
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['relationship_type_id'] ?? '',
            $data['address'] ?? '',
            emptyToNull($data['employee_salary_taka'] ?? null),
            emptyToNull($data['starting_balance_taka'] ?? null),
            $now,
            $id
        ]);
        
        return self::find($id);
    }

    public static function archive(string $id): bool
    {
        $now = date('Y-m-d H:i:s');
        return Database::execute("UPDATE persons SET archived = 1, updated_at = ? WHERE id = ?", [$now, $id]) > 0;
    }

    public static function search(string $query): array
    {
        $search = "%{$query}%";
        return Database::query("SELECT * FROM persons WHERE archived = 0 AND (name LIKE ? OR phone LIKE ?) ORDER BY name", [$search, $search]);
    }
}