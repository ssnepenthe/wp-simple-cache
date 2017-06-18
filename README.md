# wp-simple-cache
This library wraps the WordPress transient and object caches in the PSR-16 simple cache interface.

This repo represents an experiment to see what a PSR-16 implementation for WordPress might look like.

It is inefficient, especially so for the `*Multiple()` methods and it is only tested against the [WP-Redis Object Cache Backend by Pantheon Systems](https://github.com/pantheon-systems/wp-redis/).

It is *probably* ok for use on a site where you have complete control and are using the WP-Redis object cache backend, although you might want to avoid the `*Multiple()` methods.

It is *most certainly not* ready for use in plugins or themes intended for public release/distribution.

**Basically - Don't use this.**
