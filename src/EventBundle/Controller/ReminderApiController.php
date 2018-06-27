<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace EventBundle\Controller;


use EventBundle\Entity\Reminder;
use JMS\Serializer\SerializationContext;
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
        $users = $em->getRepository(Reminder::class)->findAll();

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize(
            $users,
            'json',
            SerializationContext::create()->setGroups(['default'])
        );

        return new Response($data);
    }
}