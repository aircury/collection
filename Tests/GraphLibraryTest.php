<?php declare(strict_types=1);

namespace Aircury\Collection\Tests;

use Fhaculty\Graph\Graph;
use Graphp\Algorithms\Search\DepthFirst;
use PHPUnit\Framework\TestCase;

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
}
