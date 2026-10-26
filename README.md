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

Project type: internal tool not in production.

Commitment: `R3 D1 I:N/A C1 E:N/A L2 Y:N/A B:N/A S2 O:N/A P1 U:N/A T:N/A A:N/A`

Deviations: none.

Not applicable:

- `I` — Collection does not publish an HTTP API.
- `E` — there is no application a person uses in a browser.
- `Y` — it is not deployed. Connector consumes it as a path repository on `dev-master`.
- `B` — it retains no data and has no hosted environment.
- `O` — it has no production process.
- `U` — there is no service whose availability can be promised.
- `T` — it has no support channel of its own.
- `A` — it has no user interface.

Assessed on 2026-10-26 against the standards published at
<https://curipedia.aircury.net/development-standards>.
That page did not publish a revision identifier.
Project revision: `b8f1e974cc7e83b2cdf7a50eff21be8ff7f524cc`.
See [docs/development-standards-assessment.md](docs/development-standards-assessment.md).
<!-- development-standards:end -->
