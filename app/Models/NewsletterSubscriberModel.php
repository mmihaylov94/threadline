<?php

namespace App\Models;

use CodeIgniter\Model;

class NewsletterSubscriberModel extends Model
{
    protected $table         = 'newsletter_subscribers';
    protected $returnType    = 'array';
    protected $allowedFields = ['email', 'source', 'subscribed_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Find subscriber by email.
     */
    public function findByEmail(string $email): ?array
    {
        $row = $this->where('email', $email)->first();
        return $row ?: null;
    }

    /**
     * Subscribe an email. Inserts if new, updates subscribed_at if existing.
     * Returns true on success, false on failure.
     */
    public function subscribe(string $email, string $source = 'popup'): bool
    {
        $existing = $this->findByEmail($email);
        $now      = date('Y-m-d H:i:s');

        if ($existing) {
            return $this->update($existing['id'], [
                'subscribed_at' => $now,
                'source'        => $source,
            ]);
        }

        return (bool) $this->insert([
            'email'         => $email,
            'source'        => $source,
            'subscribed_at' => $now,
        ]);
    }

    /**
     * Unsubscribe an email. Removes the row from newsletter_subscribers.
     * Returns true if already not subscribed or successfully removed.
     */
    public function unsubscribe(string $email): bool
    {
        $existing = $this->findByEmail($email);
        if (! $existing) {
            return true;
        }

        return $this->delete($existing['id']);
    }
}
