<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabelGui\Communication\Controller;

use Generated\Shared\Transfer\ProductLabelAbstractProductRelationsTransfer;
use Generated\Shared\Transfer\ProductLabelTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\ProductLabelGui\Communication\ProductLabelGuiCommunicationFactory getFactory()
 */
class CreateController extends AbstractController
{

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $productLabelAggregateForm = $this->createProductLabelAggregateForm();
        $isFormSuccessfullyHandled = $this->handleProductLabelAggregateForm(
            $request,
            $productLabelAggregateForm
        );

        if ($isFormSuccessfullyHandled) {
            return $this->redirectResponse('/product-label-gui');
        }

        return $this->viewResponse([
            'productLabelFormTabs' => $this->getFactory()->createProductLabelFormTabs()->createView(),
            'aggregateForm' => $productLabelAggregateForm->createView(),
            'relatedProductTable' => $this->getFactory()->createRelatedProductTable()->render(),
        ]);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createProductLabelAggregateForm()
    {
        $aggregateFormDataProvider = $this
            ->getFactory()
            ->createProductLabelAggregateFormDataProvider();

        $aggregateForm = $this
            ->getFactory()
            ->createProductLabelAggregateForm(
                $aggregateFormDataProvider->getData(),
                $aggregateFormDataProvider->getOptions()
            );

        return $aggregateForm;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Form\FormInterface $aggregateForm
     *
     * @return bool
     */
    protected function handleProductLabelAggregateForm(Request $request, FormInterface $aggregateForm)
    {
        $aggregateForm->handleRequest($request);

        if (!$aggregateForm->isValid()) {
            return false;
        }

        /** @var \Generated\Shared\Transfer\ProductLabelAggregateFormTransfer $aggregateFormTransfer */
        $aggregateFormTransfer = $aggregateForm->getData();

        $productLabelTransfer = $this->storeProductLabel($aggregateFormTransfer->getProductLabel());
        $this->storeRelatedProduct($aggregateFormTransfer->getAbstractProductRelations());

        $this->addSuccessMessage(sprintf(
            'Product label #%d successfully created.',
            $productLabelTransfer->getIdProductLabel()
        ));
    }

    /**
     * @param \Generated\Shared\Transfer\ProductLabelTransfer $productLabelTransfer
     *
     * @return ProductLabelTransfer
     */
    protected function storeProductLabel(ProductLabelTransfer $productLabelTransfer)
    {
        $this
            ->getFactory()
            ->getProductLabelFacade()
            ->createLabel($productLabelTransfer);

        return $productLabelTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductLabelAbstractProductRelationsTransfer $relationsTransfer
     *
     * @return void
     */
    protected function storeRelatedProduct(ProductLabelAbstractProductRelationsTransfer $relationsTransfer)
    {
        $this
            ->getFactory()
            ->getProductLabelFacade()
            ->setAbstractProductRelationsForLabel(
                $relationsTransfer->getIdProductLabel(),
                $relationsTransfer->getAbstractProductIds()
            );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction()
    {
        $productLabelTable = $this->getFactory()->createRelatedProductTable();

        return $this->jsonResponse($productLabelTable->fetchData());
    }

}
