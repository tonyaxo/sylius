<?php

declare(strict_types=1);

namespace App\Factory\Product;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\ShopApiPlugin\Factory\ImageViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValuesViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;
use Sylius\ShopApiPlugin\View\Product\ProductTaxonView;
use Sylius\ShopApiPlugin\View\Product\ProductView;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class ProductViewFactory implements ProductViewFactoryInterface
{
    /** @var ImageViewFactoryInterface */
    private $imageViewFactory;

    /** @var ProductAttributeValuesViewFactoryInterface */
    private $attributeValuesViewFactory;

    /** @var string */
    private $productViewClass;

    /** @var string */
    private $productTaxonViewClass;

    /** @var string */
    private $fallbackLocale;

    public function __construct(
        ImageViewFactoryInterface $imageViewFactory,
        ProductAttributeValuesViewFactoryInterface $attributeValuesViewFactory,
        string $productViewClass,
        string $productTaxonViewClass,
        string $fallbackLocale
    ) {
        $this->imageViewFactory = $imageViewFactory;
        $this->attributeValuesViewFactory = $attributeValuesViewFactory;
        $this->productViewClass = $productViewClass;
        $this->productTaxonViewClass = $productTaxonViewClass;
        $this->fallbackLocale = $fallbackLocale;
    }

    /** {@inheritdoc} */
    public function create(ProductInterface $product, ChannelInterface $channel, string $locale): ProductView
    {
        /** @var ProductView $productView */
        $productView = new $this->productViewClass();
        $productView->code = $product->getCode();
        $productView->averageRating = (string) $product->getAverageRating();

        /** @var ProductTranslationInterface $translation */
        $translation = $product->getTranslation($locale);
        $productView->name = $translation->getName();
        $productView->slug = $translation->getSlug();
        $productView->description = $translation->getDescription();
        $productView->shortDescription = $translation->getShortDescription();
        $productView->metaKeywords = $translation->getMetaKeywords();
        $productView->metaDescription = $translation->getMetaDescription();
        $productView->channelCode = $channel->getCode();

        /** @var ProductImageInterface $image */
        foreach ($product->getImages() as $image) {
            $imageView = $this->imageViewFactory->create($image);
            $productView->images[] = $imageView;
        }

        /** @var ProductTaxonView $taxons */
        $taxons = new $this->productTaxonViewClass();
        if (null !== $product->getMainTaxon()) {
            $taxons->main = $product->getMainTaxon()->getCode();
        }

        /** @var TaxonInterface $taxon */
        foreach ($product->getTaxons() as $taxon) {
            $taxons->others[] = $taxon->getCode();
        }

        $productView->taxons = $taxons;

        $productView->attributes = $this->attributeValuesViewFactory->create(
            $product->getAttributesByLocale($locale, $this->fallbackLocale)->toArray(),
            $locale
        );

        $productView->options = [];
        /** @var ProductOptionInterface $option */
        foreach ($product->getOptions() as $option) {
            $values = [];
            /** @var ProductOptionValueInterface $optionValue */
            foreach ($option->getValues() as $optionValue) {
                $values[$optionValue->getCode()] = $optionValue->getTranslation($locale)->getValue();
            }

            $productView->options[] = [
                'code' => $option->getCode(),
                'name' => $option->getTranslation($locale)->getName(),
                'position' => $option->getPosition(),
                'values' => $values,
            ];
        }

        return $productView;
    }
}
