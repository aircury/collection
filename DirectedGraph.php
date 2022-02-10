<?php declare(strict_types=1);

/**
 * This class is a wrapper of the following libraries to be used as a dependency graph:
 * - GraPHP, the mathematical graph/network library written in PHP: https://github.com/graphp/graph
 * - Common mathematical graph algorithms implemented in PHP: https://github.com/graphp/algorithms
 */

namespace Aircury\Collection;

use Aircury\Collection\Exceptions\DuplicateVertexIdSuppliedException;
use Aircury\Collection\Exceptions\InvalidNumberOfVerticesException;
use Aircury\Collection\Exceptions\InvalidVertexIdTypeException;
use Aircury\Collection\Exceptions\VertexAlreadyExistsException;
use Aircury\Collection\Exceptions\VertexDoesNotExistException;
use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Graph as FhacultyGraph;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Vertex;
use Graphp\Algorithms\Search\DepthFirst;

class DirectedGraph
{
    /**
     * @var FhacultyGraph
     */
    private $graph;

    public function __construct()
    {
        $this->graph = new FhacultyGraph();
    }

    /**
     * @param int|string $vertexId
     */
    public function createVertex($vertexId = null): Vertex
    {
        try {
            return $this->graph->createVertex($vertexId);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidVertexIdTypeException((string) $vertexId, $e->getCode(), $e);
        } catch (\OverflowException $e) {
            throw new VertexAlreadyExistsException($vertexId, $e->getCode(), $e);
        }
    }

    public function createVerticesByIds(array $verticesIds): Vertices
    {
        try {
            return $this->graph->createVertices($verticesIds);
        } catch (\InvalidArgumentException $e) {
            if (false !== strpos($e->getMessage(), 'integer or string')) {
                throw new InvalidVertexIdTypeException();
            } else {
                throw new DuplicateVertexIdSuppliedException();
            }
        } catch (\OverflowException $e) {
            throw new VertexAlreadyExistsException();
        }
    }

    public function createVerticesByNumber(int $numberOfVertices): Vertices
    {
        try {
            return $this->graph->createVertices($numberOfVertices);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidNumberOfVerticesException();
        }
    }

    /**
     * @param int|string $sourceVertexId
     * @param int|string $targetVertexId
     */
    public function addDirectedEdgeByVerticesIds($sourceVertexId, $targetVertexId): Directed
    {
        $sourceVertex = $this->getVertexById($sourceVertexId);
        $targetVertex = $this->getVertexById($targetVertexId);

        return $sourceVertex->createEdgeTo($targetVertex);
    }

    public function getVertices(): Vertices
    {
        return $this->graph->getVertices();
    }

    public function getVerticesIds(): array
    {
        return $this->graph->getVertices()->getIds();
    }

    /**
     * @param int|string $vertexId
     */
    public function getVertexById($vertexId): Vertex
    {
        try {
            return $this->graph->getVertex($vertexId);
        } catch (\OutOfBoundsException $e) {
            throw new VertexDoesNotExistException($vertexId, $e->getCode(), $e);
        }
    }

    /**
     * @return int[]|string[] Array of vertices Ids that the $vertex depends on (direct and transitive dependencies)
     */
    public function getAllDependencies(Vertex $vertex): array
    {
        $vertexIdToRemove = $vertex->getId();
        $dfsResult = (new DepthFirst($vertex))->getVertices()->getIds();

        return array_values(
            array_filter(
                $dfsResult,
                static function ($vertexId) use ($vertexIdToRemove) {
                    return $vertexId !== $vertexIdToRemove;
                },
            ),
        );
    }
}
