<?php
require_once __DIR__ . '/../Core/Database.php';

class MyInformation
{
    public static function get(): ?array
    {
        $info = Database::queryOne("SELECT * FROM my_information LIMIT 1");
        if ($info) {
            $info['phones'] = Database::query("SELECT * FROM my_information_phones WHERE my_information_id = ?", [$info['id']]);
        }
        return $info;
    }

    public static function update(array $data): ?array
    {
        $now = date('Y-m-d H:i:s');
        $info = self::get();
        
        if ($info) {
            Database::execute("UPDATE my_information SET name = ?, email = ?, updated_at = ? WHERE id = ?", [
                $data['name'] ?? '', $data['email'] ?? '', $now, $info['id']
            ]);
        } else {
            $id = 'my_info_001';
            Database::execute("INSERT INTO my_information (id, name, email, created_at, updated_at) VALUES (?, ?, ?, ?, ?)", [
                $id, $data['name'] ?? '', $data['email'] ?? '', $now, $now
            ]);
        }
        
        return self::get();
    }

    public static function updatePhones(array $phones): void
    {
        $info = self::get();
        if (!$info) return;
        
        Database::execute("DELETE FROM my_information_phones WHERE my_information_id = ?", [$info['id']]);
        
        foreach ($phones as $phone) {
            if (!empty($phone)) {
                $id = generateId('my_phone');
                Database::execute("INSERT INTO my_information_phones (id, my_information_id, phone) VALUES (?, ?, ?)", [
                    $id, $info['id'], $phone
                ]);
            }
        }
    }
}