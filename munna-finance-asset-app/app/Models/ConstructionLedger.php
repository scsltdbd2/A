<?php
require_once __DIR__ . '/../Core/Database.php';

class ConstructionLedger
{
    // Installments
    public static function getInstallments(string $shareId): array
    {
        return Database::query("SELECT * FROM asset_construction_installments WHERE asset_share_id = ? AND archived = 0 ORDER BY date_text DESC", [$shareId]);
    }

    public static function createInstallment(array $data): array
    {
        $id = generateInstallmentId();
        $now = date('Y-m-d H:i:s');
        
        Database::execute("INSERT INTO asset_construction_installments (id, asset_id, asset_share_id, to_account_id, from_account_id, amount_taka, date_text, bank_copy_attachment, manager_received_copy_attachment, bank_receipt_trxid_number, r_no, c_no, notes, archived, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)", [
            $id, $data['asset_id'], $data['asset_share_id'], $data['to_account_id'] ?? '', $data['from_account_id'] ?? '', $data['amount_taka'], $data['date_text'] ?? '', $data['bank_copy_attachment'] ?? '', $data['manager_received_copy_attachment'] ?? '', $data['bank_receipt_trxid_number'] ?? '', $data['r_no'] ?? '', $data['c_no'] ?? '', $data['notes'] ?? '', $now, $now
        ]);
        
        return Database::queryOne("SELECT * FROM asset_construction_installments WHERE id = ?", [$id]);
    }

    // Special Installments
    public static function getSpecialInstallments(string $shareId): array
    {
        return Database::query("SELECT * FROM asset_construction_special_installments WHERE asset_share_id = ? AND archived = 0 ORDER BY date_text DESC", [$shareId]);
    }

    public static function createSpecialInstallment(array $data): array
    {
        $id = generateSpecialInstallmentId();
        $now = date('Y-m-d H:i:s');
        
        Database::execute("INSERT INTO asset_construction_special_installments (id, asset_id, asset_share_id, to_account_id, from_account_id, amount_taka, date_text, bank_copy_attachment, manager_received_copy_attachment, bank_receipt_trxid_number, r_no, c_no, notes, archived, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)", [
            $id, $data['asset_id'], $data['asset_share_id'], $data['to_account_id'] ?? '', $data['from_account_id'] ?? '', $data['amount_taka'], $data['date_text'] ?? '', $data['bank_copy_attachment'] ?? '', $data['manager_received_copy_attachment'] ?? '', $data['bank_receipt_trxid_number'] ?? '', $data['r_no'] ?? '', $data['c_no'] ?? '', $data['notes'] ?? '', $now, $now
        ]);
        
        return Database::queryOne("SELECT * FROM asset_construction_special_installments WHERE id = ?", [$id]);
    }

    // Materials
    public static function getMaterials(string $shareId): array
    {
        return Database::query("SELECT * FROM asset_construction_materials WHERE asset_share_id = ? AND archived = 0 ORDER BY created_at DESC", [$shareId]);
    }

    public static function createMaterial(array $data): array
    {
        $id = generateMaterialId();
        $now = date('Y-m-d H:i:s');
        
        Database::execute("INSERT INTO asset_construction_materials (id, asset_id, asset_share_id, material_name, amount_taka, supplier, notes, archived, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?)", [
            $id, $data['asset_id'], $data['asset_share_id'], $data['material_name'], $data['amount_taka'], $data['supplier'] ?? '', $data['notes'] ?? '', $now, $now
        ]);
        
        return Database::queryOne("SELECT * FROM asset_construction_materials WHERE id = ?", [$id]);
    }

    // Labour
    public static function getLabour(string $shareId): array
    {
        return Database::query("SELECT * FROM asset_construction_labour WHERE asset_share_id = ? AND archived = 0 ORDER BY created_at DESC", [$shareId]);
    }

    public static function createLabour(array $data): array
    {
        $id = generateLabourId();
        $now = date('Y-m-d H:i:s');
        
        Database::execute("INSERT INTO asset_construction_labour (id, asset_id, asset_share_id, labour_for, amount_taka, notes, archived, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)", [
            $id, $data['asset_id'], $data['asset_share_id'], $data['labour_for'], $data['amount_taka'], $data['notes'] ?? '', $now, $now
        ]);
        
        return Database::queryOne("SELECT * FROM asset_construction_labour WHERE id = ?", [$id]);
    }

    // Transport
    public static function getTransport(string $shareId): array
    {
        return Database::query("SELECT * FROM asset_construction_transport WHERE asset_share_id = ? AND archived = 0 ORDER BY created_at DESC", [$shareId]);
    }

    public static function createTransport(array $data): array
    {
        $id = generateTransportId();
        $now = date('Y-m-d H:i:s');
        
        Database::execute("INSERT INTO asset_construction_transport (id, asset_id, asset_share_id, transport_for, amount_taka, notes, archived, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)", [
            $id, $data['asset_id'], $data['asset_share_id'], $data['transport_for'], $data['amount_taka'], $data['notes'] ?? '', $now, $now
        ]);
        
        return Database::queryOne("SELECT * FROM asset_construction_transport WHERE id = ?", [$id]);
    }
}