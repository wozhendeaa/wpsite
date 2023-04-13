<?php

namespace TikTokFeed\Includes;

class TikTokFeedLoader
{
	protected $actions;

	protected $filters;

	protected $shortcodes;

	public function __construct()
	{
		$this->actions = [];
		$this->filters = [];
		$this->shortcodes = [];
	}

	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	public function add_shortcode(string $tag, $component, $callback)
    {
        $this->shortcodes[] = [
            'tag' => $tag,
            'component' => $component,
            'callback' => $callback,
        ];
    }

	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args )
	{

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	public function run()
	{

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ($this->shortcodes as $shortcode) {
		    add_shortcode($shortcode['tag'], [$shortcode['component'], $shortcode['callback']]);
        }
	}
}
