<?php

namespace Pim\Bundle\ImportExportBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Pim\Bundle\CatalogBundle\Model\AttributeInterface;
use Pim\Bundle\CatalogBundle\Entity\AttributeOption;

/**
 * A normalizer to transform a AttributeInterface entity into array
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeNormalizer implements NormalizerInterface
{
    const LOCALIZABLE_PATTERN = '{locale}:{value}';
    const ITEM_SEPARATOR      = ',';
    const GROUP_SEPARATOR     = '|';
    const GLOBAL_SCOPE        = 'Global';
    const CHANNEL_SCOPE       = 'Channel';
    const ALL_LOCALES         = 'All';

    /**
     * @var array
     */
    protected $supportedFormats = array('json', 'xml');

    /**
     * @var TranslationNormalizer
     */
    protected $translationNormalizer;

    /**
     * Constructor
     *
     * @param TranslationNormalizer $translationNormalizer
     */
    public function __construct(TranslationNormalizer $translationNormalizer)
    {
        $this->translationNormalizer = $translationNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $results = array(
            'type' => $object->getAttributeType(),
            'code' => $object->getCode()
        ) + $this->translationNormalizer->normalize($object, $format, $context);

        $results = array_merge(
            $results,
            array(
                'group'                   => $object->getVirtualGroup()->getCode(),
                'unique'                  => (int) $object->isUnique(),
                'useable_as_grid_column'  => (int) $object->isUseableAsGridColumn(),
                'useable_as_grid_filter'  => (int) $object->isUseableAsGridFilter(),
                'allowed_extensions'      => implode(self::ITEM_SEPARATOR, $object->getAllowedExtensions()),
                'date_type'               => $object->getDateType(),
                'metric_family'           => $object->getMetricFamily(),
                'default_metric_unit'     => $object->getDefaultMetricUnit()
            )
        );
        if (isset($context['versioning'])) {
            $results = array_merge($results, $this->getVersionedData($object));
        } else {
            $results = array_merge(
                $results,
                array(
                    'translatable' => (int) $object->isTranslatable(),
                    'scopable'     => (int) $object->isScopable(),
                )
            );
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AttributeInterface && in_array($format, $this->supportedFormats);
    }

    /**
     * Get extra data to store in version
     *
     * @param AttributeInterface $attribute
     *
     * @return array
     */
    protected function getVersionedData(AttributeInterface $attribute)
    {
        $dateMin = (is_null($attribute->getDateMin())) ? '' : $attribute->getDateMin()->format(\DateTime::ISO8601);
        $dateMax = (is_null($attribute->getDateMax())) ? '' : $attribute->getDateMax()->format(\DateTime::ISO8601);

        return array(
            'available_locales'   => $this->normalizeAvailableLocales($attribute),
            'searchable'          => $attribute->isSearchable(),
            'localizable'         => $attribute->isTranslatable(),
            'scope'               => $attribute->isScopable() ? self::CHANNEL_SCOPE : self::GLOBAL_SCOPE,
            'options'             => $this->normalizeOptions($attribute),
            'default_options'     => $this->normalizeDefaultOptions($attribute),
            'sort_order'          => (int) $attribute->getSortOrder(),
            'required'            => (int) $attribute->isRequired(),
            'default_value'       => $this->normalizeDefaultValue($attribute),
            'max_characters'      => (string) $attribute->getMaxCharacters(),
            'validation_rule'     => (string) $attribute->getValidationRule(),
            'validation_regexp'   => (string) $attribute->getValidationRegexp(),
            'wysiwyg_enabled'     => (string) $attribute->isWysiwygEnabled(),
            'number_min'          => (string) $attribute->getNumberMin(),
            'number_max'          => (string) $attribute->getNumberMax(),
            'decimals_allowed'    => (string) $attribute->isDecimalsAllowed(),
            'negative_allowed'    => (string) $attribute->isNegativeAllowed(),
            'date_min'            => $dateMin,
            'date_max'            => $dateMax,
            'date_type'           => (string) $attribute->getDateType(),
            'metric_family'       => (string) $attribute->getMetricFamily(),
            'default_metric_unit' => (string) $attribute->getDefaultMetricUnit(),
            'max_file_size'       => (string) $attribute->getMaxFileSize(),
        );
    }

    /**
     * Normalize available locales
     *
     * @param AttributeInterface $attribute
     *
     * @return array
     */
    protected function normalizeAvailableLocales($attribute)
    {
        $locales = array();
        foreach ($attribute->getAvailableLocales() as $locale) {
            $locales[] = $locale->getCode();
        }

        return $locales;
    }

    /**
     * Normalize options
     *
     * @param AttributeInterface $attribute
     *
     * @return array
     */
    protected function normalizeOptions($attribute)
    {
        $data = array();
        $options = $attribute->getOptions();
        foreach ($options as $option) {
            $data[$option->getCode()] = array();
            foreach ($option->getOptionValues() as $value) {
                $data[$option->getCode()][$value->getLocale()] = $value->getValue();
            }
        }

        return $data;
    }

    /**
     * Normalize default value
     *
     * @param AttributeInterface $attribute
     *
     * @return array
     */
    protected function normalizeDefaultValue(AttributeInterface $attribute)
    {
        $defaultValue = $attribute->getDefaultValue();

        if ($defaultValue instanceof \DateTime) {
            return $defaultValue->format(\DateTime::ISO8601);
        } elseif ($defaultValue instanceof ArrayCollection || $defaultValue instanceof AttributeOption) {
            return $this->normalizeDefaultOptions($attribute);
        } else {
            return (string) $defaultValue;
        }
    }

    /**
     * Normalize default options
     *
     * @param AttributeInterface $attribute
     *
     * @return array
     */
    protected function normalizeDefaultOptions($attribute)
    {
        $data = array();
        $options = $attribute->getDefaultOptions();
        foreach ($options as $option) {
            $data[$option->getCode()] = array();
            foreach ($option->getOptionValues() as $value) {
                $data[$option->getCode()][$value->getLocale()] = $value->getValue();
            }
        }

        return $data;
    }
}
