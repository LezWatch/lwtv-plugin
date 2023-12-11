## Description

This plugin uses the autoload and template capabilities, as `lwtv_plugin()`.

## Components

A _component_ can be thought of as a _sub-plugin_. It is an atomic, independent module that stores business logic related to a specific feature. It may expose some template tags (functions) that can be called from within the themes.

All custom code needs should be added in the `/php/_components` directory. This includes all custom code needed for integration with plugins (Jetpack, ACF, etc).

The file should be named `class-[component].php` where `[component]` clearly indicates what the component is for. The file should be a part of the Components namespace (`LWTV\_Components`).

Components must be registered in the main `Plugin` class, in the `core_components()` function.

### Interfaces

All components will need to implement `Component` and, if needed, any of the other interfaces: `class Example implements Component {}`

Interfaces live in the `/php/_components` folder with the name `interface-[name].php`

There are the following interfaces available:

* `Component` - required by all components and used by the main `Plugin` class to initialize and manage components
* `Templater` - required if a component defines template tags, i.e. functions that are meant to be used outside the plugin, like `lwtv_plugin()->my_function_name()`

## Registering a Component

In order for our component to be initialized and available to the plugin, we have to edit `/php/class-plugin.php` and add the class name to the output array of the `core_components()` function. The order of calls added is important. If a class is used by other classes then it must be called _before_ them.

For example, if `Example` uses code from `Example_Requirement`, then your call must look like this:

```php
	protected function core_components() {
		return [
			_Components\Example_Requirement::class,
			_Components\Example::class,
		];
	}
```

## Initializing a Component

All classes implementing the `Component` interface are required to have an `init()` function, even if they don’t actively use it. The following placeholder can be used if there is no need for init:

```php
	/**
	 * Init the component. Hooks go in here.
	 *
	 * @return void
	 */
	public function init(): void {
		// We don't have something to do in here, but we need to keep it in order get component working.
	}
```

If there are actions and filters, it looks like this:

```php
	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function init(): void {
		add_action( 'init', [ $this, 'init_function' ] );
		add_filter( 'filter_name', [ $this, 'filter_name' ] );
	}
```

### Calling a 'sub' component

There are additional folders inside `/php/` that manage some of the more complex features.

To call a sub-component file, we make use of namespaces. For our example, we have a new folder and file: `/php/example/class-file.php`

And inside the main file, we call it as follows:

```
<?php
/*
 * Example
 */
namespace LWTV\_Components;

use LWTV\Example\File;

class Example implements Component, Templater {
	[code here]
}
```

This allows us to use `new File()` as we normally would summon a class.

## Exposing Template Tags

If the `Templater` interface is being used, add a function called `get_template_tags()` which will allow for a new function to be accessed from _outside_ the component via the global function `lwtv_plugin()`:

```php
	/**
	 * Gets tags to expose as methods accessible through `lwtv_plugin()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function get_template_tags(): array {
		return [
			'example_function' => [ $this, 'example_function' ],
		];
	}
```

For each item in `get_template_tags()`, there is a method name and a callback. The callback must either be a callable or an array with key `callable`. This approach is used to reserve the possibility of adding support for further arguments in the future.

To call `example_function` outside of the file, we use `lwtv_plugin()->example_function()`.

In order to minimize the potential for conflicts and to give hints to your IDE, all template tag functions added via the `Templater` interface must be added to the doc-block above the `Plugin` class in `class-plugin.php` like this:

```php
/**
 * Class Plugin
 *
 * @method void example_function( array $parameters ) \_Components\Example
 *
 */
```

Which translates as follows:

* `@method` - The fact this is a method.
* `void` - What the method returns. In this case, there is no return.
* `example_function( array $parameters )` - The function name as well as what parameters are passed.
* `\_Components\Example` - The component to which it belongs.

Interfaces may also be added here.


## Example Component

```
<?php
/*
 * Example
 */
namespace LWTV\_Components;

class Example implements Component, Templater {

	/*
	 * Init
	 */
	public function init(): void {
		add_filter( 'wp_random_example_filter', array( $this, 'example_filter' ) );
	}

	/**
	 * Gets tags to expose as methods accessible through `lwtv_plugin()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function get_template_tags(): array {
		return [
			'get_example' => array( $this, 'get_example' ),
		];
	}

	/**
	 * Get the example and return it.
	 */
	public static function get_example() {
		return 'This is an example';
	}

	/**
	 * Filter
	 */
	public static function example_filter() {
		return 'This is a filter';
	}
}
```
