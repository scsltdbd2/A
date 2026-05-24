<?php
require_once __DIR__ . '/../Core/Database.php';

class Lookup
{
    public static function getTable(string $table): array
    {
        return Database::query("SELECT * FROM {$table} ORDER BY sort_order");
    }

    public static function getActive(string $table): array
    {
        return Database::query("SELECT * FROM {$table} WHERE is_active = 1 ORDER BY sort_order");
    }

    public static function find(string $table, string $id): ?array
    {
        return Database::queryOne("SELECT * FROM {$table} WHERE id = ?", [$id]);
    }

    public static function create(string $table, array $data): array
    {
        $id = $data['id'] ?? generateId(str_replace('lookup_', '', $table));
        $now = date('Y-m-d H:i:s');
        
        Database::execute("INSERT INTO {$table} (id, name, sort_order, is_active) VALUES (?, ?, ?, 1)", [
            $id, $data['name'] ?? '', $data['sort_order'] ?? 0
        ]);
        
        return self::find($table, $id);
    }

    public static function update(string $table, string $id, array $data): ?array
    {
        Database::execute("UPDATE {$table} SET name = ?, sort_order = ? WHERE id = ?", [
            $data['name'] ?? '', $data['sort_order'] ?? 0, $id
        ]);
        
        return self::find($table, $id);
    }

    public static function archive(string $table, string $id): bool
    {
        return Database::execute("UPDATE {$table} SET is_active = 0 WHERE id = ?", [$id]) > 0;
    }

    public static function getAllTables(): array
    {
        return [
            'lookup_financial_types',
            'lookup_apartment_statuses',
            'lookup_share_types',
            'lookup_expense_categories',
            'lookup_relationship_types',
            'lookup_bank_names',
            'lookup_mobile_channels',
            'lookup_mobile_account_types',
            'lookup_transfer_types',
            'lookup_paid_by_options'
        ];
    }
}