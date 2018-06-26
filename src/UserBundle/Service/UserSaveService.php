<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace UserBundle\Service;


class UserSaveService
{
    public function setDataToEntity($existingEntity, $requestEntity)
    {
        if (!is_object($existingEntity) || !is_object($requestEntity)) {
            return false;
        }

        $objectClass = get_class($existingEntity);

        if (!is_a($existingEntity, $objectClass) || !is_a($requestEntity, $objectClass)) {
            return false;
        }

        $attributes = get_object_vars($requestEntity);

        foreach (get_object_vars($requestEntity) as $attribute) {
            if ($existingEntity->{$attribute} != $requestEntity->{$attribute}) {
                $existingEntity->{$attribute} = $requestEntity->{$attribute};
            }
        }
    }
}