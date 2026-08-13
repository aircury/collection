# Collection

PHP Arrays with additional functionality. Arrays on steroids.

## Usage

There are two ways of using this library. To create Collections of objects or Collections of PHP scalar types.

### Object Collections

To create you own Collection, the easiest way is to copy and adjust the sample `CarCollection`, replacing `Car` with
the class that you need.

More generically, these are the steps to create your own Collection:
  - Extend the `AbstractCollection` class
  - Implement on the child these methods, 
    - `getClass()` to provide information about the expected class or interface that all the objects on the collection
      should be.
    - `offsetGet($offset)` to specify the return class.
    - `toArray()` to provide PHPDoc information about the elements of the array.
    - `first()` to specify the return class.

### Scalar type Collections

This library comes out of the box with the following Collections, ready to use:
  - `StringCollection`
  - `StringOrNullCollection`
  - `IntegerCollection`
  - `IntegerOrNullCollection`

These classes include additional expected functionality that you would expect from the data type, e.g. `implode()`, 
`sum()`, ...

If they do not suit your needs, you can extend any of them or the `AbstractScalarTypeCollection` to implement your own. 

<!-- development-standards:begin -->
## Development standards

Commitment: `R1 D1 C1 L1 S1 P1`

Not applicable:

- `I` — this is a PHP library and does not expose an HTTP API.
- `E` — this package does not provide browser-based end-to-end journeys.
- `Y` — this package is not deployed as a standalone service.
- `B` — the package does not retain any data of its own.
- `O` — this package does not run as a production service with operational observability.
- `U` — this package does not publish an availability commitment.
- `T` — this package does not publish a support SLA.
- `A` — this package does not include user-facing browser UI.

Assessed on 2026-08-11 — see
[docs/development-standards-assessment.md](docs/development-standards-assessment.md).
<!-- development-standards:end -->
