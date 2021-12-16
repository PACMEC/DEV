<?php
namespace Elementor\Tests\Phpunit\Elementor\Modules\Usage;

use Elementor\Core\Base\Document;
use Elementor\Modules\Usage\Module;
use Elementor\Plugin;
use Elementor\Testing\Elementor_Test_Base;
use Elementor\Tests\Phpunit\Elementor\Modules\Usage\DynamicTags\Link;
use Elementor\Tests\Phpunit\Elementor\Modules\Usage\DynamicTags\Title;

class Test_Module extends Elementor_Test_Base {
	/**
	 * @var Document
	 */
	static $document;

	/**
	 * @var array
	 */
	static $document_mock_default = [
		'settings' => [
			'post_status' => 'publish'
		],
		'elements' => [
			[
				'id' => 'd50d8c5',
				'elType' => 'section',
				'isInner' => false,
				'settings' => [],
				'elements' =>
					[
						[
							'id' => 'a2e9b68',
							'elType' => 'column',
							'isInner' => false,
							'settings' => [ '_column_size' => 100, ],
							'elements' =>
								[
									[
										'id' => '5a1e8e5',
										'elType' => 'widget',
										'isInner' => false,
										'settings' => [ 'text' => 'I\'m not a default', ],
										'elements' => [],
										'widgetType' => 'button',
									],
								],
						],
					],
			],
		]
	];

	/**
	 * @var array
	 */
	static $document_mock_without_elements = [
		'settings' => [
			'post_status' => 'publish'
		],
		'elements' => []
	];

	/**
	 * @var array
	 */
	static $section_mock = [
		'id' => 'd50d8c5',
		'elType' => 'section',
		'isInner' => false,
		'settings' => [],
		'elements' => [],
	];

	/**
	 * @var array
	 */
	static $column_mock = [
		'id' => 'a2e9b68',
		'elType' => 'column',
		'isInner' => false,
		'settings' => [ '_column_size' => 100, ],
		'elements' => [],
	];

	/**
	 * @var array
	 */
	static $element_mock = [
		'id' => '5a1e8e5',
		'elType' => 'widget',
		'isInner' => false,
		'settings' => [ 'text' => 'I\'m not a default', ],
		'elements' => [],
		'widgetType' => 'button',
	];

	/**
	 * @var array
	 */
	static $element_with_dynamic_settings_mock = [
		'id' => '5a1e8e7',
		'elType' => 'widget',
		'settings' => [
			'title' => 'Add Your Heading Text Here',
			'header_size' => 'h3',
			'align' => 'right',
			'__dynamic__' => [
				'title' => '[elementor-tag id="2e7ade9" name="post-title" settings="%7B%7D"]',
				'link' => '[elementor-tag id="68a0003" name="post-url" settings="%7B%7D"]',
			],
		],
		'elements' => [],
		'widgetType' => 'heading',
	];

	/**
	 * @var string
	 */
	static $meta_data_mock_default = '[{"id":"3d605a1","elType":"section","settings":[],"elements":[{"id":"d7bc6ea","elType":"column","settings":{"_column_size":100},"elements":[{"id":"bf7ca8a","elType":"widget","settings":{"text":"Click here"},"elements":[],"widgetType":"button"}],"isInner":false}],"isInner":false}]';

	public function setUp() {
		parent::setUp();

		// Create post.
		self::$document = $this->create_post();

		// Save document.
		self::$document->save( self::$document_mock_default );
	}

	public function test_save_document_usage() {
		// Get meta.
		$usage_meta_after = self::$document->get_meta( Module::META_KEY );

		// Check meta.
		$this->assertEquals( 1, $usage_meta_after['button']['count'] );
	}

	public function test_add_to_global() {
		$doc_name = self::$document->get_name();

		// Get global usage.
		$global_usage = get_option( Module::OPTION_NAME, [] );

		// Check `$doc_name` exist in global usage.
		$this->assertArrayHaveKeys( [ $doc_name ], $global_usage );

		// Check `button` exist in global usage.
		$this->assertEquals( 1, $global_usage[ $doc_name ]['button']['count'] );
	}

	public function test_remove_from_global() {
		$doc_name = self::$document->get_name();

		// Save Document.
		self::$document->save( self::$document_mock_without_elements );

		// Get meta.
		$usage_meta_after = self::$document->get_meta( Module::META_KEY );

		// Check meta.
		$this->assertArrayNotHasKey( 'button', $usage_meta_after );

		// Get global.
		$global_usage = get_option( Module::OPTION_NAME, [] );

		// Check global.
		$this->assertArrayNotHasKey( $doc_name, $global_usage );
	}

	public function test_remove_data_after_delete_post() {
		$doc_name = self::$document->get_name();

		// Ensure global.
		$this->test_add_to_global();

		// Delete the post.
		wp_delete_post( self::$document->get_main_id(), true );

		// Get global.
		$global_usage = get_option( Module::OPTION_NAME, [] );

		// Check global.
		$this->assertArrayNotHasKey( $doc_name, $global_usage );
	}

	public function test_recalc() {
		$doc_name = self::$document->get_name();

		/** @var Module $module */
		$module = Module::instance();

		// Get document of new post.
		$document = $this->create_post();

		// Inject meta.
		$document->update_meta( '_elementor_data', self::$meta_data_mock_default );

		// Do recalc.
		$module->recalc_usage();

		// Get meta.
		$usage = $document->get_meta( Module::META_KEY );

		// Check meta.
		$this->assertEquals( 1, $usage['button']['count'] );

		// Get Global.
		$global_usage = get_option( Module::OPTION_NAME, [] );

		// Check if inject meta exist in `$global_usage`.
		$this->assertEquals( 2, $global_usage[ $doc_name ]['button']['count'] );
	}

	public function test_draft_and_republish() {
		$doc_name = self::$document->get_name();

		// Ensure global.
		$this->test_add_to_global();

		// Put to draft.
		wp_update_post( [
			'ID' => self::$document->get_main_id(),
			'post_status' => 'draft'
		] );

		// Get meta.
		$usage_meta_after = self::$document->get_meta( Module::META_KEY );

		// Check meta.
		$this->assertEquals( 'string', gettype( $usage_meta_after ) );

		// Get global.
		$global_usage = get_option( Module::OPTION_NAME, [] );

		// Check global.
		$this->assertArrayNotHasKey( $doc_name, $global_usage );

		// Put to published.
		wp_update_post( [
			'ID' => self::$document->get_main_id(),
			'post_status' => 'publish'
		] );

		// Check if `wp_update_post to published`, added it to global.
		$this->test_add_to_global();
	}

	public function test_draft_and_private() {
		$doc_name = self::$document->get_name();

		// Put to private.
		wp_update_post( [
			'ID' => self::$document->get_main_id(),
			'post_status' => 'private'
		] );

		// Check if, its still in global.
		$this->test_add_to_global();

		// Put to draft.
		wp_update_post( [
			'ID' => self::$document->get_main_id(),
			'post_status' => 'draft'
		] );

		// Get meta.
		$usage_meta_after = self::$document->get_meta( Module::META_KEY );

		// Check meta.
		$this->assertEquals( 'string', gettype( $usage_meta_after ) );

		// Get global.
		$global_usage = get_option( Module::OPTION_NAME, [] );

		// Check global.
		$this->assertArrayNotHasKey( $doc_name, $global_usage );
	}

	public function test_formatted_usage() {
		$doc_name = self::$document->get_name();
		/** @var Module $module */
		$module = Module::instance();

		// Get formatted usage.
		$formatted_usage = $module->get_formatted_usage();

		// Check if button exist and it value is `1`.
		$this->assertEquals( 1, $formatted_usage[ $doc_name ]['elements']['Button'] );
	}

	public function test_controls() {
		$doc_name = self::$document->get_name();

		// Ensure add to global.
		$this->test_add_to_global();

		// Get meta.
		$usage_meta_after = self::$document->get_meta( Module::META_KEY );

		// Check control exist.
		$this->assertEquals( 1, $usage_meta_after['button']['controls']['content']['section_button']['text'] );

		// Get global.
		$global_usage = get_option( Module::OPTION_NAME, [] );

		// Check global.
		$this->assertEquals( 1, $global_usage[ $doc_name ]['button']['controls']['content']['section_button']['text'] );

		// Create post.
		$document2 = $this->create_post();

		// Save document.
		$document2->save( self::$document_mock_default );

		// Get global.
		$global_usage = get_option( Module::OPTION_NAME, [] );

		// Check global.
		$this->assertEquals( 2, $global_usage[ $doc_name ]['button']['controls']['content']['section_button']['text'] );
	}

	public function test_elements() {
		$doc_name = self::$document->get_name();

		// Document with two elements.
		$document = self::$document_mock_without_elements;
		$column = self::$column_mock;
		$section = self::$section_mock;

		$column['elements'][] = self::$element_mock;
		$column['elements'][] = self::$element_mock;

		$section['elements'][] = $column;
		$document['elements'][] = $section;

		// Save document
		self::$document->save( $document );

		// Get global usage.
		$global_usage = get_option( Module::OPTION_NAME, [] );

		// Check if both new two elements exist in global usage.
		$this->assertEquals( 2, $global_usage[ $doc_name ]['button']['count'] );

		unset( $document['elements'][0]['elements'][0]['elements'][1] );

		// Save document
		self::$document->save( $document );

		// Get global usage.
		$global_usage = get_option( Module::OPTION_NAME, [] );

		// Check if both new two elements exist in global usage.
		$this->assertEquals( 1, $global_usage[ $doc_name ]['button']['count'] );
	}

	public function test_dynamic_control() {
		$doc_name = self::$document->get_name();

		Plugin::$instance->dynamic_tags->register_tag( new Title() );
		Plugin::$instance->dynamic_tags->register_tag( new Link() );

		// Document with element that includes control with dynamic settings.
		$document = self::$document_mock_without_elements;
		$section = self::$section_mock;
		$column = self::$column_mock;
		$element = self::$element_with_dynamic_settings_mock;

		$column['elements'][] = &$element;
		$section['elements'][] = &$column;
		$document['elements'][] = &$section;

		// Add element with dynamic control.
		self::$document->save( $document );

		// Check element with dynamic control.
		$global_usage = get_option( Module::OPTION_NAME, [] );

		$this->assertArrayHasKey( Module::GENERAL_TAB, $global_usage[ $doc_name ]['heading']['controls'] );
		$this->assertEquals(1, $global_usage[ $doc_name ]['heading']['controls']['content']['section_title']['link']);
		$this->assertEquals(1, $global_usage[ $doc_name ]['heading']['controls']['content']['section_title']['title']);

		// Remove settings.
		$element['settings'] = [];

		self::$document->save( $document );

		// Check element without dynamic control.
		$global_usage = get_option( Module::OPTION_NAME, [] );

		$this->assertEquals( 0, count( $global_usage[ $doc_name ]['heading']['controls'] ) );
	}

	/**
	 * @return Document|false
	 */
	private function create_post() {
		$admin = $this->factory()->create_and_get_administrator_user();

		wp_set_current_user( $admin->ID );

		$post = $this->factory()->create_and_get_custom_post( [
			'post_author' => $admin->ID,
			'post_type' => 'post',
		] );

		$document = self::elementor()->documents->get( $post->ID );

		return $document;
	}
}
