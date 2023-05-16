<?php

namespace App\Resolver;

use App\Attribute\MapRequestDTO;
use App\Helper\Helper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MapRequestDtoResolver implements ValueResolverInterface
{
    private PropertyAccessorInterface $propertyAccessor;
    private ValidatorInterface $validator;

    public function __construct(PropertyAccessorInterface $propertyAccessor, ValidatorInterface $validator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->validator = $validator;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $className = $argument->getType();
        $attribute = $argument->getAttributesOfType(MapRequestDTO::class)[0] ?? null;
        if ($attribute) {
            $data = $request->getContentTypeFormat() == "form" ? array_merge($request->request->all(), $request->files->all(), $request->query->all()) : json_decode($request->getContent(), 1);
            $dto = new $className($request);
            $reflect = new \ReflectionClass($dto);
            foreach ($reflect->getProperties() as $property) {
                $name = $property->getName();
                $value = $data[$name] ?? null;
                $this->propertyAccessor->setValue($dto, $name, $value);
            }
            $group[] = $attribute->getValidationGroup();
            $violation = Helper::formatViolationMessage($this->validator->validate($dto, null, $group));
            if ($violation) {
                throw new \Exception( $violation, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            yield $dto;
        }
        return [];

    }


}