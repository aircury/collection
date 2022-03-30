<?php declare(strict_types=1);

namespace Aircury\Collection\Tests;

use Fhaculty\Graph\Graph;
use Graphp\Algorithms\Search\DepthFirst;
use Graphp\Algorithms\TopologicalSort;
use PHPUnit\Framework\TestCase;

/**
 * This class is meant to be used as an entry point to start using the graph libraries:
 * - GraPHP, the mathematical graph/network library written in PHP: https://github.com/graphp/graph
 * - Common mathematical graph algorithms implemented in PHP: https://github.com/graphp/algorithms
 *
 * It can also be used as documentation for its functionalities or test anything before including it in another class.
 */
class GraphLibraryTest extends TestCase
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

    private function useGraphLibraryToCreateAGraphWithALoop(): Graph
    {
        // v1 --> v1
        $graph = new Graph();

        $graph->createVertex(self::SAMPLE_VERTICES_IDS[0]);

        $v1Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[0]);

        $v1Vertex->createEdgeTo($v1Vertex);

        return $graph;
    }

    public function test_i_can_create_a_graph(): void
    {
        $graph = $this->useGraphLibraryToCreateASimpleDirectedGraphWithEdges();

        $verticesIds = $graph->getVertices()->getIds();

        foreach (self::SAMPLE_VERTICES_IDS as $index => $viewName) {
            $this->assertEquals($viewName, $verticesIds[$index]);
        }

        $v1Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[0]);
        $v2Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[1]);
        $v3Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[2]);
        $v4Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[3]);

        $this->assertSame(
            [self::SAMPLE_VERTICES_IDS[1], self::SAMPLE_VERTICES_IDS[2]],
            $v1Vertex->getVerticesEdgeFrom()->getIds(),
        );
        $this->assertSame([self::SAMPLE_VERTICES_IDS[3]], $v2Vertex->getVerticesEdgeFrom()->getIds());
        $this->assertSame([], $v3Vertex->getVerticesEdgeFrom()->getIds());
        $this->assertSame([], $v4Vertex->getVerticesEdgeFrom()->getIds());
    }

    public function test_i_can_obtain_the_direct_and_transitive_dependencies_for_each_vertex_in_a_graph(): void
    {
        $graph = $this->useGraphLibraryToCreateASimpleDirectedGraphWithEdges();

        $expectedDependencies = [
            'v1' => ['v1'],
            'v2' => ['v2', 'v1'],
            'v3' => ['v3', 'v1'],
            'v4' => ['v4', 'v2', 'v1'],
        ];

        foreach (self::SAMPLE_VERTICES_IDS as $vertexId) {
            $vertexDFSAlg = new DepthFirst($graph->getVertex($vertexId));

            $this->assertEquals($expectedDependencies[$vertexId], $vertexDFSAlg->getVertices()->getIds());
        }
    }

    public function test_direct_and_transitive_dependencies_with_a_cycle(): void
    {
        $graph = $this->useGraphLibraryToCreateADirectedGraphWithACycle();

        $expectedDependencies = [
            'v1' => ['v1', 'v2', 'v3'],
            'v2' => ['v2', 'v3', 'v1'],
            'v3' => ['v3', 'v1', 'v2'],
            'v4' => ['v4', 'v2', 'v3', 'v1'],
        ];

        foreach (self::SAMPLE_VERTICES_IDS as $vertexId) {
            $vertexDFSAlg = new DepthFirst($graph->getVertex($vertexId));

            $this->assertEquals($expectedDependencies[$vertexId], $vertexDFSAlg->getVertices()->getIds());
        }
    }

    public function test_get_topological_sort_of_a_directed_graph(): void
    {
        $graph = $this->useGraphLibraryToCreateASimpleDirectedGraphWithEdges();
        $expectedOrder = ['v1', 'v2', 'v4', 'v3'];
        $verticesIdsSorted = (new TopologicalSort($graph))->getVertices()->getIds();

        $this->assertEquals($expectedOrder, array_reverse($verticesIdsSorted));
    }

    public function test_exception_for_a_topological_sort_for_a_graph_with_a_cycle_or_a_loop(): void
    {
        $graphsToTest = [
            $this->useGraphLibraryToCreateADirectedGraphWithACycle(),
            $this->useGraphLibraryToCreateAGraphWithALoop(),
        ];

        $numberOfExceptions = 0;

        foreach ($graphsToTest as $graph) {
            try {
                (new TopologicalSort($graph))->getVertices();
            } catch (\UnexpectedValueException $e) {
                ++$numberOfExceptions;

                $this->assertEquals('Not a DAG', $e->getMessage());
            }
        }

        $this->assertEquals(count($graphsToTest), $numberOfExceptions);
    }
}
