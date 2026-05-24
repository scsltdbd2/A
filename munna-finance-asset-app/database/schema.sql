-- Lookup Tables
CREATE TABLE IF NOT EXISTS lookup_financial_types (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS lookup_apartment_statuses (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS lookup_share_types (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS lookup_expense_categories (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS lookup_relationship_types (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS lookup_bank_names (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS lookup_mobile_channels (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS lookup_mobile_account_types (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS lookup_transfer_types (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS lookup_paid_by_options (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

-- My Information Tables
CREATE TABLE IF NOT EXISTS my_information (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT DEFAULT '',
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS my_information_phones (
    id TEXT PRIMARY KEY,
    my_information_id TEXT NOT NULL,
    phone TEXT NOT NULL
);

-- Accounts Table
CREATE TABLE IF NOT EXISTS accounts (
    id TEXT PRIMARY KEY,
    owner_type TEXT NOT NULL,
    owner_id TEXT NOT NULL,
    kind TEXT NOT NULL,
    channel_name_id TEXT DEFAULT '',
    account_type_id TEXT DEFAULT '',
    bank_name TEXT DEFAULT '',
    branch TEXT DEFAULT '',
    account_number TEXT DEFAULT '',
    account_holder_name TEXT DEFAULT '',
    routing_number TEXT DEFAULT '',
    account TEXT DEFAULT '',
    archived INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Persons Table
CREATE TABLE IF NOT EXISTS persons (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    photo TEXT DEFAULT '',
    phone TEXT DEFAULT '',
    email TEXT DEFAULT '',
    relationship_type_id TEXT DEFAULT '',
    address TEXT DEFAULT '',
    employee_salary_taka REAL DEFAULT NULL,
    starting_balance_taka REAL DEFAULT NULL,
    archived INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Transactions Table
CREATE TABLE IF NOT EXISTS transactions (
    id TEXT PRIMARY KEY,
    person_id TEXT NOT NULL,
    person_name TEXT DEFAULT '',
    type_id TEXT NOT NULL,
    amount_taka REAL NOT NULL,
    due_taka REAL DEFAULT NULL,
    due_date TEXT DEFAULT '',
    paid_by_id TEXT NOT NULL,
    to_method TEXT DEFAULT '',
    to_person_or_name TEXT DEFAULT '',
    to_account_id TEXT DEFAULT '',
    from_person_or_name TEXT DEFAULT '',
    from_account_id TEXT DEFAULT '',
    notes TEXT DEFAULT '',
    archived INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (person_id) REFERENCES persons(id)
);

-- Assets Table
CREATE TABLE IF NOT EXISTS assets (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    address TEXT DEFAULT '',
    land_share_value_taka REAL DEFAULT NULL,
    total_size_sq_feet REAL DEFAULT NULL,
    actual_size_sq_feet REAL DEFAULT NULL,
    unit_number TEXT DEFAULT '',
    status_id TEXT NOT NULL,
    lift_integration_ready TEXT NOT NULL DEFAULT 'no',
    spec_bedroom INTEGER DEFAULT NULL,
    spec_drawing INTEGER DEFAULT NULL,
    spec_dining INTEGER DEFAULT NULL,
    spec_bathroom INTEGER DEFAULT NULL,
    spec_belconies INTEGER DEFAULT NULL,
    spec_kitchen INTEGER DEFAULT NULL,
    spec_living_room INTEGER DEFAULT NULL,
    spec_others TEXT DEFAULT '',
    manager_name TEXT DEFAULT '',
    manager_phone TEXT DEFAULT '',
    engineer_name TEXT DEFAULT '',
    engineer_phone TEXT DEFAULT '',
    fin_type_id TEXT DEFAULT '',
    fin_bank_name TEXT DEFAULT '',
    fin_bank_branch TEXT DEFAULT '',
    fin_routing_number TEXT DEFAULT '',
    fin_account_number TEXT DEFAULT '',
    fin_account_holder_name TEXT DEFAULT '',
    archived INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Asset Shares Table
CREATE TABLE IF NOT EXISTS asset_shares (
    id TEXT PRIMARY KEY,
    asset_id TEXT NOT NULL,
    share_number TEXT NOT NULL,
    share_type_id TEXT NOT NULL,
    seller_name TEXT DEFAULT '',
    share_purchase_amount_taka REAL DEFAULT NULL,
    archived INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id)
);

-- Construction Ledger Tables
CREATE TABLE IF NOT EXISTS asset_construction_installments (
    id TEXT PRIMARY KEY,
    asset_id TEXT NOT NULL,
    asset_share_id TEXT NOT NULL,
    to_account_id TEXT DEFAULT '',
    from_account_id TEXT DEFAULT '',
    amount_taka REAL NOT NULL,
    date_text TEXT DEFAULT '',
    bank_copy_attachment TEXT DEFAULT '',
    manager_received_copy_attachment TEXT DEFAULT '',
    bank_receipt_trxid_number TEXT DEFAULT '',
    r_no TEXT DEFAULT '',
    c_no TEXT DEFAULT '',
    notes TEXT DEFAULT '',
    archived INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS asset_construction_special_installments (
    id TEXT PRIMARY KEY,
    asset_id TEXT NOT NULL,
    asset_share_id TEXT NOT NULL,
    to_account_id TEXT DEFAULT '',
    from_account_id TEXT DEFAULT '',
    amount_taka REAL NOT NULL,
    date_text TEXT DEFAULT '',
    bank_copy_attachment TEXT DEFAULT '',
    manager_received_copy_attachment TEXT DEFAULT '',
    bank_receipt_trxid_number TEXT DEFAULT '',
    r_no TEXT DEFAULT '',
    c_no TEXT DEFAULT '',
    notes TEXT DEFAULT '',
    archived INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS asset_construction_materials (
    id TEXT PRIMARY KEY,
    asset_id TEXT NOT NULL,
    asset_share_id TEXT NOT NULL,
    material_name TEXT NOT NULL,
    amount_taka REAL NOT NULL,
    supplier TEXT DEFAULT '',
    notes TEXT DEFAULT '',
    archived INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS asset_construction_labour (
    id TEXT PRIMARY KEY,
    asset_id TEXT NOT NULL,
    asset_share_id TEXT NOT NULL,
    labour_for TEXT NOT NULL,
    amount_taka REAL NOT NULL,
    notes TEXT DEFAULT '',
    archived INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS asset_construction_transport (
    id TEXT PRIMARY KEY,
    asset_id TEXT NOT NULL,
    asset_share_id TEXT NOT NULL,
    transport_for TEXT NOT NULL,
    amount_taka REAL NOT NULL,
    notes TEXT DEFAULT '',
    archived INTEGER DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role TEXT DEFAULT 'admin',
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- AI Pending Actions Table
CREATE TABLE IF NOT EXISTS ai_pending_actions (
    id TEXT PRIMARY KEY,
    user_message TEXT NOT NULL,
    domain TEXT NOT NULL,
    intent TEXT NOT NULL,
    api_action TEXT NOT NULL,
    payload_json TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'waiting_confirmation',
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- Computed Views
CREATE VIEW IF NOT EXISTS view_person_balances AS
SELECT 
    p.id AS person_id, 
    p.name AS person_name, 
    COALESCE(p.starting_balance_taka, 0) AS starting_balance_taka,
    COALESCE(SUM(CASE WHEN t.type_id = 'transfer_giving_loan' THEN t.amount_taka ELSE 0 END), 0) AS giving_loan_total,
    COALESCE(SUM(CASE WHEN t.type_id = 'transfer_collect_due' THEN t.amount_taka ELSE 0 END), 0) AS collect_due_total,
    COALESCE(SUM(CASE WHEN t.type_id = 'transfer_taking_loan' THEN t.amount_taka ELSE 0 END), 0) AS taking_loan_total,
    COALESCE(SUM(CASE WHEN t.type_id = 'transfer_pay_due' THEN t.amount_taka ELSE 0 END), 0) AS pay_due_total,
    COALESCE(SUM(CASE WHEN t.type_id = 'transfer_salary' THEN COALESCE(t.due_taka, 0) ELSE 0 END), 0) AS salary_due_total,
    COALESCE(SUM(CASE WHEN t.type_id = 'transfer_service_bill' THEN t.amount_taka ELSE 0 END), 0) AS service_bill_total,
    0 AS gift_effect,
    ( 
        COALESCE(p.starting_balance_taka, 0) 
        + COALESCE(SUM(CASE WHEN t.type_id = 'transfer_giving_loan' THEN t.amount_taka ELSE 0 END), 0) 
        - COALESCE(SUM(CASE WHEN t.type_id = 'transfer_collect_due' THEN t.amount_taka ELSE 0 END), 0) 
        - COALESCE(SUM(CASE WHEN t.type_id = 'transfer_taking_loan' THEN t.amount_taka ELSE 0 END), 0) 
        + COALESCE(SUM(CASE WHEN t.type_id = 'transfer_pay_due' THEN t.amount_taka ELSE 0 END), 0) 
        - COALESCE(SUM(CASE WHEN t.type_id = 'transfer_salary' THEN COALESCE(t.due_taka, 0) ELSE 0 END), 0) 
        - COALESCE(SUM(CASE WHEN t.type_id = 'transfer_service_bill' THEN t.amount_taka ELSE 0 END), 0) 
    ) AS final_balance 
FROM persons p
LEFT JOIN transactions t ON t.person_id = p.id AND t.archived = 0
WHERE p.archived = 0
GROUP BY p.id;

CREATE VIEW IF NOT EXISTS view_asset_share_totals AS
SELECT 
    s.id AS asset_share_id, 
    s.asset_id, 
    s.share_number,
    COALESCE((SELECT SUM(amount_taka) FROM asset_construction_installments WHERE asset_share_id = s.id AND archived = 0), 0) AS installments_total_taka,
    COALESCE((SELECT SUM(amount_taka) FROM asset_construction_special_installments WHERE asset_share_id = s.id AND archived = 0), 0) AS special_installments_total_taka,
    COALESCE((SELECT SUM(amount_taka) FROM asset_construction_materials WHERE asset_share_id = s.id AND archived = 0), 0) AS materials_total_taka,
    COALESCE((SELECT SUM(amount_taka) FROM asset_construction_labour WHERE asset_share_id = s.id AND archived = 0), 0) AS labour_total_taka,
    COALESCE((SELECT SUM(amount_taka) FROM asset_construction_transport WHERE asset_share_id = s.id AND archived = 0), 0) AS transport_total_taka,
    (
        COALESCE((SELECT SUM(amount_taka) FROM asset_construction_installments WHERE asset_share_id = s.id AND archived = 0), 0) +
        COALESCE((SELECT SUM(amount_taka) FROM asset_construction_special_installments WHERE asset_share_id = s.id AND archived = 0), 0) +
        COALESCE((SELECT SUM(amount_taka) FROM asset_construction_materials WHERE asset_share_id = s.id AND archived = 0), 0) +
        COALESCE((SELECT SUM(amount_taka) FROM asset_construction_labour WHERE asset_share_id = s.id AND archived = 0), 0) +
        COALESCE((SELECT SUM(amount_taka) FROM asset_construction_transport WHERE asset_share_id = s.id AND archived = 0), 0)
    ) AS grand_total_taka
FROM asset_shares s
WHERE s.archived = 0;

CREATE VIEW IF NOT EXISTS view_asset_totals AS
SELECT 
    a.id AS asset_id, 
    a.name AS asset_name, 
    COALESCE(a.land_share_value_taka, 0) AS land_share_value_taka,
    COALESCE(SUM(v.installments_total_taka), 0) AS apartment_installments_total_taka,
    COALESCE(SUM(v.special_installments_total_taka), 0) AS apartment_special_installments_total_taka,
    COALESCE(SUM(v.materials_total_taka), 0) AS apartment_materials_total_taka,
    COALESCE(SUM(v.labour_total_taka), 0) AS apartment_labour_total_taka,
    COALESCE(SUM(v.transport_total_taka), 0) AS apartment_transport_total_taka,
    (
        COALESCE(SUM(v.installments_total_taka), 0) +
        COALESCE(SUM(v.special_installments_total_taka), 0) +
        COALESCE(SUM(v.materials_total_taka), 0) +
        COALESCE(SUM(v.labour_total_taka), 0) +
        COALESCE(SUM(v.transport_total_taka), 0)
    ) AS apartment_construction_total_taka,
    (COALESCE(a.land_share_value_taka, 0) + COALESCE(SUM(v.grand_total_taka), 0)) AS apartment_grand_total_taka
FROM assets a
LEFT JOIN view_asset_share_totals v ON v.asset_id = a.id
WHERE a.archived = 0
GROUP BY a.id;