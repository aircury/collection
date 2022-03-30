<?php declare(strict_types=1);

namespace Aircury\Collection\Tests;

use Aircury\Collection\Exceptions\NotSupportedGraphAlgorithmException;
use Aircury\Collection\TarjanStronglyConnectedComponents;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Set\Vertices;
use PHPUnit\Framework\TestCase;

class TarjanStronglyConnectedComponentsTest extends TestCase
{
    private const SAMPLE_VERTICES_IDS = ['v1', 'v2', 'v3', 'v4'];

    private function useGraphLibraryToCreateASimpleDirectedGraphWithEdges(): Graph
    {
        $graph = new Graph();

        /**
         * The expected graph (it must be directed pointing upwards):
         *
         * v1
         * |  \
         * v2  v3
         * |
         * v4
         */

        $graph->createVertices(self::SAMPLE_VERTICES_IDS);

        $v1Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[0]);
        $v2Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[1]);
        $v3Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[2]);
        $v4Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[3]);

        $v2Vertex->createEdgeTo($v1Vertex);
        $v3Vertex->createEdgeTo($v1Vertex);
        $v4Vertex->createEdgeTo($v2Vertex);

        return $graph;
    }

    private function useGraphLibraryToCreateADirectedGraphWithACycle(): Graph
    {
        $graph = new Graph();

        /**
         * The expected graph:
         *
         * v1
         * |  \
         * v2 -> v3
         * |
         * v4
         *
         * v1 depends on v2
         * v2 depends on v3
         * v3 depends on v1
         * v4 depends on v2
         */

        $graph->createVertices(self::SAMPLE_VERTICES_IDS);

        $v1Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[0]);
        $v2Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[1]);
        $v3Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[2]);
        $v4Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[3]);

        $v1Vertex->createEdgeTo($v2Vertex);
        $v2Vertex->createEdgeTo($v3Vertex);
        $v3Vertex->createEdgeTo($v1Vertex);
        $v4Vertex->createEdgeTo($v2Vertex);

        return $graph;
    }

    private function useGraphLibraryToCreateASimpleLoopDirectedGraph(): Graph
    {
        // v1 --> v1
        $graph = new Graph();

        $graph->createVertex(self::SAMPLE_VERTICES_IDS[0]);

        $v1Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[0]);

        $v1Vertex->createEdgeTo($v1Vertex);

        return $graph;
    }

    private function useGraphLibraryToCreateACompleteExampleDirectedGraphWithCyclesAndLoops(): Graph
    {
        // v1 <-- v2 <-- v6 === v7
        // | _____||     |      |
        // ||      |     |      |
        // v3 <-- v4 === v5 <-- v8*
        //
        // v1 depends on v3
        // v2 depends on v1
        // v3 depends on v2
        // v4 depends on v2, v3, v5
        // v5 depends on v4, v6
        // v6 depends on v2, v7
        // v7 depends on v6
        // v8 depends on v5, v7, v8 (loop)

        $graph = new Graph();

        $graph->createVertices(['v1', 'v2', 'v3', 'v4', 'v5', 'v6', 'v7', 'v8']);

        $v1Vertex = $graph->getVertex('v1');
        $v2Vertex = $graph->getVertex('v2');
        $v3Vertex = $graph->getVertex('v3');
        $v4Vertex = $graph->getVertex('v4');
        $v5Vertex = $graph->getVertex('v5');
        $v6Vertex = $graph->getVertex('v6');
        $v7Vertex = $graph->getVertex('v7');
        $v8Vertex = $graph->getVertex('v8');

        $v1Vertex->createEdgeTo($v3Vertex);
        $v2Vertex->createEdgeTo($v1Vertex);
        $v3Vertex->createEdgeTo($v2Vertex);
        $v4Vertex->createEdgeTo($v2Vertex);
        $v4Vertex->createEdgeTo($v3Vertex);
        $v4Vertex->createEdgeTo($v5Vertex);
        $v5Vertex->createEdgeTo($v4Vertex);
        $v5Vertex->createEdgeTo($v6Vertex);
        $v6Vertex->createEdgeTo($v2Vertex);
        $v6Vertex->createEdgeTo($v7Vertex);
        $v7Vertex->createEdgeTo($v6Vertex);
        $v8Vertex->createEdgeTo($v5Vertex);
        $v8Vertex->createEdgeTo($v7Vertex);
        $v8Vertex->createEdgeTo($v8Vertex);

        return $graph;
    }

    private function useGraphLibraryToCreateASimpleUndirectedGraph(): Graph
    {
        // v1 --- v2
        $graph = new Graph();

        $graph->createVertex(self::SAMPLE_VERTICES_IDS[0]);
        $graph->createVertex(self::SAMPLE_VERTICES_IDS[1]);

        $v1Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[0]);
        $v2Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[1]);

        $v1Vertex->createEdge($v2Vertex);

        return $graph;
    }

    private function useGraphLibraryToCreateASimpleMixedGraph(): Graph
    {
        // v1 --- v2 --> v3
        $graph = new Graph();

        $graph->createVertex(self::SAMPLE_VERTICES_IDS[0]);
        $graph->createVertex(self::SAMPLE_VERTICES_IDS[1]);
        $graph->createVertex(self::SAMPLE_VERTICES_IDS[2]);

        $v1Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[0]);
        $v2Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[1]);
        $v3Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[2]);

        $v1Vertex->createEdge($v2Vertex);
        $v2Vertex->createEdgeTo($v3Vertex);

        return $graph;
    }

    private function useGraphLibraryToCreateAGraphWithoutEdges(): Graph
    {
        // v1  v2  v3
        $graph = new Graph();

        $graph->createVertex(self::SAMPLE_VERTICES_IDS[0]);
        $graph->createVertex(self::SAMPLE_VERTICES_IDS[1]);
        $graph->createVertex(self::SAMPLE_VERTICES_IDS[2]);

        return $graph;
    }

    public function test_get_strongly_connected_components_for_an_acyclic_directed_graph(): void
    {
        $graph = $this->useGraphLibraryToCreateASimpleDirectedGraphWithEdges();

        $tarjanSCCAlgorithm = new TarjanStronglyConnectedComponents($graph);

        $expectedStronglyConnectedComponents = [
            ['v1'],
            ['v2'],
            ['v3'],
            ['v4'],
        ];

        $stronglyConnectedComponents = $tarjanSCCAlgorithm->getStronglyConnectedComponents();

        $this->checkExpectedSCCs($stronglyConnectedComponents, $expectedStronglyConnectedComponents);
    }

    public function test_get_strongly_connected_components_for_a_graph_with_a_loop(): void
    {
        $graph = $this->useGraphLibraryToCreateASimpleLoopDirectedGraph();

        $tarjanSCCAlgorithm = new TarjanStronglyConnectedComponents($graph);

        $expectedStronglyConnectedComponents = [
            ['v1'],
        ];

        $stronglyConnectedComponents = $tarjanSCCAlgorithm->getStronglyConnectedComponents();

        $this->checkExpectedSCCs($stronglyConnectedComponents, $expectedStronglyConnectedComponents);
    }

    public function test_get_strongly_connected_components_complete_example(): void
    {
        $graph = $this->useGraphLibraryToCreateACompleteExampleDirectedGraphWithCyclesAndLoops();

        $tarjanSCCAlgorithm = new TarjanStronglyConnectedComponents($graph);

        // Ordered by the moment the SCC is generated
        $expectedStronglyConnectedComponents = [
            ['v1', 'v2', 'v3'],
            ['v6', 'v7'],
            ['v4', 'v5'],
            ['v8'],
        ];

        $stronglyConnectedComponents = $tarjanSCCAlgorithm->getStronglyConnectedComponents();

        $this->checkExpectedSCCs($stronglyConnectedComponents, $expectedStronglyConnectedComponents);
    }

    public function test_get_strongly_connected_components_for_a_graph_without_edges(): void
    {
        $graph = $this->useGraphLibraryToCreateAGraphWithoutEdges();

        $tarjanSCCAlgorithm = new TarjanStronglyConnectedComponents($graph);

        $expectedStronglyConnectedComponents = [
            ['v1'],
            ['v2'],
            ['v3'],
        ];

        $stronglyConnectedComponents = $tarjanSCCAlgorithm->getStronglyConnectedComponents();

        $this->checkExpectedSCCs($stronglyConnectedComponents, $expectedStronglyConnectedComponents);
    }

    /**
     * @param Vertices[] $stronglyConnectedComponents
     * @param string[][] $expectedStronglyConnectedComponents
     */
    private function checkExpectedSCCs(
        array $stronglyConnectedComponents,
        array $expectedStronglyConnectedComponents
    ): void {
        foreach ($stronglyConnectedComponents as $index => $component) {
            $this->assertSame(
                $expectedStronglyConnectedComponents[$index],
                $component->getVerticesOrder(Vertices::ORDER_ID)->getIds(),
            );
        }
    }

    public function test_it_cannot_use_algorithm_for_an_undirected_graph(): void
    {
        $graphsToTest = [
            $this->useGraphLibraryToCreateASimpleUndirectedGraph(),
            $this->useGraphLibraryToCreateASimpleMixedGraph(),
        ];

        $exceptionsThrown = 0;

        foreach ($graphsToTest as $graph) {
            try {
                new TarjanStronglyConnectedComponents($graph);
            } catch (NotSupportedGraphAlgorithmException $e) {
                ++$exceptionsThrown;

                $this->assertEquals(
                    'Only directed graphs supported for Tarjan\'s Strongly Connected Components algorithm',
                    $e->getMessage(),
                );
            }
        }

        $this->assertEquals(count($graphsToTest), $exceptionsThrown);
    }
}
