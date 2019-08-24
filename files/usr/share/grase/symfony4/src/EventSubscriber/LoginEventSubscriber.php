<?php

namespace App\EventSubscriber;

use App\Util\AuditLogger;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginEventSubscriber implements EventSubscriberInterface
{
    /** @var Logger $logger */
    protected $auditLogger;

    public function __construct(Logger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => [
                ['interactiveLoginAudit', 0]
            ]
        ];
    }

    public function interactiveLoginAudit(InteractiveLoginEvent $interactiveLoginEvent)
    {
        $this->auditLogger->info('audit.login.interactive', [
            'user' => $interactiveLoginEvent->getAuthenticationToken()->getUsername(),
        ]);

        return $interactiveLoginEvent;
    }
}