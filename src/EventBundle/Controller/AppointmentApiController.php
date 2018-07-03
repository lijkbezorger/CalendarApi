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
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 *
 * Class AppointmentApiController
 * @package EventBundle\Controller
 */
class AppointmentApiController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $parameters = $request->query->all();
        $appointments = $em->getRepository(Appointment::class)->search($parameters);

        $statusCode = (count($appointments)) ? Response::HTTP_OK : Response::HTTP_NO_CONTENT;
        $view = $this->view($appointments, $statusCode);
        $view->setContext((new Context())->setGroups(['view']));

        return $this->handleView($view);
    }

    /**
     * @ParamConverter("appointment", converter="fos_rest.request_body")
     *
     * @param Request $request
     * @param ConstraintViolationListInterface $validationErrors
     * @param Appointment $appointment
     * @return Response
     */
    public function createAction(Request $request, ConstraintViolationListInterface $validationErrors, Appointment $appointment)
    {
        $invitedUsers = $appointment->getInvitedUsers();
        if (is_a($invitedUsers, ArrayCollection::class)) {
            foreach ($invitedUsers as $invitedUser) {
                $appointment->addUser($invitedUser);
            }
        }

        if (count($validationErrors)) {
            $errorsString = (string)$validationErrors;
            $view = $this->view($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
            return $this->handleView($view);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($appointment);
        $em->flush();

        $view = $this->view($appointment, Response::HTTP_CREATED);
        $view->setContext((new Context())->setGroups(['view']));
        return $this->handleView($view);
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

        if ($appointment) {
            $data = $appointment;
            $statusCode = Response::HTTP_OK;
        } else {
            $data = 'Appointment not found';
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        $view = $this->view($data, $statusCode);
        $view->setContext((new Context())->setGroups(['view']));

        return $this->handleView($view);
    }

    /**
     * @ParamConverter("appointment")
     * @ParamConverter("newAppointment", converter="fos_rest.request_body")
     *
     * @param Request $request
     * @param ParamFetcherInterface $fetcher
     * @param ConstraintViolationListInterface $validationErrors
     * @param Appointment $appointment
     * @param Appointment $newAppointment
     * @return Response
     */
    public function updateAction(
        Request $request,
        ParamFetcherInterface $fetcher,
        ConstraintViolationListInterface $validationErrors,
        Appointment $appointment,
        Appointment $newAppointment
    )
    {
        if (!$appointment) {
            $view = $this->view('Appointment not found', Response::HTTP_NOT_FOUND);
            $view->setContext((new Context())->setGroups(['view']));

            return $this->handleView($view);
        }

        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');
        $appointment = $this->get('event.event_save_service')
            ->setDataToEntity($appointment, $newAppointment, $serializer->getMetadataFactory());

        $em = $this->getDoctrine()->getManager();
        $em->persist($appointment);
        $em->flush();

        $view = $this->view($appointment, Response::HTTP_OK);
        $view->setContext((new Context())->setGroups(['view']));
        return $this->handleView($view);
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
        if ($appointment) {
            $statusCode = Response::HTTP_NO_CONTENT;
            $data = 'Appointment was deleted';
            $em->remove($appointment);
            $em->flush();
        } else {
            $data = 'Appointment not found';
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        $view = $this->view($data, $statusCode);
        return $this->handleView($view);
    }
}