<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // Demo users + roles
        // Login password for all three: "password"
        $users = [
            ['username' => 'alice', 'email' => 'alice@example.com', 'role' => 'admin'],
            ['username' => 'bob', 'email' => 'bob@example.com', 'role' => 'moderator'],
            ['username' => 'charlie', 'email' => 'charlie@example.com', 'role' => 'member'],
        ];

        $passwordHash = password_hash('password', PASSWORD_DEFAULT);

        foreach ($users as $u) {
            $existing = $this->db->table('users')->where('username', $u['username'])->get()->getRowArray();

            if ($existing) {
                // Ensure role/status are correct even if user already exists
                $update = [
                    'email' => $u['email'],
                    'status' => 'active',
                    'email_verified_at' => $existing['email_verified_at'] ?? $now,
                ];

                if ($this->db->fieldExists('role', 'users')) {
                    $update['role'] = $u['role'];
                }

                $this->db->table('users')->where('id', (int) $existing['id'])->update($update);
                $userId = (int) $existing['id'];
            } else {
                $insert = [
                    'username' => $u['username'],
                    'email' => $u['email'],
                    'email_verified_at' => $now,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($this->db->fieldExists('role', 'users')) {
                    $insert['role'] = $u['role'];
                }

                $this->db->table('users')->insert($insert);
                $userId = (int) $this->db->insertID();
            }

            // Ensure local credential exists
            $cred = $this->db->table('user_credentials')
                ->where('user_id', $userId)
                ->where('provider', 'local')
                ->get()
                ->getRowArray();

            if ($cred) {
                $this->db->table('user_credentials')
                    ->where('id', (int) $cred['id'])
                    ->update([
                        'password_hash' => $passwordHash,
                        'updated_at' => $now,
                    ]);
            } else {
                $this->db->table('user_credentials')->insert([
                    'user_id' => $userId,
                    'provider' => 'local',
                    'provider_id' => null,
                    'password_hash' => $passwordHash,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Ensure profile exists
            $profile = $this->db->table('user_profiles')->where('user_id', $userId)->get()->getRowArray();
            if ($profile) {
                // Keep existing display_name if set; otherwise set to username
                $dn = isset($profile['display_name']) ? trim((string) $profile['display_name']) : '';
                if ($dn === '') {
                    $this->db->table('user_profiles')->where('id', (int) $profile['id'])->update([
                        'display_name' => $u['username'],
                        'updated_at' => $now,
                    ]);
                }
            } else {
                $this->db->table('user_profiles')->insert([
                    'user_id' => $userId,
                    'display_name' => $u['username'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}

