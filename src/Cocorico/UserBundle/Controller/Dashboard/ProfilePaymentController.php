<?php

/*
 * This file is part of the Cocorico package.
 *
 * (c) Cocolabs SAS <contact@cocolabs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Cocorico\UserBundle\Controller\Dashboard;

use Cocorico\UserBundle\Form\Type\ProfilePaymentFormType;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ProfileController
 *
 * @Route("/user")
 */
class ProfilePaymentController extends Controller
{
    /**
     * Edit user profile
     *
     * @Route("/edit-payment", name="cocorico_user_dashboard_profile_edit_payment")
     * @Method({"GET", "POST"})
     *
     * @param $request Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editPaymentAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->createEditPaymentForm($user);
        $success = $this->get('cocorico_user.form.handler.edit_payment')->process($form);

        $session = $this->container->get('session');
        $translator = $this->container->get('translator');

        if ($success > 0) {
            $session->getFlashBag()->add(
                'success',
                $translator->trans('user.edit.payment.success', array(), 'cocorico_user')
            );

            return $this->redirect(
                $this->generateUrl(
                    'cocorico_user_dashboard_profile_edit_payment'
                )
            );
        } elseif ($success < 0) {
            $session->getFlashBag()->add(
                'error',
                $translator->trans('user.edit.payment.error', array(), 'cocorico_user')
            );
        }

        return $this->render(
            'CocoricoUserBundle:Dashboard/Profile:edit_payment.html.twig',
            array(
                'form' => $form->createView(),
                'user' => $user
            )
        );
    }

    /**
     * Creates a form to edit a user entity.
     *
     * @param mixed $user
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditPaymentForm($user)
    {
        $form = $this->get('form.factory')->createNamed(
            'user',
            new ProfilePaymentFormType(),
            $user,
            array(
                'method' => 'POST',
                'action' => $this->generateUrl('cocorico_user_dashboard_profile_edit_payment'),
            )
        );

        return $form;
    }
}
