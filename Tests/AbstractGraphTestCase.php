<?php declare(strict_types=1);

namespace Aircury\Collection\Tests;

use Fhaculty\Graph\Graph;
use PHPUnit\Framework\TestCase;

/**
 * Sample graphs shared by the graph tests.
 */
abstract class AbstractGraphTestCase extends TestCase
{
    protected const SAMPLE_VERTICES_IDS = ['v1', 'v2', 'v3', 'v4'];

    protected function useGraphLibraryToCreateASimpleDirectedGraphWithEdges(): Graph
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

    protected function useGraphLibraryToCreateADirectedGraphWithACycle(): Graph
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

    protected function useGraphLibraryToCreateASimpleLoopDirectedGraph(): Graph
    {
        // v1 --> v1
        $graph = new Graph();

        $graph->createVertex(self::SAMPLE_VERTICES_IDS[0]);

        $v1Vertex = $graph->getVertex(self::SAMPLE_VERTICES_IDS[0]);

        $v1Vertex->createEdgeTo($v1Vertex);

        return $graph;
    }
}
