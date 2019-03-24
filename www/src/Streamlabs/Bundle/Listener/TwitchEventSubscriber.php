<?php

namespace Streamlabs\Bundle\Listener;


use Doctrine\ORM\EntityManager;
use Streamlabs\Entities\StreamersEventLog;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;

class TwitchEventSubscriber implements EventSubscriberInterface
{
    /** @var EntityManager */
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
                        $oStreamersEvent = new StreamersEventLog();

                        $oStreamersEvent->setStreamerId($json->to_id);
                        $oStreamersEvent->setEventType('Follows');
                        $oStreamersEvent->setEventData(json_encode($json));
                        $oStreamersEvent->setCreatedAt(new \DateTime($json->followed_at));

                        $this->entityManager->persist($oStreamersEvent);
                        $this->entityManager->flush();

                    }
                }

                $response = new Response(
                    'Success',
                    Response::HTTP_OK,
                    ['content-type' => 'text/html']
                );

                $event->setResponse($response);
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