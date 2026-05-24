<?php
require_once __DIR__ . '/../Core/Database.php';

class DashboardService
{
    public static function getSummary(): array
    {
        $totalAssetsValue = Database::queryOne("SELECT COALESCE(SUM(land_share_value_taka), 0) as total FROM assets WHERE archived = 0");
        $totalConstruction = Database::queryOne("SELECT COALESCE(SUM(apartment_construction_total_taka), 0) as total FROM view_asset_totals");
        $totalGrand = Database::queryOne("SELECT COALESCE(SUM(apartment_grand_total_taka), 0) as total FROM view_asset_totals");
        $totalPeopleBalance = Database::queryOne("SELECT COALESCE(SUM(final_balance), 0) as total FROM view_person_balances");
        $totalReceivable = Database::queryOne("SELECT COALESCE(SUM(final_balance), 0) as total FROM view_person_balances WHERE final_balance > 0");
        $totalPayable = Database::queryOne("SELECT COALESCE(SUM(final_balance), 0) as total FROM view_person_balances WHERE final_balance < 0");
        $totalSalaryDue = Database::queryOne("SELECT COALESCE(SUM(due_taka), 0) as total FROM transactions WHERE type_id = 'transfer_salary' AND archived = 0");
        
        return [
            'total_assets_value' => (float)($totalAssetsValue['total'] ?? 0),
            'total_construction_expense' => (float)($totalConstruction['total'] ?? 0),
            'total_grand_asset_value' => (float)($totalGrand['total'] ?? 0),
            'total_people_balance' => (float)($totalPeopleBalance['total'] ?? 0),
            'total_money_receivable' => (float)($totalReceivable['total'] ?? 0),
            'total_money_payable' => abs((float)($totalPayable['total'] ?? 0)),
            'total_salary_due' => (float)($totalSalaryDue['total'] ?? 0),
            'persons_count' => Database::queryOne("SELECT COUNT(*) as cnt FROM persons WHERE archived = 0")['cnt'],
            'assets_count' => Database::queryOne("SELECT COUNT(*) as cnt FROM assets WHERE archived = 0")['cnt'],
            'accounts_count' => Database::queryOne("SELECT COUNT(*) as cnt FROM accounts WHERE archived = 0")['cnt'],
        ];
    }

    public static function getRecentPersonTransactions(int $limit = 10): array
    {
        return Database::query("SELECT t.*, p.name as person_name FROM transactions t LEFT JOIN persons p ON p.id = t.person_id WHERE t.archived = 0 ORDER BY t.created_at DESC LIMIT ?", [$limit]);
    }

    public static function getRecentAssetHistory(int $limit = 10): array
    {
        $history = [];
        
        $installments = Database::query("SELECT i.*, a.name as asset_name, 'installment' as type FROM asset_construction_installments i LEFT JOIN assets a ON a.id = i.asset_id WHERE i.archived = 0 ORDER BY i.created_at DESC LIMIT ?", [$limit]);
        $specials = Database::query("SELECT s.*, a.name as asset_name, 'special_installment' as type FROM asset_construction_special_installments s LEFT JOIN assets a ON a.id = s.asset_id WHERE s.archived = 0 ORDER BY s.created_at DESC LIMIT ?", [$limit]);
        $materials = Database::query("SELECT m.*, a.name as asset_name, 'material' as type FROM asset_construction_materials m LEFT JOIN assets a ON a.id = m.asset_id WHERE m.archived = 0 ORDER BY m.created_at DESC LIMIT ?", [$limit]);
        $labour = Database::query("SELECT l.*, a.name as asset_name, 'labour' as type FROM asset_construction_labour l LEFT JOIN assets a ON a.id = l.asset_id WHERE l.archived = 0 ORDER BY l.created_at DESC LIMIT ?", [$limit]);
        $transport = Database::query("SELECT t.*, a.name as asset_name, 'transport' as type FROM asset_construction_transport t LEFT JOIN assets a ON a.id = t.asset_id WHERE t.archived = 0 ORDER BY t.created_at DESC LIMIT ?", [$limit]);
        
        $all = array_merge($installments, $specials, $materials, $labour, $transport);
        usort($all, function($a, $b) {
            return strtotime($b['created_at'] ?? '') - strtotime($a['created_at'] ?? '');
        });
        
        return array_slice($all, 0, $limit);
    }
}