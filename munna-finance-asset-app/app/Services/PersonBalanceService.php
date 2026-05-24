<?php
require_once __DIR__ . '/../Core/Database.php';

class PersonBalanceService
{
    public static function getBalance(string $personId): ?array
    {
        return Database::queryOne("SELECT * FROM view_person_balances WHERE person_id = ?", [$personId]);
    }

    public static function getAllBalances(): array
    {
        return Database::query("SELECT * FROM view_person_balances ORDER BY person_name");
    }

    public static function getTotalReceivable(): float
    {
        $result = Database::queryOne("SELECT COALESCE(SUM(final_balance), 0) as total FROM view_person_balances WHERE final_balance > 0");
        return (float)($result['total'] ?? 0);
    }

    public static function getTotalPayable(): float
    {
        $result = Database::queryOne("SELECT ABS(COALESCE(SUM(final_balance), 0)) as total FROM view_person_balances WHERE final_balance < 0");
        return (float)($result['total'] ?? 0);
    }

    public static function getTotalBalance(): float
    {
        $result = Database::queryOne("SELECT COALESCE(SUM(final_balance), 0) as total FROM view_person_balances");
        return (float)($result['total'] ?? 0);
    }
}