<?php

namespace EventBundle\ArgumentResolver;


use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class FosResolver implements ArgumentValueResolverInterface
{
    private $fetcher;

    public function __construct(ParamFetcherInterface $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    /**
     * Whether this resolver can resolve the value for the given ArgumentMetadata.
     *
     * @param Request $request
     * @param ArgumentMetadata $argument
     *
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        if (ParamFetcher::class !== $argument->getType()) {
            return false;
        }

        return $this->fetcher instanceof ParamFetcher;
    }

    /**
     * Returns the possible value(s).
     *
     * @param Request $request
     * @param ArgumentMetadata $argument
     *
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->fetcher;
    }
}