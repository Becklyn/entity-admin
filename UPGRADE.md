1.x to 2.0
==========

*   Non-`EntityInterface` usages are now allowed. The signature of some interfaces have changed: 
    *   `EntityUsagesProviderInterface::provideUsages()` can now return `object[]` instead of `EntityInterface[]`.
    *   `EntityUsageTransformerInterface::transform()` now receives `object` instead of `EntityInterface` as `$usage`.
