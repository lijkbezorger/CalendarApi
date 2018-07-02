<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace EventBundle\Service;


use EventBundle\Entity\IEvent;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class EventSaveService
{
    public function setDataToEntity(IEvent $existingEntity, IEvent $requestEntity, MetadataFactoryInterface $metaDataFactory)
    {
        if (!is_object($existingEntity) || !is_object($requestEntity)) {
            return false;
        }

        $objectClass = get_class($existingEntity);

        if (!is_a($existingEntity, $objectClass) || !is_a($requestEntity, $objectClass)) {
            return false;
        }

        $propertyAccessor = new PropertyAccessor();

        $propertyMetaData = $metaDataFactory->getMetadataForClass($objectClass)
            ->propertyMetadata;

        foreach ($propertyMetaData as $propertyName => $data) {
            $currentName = $data->name;
            $newValue = $propertyAccessor->getValue($requestEntity, $currentName);
            if ($newValue !== null) {
                $propertyAccessor->setValue($existingEntity, $currentName, $newValue);
            }
        }

        return $existingEntity;
    }
}