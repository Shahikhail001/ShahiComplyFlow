<?php
/**
 * Hook Loader Class
 *
 * Register all actions and filters for the plugin.
 *
 * @package ComplyFlow\Core
 * @since 1.0.0
 */

namespace ComplyFlow\Core;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Loader Class
 *
 * Maintains a list of all hooks that are registered throughout the plugin,
 * and registers them with the WordPress API. Run the loader to execute the list.
 *
 * @since 1.0.0
 */
class Loader {
    /**
     * Array of actions registered with WordPress
     *
     * @var array<int, array<string, mixed>>
     */
    protected array $actions = [];

    /**
     * Array of filters registered with WordPress
     *
     * @var array<int, array<string, mixed>>
     */
    protected array $filters = [];

    /**
     * Add a new action to the collection
     *
     * @param string   $hook          The name of the WordPress action that is being registered.
     * @param object   $component     A reference to the instance of the object on which the action is defined.
     * @param string   $callback      The name of the function definition on the $component.
     * @param int      $priority      Optional. The priority at which the function should be fired. Default is 10.
     * @param int      $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
     * @return void
     */
    public function add_action(string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1): void {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add a new filter to the collection
     *
     * @param string   $hook          The name of the WordPress filter that is being registered.
     * @param object   $component     A reference to the instance of the object on which the filter is defined.
     * @param string   $callback      The name of the function definition on the $component.
     * @param int      $priority      Optional. The priority at which the function should be fired. Default is 10.
     * @param int      $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
     * @return void
     */
    public function add_filter(string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1): void {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add hook to collection
     *
     * @param array<int, array<string, mixed>> $hooks         The collection of hooks that is being registered.
     * @param string                           $hook          The name of the WordPress action/filter being registered.
     * @param object                           $component     A reference to the instance of the object on which the hook is defined.
     * @param string                           $callback      The name of the function definition on the $component.
     * @param int                              $priority      The priority at which the function should be fired.
     * @param int                              $accepted_args The number of arguments that should be passed to the $callback.
     * @return array<int, array<string, mixed>>
     */
    private function add(array $hooks, string $hook, object $component, string $callback, int $priority, int $accepted_args): array {
        $hooks[] = [
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        ];

        return $hooks;
    }

    /**
     * Register hooks with WordPress
     *
     * Run all added actions and filters with WordPress.
     *
     * @return void
     */
    public function run(): void {
        foreach ($this->filters as $hook) {
            add_filter(
                $hook['hook'],
                [$hook['component'], $hook['callback']],
                $hook['priority'],
                $hook['accepted_args']
            );
        }

        foreach ($this->actions as $hook) {
            add_action(
                $hook['hook'],
                [$hook['component'], $hook['callback']],
                $hook['priority'],
                $hook['accepted_args']
            );
        }
    }

    /**
     * Get all registered actions
     *
     * @return array<int, array<string, mixed>>
     */
    public function get_actions(): array {
        return $this->actions;
    }

    /**
     * Get all registered filters
     *
     * @return array<int, array<string, mixed>>
     */
    public function get_filters(): array {
        return $this->filters;
    }

    /**
     * Remove all registered hooks
     *
     * @return void
     */
    public function remove_all_hooks(): void {
        $this->actions = [];
        $this->filters = [];
    }
}
