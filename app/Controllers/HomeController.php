<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\ThreadModel;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class HomeController extends BaseController
{
    public function index(): string
    {
        $threadModel = model(ThreadModel::class);
        $postModel   = model(PostModel::class);
        $userModel   = model(UserModel::class);

        $stats = [
            'threads'    => number_format($threadModel->countAllResults()),
            'posts'      => number_format($postModel->countAllResults()),
            'members'    => number_format($userModel->countAllResults()),
            'moderators' => $this->getPlaceholderModeratorCount(),
        ];

        $recentThreads = $this->buildRecentThreadsForView($threadModel->getRecentForHome(4));
        $testimonials  = $this->getTestimonialsPlaceholder();

        return view('home', [
            'title'         => 'Threadline',
            'noContainer'   => true,
            'stats'         => $stats,
            'recentThreads' => $recentThreads,
            'testimonials'  => $testimonials,
        ]);
    }

    public function newsletter()
    {
        $this->request->getPost('newsletter_email');
        return redirect()->to('/')->with('success', 'Thanks for subscribing!');
    }

    /**
     * Maps thread rows from getRecentForHome into the shape expected by the home view.
     */
    private function buildRecentThreadsForView(array $threads): array
    {
        $out = [];
        foreach ($threads as $t) {
            $at   = $t['last_post_at'] ?? $t['created_at'];
            $body = isset($t['body']) ? strip_tags((string) $t['body']) : '';
            $desc = mb_strlen($body) > 140 ? mb_substr($body, 0, 137) . '...' : $body;
            $out[] = [
                'slug'     => $t['slug'],
                'category' => $t['category_name'] ?? '',
                'time'     => $at ? Time::parse($at)->humanize() : '',
                'title'    => $t['title'] ?? '',
                'desc'     => $desc,
                'img'      => ! empty($t['background_image']) ? $t['background_image'] : 'https://picsum.photos/seed/threadline-' . ($t['id'] ?? 0) . '/800/400',
            ];
        }
        return $out;
    }

    /**
     * Placeholder: moderator count (no roles/moderators implementation yet).
     */
    private function getPlaceholderModeratorCount(): string
    {
        return '47';
    }

    /**
     * Placeholder: testimonials (no testimonials in DB yet).
     */
    private function getTestimonialsPlaceholder(): array
    {
        return [
            ['quote' => 'Threadline feels like a place where people actually listen to each other. No chaos, no distractions. Just honest conversation.', 'name' => 'Sarah Chen', 'role' => 'Community moderator', 'avatar' => 'https://picsum.photos/seed/t1/80/80', 'active' => false],
            ['quote' => 'The moderation tools are straightforward and fair. I know where I stand and why decisions are made.', 'name' => 'James Mitchell', 'role' => 'Active member', 'avatar' => 'https://picsum.photos/seed/t2/80/80', 'active' => true],
            ['quote' => 'Finally, a forum that respects my time and intelligence. The structure keeps everything readable and relevant.', 'name' => 'Elena Rodriguez', 'role' => 'Forum administrator', 'avatar' => 'https://picsum.photos/seed/t3/80/80', 'active' => false],
        ];
    }
}
