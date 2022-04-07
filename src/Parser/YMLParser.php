<?php

namespace Whitebox\EcommerceImport\Parser;

use Sirian\YMLParser\Category;
use Sirian\YMLParser\Offer\Offer;
use Sirian\YMLParser\Offer\VendorModelOffer;
use Whitebox\EcommerceImport\Schema;
use Sirian\YMLParser\Parser;
use Whitebox\EcommerceImport\Entity;
use Whitebox\EcommerceImport\Schema\Entity as EntitySchema;
use Whitebox\EcommerceImport\Schema\Param;

class YMLParser extends AbstractParser
{

    /**
     * @param string $file
     * @param Schema $schema
     * @return void
     */
    public function parse($file, Schema $schema)
    {
        $parser = new Parser();
        $result = $parser->parse($file);
        // NOTE: need to call it before accessing offers
        $result->getShop();
        $offer_schema = $schema->getEntity('offer');
        if (is_null($offer_schema)) {
            $this->addError('No offer entity');
            return [];
        }
        $entities = [];
        foreach ($result->getOffers() as $offer) {
            $entity = $this->offerToEntity($offer, $offer_schema);
            if (!is_null($entity)) {
                $entities[] = $entity;
            }
        }
        return $entities;
    }

    /**
     * @param string $file
     * @param Schema $schema
     * 
     * @return array
     */
    public function parseAll($file, Schema $schema)
    {
        $parser = new Parser();
        $result = $parser->parse($file);

        // NOTE: need to call it before accessing offers
        $shop = $result->getShop();

        $offer_schema = $schema->getEntity('offer');
        if (is_null($offer_schema)) {
            $this->addError('No offer entity');
            return [];
        }

        $cat_entities = [];
        foreach ($shop->getCategories() as $category) {
            $cat_entity = $this->categoryToEntity($category);
            if (!is_null($cat_entity)) {
                $cat_entities[] = $cat_entity;
            }
        }

        $entities = [];
        $par_entities = [];
        foreach ($result->getOffers() as $offer) {
            $entity = $this->offerToEntity($offer, $offer_schema);
            if (!is_null($entity)) {
                $entities[] = $entity;

                if (!empty($entity->params)) {
                    foreach ($entity->params as $parameter) {
                        $par_name = $parameter->getName();
                        $par_value = $parameter->getValue();

                        if (array_key_exists($par_name, $par_entities)) {
                            if (!in_array($par_value, $par_entities[$par_name]['values'])) {
                                $par_entities[$par_name]['values'][] = $par_value;
                            }
                        } else {
                            $par_entities[$par_name] = [
                                'type' => 'checkboxes',
                                'values' => [$par_value],
                            ];
                        }
                    }
                }
            }
        }
        
        // Set attribute types
        foreach ($par_entities as &$param) {
            $is_numeric = true;
            foreach ($param['values'] as $v) {
                $is_numeric = is_numeric($v);
                if (!$is_numeric) {
                    break;
                }
            }

            if ($is_numeric) {
                $param['type'] = 'number';
            }
        }

        return [
            'categories' => $cat_entities,
            'parameters' => $par_entities,
            'offers'     => $entities,
        ];
    }


    /**
     * @param Offer $offer
     * @param Param $param
     * @return mixed|null
     */
    public function extractParamValueFromOffer(Offer $offer, Param $param)
    {
        switch ($param->getName()) {
            case 'id':
                return $offer->getId();
            case 'name':
                return $offer->getName();
            case 'available':
                return $offer->isAvailable();
            case 'description':
                return $offer->getDescription();
            case 'price':
                return $offer->getPrice();
            case 'oldprice':
                $value = $this->extractParamValueFromOfferUsingOptions($offer, $param);
                return is_null($value) ? $value : floatval($value);
            case 'currency':
                $currency = $offer->getCurrency();
                return !is_null($currency) ? $currency->getId() : null;
            case 'vendor':
                return $offer instanceof VendorModelOffer ? $offer->getVendor() : null;
            case 'vendorCode':
                return $offer instanceof VendorModelOffer ? $offer->getVendorCode() : null;
            case 'pictures':
                return $offer->getPictures();
            case 'picture':
                return $offer->getPictures();
            case 'params':
                return $offer->getParams();
            case 'outlets':
                $value = $this->extractParamValueFromOfferUsingOptions($offer, $param);
                return $value;
            default:
                return $this->extractParamValueFromOfferUsingOptions($offer, $param);
        }
    }

    /**
     * @param Offer $offer
     * @param Param $param
     * @return mixed|null
     */
    public function extractParamValueFromOfferUsingOptions(Offer $offer, Param $param)
    {
        switch ($param->getParserOptions()) {
            case 'attribute':
                return $offer->getAttribute($param->getName());
            case 'instock':
                $data = $offer->getXml()->{$param->getName()};
                $amount = 0.0;
                foreach ($data->outlet as $item) {
                    $amount += floatval($item->attributes()['instock']->__toString());
                }
                return $amount;
            default:
                $xml = $offer->getXml();
                $xpath = $xml->xpath($param->getName());
                $result = array();
                foreach ($xpath as $value) {
                    $result[] = (string) $value;
                }
                if (empty($result)) {
                    return null;
                }
                return count($result) == 1
                    ? $result[0]
                    : $result;
        }
    }

    /**
     * @param Category $category
     * @param Param $param
     * 
     * @return mixed|null
     */
    public function categoryToEntity(Category $category)
    {
        $entity = new Entity('category');
        $entity->id = $category->getId();
        $entity->name = $category->getName();
        $entity->parent_id = ($category->getParent()) ? $category->getParent()->getId() : null;
        return $entity;
    }

    /**
     * @param Offer $offer
     * @param EntitySchema $schema
     * @return Entity|null
     */
    public function offerToEntity(Offer $offer, EntitySchema $schema)
    {
        $entity = new Entity('offer');
        $message = '%s, offer_id: ' . $offer->getId();
        if (($schema->hasParam('vendor') || $schema->hasParam('vendorCode'))
            && !($offer instanceof VendorModelOffer)
        ) {
            $this->addError(sprintf($message, 'Expected vendor model offer'));
            return null;
        }
        foreach ($schema->getParams() as $param) {
            $value = $this->extractParamValueFromOffer($offer, $param);
            
            if ($param->isRequired() && is_null($value)) {
                $this->addError(sprintf($message, 'Missing required param: ' . $param->getName()));
                return null;
            }
            if (is_null($value)) {
                $value = $param->getDefault();
            }

            $entity->{$param->getAlias()} = $value;
            
            if (!$param->isValidValue($value)) {
                if ($param->isRequired()) {
                    $this->addError(sprintf($message, 'Required param: "' . $param->getName() . '" is invalid'));
                    return null;
                }
                $this->addWarning(sprintf($message, 'Param is invalid: ' . $param->getName()));
            } else {
                $entity->{$param->getAlias()} = $value;
            }
        }
        return $entity;
    }
}
