<?php

namespace Pim\Bundle\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ValidatorInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use Pim\Bundle\CatalogBundle\AbstractController\AbstractDoctrineController;
use Pim\Bundle\GridBundle\Helper\DatagridHelperInterface;
use Pim\Bundle\CatalogBundle\Entity\AssociationType;
use Pim\Bundle\CatalogBundle\Form\Handler\AssociationTypeHandler;

/**
 * Association type controller
 *
 * @author    Filips Alpe <filips@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AssociationTypeController extends AbstractDoctrineController
{
    /**
     * @var DatagridHelperInterface
     */
    protected $datagridHelper;

    /**
     * @var AssociationTypeHandler
     */
    protected $assocTypeHandler;

    /**
     * @var Form
     */
    protected $assocTypeForm;

    /**
     * Constructor
     *
     * @param Request                  $request
     * @param EngineInterface          $templating
     * @param RouterInterface          $router
     * @param SecurityContextInterface $securityContext
     * @param FormFactoryInterface     $formFactory
     * @param ValidatorInterface       $validator
     * @param TranslatorInterface      $translator
     * @param RegistryInterface        $doctrine
     * @param DatagridHelperInterface  $dataGridHelper
     * @param AssociationTypeHandler   $assocTypeHandler
     * @param Form                     $assocTypeForm
     */
    public function __construct(
        Request $request,
        EngineInterface $templating,
        RouterInterface $router,
        SecurityContextInterface $securityContext,
        FormFactoryInterface $formFactory,
        ValidatorInterface $validator,
        TranslatorInterface $translator,
        RegistryInterface $doctrine,
        DatagridHelperInterface $dataGridHelper,
        AssociationTypeHandler $assocTypeHandler,
        Form $assocTypeForm
    ) {
        parent::__construct(
            $request,
            $templating,
            $router,
            $securityContext,
            $formFactory,
            $validator,
            $translator,
            $doctrine
        );

        $this->datagridHelper   = $dataGridHelper;
        $this->assocTypeHandler = $assocTypeHandler;
        $this->assocTypeForm    = $assocTypeForm;
    }

    /**
     * List association types
     *
     * @param Request $request
     *
     * @Template
     * @AclAncestor("pim_catalog_association_type_index")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $datagridView = $this->datagridHelper->getDatagrid('association_type')->createView();

        if ('json' === $request->getRequestFormat()) {
            return $this->datagridHelper->getDatagridRenderer()->renderResultsJsonResponse($datagridView);
        }

        return array(
            'datagrid' => $datagridView
        );
    }

    /**
     * Create an association type
     *
     * @param Request $request
     *
     * @Template
     * @AclAncestor("pim_catalog_association_type_create")
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('pim_catalog_association_type_index');
        }

        $associationType = new AssociationType();

        if ($this->assocTypeHandler->process($associationType)) {
            $this->addFlash('success', 'flash.association type.created');

            $response = array(
                'status' => 1,
                'url' =>
                    $this->generateUrl('pim_catalog_association_type_edit', array('id' => $associationType->getId()))
            );

            return new Response(json_encode($response));
        }

        return array(
            'form' => $this->assocTypeForm->createView(),
        );
    }

    /**
     * Edit an association type
     *
     * @param Request $request
     * @param integer $id
     *
     * @Template
     * @AclAncestor("pim_catalog_association_type_edit")
     * @return array
     */
    public function editAction(Request $request, $id)
    {
        $associationType = $this->findOr404('PimCatalogBundle:AssociationType', $id);

        if ($this->assocTypeHandler->process($associationType)) {
            $this->addFlash('success', 'flash.association type.updated');

            return $this->redirectToRoute('pim_catalog_association_type_edit', array('id' => $id));
        }

        $usageCount = $this
            ->getRepository('Pim\Bundle\CatalogBundle\Model\Association')
            ->countForAssociationType($associationType);

        return array(
            'form'            => $this->assocTypeForm->createView(),
            'historyDatagrid' => $this->getHistoryGrid($associationType)->createView(),
            'usageCount'      => $usageCount
        );
    }

    /**
     * History of an association type
     *
     * @param Request         $request
     * @param AssociationType $associationType
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|template
     */
    public function historyAction(Request $request, AssociationType $associationType)
    {
        $historyGridView = $this->getHistoryGrid($associationType)->createView();

        if ('json' === $request->getRequestFormat()) {
            return $this->datagridHelper->getDatagridRenderer()->renderResultsJsonResponse($historyGridView);
        }
    }

    /**
     * Remove an association type
     *
     * @param AssociationType $associationType
     *
     * @AclAncestor("pim_catalog_association_type_remove")
     * @return Response|RedirectResponse
     */
    public function removeAction(AssociationType $associationType)
    {
        $this->getManager()->remove($associationType);
        $this->getManager()->flush();

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new Response('', 204);
        } else {
            return $this->redirectToRoute('pim_catalog_association_type_index');
        }
    }

    /**
     * @param AssociationType $associationType
     *
     * @return Datagrid
     */
    protected function getHistoryGrid(AssociationType $associationType)
    {
        $historyGrid = $this->datagridHelper->getDataAuditDatagrid(
            $associationType,
            'pim_catalog_association_type_history',
            array('id' => $associationType->getId())
        );

        return $historyGrid;
    }
}
