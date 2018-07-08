<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace EventBundle\Controller;


use EventBundle\Entity\Reminder;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReminderApiController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $parameters = $request->query->all();
        $reminders = $em->getRepository(Reminder::class)->search($parameters);

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize(
            $reminders,
            'json',
            SerializationContext::create()->setGroups(['view'])
        );

        return new Response($data);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');

        $requestContent = $request->getContent();

        try {
            /** @var Reminder $reminder */
            $reminder = $serializer->deserialize(
                $requestContent,
                Reminder::class,
                'json',
                DeserializationContext::create()->setGroups(['create'])
            );

            $validator = $this->get('validator');
            $errors = $validator->validate($reminder, null, ['create']);

            if (count($errors) > 0) {
                $errorsString = (string)$errors;

                return new Response($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($reminder);
            $em->flush();

            $data = $serializer->serialize(
                $reminder,
                'json',
                SerializationContext::create()->setGroups(['default'])
            );

            return new Response($data, Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            return new Response('Wrong data provided', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function viewAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Reminder $reminder */
        $reminder = $em->getRepository(Reminder::class)->find($id);
        if (!$reminder) {
            return new Response('Reminder not found', Response::HTTP_NOT_FOUND);
        }

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize(
            $reminder,
            'json',
            SerializationContext::create()->setGroups(['default'])
        );

        return new Response($data);
    }

    /**
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function updateAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Reminder $reminderEntity */
        $reminderEntity = $em->getRepository(Reminder::class)->find($id);

        if (!$reminderEntity) {
            return new Response('Reminder not found', Response::HTTP_NOT_FOUND);
        }

        $requestContent = $request->getContent();
        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');
        /** @var Reminder $reminder */
        $reminder = $serializer->deserialize(
            $requestContent,
            Reminder::class,
            'json',
            DeserializationContext::create()->setGroups(['update'])
        );

        $reminderEntity = $this->get('event.event_save_service')
            ->setDataToEntity($reminderEntity, $reminder, $serializer->getMetadataFactory());
        $em->persist($reminderEntity);
        $em->flush();

        $data = $serializer->serialize(
            $reminderEntity,
            'json',
            SerializationContext::create()->setGroups(['update'])
        );

        return new Response($data);
    }

    /**
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function deleteAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Reminder $reminder */
        $reminder = $em->getRepository(Reminder::class)->find($id);
        if (!$reminder) {
            return new Response('Reminder not found', Response::HTTP_NOT_FOUND);
        }

        $em->remove($reminder);
        $em->flush();

        return new Response('Reminder was deleted', Response::HTTP_NO_CONTENT);
    }
}