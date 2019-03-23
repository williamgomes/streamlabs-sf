<?php

namespace Streamlabs\Bundle\Listener;


use Doctrine\ORM\EntityManager;
use Streamlabs\Entities\StreamersEventLog;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class TwitchEventSubscriber implements EventSubscriberInterface
{
    /** @var EntityManager  */
    private $entityManager;

    /**
     * TwitchEventSubscriber constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param GetResponseEvent $event
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ('twitch_webhook_follows' === $request->attributes->get('_route')) {
            if ($request->isMethod('POST')) {
                $data = $request->getContent();
                $oJson = json_decode($data);
                if (count($oJson->data) > 0) {
                    foreach ($oJson->data as $json) {
                        $dateTimeTz = new \DateTime($json->followed_at);
                        $dateTime = $dateTimeTz->format('Y-m-d H:i:s');
                        $oStreamersEvent = new StreamersEventLog();

                        $oStreamersEvent->setStreamerId($json->to_id);
                        $oStreamersEvent->setEventType('Follows');
                        $oStreamersEvent->setEventData($data);
                        $oStreamersEvent->setCreatedAt($dateTime);

                        $this->entityManager->persist($oStreamersEvent);
                        $this->entityManager->flush();
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => 'onKernelRequest'
        );
    }
}