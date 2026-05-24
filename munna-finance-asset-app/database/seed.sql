-- Seed Lookup: Financial Types
INSERT OR IGNORE INTO lookup_financial_types (id, name, sort_order) VALUES 
('fin_type_bank_loan', 'Bank Loan', 1),
('fin_type_self_finance', 'Self Finance', 2);

-- Seed Lookup: Apartment Statuses
INSERT OR IGNORE INTO lookup_apartment_statuses (id, name, sort_order) VALUES 
('apt_status_pending', 'Pending', 1),
('apt_status_lottery_done', 'Lottery Done', 2),
('apt_status_booking_confirmed', 'Booking Confirmed', 3),
('apt_status_money_collected', 'Money Collected', 4),
('apt_status_construction_complete', 'Construction Complete', 5),
('apt_status_handover', 'Handover', 6);

-- Seed Lookup: Share Types
INSERT OR IGNORE INTO lookup_share_types (id, name, sort_order) VALUES 
('share_type_starter', 'Starter', 1),
('share_type_purchased', 'Purchased', 2);

-- Seed Lookup: Expense Categories
INSERT OR IGNORE INTO lookup_expense_categories (id, name, sort_order) VALUES 
('expense_cement', 'Cement', 1),
('expense_steel', 'Steel', 2),
('expense_brick', 'Brick', 3),
('expense_sand', 'Sand', 4),
('expense_aggregate', 'Aggregate', 5),
('expense_paint', 'Paint', 6),
('expense_tile', 'Tile', 7),
('expense_glass', 'Glass', 8),
('expense_electrical', 'Electrical', 9),
('expense_plumbing', 'Plumbing', 10),
('expense_labour', 'Labour', 11),
('expense_transport', 'Transport', 12),
('expense_other', 'Other', 13);

-- Seed Lookup: Relationship Types
INSERT OR IGNORE INTO lookup_relationship_types (id, name, sort_order) VALUES 
('rel_employee', 'Employee', 1),
('rel_family', 'Family', 2),
('rel_friend', 'Friend', 3),
('rel_neighbor', 'Neighbor', 4),
('rel_colleague', 'Colleague', 5),
('rel_other', 'Other', 6);

-- Seed Lookup: Bank Names
INSERT OR IGNORE INTO lookup_bank_names (id, name, sort_order) VALUES 
('bank_dbbl', 'DBBL', 1),
('bank_brac', 'BRAC Bank', 2),
('bank_city', 'City Bank', 3),
('bank_ebl', 'EBL', 4),
('bank_nrb', 'NRB Bank', 5),
('bank_standard_chartered', 'Standard Chartered', 6),
('bank_hsbc', 'HSBC', 7),
('bank_commercial', 'Commercial Bank', 8),
('bank_others', 'Others', 9);

-- Seed Lookup: Mobile Channels
INSERT OR IGNORE INTO lookup_mobile_channels (id, name, sort_order) VALUES 
('mobile_bkash', 'bKash', 1),
('mobile_nagad', 'Nagad', 2),
('mobile_rocket', 'Rocket', 3),
('mobile_upay', 'Upay', 4),
('mobile_tcash', 'Tcash', 5),
('mobile_others', 'Others', 6);

-- Seed Lookup: Mobile Account Types
INSERT OR IGNORE INTO lookup_mobile_account_types (id, name, sort_order) VALUES 
('mobile_personal', 'Personal', 1),
('mobile_merchant', 'Merchant', 2);

-- Seed Lookup: Transfer Types
INSERT OR IGNORE INTO lookup_transfer_types (id, name, sort_order) VALUES 
('transfer_giving_loan', 'Giving Loan', 1),
('transfer_taking_loan', 'Taking Loan', 2),
('transfer_collect_due', 'Collect Due', 3),
('transfer_pay_due', 'Pay Due', 4),
('transfer_salary', 'Salary', 5),
('transfer_service_bill', 'Service Bill', 6),
('transfer_gift', 'Gift', 7);

-- Seed Lookup: Paid By Options
INSERT OR IGNORE INTO lookup_paid_by_options (id, name, sort_order) VALUES 
('paid_by_e_transfer', 'E-Transfer', 1),
('paid_by_cash', 'Cash', 2);

-- Seed My Information
INSERT OR IGNORE INTO my_information (id, name, email) VALUES 
('my_info_001', 'Mehedi Hasan Munna', 'soundcastingsolution@gmail.com');

INSERT OR IGNORE INTO my_information_phones (id, my_information_id, phone) VALUES 
('my_phone_001', 'my_info_001', '01752701707'),
('my_phone_002', 'my_info_001', '01303700700');

-- Seed User (password: admin123)
INSERT OR IGNORE INTO users (id, name, email, password_hash, role) VALUES 
('user_001', 'Mehedi Hasan Munna', 'soundcastingsolution@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');