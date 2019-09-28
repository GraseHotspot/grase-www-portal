<?php

namespace App\EventSubscriber;

use App\Util\AuditLogger;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Class LoginEventSubscriber
 *
 * Event subscriber for login events, creates an audit log entry when a login occurs
 */
class LoginEventSubscriber implements EventSubscriberInterface
{
    /** @var Logger $logger Audit Logger instance of Monolog */
    protected $auditLogger;

    /**
     * LoginEventSubscriber constructor.
     *
     * @param Logger $auditLogger
     */
    public function __construct(Logger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => [
                ['interactiveLoginAudit', 0],
            ],
        ];
    }

    /**
     * @param InteractiveLoginEvent $interactiveLoginEvent
     *
     * @return InteractiveLoginEvent
     *
     * Fired when an interactive login event occurs, audit logs the event
     */
    public function interactiveLoginAudit(InteractiveLoginEvent $interactiveLoginEvent)
    {
        $this->auditLogger->info('audit.login.interactive', [
            'user' => $interactiveLoginEvent->getAuthenticationToken()->getUsername(),
        ]);

        return $interactiveLoginEvent;
    }
}
