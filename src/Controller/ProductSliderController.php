<?php

declare(strict_types=1);

namespace Oksydan\IsProductSlider\Controller;

use Exception;
use Oksydan\IsProductSlider\Entity\ProductSlider;
use Oksydan\IsProductSlider\Filter\ProductSliderFilters;
use Oksydan\IsProductSlider\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\Shop;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductSliderController extends FrameworkBundleAdminController
{
    /**
     * @var array
     */
    private $languages;

    public function __construct($languages)
    {
        parent::__construct();
        $this->languages = $languages;
    }

    public function index(ProductSliderFilters $filters): Response
    {
        $sliderGridFactory = $this->get('oksydan.is_productslider.grid.product_slider_grid_factory');
        $sliderGrid = $sliderGridFactory->getGrid($filters);

        $configurationForm = $this->get('oksydan.is_productslider.product_slider_configuration.form_handler')->getForm();

        return $this->render('@Modules/is_productslider/views/templates/admin/index.html.twig', [
            'translationDomain' => TranslationDomains::TRANSLATION_DOMAIN_ADMIN,
            'sliderGrid' => $this->presentGrid($sliderGrid),
            'configurationForm' => $configurationForm->createView(),
            'help_link' => false,
        ]);
    }

    public function create(Request $request): Response
    {
        $formDataHandler = $this->get('oksydan.is_productslider.form.identifiable_object.builder.product_slider_form_builder');
        $form = $formDataHandler->getForm();
        $form->handleRequest($request);

        $formHandler = $this->get('oksydan.is_productslider.form.identifiable_object.handler.product_slider_form_handler');

        try {
            $result = $formHandler->handle($form);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful creation.', 'Admin.Notifications.Success')
                );

                return $this->redirectToRoute('productslider_controller');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@Modules/is_productslider/views/templates/admin/form.html.twig', [
            'sliderForm' => $form->createView(),
            'title' => $this->trans('Slider', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
            'help_link' => false,
        ]);
    }

    public function edit(Request $request, int $sliderId): Response
    {
        $formBuilder = $this->get('oksydan.is_productslider.form.identifiable_object.builder.product_slider_form_builder');
        $form = $formBuilder->getFormFor((int) $sliderId);
        $form->handleRequest($request);

        $formHandler = $this->get('oksydan.is_productslider.form.identifiable_object.handler.product_slider_form_handler');

        try {
            $result = $formHandler->handleFor($sliderId, $form);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful edition.', 'Admin.Notifications.Success')
                );

                return $this->redirectToRoute('productslider_controller');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@Modules/is_productslider/views/templates/admin/form.html.twig', [
            'sliderForm' => $form->createView(),
            'title' => $this->trans('Slider edit', TranslationDomains::TRANSLATION_DOMAIN_ADMIN),
            'help_link' => false,
        ]);
    }

    public function delete(Request $request, int $sliderId): Response
    {
        $slider = $this->getDoctrine()
            ->getRepository(ProductSlider::class)
            ->find($sliderId);

        if (!empty($slider)) {
            $multistoreContext = $this->get('prestashop.adapter.shop.context');
            $entityManager = $this->get('doctrine.orm.entity_manager');

            if ($multistoreContext->isAllShopContext()) {
                $slider->clearShops();

                $entityManager->remove($slider);
            } else {
                $shopList = $this->getDoctrine()
                    ->getRepository(Shop::class)
                    ->findBy(['id' => $multistoreContext->getContextListShopID()]);

                foreach ($shopList as $shop) {
                    $slider->removeShop($shop);
                    $entityManager->flush();
                }

                if (count($slider->getShops()) === 0) {
                    $entityManager->remove($slider);
                }
            }

            $entityManager->flush();
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );

            return $this->redirectToRoute('productslider_controller');
        }

        $this->addFlash(
            'error',
            $this->trans('Cannot find slider %d', TranslationDomains::TRANSLATION_DOMAIN_ADMIN, ['%d' => $sliderId])
        );

        return $this->redirectToRoute('productslider_controller');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function saveConfiguration(Request $request): Response
    {
        $redirectResponse = $this->redirectToRoute('productslider_controller');

        $form = $this->get('oksydan.is_productslider.product_slider_configuration.form_handler')->getForm();
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $redirectResponse;
        }

        if ($form->isValid()) {
            $data = $form->getData();
            $saveErrors = $this->get('oksydan.is_productslider.product_slider_configuration.form_handler')->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $redirectResponse;
            }
        }

        $formErrors = [];

        foreach ($form->getErrors(true) as $error) {
            $formErrors[] = $error->getMessage();
        }

        $this->flashErrors($formErrors);

        return $redirectResponse;
    }

    /**
     * @param Request $request
     * @param int $sliderId
     *
     * @return Response
     */
    public function toggleStatus(Request $request, int $sliderId): Response
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $slider = $entityManager
            ->getRepository(ProductSlider::class)
            ->findOneBy(['id' => $sliderId]);

        if (empty($slider)) {
            $response = [
                'status' => false,
                'message' => sprintf('Entity %d doesn\'t exist', $sliderId),
            ];
            $errors = [$response];
            $this->flashErrors($errors);

            return $this->redirectToRoute('productslider_controller');
        }

        try {
            $slider->setActive(!$slider->getActive());
            $entityManager->flush();

            $this->addFlash('success', $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success'));
        } catch (\Exception $e) {
            $response = [
                'status' => false,
                'message' => sprintf(
                    'There was an error while updating the status of slide %d: %s',
                    $sliderId,
                    $e->getMessage()
                ),
            ];
            $errors = [$response];
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('productslider_controller');
    }

    public function updatePositionAction(Request $request): Response
    {
        try {
            $positionsData = [
                'positions' => $request->request->get('positions'),
            ];

            $positionDefinition = $this->get('oksydan.is_productslider.grid.position_definition');

            $positionUpdateFactory = $this->get('prestashop.core.grid.position.position_update_factory');
            $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);

            $updater = $this->get('prestashop.core.grid.position.doctrine_grid_position_updater');

            $updater->update($positionUpdate);

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (PositionDataException|PositionUpdateException $e) {
            $errors = [$e->toArray()];
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('productslider_controller');
    }

    /**
     * Provides translated error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages(Exception $e): array
    {
        return [
            Exception::class => [
                $this->trans(
                    'Generic Exception',
                    TranslationDomains::TRANSLATION_DOMAIN_EXCEPTION
                ),
            ],
        ];
    }
}
