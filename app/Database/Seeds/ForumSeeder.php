<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // 1. Categories (if empty)
        if ($this->db->table('categories')->countAllResults() === 0) {
            $this->db->table('categories')->insertBatch([
                ['name' => 'General', 'slug' => 'general', 'description' => 'General discussion', 'sort_order' => 0, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Design', 'slug' => 'design', 'description' => 'Design and UX', 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Development', 'slug' => 'development', 'description' => 'Development and code', 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Feedback', 'slug' => 'feedback', 'description' => 'Feedback and feature requests', 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }
        $categoryRows = $this->db->table('categories')->get()->getResult();
        $categoryIds  = array_map(fn ($r) => (int) $r->id, $categoryRows);
        if (empty($categoryIds)) {
            return;
        }

        // 2. Users (if empty)
        if ($this->db->table('users')->countAllResults() === 0) {
            $users = [
                ['username' => 'alice', 'email' => 'alice@example.com'],
                ['username' => 'bob', 'email' => 'bob@example.com'],
                ['username' => 'charlie', 'email' => 'charlie@example.com'],
            ];
            $pw = password_hash('password', PASSWORD_DEFAULT);
            foreach ($users as $u) {
                $this->db->table('users')->insert([
                    'username'         => $u['username'],
                    'email'            => $u['email'],
                    'email_verified_at' => $now,
                    'status'           => 'active',
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
                $uid = $this->db->insertID();
                $this->db->table('user_credentials')->insert([
                    'user_id'       => $uid,
                    'provider'      => 'local',
                    'provider_id'   => null,
                    'password_hash' => $pw,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
                $this->db->table('user_profiles')->insert([
                    'user_id'      => $uid,
                    'display_name' => $u['username'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }
        }
        $userRows = $this->db->table('users')->select('id')->get()->getResult();
        $userIds  = array_map(fn ($r) => (int) $r->id, $userRows);
        if (empty($userIds)) {
            return;
        }

        // 3. Threads
        helper('text');
        $threadData = [
            ['Welcome to the forum', 'Share your thoughts and ideas here.'],
            ['Getting started with Threadline', 'New to Threadline? Start here.'],
            ['Design systems that scale', 'A place to discuss design systems.'],
            ['Typography in 2024', 'Trends and techniques in typography.'],
            ['Best PHP practices', 'Discuss PHP and CodeIgniter.'],
            ['PostgreSQL tips and tricks', 'Database tips for PostgreSQL users.'],
            ['How we moderate discussions', 'Our approach to keeping discussions civil.'],
            ['Feature request: dark mode', 'Would love to see a dark theme.'],
            ['Introductions thread', 'Say hello and introduce yourself!'],
            ['Community guidelines', 'Please read before posting.'],
        ];
        $threadIds = [];
        foreach ($threadData as $i => $pair) {
            $title   = $pair[0];
            $body    = $pair[1];
            $slug    = url_title($title, '-', true) ?: 'thread-' . ($i + 1);
            $catId   = $categoryIds[$i % count($categoryIds)];
            $authorId = $userIds[$i % count($userIds)];
            $this->db->table('threads')->insert([
                'category_id'  => $catId,
                'author_id'    => $authorId,
                'title'        => $title,
                'slug'         => $slug,
                'body'         => $body,
                'locked'       => false,
                'post_count'   => 0,
                'last_post_at' => null,
                'created_at'   => $now,
                'updated_at'   => $now,
                'deleted_at'   => null,
            ]);
            $threadIds[] = $this->db->insertID();
        }

        // 3b. Set background images on first 3 threads (if column exists)
        $bgUrls = [
            'https://picsum.photos/seed/thread-bg-1/1200/400',
            'https://picsum.photos/seed/thread-bg-2/1200/400',
            'https://picsum.photos/seed/thread-bg-3/1200/400',
        ];
        foreach (array_slice($threadIds, 0, 3) as $idx => $tid) {
            $this->db->table('threads')->where('id', $tid)->update(['background_image' => $bgUrls[$idx]]);
        }

        // 4. Posts
        $postBodies = [
            'Great point!', 'I agree.', 'Thanks for sharing.', 'Has anyone tried this?', 'Another perspective here.',
            'Follow-up question.', 'Useful links: ...', 'I ran into this too.', 'Solution: ...', 'More details would help.',
            'Good read.', 'Seconded.', 'Looking forward to it.', 'Same for me.', 'Works for me.',
        ];
        for ($i = 0; $i < 30; $i++) {
            $threadId  = $threadIds[$i % count($threadIds)];
            $authorId  = $userIds[$i % count($userIds)];
            $body      = $postBodies[$i % count($postBodies)];
            $created   = date('Y-m-d H:i:s', strtotime($now) + $i * 60);
            $this->db->table('posts')->insert([
                'thread_id'  => $threadId,
                'author_id'  => $authorId,
                'parent_id'  => null,
                'body'       => $body,
                'created_at' => $created,
                'updated_at' => $created,
                'deleted_at' => null,
            ]);
        }

        // 5. Update thread post_count and last_post_at
        foreach ($threadIds as $tid) {
            $lastRow = $this->db->table('posts')->where('thread_id', $tid)->orderBy('created_at', 'DESC')->limit(1)->get()->getRow();
            $count   = $this->db->table('posts')->where('thread_id', $tid)->countAllResults();
            $lastAt  = $lastRow ? $lastRow->created_at : null;
            $this->db->table('threads')->where('id', $tid)->update(['post_count' => $count, 'last_post_at' => $lastAt, 'updated_at' => $now]);
        }
    }
}
