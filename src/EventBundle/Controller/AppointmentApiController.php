<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace EventBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use EventBundle\Entity\Appointment;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppointmentApiController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $appointments = $em->getRepository(Appointment::class)->findAll();

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize(
            $appointments,
            'json',
            SerializationContext::create()->setGroups(['default'])
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
            /** @var Appointment $appointment */
            $appointment = $serializer->deserialize(
                $requestContent,
                Appointment::class,
                'json',
                DeserializationContext::create()->setGroups(['create'])->enableMaxDepthChecks()
            );

            $invitedUsers = $appointment->getInvitedUsers();
            if (is_a($invitedUsers, ArrayCollection::class)) {
                foreach ($invitedUsers as $invitedUser) {
                    $appointment->addUser($invitedUser);
                }
            }

            $validator = $this->get('validator');
            $errors = $validator->validate($appointment, null, ['create']);

            if (count($errors) > 0) {
                $errorsString = (string)$errors;

                return new Response($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($appointment);
            $em->flush();

            $data = $serializer->serialize(
                $appointment,
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
        /** @var Appointment $appointment */
        $appointment = $em->getRepository(Appointment::class)->find($id);
        if (!$appointment) {
            return new Response('Appointment not found', Response::HTTP_NOT_FOUND);
        }

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize(
            $appointment,
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
        /** @var Appointment $appointmentEntity */
        $appointmentEntity = $em->getRepository(Appointment::class)->find($id);

        if (!$appointmentEntity) {
            return new Response('Appointment not found', Response::HTTP_NOT_FOUND);
        }

        $requestContent = $request->getContent();
        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');
        /** @var Appointment $appointment */
        $appointment = $serializer->deserialize(
            $requestContent,
            Appointment::class,
            'json',
            DeserializationContext::create()->setGroups(['update'])
        );

        $appointmentEntity = $this->get('event.event_save_service')
            ->setDataToEntity($appointmentEntity, $appointment, $serializer->getMetadataFactory());
        $em->persist($appointmentEntity);
        $em->flush();

        $data = $serializer->serialize(
            $appointmentEntity,
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
        /** @var Appointment $appointment */
        $appointment = $em->getRepository(Appointment::class)->find($id);
        if (!$appointment) {
            return new Response('Appointment not found', Response::HTTP_NOT_FOUND);
        }

        $em->remove($appointment);
        $em->flush();

        return new Response('Appointment was deleted', Response::HTTP_NO_CONTENT);
    }
}