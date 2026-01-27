<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMarketingAndNotificationPreferencesToUserProfiles extends Migration
{
    public function up()
    {
        $fields = [
            'marketing_emails' => [
                'type'       => 'BOOLEAN',
                'null'       => false,
                'default'    => true,
            ],
            'newsletter_subscription' => [
                'type'       => 'BOOLEAN',
                'null'       => false,
                'default'    => true,
            ],
            'notify_new_threads' => [
                'type'       => 'BOOLEAN',
                'null'       => false,
                'default'    => true,
            ],
            'notify_new_replies' => [
                'type'       => 'BOOLEAN',
                'null'       => false,
                'default'    => true,
            ],
            'notify_mentions' => [
                'type'       => 'BOOLEAN',
                'null'       => false,
                'default'    => true,
            ],
            'notify_moderation' => [
                'type'       => 'BOOLEAN',
                'null'       => false,
                'default'    => false,
            ],
        ];

        $this->forge->addColumn('user_profiles', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('user_profiles', [
            'marketing_emails',
            'newsletter_subscription',
            'notify_new_threads',
            'notify_new_replies',
            'notify_mentions',
            'notify_moderation',
        ]);
    }
}
