<?php
class PPT_Test extends WP_UnitTestCase {
    public function setUp() {
        parent::setUp();
		$editor_id = $this->factory->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $editor_id );
        Post_Primary_Tag::factory()->setup();
        $this->test_post_id = $this->create_test_post();
    }

	public function create_test_post() {
        $this->factory->term->create( array( 'name' => 'ppt-tag-one') );
		$args = array(
			'public' => true,
            'post_type' => 'post',
            'post_title' => 'Test Post',
			'tags_input' => array('ppt-tag-one', 'ppt-tag-two', 'ppt-tag-three')
		);
        return $this->factory->post->create( $args );
	}

    public function tearDown() {
        parent::tearDown();
    }

    public function testObjectCreationFactory() {
        $test_obj = Post_Primary_Tag::factory();
        $this->assertTrue( is_a( $test_obj, 'Post_Primary_Tag' ) );
    }

    public function testGetPrimaryTagNameDefaultEmpty() {
        $ppt_name = ppt_get_primary_tag_name($this->test_post_id);
        $this->assertEquals('', $ppt_name);
    }

    public function testGetPrimaryTagName() {
        $this->markTestSkipped('Getting Invalid Term Issue (revisit)');
        $tag = get_term_by( 'slug', 'ppt-tag-one', 'post_tag');
        $result = update_post_meta( $this->test_post_id, 'pt_primary_tag_id', $tag->id );
        $ppt_name = ppt_get_primary_tag_name($this->test_post_id);
        $this->assertEquals('ppt-tag-one', $ppt_name);
    }
}
