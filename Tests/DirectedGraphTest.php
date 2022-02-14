<?php declare(strict_types=1);

namespace Aircury\Collection\Tests;

use Aircury\Collection\DirectedGraph;
use Aircury\Collection\Exceptions\DuplicateVertexIdSuppliedException;
use Aircury\Collection\Exceptions\InvalidNumberOfVerticesException;
use Aircury\Collection\Exceptions\InvalidVertexIdTypeException;
use Aircury\Collection\Exceptions\VertexAlreadyExistsException;
use Aircury\Collection\Exceptions\VertexDoesNotExistException;
use Fhaculty\Graph\Set\Vertices;
use PHPUnit\Framework\TestCase;

class DirectedGraphTest extends TestCase
{
    private const SAMPLE_VERTICES_IDS = ['v1', 'v2', 'v3', 'v4'];

    public function test_i_can_create_an_integer_vertex(): void
    {
        $graph = new DirectedGraph();

        $vertex = $graph->createVertex(8);

        $this->assertEquals(8, $vertex->getId());
    }

    public function test_i_can_create_a_string_vertex(): void
    {
        $graph = new DirectedGraph();

        $vertex = $graph->createVertex('v1');

        $this->assertEquals('v1', $vertex->getId());
    }

    public function test_i_can_create_a_null_id_vertex(): void
    {
        $graph = new DirectedGraph();

        $vertex1 = $graph->createVertex();
        $vertex2 = $graph->createVertex();

        $this->assertEquals(0, $vertex1->getId());
        $this->assertEquals(1, $vertex2->getId());
    }

    public function test_create_vertex_exception_for_wrong_id_type(): void
    {
        $graph = new DirectedGraph();

        $this->expectException(InvalidVertexIdTypeException::class);
        $this->expectExceptionMessage('Vertex ID type not allowed for \'7.5\'. Allowed types: string, integer');

        $graph->createVertex(7.5);
    }

    public function test_create_vertex_exception_for_an_already_existing_vertex(): void
    {
        $graph = new DirectedGraph();

        $graph->createVertex('v1');

        $this->expectException(VertexAlreadyExistsException::class);
        $this->expectExceptionMessage('Vertex v1 already exists');

        $graph->createVertex('v1');
    }

    public function test_get_vertex_by_id_exception_when_a_vertex_id_does_not_exist(): void
    {
        $graph = new DirectedGraph();

        $this->expectException(VertexDoesNotExistException::class);
        $this->expectExceptionMessage('Vertex v1 does not exist');

        $graph->getVertexById('v1');
    }

    public function test_create_vertices_by_ids(): void
    {
        $graph = new DirectedGraph();
        $idsArray = [2, 4, 6, 8];

        $graph->createVerticesByIds($idsArray);

        $verticesIds = $graph->getVerticesIds();

        foreach ($idsArray as $index => $id) {
            $this->assertEquals($id, $verticesIds[$index]);
        }
    }

    public function test_i_can_create_vertices_by_ids_mixing_valid_types(): void
    {
        $graph = new DirectedGraph();
        $idsArray = ['v1', 4, 'v2', 8];

        $graph->createVerticesByIds($idsArray);

        $verticesIds = $graph->getVerticesIds();

        foreach ($idsArray as $index => $id) {
            $this->assertEquals($id, $verticesIds[$index]);
        }
    }

    public function test_create_vertices_by_number_of_vertices(): void
    {
        $graph = new DirectedGraph();

        $graph->createVerticesByNumber(10);

        $verticesIds = $graph->getVerticesIds();

        foreach (range(0, 9) as $index => $id) {
            $this->assertEquals($id, $verticesIds[$index]);
        }
    }

    public function test_create_vertices_by_number_exception_for_negative_number_of_vertices(): void
    {
        $graph = new DirectedGraph();

        $this->expectException(InvalidNumberOfVerticesException::class);
        $this->expectExceptionMessage('Invalid number of vertices given. Must be non-negative integer');

        $graph->createVerticesByNumber(-2);
    }

    public function test_create_vertices_by_ids_exception_for_invalid_vertex_id_type(): void
    {
        $graph = new DirectedGraph();

        $this->expectException(InvalidVertexIdTypeException::class);
        $this->expectExceptionMessage('Vertex ID type not allowed. Allowed types: string, integer');

        $graph->createVerticesByIds(['v1', 4.5, 'v3']);
    }

    public function test_create_vertices_by_ids_exception_for_duplicated_vertices_ids_in_array(): void
    {
        $graph = new DirectedGraph();

        $this->expectException(DuplicateVertexIdSuppliedException::class);
        $this->expectExceptionMessage('All vertex IDs supplied must be unique');

        $graph->createVerticesByIds(['v1', 'v2', 'v1']);
    }

    public function test_create_vertices_by_ids_exception_for_vertex_id_that_already_existed(): void
    {
        $graph = new DirectedGraph();

        $graph->createVertex('v1');

        $this->expectException(VertexAlreadyExistsException::class);
        $this->expectExceptionMessage('Vertex ID supplied already exists');

        $graph->createVerticesByIds(['v1', 'v2', 'v3']);
    }

    private function createASimpleDirectedGraph(): DirectedGraph
    {
        /**
         * The expected graph (it must be directed pointing upwards):
         *
         * v1
         * |  \
         * v2   v3
         * |
         * v4
         */

        $graph = new DirectedGraph();

        $graph->createVerticesByIds(self::SAMPLE_VERTICES_IDS);

        $graph->addDirectedEdgeByVerticesIds(self::SAMPLE_VERTICES_IDS[1], self::SAMPLE_VERTICES_IDS[0]);
        $graph->addDirectedEdgeByVerticesIds(self::SAMPLE_VERTICES_IDS[2], self::SAMPLE_VERTICES_IDS[0]);
        $graph->addDirectedEdgeByVerticesIds(self::SAMPLE_VERTICES_IDS[3], self::SAMPLE_VERTICES_IDS[1]);

        return $graph;
    }

    private function createASimpleDirectedGraphWithACycle(): DirectedGraph
    {
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

        $graph = new DirectedGraph();

        $graph->createVerticesByIds(self::SAMPLE_VERTICES_IDS);

        $graph->addDirectedEdgeByVerticesIds(self::SAMPLE_VERTICES_IDS[0], self::SAMPLE_VERTICES_IDS[1]);
        $graph->addDirectedEdgeByVerticesIds(self::SAMPLE_VERTICES_IDS[1], self::SAMPLE_VERTICES_IDS[2]);
        $graph->addDirectedEdgeByVerticesIds(self::SAMPLE_VERTICES_IDS[2], self::SAMPLE_VERTICES_IDS[0]);
        $graph->addDirectedEdgeByVerticesIds(self::SAMPLE_VERTICES_IDS[3], self::SAMPLE_VERTICES_IDS[1]);

        return $graph;
    }

    private function createASimpleDirectedGraphWithALoop(): DirectedGraph
    {
        // v1 --> v1
        $graph = new DirectedGraph();

        $graph->createVerticesByIds([self::SAMPLE_VERTICES_IDS[0]]);
        $graph->addDirectedEdgeByVerticesIds(self::SAMPLE_VERTICES_IDS[0], self::SAMPLE_VERTICES_IDS[0]);

        return $graph;
    }

    public function test_i_can_create_a_directed_graph(): void
    {
        $graph = $this->createASimpleDirectedGraph();
        $vertices = $graph->getVertices();

        $this->assertCount(4, $vertices);

        $verticesIds = $graph->getVerticesIds();

        foreach (self::SAMPLE_VERTICES_IDS as $index => $vertexId) {
            $this->assertEquals($vertexId, $verticesIds[$index]);
        }

        $v1Vertex = $graph->getVertexById(self::SAMPLE_VERTICES_IDS[0]);
        $v2Vertex = $graph->getVertexById(self::SAMPLE_VERTICES_IDS[1]);
        $v3Vertex = $graph->getVertexById(self::SAMPLE_VERTICES_IDS[2]);
        $v4Vertex = $graph->getVertexById(self::SAMPLE_VERTICES_IDS[3]);

        $this->assertSame(
            [self::SAMPLE_VERTICES_IDS[1], self::SAMPLE_VERTICES_IDS[2]],
            $v1Vertex->getVerticesEdgeFrom()->getIds(),
        );
        $this->assertSame([self::SAMPLE_VERTICES_IDS[3]], $v2Vertex->getVerticesEdgeFrom()->getIds());
        $this->assertSame([], $v3Vertex->getVerticesEdgeFrom()->getIds());
        $this->assertSame([], $v4Vertex->getVerticesEdgeFrom()->getIds());
    }

    public function test_exception_when_trying_to_add_a_directed_edge_and_the_source_vertex_id_does_not_exist(): void
    {
        $graph = new DirectedGraph();

        $this->expectException(VertexDoesNotExistException::class);
        $this->expectExceptionMessage('Vertex v1 does not exist');

        $graph->addDirectedEdgeByVerticesIds('v1', 'v2');
    }

    public function test_exception_when_trying_to_add_a_directed_edge_and_the_target_vertex_id_does_not_exist(): void
    {
        $graph = new DirectedGraph();

        $graph->createVertex('v1');

        $this->expectException(VertexDoesNotExistException::class);
        $this->expectExceptionMessage('Vertex v2 does not exist');

        $graph->addDirectedEdgeByVerticesIds('v1', 'v2');
    }

    public function test_i_can_get_the_direct_and_transitive_dependencies_of_every_vertex_in_a_directed_graph(): void
    {
        $graph = $this->createASimpleDirectedGraph();

        $expectedDependencies = [
            'v1' => [],
            'v2' => ['v1'],
            'v3' => ['v1'],
            'v4' => ['v2', 'v1'],
        ];

        $verticesArray = $graph->getVertices()->getVector();

        foreach (self::SAMPLE_VERTICES_IDS as $index => $vertexId) {
            $this->assertEquals($expectedDependencies[$vertexId], $graph->getAllDependencies($verticesArray[$index]));
        }
    }

    public function test_i_can_get_all_dependencies_of_every_vertex_in_a_directed_graph_with_a_cycle(): void
    {
        $graph = $this->createASimpleDirectedGraphWithACycle();

        $expectedDependencies = [
            'v1' => ['v2', 'v3'],
            'v2' => ['v3', 'v1'],
            'v3' => ['v1', 'v2'],
            'v4' => ['v2', 'v3', 'v1'],
        ];

        $verticesArray = $graph->getVertices()->getVector();

        foreach (self::SAMPLE_VERTICES_IDS as $index => $vertexId) {
            $this->assertEquals($expectedDependencies[$vertexId], $graph->getAllDependencies($verticesArray[$index]));
        }
    }

    public function test_get_cycles_for_a_directed_graph_without_cycles(): void
    {
        $graph = $this->createASimpleDirectedGraph();

        $cycles = $graph->getCycles();

        $this->assertEmpty($cycles);
    }

    public function test_get_cycles_for_a_directed_graph_with_a_cycle(): void
    {
        $graph = $this->createASimpleDirectedGraphWithACycle();

        $cycles = $graph->getCycles();

        $this->assertNotEmpty($cycles);

        $expectedCycles = [
            ['v1', 'v2', 'v3'],
        ];

        foreach ($cycles as $index => $vertexSet) {
            $this->assertSame($expectedCycles[$index], $vertexSet->getVerticesOrder(Vertices::ORDER_ID)->getIds());
        }
    }

    public function test_get_cycles_for_a_directed_graph_with_a_loop(): void
    {
        $graph = $this->createASimpleDirectedGraphWithALoop();

        $cycles = $graph->getCycles();

        $this->assertNotEmpty($cycles);

        $expectedCycles = [
            ['v1'],
        ];

        foreach ($cycles as $index => $vertexSet) {
            $this->assertSame($expectedCycles[$index], $vertexSet->getVerticesOrder(Vertices::ORDER_ID)->getIds());
        }
    }
}
