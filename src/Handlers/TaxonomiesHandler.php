<?php
namespace GravApi\Handlers;

use GravApi\Resources\PageCollectionResource;

/**
 * Class TaxonomiesHandler
 * @package GravApi\Handlers
 */
class TaxonomiesHandler extends BaseHandler
{
    public function getTaxonomies($request, $response, $args)
    {
        $parsedBody = $request->getParsedBody();
        $operation = $parsedBody['operation'] ?? 'or';
        $filter = $parsedBody['taxonomyFilter'] ?? array();
        $collection = $this->grav['taxonomy']->findTaxonomy($filter, strtolower($operation));

        $resource = new PageCollectionResource($collection);

        return $response->withJson($resource->toJson());
    }
}
