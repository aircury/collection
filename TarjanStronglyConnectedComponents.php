<?php declare(strict_types=1);

namespace Aircury\Collection;

use Aircury\Collection\Exceptions\NotSupportedGraphAlgorithmException;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Vertex;
use Graphp\Algorithms\Directed;

/**
 * Algorithm for getting the Strongly Connected Components (SCCs) of a directed Graph.
 * Based on the Tarjan's Strongly Connected Components algorithm.
 *
 * @link https://en.wikipedia.org/wiki/Tarjan%27s_strongly_connected_components_algorithm
 */
class TarjanStronglyConnectedComponents
{
    /**
     * @var Graph $graph
     */
    private $graph;

    /**
     * Map of the indexes for every vertex used, using the vertex ID as key.
     * Index represents the order the vertices are visited using DFS.
     * @var int[] $vertexIndex
     */
    private $vertexIndex;

    /**
     * Map of the low link values for every vertex used, using the vertex ID as key.
     * Low link represents the smallest index of any vertex on the stack known to be
     * reachable from the vertex represented by its ID as key through every successor
     * reached by DFS and including itself.
     * @var int[] $vertexLowLink
     */
    private $vertexLowLink;

    /**
     * @var Vertex[] $vertexStack
     */
    private $vertexStack;

    /**
     * @var boolean[] $onStack
     */
    private $onStack;

    /**
     * An array of Vertex sets that each one form an SCC
     * @var Vertices[] $stronglyConnectedComponents
     */
    private $stronglyConnectedComponents;

    /**
     * @var int $index
     */
    private $index;

    public function __construct(Graph $graph)
    {
        $algorithmDirected = new Directed($graph);

        if ($algorithmDirected->hasUndirected() || $algorithmDirected->isMixed()) {
            throw new NotSupportedGraphAlgorithmException(
                'Only directed graphs supported for Tarjan\'s Strongly Connected Components algorithm',
            );
        }

        $this->graph = $graph;

        $this->initialiseData();

        $this->stronglyConnectedComponents = [];
    }

    private function initialiseData(): void
    {
        $this->index = 0;
        $this->vertexIndex = [];
        $this->vertexLowLink = [];
        $this->onStack = [];
    }

    /**
     * @return Vertices[]
     */
    public function getStronglyConnectedComponents(): array
    {
        $this->stronglyConnectedComponents = [];
        $vertices = $this->graph->getVertices()->getVector();

        foreach ($vertices as $vertex) {
            if (!isset($this->vertexIndex[$vertex->getId()])) {
                $this->strongConnect($vertex);
            }
        }

        $this->initialiseData();

        return $this->stronglyConnectedComponents;
    }

    /**
     * This method uses DFS to visit all the vertices connected to the given vertex (root) and generate the SCCs.
     */
    private function strongConnect(Vertex $vertex): void
    {
        $this->vertexIndex[$vertex->getId()] = $this->index;
        $this->vertexLowLink[$vertex->getId()] = $this->index;
        $this->vertexStack[] = $vertex;
        $this->onStack[$vertex->getId()] = true;

        ++$this->index;

        $successors = $vertex->getVerticesEdgeTo()->getVector();

        foreach ($successors as $successor) {
            if (!isset($this->vertexIndex[$successor->getId()])) {
                // Successor has not yet been visited. Recurse on it.
                $this->strongConnect($successor);

                // Update the low link
                $this->vertexLowLink[$vertex->getId()] = min(
                    $this->vertexLowLink[$vertex->getId()],
                    $this->vertexLowLink[$successor->getId()],
                );
            } elseif (isset($this->onStack[$successor->getId()])) {
                // The successor is in the stack and hence in the current SSC. Update the current $vertex low link.
                // If it isn't then ($vertex, $successor) is an edge pointing to an SCC already found and must be ignored.
                $this->vertexLowLink[$vertex->getId()] = min(
                    $this->vertexLowLink[$vertex->getId()],
                    $this->vertexIndex[$successor->getId()],
                );
            }
        }

        // If $vertex is a root node, pop the stack until reaching the root node and generate an SCC.
        if ($this->vertexLowLink[$vertex->getId()] === $this->vertexIndex[$vertex->getId()]) {
            $newSCC = [];

            do {
                $poppedVertex = array_pop($this->vertexStack);

                unset($this->onStack[$poppedVertex->getId()]);

                $newSCC[] = $poppedVertex;
            } while ($vertex->getId() !== $poppedVertex->getID());

            $this->stronglyConnectedComponents[] = new Vertices($newSCC);
        }
    }
}
