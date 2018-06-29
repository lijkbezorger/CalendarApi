<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace UserBundle\Controller;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;

class UserApiController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize(
            $users,
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
        $requestContent = $request->getContent();
        /** @var User $user */
        $user = $this->get('jms_serializer')->deserialize(
            $requestContent,
            User::class,
            'json',
            DeserializationContext::create()->setGroups(['create'])
        );

        $validator = $this->get('validator');
        $errors = $validator->validate($user, null, ['create']);

        if (count($errors) > 0) {
            $errorsString = (string)$errors;

            return new Response($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize(
            $user,
            'json',
            SerializationContext::create()->setGroups(['default', 'detail'])
        );

        return new Response($data, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function viewAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        $serializer = $this->get('jms_serializer');
        $data = $serializer->serialize(
            $user,
            'json',
            SerializationContext::create()->setGroups(['default', 'detail'])
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
        /** @var User $userEntity */
        $userEntity = $em->getRepository(User::class)->find($id);

        if (!$userEntity) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        $requestContent = $request->getContent();
        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');
        /** @var User $user */
        $user = $serializer->deserialize(
            $requestContent,
            User::class,
            'json',
            DeserializationContext::create()->setGroups(['update'])
        );

        $userEntity = $this->get('user.user_save_service')->setDataToEntity($userEntity, $user, $serializer->getMetadataFactory());
        $em->persist($userEntity);
        $em->flush();

        $data = $serializer->serialize(
            $userEntity,
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
    public function deleteAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        $em->remove($user);
        $em->flush();

        return new Response('User was deleted', Response::HTTP_NO_CONTENT);
    }
}