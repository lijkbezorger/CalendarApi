<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace CustomSecurityBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Model\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use UserBundle\Entity\User;

/**
 * Class SecurityApiController
 * @package CustomSecurityBundle\Controller
 */
class SecurityApiController extends FOSRestController
{
    /**
     * @ParamConverter("user", converter="fos_rest.request_body")
     *
     * @param Request $request
     * @param User $user
     * @return Response
     * @throws \Exception
     */
    public function authorizeAction(Request $request, User $user)
    {
        /** @var UserManager $um */
        $um = $this->get('fos_user.user_manager');
        /** @var User $foundUser */
        $foundUser = $um->findUserByUsername($user->getUsername());

        if (!$foundUser) {
            throw $this->createNotFoundException('User not found');
        }

        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);

        $isRightPassword = $encoder->isPasswordValid($foundUser->getPassword(), $user->getPassword(), $foundUser->getSalt());
        if (!$isRightPassword) {
            throw new BadCredentialsException('Bad credential');
        }

        $view = $this->view([
            'token' => $this->generateToken($user),
        ], Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * @ParamConverter("user", converter="fos_rest.request_body")
     *
     * @param Request $request
     * @param ConstraintViolationListInterface $validationErrors
     * @param User $user
     *
     * @return Response
     */
    public function registerAction(Request $request, ConstraintViolationListInterface $validationErrors, User $user)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();
        $repository = $em->getRepository(User::class);
        $username = $user->getUsername();
        /** @var User $existingUser */
        $existingUser = $repository->findByUsername($username);

        if ($existingUser) {
            $view = $this->view('User already exists', Response::HTTP_CONFLICT);
            return $this->handleView($view);
        }

        if (count($validationErrors)) {
            $errorsString = (string)$validationErrors;
            $view = $this->view($errorsString, Response::HTTP_UNPROCESSABLE_ENTITY);
            return $this->handleView($view);
        }

        /** @var UserManager $um */
        $um = $this->get('fos_user.user_manager');
        $user->setEnabled(true);
        $user->setPlainPassword($user->getPassword());
        $user->setRoles(['ROLE_USER']);
        $um->updateUser($user);


        $view = $this->view($user, Response::HTTP_CREATED);
        $view->setContext((new Context())->setGroups(['view']));
        return $this->handleView($view);
    }

    /**
     * @param $user User
     * @return string
     * @throws \Exception
     */
    protected function generateToken(User $user)
    {
        try {
            return $this->get('lexik_jwt_authentication.encoder.lcobucci')
                ->encode([
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'exp' => time() + 216000
                ]);
        } catch (\Exception $e) {
            return '';
        }

    }
}