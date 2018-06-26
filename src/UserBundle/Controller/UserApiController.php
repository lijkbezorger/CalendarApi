<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace UserBundle\Controller;

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
        $data = $this->get('serializer')->serialize($users, 'json');

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
        $user = $this->get('serializer')->deserialize($requestContent, User::class, 'json');

        $validator = $this->get('validator');
        $errors = $validator->validate($user, null, ['create']);

        if (count($errors) > 0) {
            $errorsString = (string)$errors;

            return new Response($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $data = $this->get('serializer')->serialize($user, 'json');

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
        $data = $this->get('serializer')->serialize($user, 'json');

        return new Response($data);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $userObject */
        $userObject = $em->getRepository(User::class)->find($id);

        $requestContent = $request->getContent();
        /** @var User $user */
        $user = $this->get('serializer')->deserialize($requestContent, User::class, 'json');

        //TODO add tool to load new data
        if ($email = $user->getEmail()) {
            $userObject->setEmail($email);
        }
        if ($username = $user->getUsername()) {
            $userObject->setUsername($username);
        }
        if ($firstName = $user->getFirstName()) {
            $userObject->setFirstName($firstName);
        }
        if ($lastName = $user->getLastName()) {
            $userObject->setLastName($lastName);
        }
        //End TODO

        $em->persist($userObject);
        $em->flush();

        $data = $this->get('serializer')->serialize($userObject, 'json');

        return new Response($data);

    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();


        return new Response('User was deleted', Response::HTTP_NO_CONTENT);
    }
}