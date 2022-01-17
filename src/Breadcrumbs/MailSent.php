<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Mail\Events\MessageSent;

class MailSent extends Breadcrumb
{
    public $handles = MessageSent::class;

    public function handleEvent(MessageSent $event)
    {
        $metadata = [
            'queue' => $event->data['queue'] ?? null,
            'replyTo' => $this->extractAddresses($event->message->getReplyTo()),
            'to' => $this->extractAddresses($event->message->getTo()),
            'cc' => $this->extractAddresses(($event->message->getCc() ?? [])),
            'bcc' => $this->extractAddresses(($event->message->getBcc() ?? [])),
            'subject' => $event->message->getSubject(),
        ];

        Honeybadger::addBreadcrumb('Mail sent', $metadata, 'mail');
    }

    protected function extractAddresses($addresses): ?string
    {
        if ($addresses == null) {
            return null;
        }

        $keys = array_keys($addresses);
        if (($keys[0] ?? null) == 0) {
            // Symfony < v6 (SwiftMailer) uses an array keyed by email,
            // but v6 (Symfony Mailer) uses a list of Address objects
            $addresses = collect($addresses)->map->getAddress()->toArray();
        } else {
            $addresses = $keys;
        }
        return implode(',', $addresses) ?: null;
    }
}
