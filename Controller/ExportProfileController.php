<?php
namespace Pim\Bundle\ProductBundle\Controller;

use Pim\Bundle\ProductBundle\Entity\ExportProfile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Export profile Controller
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @Route("/export-profile")
 */
class ExportProfileController extends Controller
{

    /**
     * List export profiles
     *
     * @Route("/index")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        $profiles = $this->getExportProfileRepository()->findAll();

        return array('profiles' => $profiles);
    }

    /**
     * Create export profile
     *
     * @Route("/create")
     * @Template("PimProductBundle:ExportProfile:edit.html.twig")
     *
     * @return array
     */
    public function createAction()
    {
        $profile = new ExportProfile();

        return $this->editAction($profile);
    }

    /**
     * Edit export profile
     *
     * @param ExportProfile $profile
     *
     * @Route("/edit/{id}", requirements={"id"="\d+"}, defaults={"id"=0})
     * @Template()
     *
     * @return array
     */
    public function editAction(ExportProfile $profile)
    {
        if ($this->get('pim_product.form.handler.export_profile')->process($profile)) {
            $this->get('session')->getFlashBag()->add('success', 'Export profile successfully saved');

            return $this->redirect(
                $this->generateUrl('pim_product_exportprofile_index')
            );
        }

        return array(
            'form' => $this->get('pim_product.form.export_profile')->createView()
        );
    }

    /**
     * Remove export profile
     *
     * @param ExportProfile $profile
     *
     * @Route("/remove/{id}", requirements={"id"="\d+"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(ExportProfile $profile)
    {
        $this->getEntityManager()->remove($profile);
        $this->getEntityManager()->flush();

        $this->get('session')->getFlashBag()->add('success', 'Export Profile successfully removed');

        return $this->redirect($this->generateUrl('pim_product_exportprofile_index'));
    }

    /**
     * Get entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getEntityManager();
    }

    /**
     * Get export profile repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getExportProfileRepository()
    {
        return $this->getEntityManager()->getRepository('PimProductBundle:ExportProfile');
    }
}
