<?php
namespace Pim\Bundle\DemoBundle\DataFixtures\ORM;

use Pim\Bundle\ProductBundle\Entity\CategoryTranslation;

use Pim\Bundle\ProductBundle\Entity\Category;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;

/**
 * Load data for category tree
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        // get products
        $product1 = $this->getReference('product.sku-001');
        $product2 = $this->getReference('product.sku-002');
        $product3 = $this->getReference('product.sku-003');
        $product4 = $this->getReference('product.sku-004');
        $product5 = $this->getReference('product.sku-005');

        // create trees
        $treeCatalog     = $this->createCategory('Master Catalog (default)');
        $treeCollections = $this->createCategory('Collections (default)');
        $treeColors      = $this->createCategory('Colors (default)');
        $treeSales       = $this->createCategory('Europe Sales Catalog (default)');

        // enrich master catalog with categories
        $nodeBooks       = $this->createCategory('Books (default)', $treeCatalog);
        $nodeComputers   = $this->createCategory('Computers (default)', $treeCatalog);
        $nodeDesktops    = $this->createCategory('Desktops (default)', $nodeComputers);
        $nodeNotebooks   = $this->createCategory('Notebooks (default)', $nodeComputers);
        $nodeAccessories = $this->createCategory('Accessories (default)', $nodeComputers);
        $nodeGames       = $this->createCategory('Games (default)', $nodeComputers);
        $nodeSoftware    = $this->createCategory('Software (default)', $nodeComputers);
        $nodeClothing    = $this->createCategory('Apparels & Shoes (default)', $treeCatalog);

        $nodeShirts = $this->createCategory('Shirts (default)', $nodeClothing, array($product5));
        $nodeJeans  = $this->createCategory('Jeans (default)', $nodeClothing, array($product3, $product4));
        $nodeShoes  = $this->createCategory('Shoes (default)', $nodeClothing, array($product1, $product2, $product3));

        $nodeClothingEu  = $this->createCategory('Apparels & Shoes (eu sales)', $treeSales);
        $nodeShirtsEu = $this->createCategory('Shirts (eu sales)', $nodeClothingEu, array($product5));
        $nodeJeansEu  = $this->createCategory('Jeans (eu sales)', $nodeClothingEu, array($product3, $product4));
        $nodeShoesEu  = $this->createCategory('Shoes (eu sales)', $nodeClothingEu, array($product1));

        // translate data in en_US
        $locale = $this->getReference('locale.en_US');
        $this->translate($treeCatalog, $locale, 'Master Catalog');
        $this->translate($treeCollections, $locale, 'Collections');
        $this->translate($treeColors, $locale, 'Colors');
        $this->translate($treeSales, $locale, 'Europe Sales Catalog');

        $this->translate($nodeBooks, $locale, 'Books');
        $this->translate($nodeComputers, $locale, 'Computers');
        $this->translate($nodeDesktops, $locale, 'Desktops');
        $this->translate($nodeNotebooks, $locale, 'Notebooks');
        $this->translate($nodeAccessories, $locale, 'Accessories');
        $this->translate($nodeGames, $locale, 'Games');
        $this->translate($nodeSoftware, $locale, 'Software');
        $this->translate($nodeClothing, $locale, 'Apparels & Shoes');

        $this->translate($nodeShirts, $locale, 'Shirts');
        $this->translate($nodeJeans, $locale, 'Jeans');
        $this->translate($nodeShoes, $locale, 'Shoes');

        $this->translate($nodeClothingEu, $locale, 'Apparels & Shoes in Europe Sales');
        $this->translate($nodeShirtsEu, $locale, 'Shirts');
        $this->translate($nodeJeansEu, $locale, 'Jeans');
        $this->translate($nodeShoesEu, $locale, 'Shoes');

        $this->manager->flush();

        // translate data in fr_FR
        $locale = $this->getReference('locale.fr_FR');
        $this->translate($treeCatalog, $locale, 'Catalogue Principal');
        $this->translate($treeCollections, $locale, 'Collections');
        $this->translate($treeColors, $locale, 'Couleurs');
        $this->translate($treeSales, $locale, 'Catalogue des ventes européennes');

        $this->translate($nodeBooks, $locale, 'Livres');
        $this->translate($nodeComputers, $locale, 'Ordinateurs');
        $this->translate($nodeDesktops, $locale, 'Ordinateurs de bureau');
        $this->translate($nodeNotebooks, $locale, 'Ordinateur portable');
        $this->translate($nodeAccessories, $locale, 'Accessoires');
        $this->translate($nodeGames, $locale, 'Jeux');
        $this->translate($nodeSoftware, $locale, 'Logiciels');
        $this->translate($nodeClothing, $locale, 'Habillements & Chaussures');

        $this->translate($nodeShirts, $locale, 'Chemises');
        $this->translate($nodeJeans, $locale, 'Jeans');
        $this->translate($nodeShoes, $locale, 'Chaussures');

        $this->translate($nodeClothingEu, $locale, 'Apparels & Shoes in Europe Sales in fr');
        $this->translate($nodeShirtsEu, $locale, 'Shirts in fr');
        $this->translate($nodeJeansEu, $locale, 'Jeans in fr');
        $this->translate($nodeShoesEu, $locale, 'Shoes in fr');

        $this->manager->flush();
    }

    /**
     * Translate a category
     *
     * @param Category $category Category
     * @param string   $locale   Locale used
     * @param string   $title    Title translated in locale value linked
     */
    protected function translate(Category $category, $locale, $title)
    {
        $translation = $this->createTranslation($category, $locale, $title);
        $category->addTranslation($translation);

        $this->manager->persist($category);
    }

    /**
     * Create a translation entity
     *
     * @param AttributeGroup $entity AttributeGroup entity
     * @param string         $locale Locale used
     * @param string         $title  Title translated in locale value linked
     *
     * @return \Pim\Bundle\ProductBundle\Entity\CategoryTranslation
     */
    protected function createTranslation($entity, $locale, $title)
    {
        $translation = new CategoryTranslation();
        $translation->setContent($title);
        $translation->setField('title');
        $translation->setForeignKey($entity);
        $translation->setLocale($locale);
        $translation->setObjectClass('Pim\Bundle\ProductBundle\Entity\Category');

        return $translation;
    }

    /**
     * Create a Category entity
     *
     * @param string   $title    Title of the category
     * @param Category $parent   Parent category
     * @param array    $products Products that should be associated to this category
     *
     * @return Category
     */
    protected function createCategory($title, $parent = null, $products = array())
    {
        $category = new Category();
        $category->setCode($title);
        $category->setTitle($title);
        $category->setParent($parent);

        $translation = $this->createTranslation($category, 'default', $title);
        $category->addTranslation($translation);

        foreach ($products as $product) {
            $category->addProduct($product);
        }

        $this->manager->persist($category);

        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 50;
    }
}
