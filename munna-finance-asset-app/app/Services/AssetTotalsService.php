<?php
require_once __DIR__ . '/../Core/Database.php';

class AssetTotalsService
{
    public static function getTotals(string $assetId): ?array
    {
        return Database::queryOne("SELECT * FROM view_asset_totals WHERE asset_id = ?", [$assetId]);
    }

    public static function getAllTotals(): array
    {
        return Database::query("SELECT * FROM view_asset_totals ORDER BY asset_name");
    }

    public static function getShareTotals(string $shareId): ?array
    {
        return Database::queryOne("SELECT * FROM view_asset_share_totals WHERE asset_share_id = ?", [$shareId]);
    }

    public static function getTotalLandValue(): float
    {
        $result = Database::queryOne("SELECT COALESCE(SUM(land_share_value_taka), 0) as total FROM assets WHERE archived = 0");
        return (float)($result['total'] ?? 0);
    }

    public static function getTotalConstructionExpense(): float
    {
        $result = Database::queryOne("SELECT COALESCE(SUM(apartment_construction_total_taka), 0) as total FROM view_asset_totals");
        return (float)($result['total'] ?? 0);
    }

    public static function getTotalGrandValue(): float
    {
        $result = Database::queryOne("SELECT COALESCE(SUM(apartment_grand_total_taka), 0) as total FROM view_asset_totals");
        return (float)($result['total'] ?? 0);
    }
}