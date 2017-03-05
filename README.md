# Collection

PHP Arrays with additional functionality. Arrays on steroids.

## Usage

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
