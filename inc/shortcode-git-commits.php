<?php

namespace Sixohthree\Shortcodes;


/**
 * Sample usage:
 *
 * <h2>(master)$ git commit</h2>
 *
 * [git-commits]
 * { hash: 'a' },
 * { hash: 'b' },
 * { hash: 'c' },
 * { hash: 'd' }
 * [/git-commits]
 *
 * <h2>(master)$ git checkout -b dev</h2>
 *
 * [git-commits]
 * { hash: 'a' },
 * { hash: 'b' },
 * { hash: 'c', branch: 'dev' },
 * { hash: 'd', branch: 'dev' },
 * { hash: 'e', parents: ['b'] },
 * { hash: 'f', branch: 'dev', parents: ['d'] }
 * [/git-commits]
 *
 * <h2>(master)$ git rebase master</h2>
 *
 * <h3>Before:</h3>
 *
 * [git-commits]
 * { hash: 'a' },
 * { hash: 'b' },
 * { hash: 'c', branch: 'dev' },
 * { hash: 'd', parents: ['b'] },
 * { hash: 'e', branch: 'dev',  parents: ['c'] },
 * { hash: 'f', parents: ['d'] }
 * [/git-commits]
 *
 * <h3>After:</h3>
 *
 * [git-commits]
 * { hash: 'a' },
 * { hash: 'b' },
 * { hash: 'e' },
 * { hash: 'f' },
 * { hash: 'c', branch: 'dev' },
 * { hash: 'd', branch: 'dev' }
 * [/git-commits]
 *
 * <h2>(master)$ git merge --ff dev</h2>
 *
 * <h3>Before:</h3>
 *
 * [git-commits]
 * { hash: 'a' },
 * { hash: 'b' },
 * { hash: 'c', branch: 'dev' }
 * [/git-commits]
 *
 * <h3>After:</h3>
 *
 * [git-commits]
 * { hash: 'a' },
 * { hash: 'b' },
 * { hash: 'c' }
 * [/git-commits]
 *
 * <h2>(master)$ git merge --no-ff dev</h2>
 *
 * <h3>Before:</h3>
 *
 * [git-commits]
 * { hash: 'a' },
 * { hash: 'b' },
 * { hash: 'c', branch: 'dev' }
 * [/git-commits]
 *
 * <h3>After:</h3>
 *
 * [git-commits]
 * { hash: 'a' },
 * { hash: 'b' },
 * { hash: 'c', branch: 'dev' },
 * { hash: 'd', parents: ['b', 'c'] }
 * [/git-commits]
 *
 * <h2>(master)$ git merge --squash patch-1 patch-2</h2>
 *
 * <h3>Before:</h3>
 *
 * [git-commits]
 * { hash: 'a' },
 * { hash: 'b' },
 * { hash: 'c', branch: 'patch-1' },
 * { hash: 'd', branch: 'patch-2', parents: ['b'] },
 * { hash: 'e', branch: 'patch-1', parents: ['c'] }
 * [/git-commits]
 *
 * <h3>After:</h3>
 *
 * [git-commits]
 * { hash: 'a' },
 * { hash: 'b' },
 * { hash: 'c' }
 * [/git-commits]
 *
 * <h2>Multiple Branches</h2>
 *
 * [git-commits]
 * { hash: 'a' },
 * { hash: 'b' },
 * { hash: 'c', branch: 'dev' },
 * { hash: 'd', branch: 'dev' },
 * { hash: 'e', branch: 'fixup', parents: [ 'b' ] },
 * { hash: 'f', parents: [ 'e', 'b' ] },
 * { hash: 'g', parents: [ 'd', 'f' ] }
 * [/git-commits]
 */
class GitCommits {
    private $commit_groups = array();

    public function do_shortcode( $atts, $content = null ) {
        wp_enqueue_script( 'sixohthree_git_commits' );

        $id = 'gitcommit_block_' . count( $this->commit_groups );

        // linebreaks, and any unicode characters that aren't
        // translated correctly by iconv()
        $find = array('<br />', '′');
        $replace = array('' , "'");

        $content = html_entity_decode( $content );
        $content = str_replace( $find, $replace, $content );
        $content = iconv( 'UTF-8', 'ASCII//TRANSLIT', $content );
        $this->commit_groups[$id] = $content;

        return '<p id="' . esc_attr($id) . '"></p>';
    }

    private function has_commits() {
        return count($this->commit_groups) > 0;
    }

    public function do_footer() {
        if( !$this->has_commits() ) {
            return;
        }

        $html = '<script type="text/javascript">' .
            '(function(){' .
            'var c = new git_canvas();';

        foreach( $this->commit_groups as $name => $commits ) {
            $html .= $this->insert_group( $name, $commits );
        }

        $html .= '})();</script>';

        echo $html;
    }

    public function insert_group( $name, $commits ) {
        return 'c.commits([' . $commits . ']).draw("' . $name . '");';
    }

    public function init() {
        wp_register_script( 'sixohthree_git_commits', plugins_url( 'js/git-canvas.min.js', __DIR__ ), array( 'raphael' ), 1354330055, true );
        wp_register_script( 'raphael', plugins_url( 'js/raphael-min.js', __DIR__ ), null, 1354330055, true );

        remove_filter( 'the_content', 'wpautop' );
        add_filter( 'the_content', 'wpautop' , 99);
        add_filter( 'the_content', 'shortcode_unautop',100 );
        add_shortcode( 'git-commits', array( $this, 'do_shortcode' ) );
        add_action( 'wp_footer', array( $this, 'do_footer' ), 20 );
    }
}
